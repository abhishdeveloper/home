<?php

class PaymentController extends Controller {
    public function __construct() {
        Session::init();
        if (!Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function checkout() {
        if (!isset($_GET['type']) || !isset($_GET['ref_id'])) {
            die("Invalid payment request.");
        }

        $type = $_GET['type'];
        $ref_id = (int)$_GET['ref_id'];

        $base_amount = 0.00;
        $title = "Payment Checkout";
        $description = "";

        if ($type == 'profile_upgrade') {
            if (Session::get('user_role_id') != 2) die("Unauthorized");
            $base_amount = 999.00; // Fixed demo upgrade fee
            $title = "Upgrade to Premium Profile";
            $description = "Remove universal branding from your clinic public profile.";
        } elseif ($type == 'appointment') {
            if (Session::get('user_role_id') != 3) die("Unauthorized");
            // ref_id is the appointment_id. We need to fetch the clinic's fee.
            $appointmentModel = $this->model('AppointmentModel');
            $appt = $appointmentModel->getById($ref_id);
            if (!$appt || $appt->patient_id != Session::get('user_id') || $appt->status != 'pending_payment') {
                die("Invalid or already paid appointment.");
            }

            $clinicModel = $this->model('ClinicModel');
            $clinic = $clinicModel->getProfileById($appt->clinic_id);
            $base_amount = $clinic->consultation_fee;
            $title = "Appointment Consultation Fee";
            $description = "Consultation with " . $appt->clinic_name . " on " . $appt->appointment_date . " at " . $appt->appointment_time;
        } else {
            die("Unknown payment type.");
        }

        // Calculate Breakdown
        // User requested: 5% service tax added on doctor fee, and platform commission 5% additional after service tax.
        // Tax = Base * 0.05
        // Platform Fee = (Base + Tax) * 0.05
        // Total = Base + Tax + Platform Fee

        $tax_amount = round($base_amount * 0.05, 2);
        $platform_fee = round(($base_amount + $tax_amount) * 0.05, 2);
        $total_amount = $base_amount + $tax_amount + $platform_fee;

        $data = [
            'type' => $type,
            'ref_id' => $ref_id,
            'title' => $title,
            'description' => $description,
            'base_amount' => $base_amount,
            'tax_amount' => $tax_amount,
            'platform_fee' => $platform_fee,
            'total_amount' => $total_amount,
            'error' => ''
        ];

        $this->view('payment/checkout', $data);
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $type = $_POST['type'];
            $ref_id = (int)$_POST['ref_id'];
            $status = $_POST['payment_status']; // 'success' or 'failed' from our dummy buttons

            $base_amount = (float)$_POST['base_amount'];
            $tax_amount = (float)$_POST['tax_amount'];
            $platform_fee = (float)$_POST['platform_fee'];
            $total_amount = (float)$_POST['total_amount'];

            if ($status == 'failed') {
                if ($type == 'appointment') {
                    // Mark appointment as rejected or delete it so the slot frees up
                    $this->model('AppointmentModel')->updateStatus($ref_id, 'rejected');
                    header('Location: ' . URL_ROOT . '/patientDashboard?error=Payment Failed');
                } else {
                    header('Location: ' . URL_ROOT . '/billing?error=Payment Failed');
                }
                exit;
            }

            // Success Flow
            $user_id = Session::get('user_id');

            // 1. Record Transaction
            $this->model('ClinicModel')->db->query('INSERT INTO payments (user_id, payment_type, reference_id, base_amount, tax_amount, platform_fee, total_amount, status) VALUES (:user_id, :type, :ref_id, :base, :tax, :plat, :total, "success")');
            $this->model('ClinicModel')->db->bind(':user_id', $user_id);
            $this->model('ClinicModel')->db->bind(':type', $type);
            $this->model('ClinicModel')->db->bind(':ref_id', $ref_id);
            $this->model('ClinicModel')->db->bind(':base', $base_amount);
            $this->model('ClinicModel')->db->bind(':tax', $tax_amount);
            $this->model('ClinicModel')->db->bind(':plat', $platform_fee);
            $this->model('ClinicModel')->db->bind(':total', $total_amount);
            $this->model('ClinicModel')->db->execute();

            // 2. Handle Entity Logic
            if ($type == 'profile_upgrade') {
                $clinicModel = $this->model('ClinicModel');
                $profile = $clinicModel->getProfileByUserId($user_id);
                $clinicModel->upgradeBranding($profile->id);

                // Send success email
                $userModel = $this->model('UserModel');
                $user = $userModel->findUserById($user_id);
                if ($user) {
                    $subject = "Upgrade Successful - Branding Removed";
                    $body = "<h2>Thank you for upgrading!</h2><p>Your payment of ₹{$total_amount} was successful.</p><p>The universal platform branding has now been removed from your public clinic profile.</p>";
                    EmailHelper::sendEmail($user->email, $subject, $body);
                }

                header('Location: ' . URL_ROOT . '/billing?success=1');
                exit;

            } elseif ($type == 'appointment') {
                $appointmentModel = $this->model('AppointmentModel');
                // Move from pending_payment to pending (awaiting doctor approval)
                $appointmentModel->updateStatus($ref_id, 'pending');

                $appt = $appointmentModel->getById($ref_id);

                // Email Clinic about new paid request
                $userModel = $this->model('UserModel');
                $clinicUser = $userModel->findUserById($appt->doctor_user_id);
                if ($clinicUser) {
                    $subject = "New Paid Appointment Request";
                    $body = "<h2>New Appointment Request</h2><p>You have a new paid appointment request from {$appt->patient_name} for {$appt->appointment_date} at {$appt->appointment_time}.</p><p>Please log in to your dashboard to approve or reject it.</p>";
                    EmailHelper::sendEmail($clinicUser->email, $subject, $body);
                }

                header('Location: ' . URL_ROOT . '/patientDashboard?msg=Payment Successful. Appointment Request Sent.');
                exit;
            }
        }
    }
}
