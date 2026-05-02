<?php
class AppointmentController extends Controller {
    public function __construct() {
        Session::init();
        if (!Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function index() {
        $appointmentModel = $this->model('AppointmentModel');
        $role_id = Session::get('user_role_id');

        $data = [
            'appointments' => []
        ];

        if ($role_id == 3) {
            // Patient
            $data['appointments'] = $appointmentModel->getPatientAppointments(Session::get('user_id'));
            $this->view('appointments/patient_list', $data);
        } elseif ($role_id == 2) {
            // Clinic/Doctor
            $clinicModel = $this->model('ClinicModel');
            $profile = $clinicModel->getProfileByUserId(Session::get('user_id'));
            if ($profile) {
                $data['appointments'] = $appointmentModel->getClinicAppointments($profile->id);
            }
            $this->view('appointments/clinic_list', $data);
        } else {
            header('Location: ' . URL_ROOT . '/pages/index');
        }
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Session::get('user_role_id') == 2) {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $appointment_id = (int)$_POST['appointment_id'];
            $status = $_POST['status'];

            $appointmentModel = $this->model('AppointmentModel');
            $appt = $appointmentModel->getById($appointment_id);

            // Verify doctor owns this appointment
            $clinicModel = $this->model('ClinicModel');
            $profile = $clinicModel->getProfileByUserId(Session::get('user_id'));

            if ($appt && $profile && $appt->clinic_id == $profile->id) {
                if (in_array($status, ['approved', 'rejected', 'completed'])) {
                    $appointmentModel->updateStatus($appointment_id, $status);

                    // Send Email to Patient
                    $userModel = $this->model('UserModel');
                    $patientUser = $userModel->findUserById($appt->patient_id);
                    if ($patientUser) {
                        $subject = "Appointment Status Updated";
                        $body = "<h2>Appointment {$status}</h2><p>Your appointment with {$profile->clinic_name} on {$appt->appointment_date} at {$appt->appointment_time} has been marked as <strong>{$status}</strong>.</p>";

                        if ($status == 'approved') {
                            $body .= "<p>You can now log in to access the secure chat and video call link.</p>";
                        }

                        EmailHelper::sendEmail($patientUser->email, $subject, $body);
                    }
                }
            }

            header('Location: ' . URL_ROOT . '/appointment');
            exit;
        }
    }

    public function view($id) {
        $appointmentModel = $this->model('AppointmentModel');
        $appt = $appointmentModel->getById($id);

        if (!$appt) {
            die("Appointment not found.");
        }

        // Verify access (must be the patient or the doctor)
        if (Session::get('user_id') != $appt->patient_id && Session::get('user_id') != $appt->doctor_user_id) {
            die("Unauthorized access.");
        }

        $reviewModel = $this->model('ReviewModel');
        $patientReview = $reviewModel->getPatientReviewByAppointment($id);
        $clinicReview = $reviewModel->getClinicReviewByAppointment($id);

        $attachments = $appointmentModel->getAttachments($id);

        $data = [
            'appointment' => $appt,
            'patientReview' => $patientReview,
            'clinicReview' => $clinicReview,
            'attachments' => $attachments,
            'attachment_error' => ''
        ];

        // Handle attachment upload
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['attachment'])) {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            // Only allow uploads if appointment is approved or pending
            if ($appt->status == 'completed' || $appt->status == 'rejected') {
                $data['attachment_error'] = 'Cannot upload files to completed or rejected appointments.';
            } else {
                if ($_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = APP_ROOT . '/public/attachments/';
                    $fileTmp = $_FILES['attachment']['tmp_name'];
                    $originalName = basename($_FILES['attachment']['name']);
                    $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];

                    if (in_array($fileExt, $allowedExts)) {
                        $newFileName = md5(time() . $originalName . Session::get('user_id')) . '.' . $fileExt;
                        $destPath = $uploadDir . $newFileName;
                        if (move_uploaded_file($fileTmp, $destPath)) {
                            $file_url = '/attachments/' . $newFileName;
                            $appointmentModel->addAttachment($id, Session::get('user_id'), Security::escape($originalName), $file_url);
                            header('Location: ' . URL_ROOT . '/appointment/view/' . $id);
                            exit;
                        } else {
                            $data['attachment_error'] = 'Failed to move uploaded file.';
                        }
                    } else {
                        $data['attachment_error'] = 'Invalid file type. Allowed: JPG, PNG, PDF, DOC, DOCX.';
                    }
                } else {
                    $data['attachment_error'] = 'File upload error.';
                }
            }
        }

        $this->view('appointments/view', $data);
    }

    // AJAX Chat Endpoints
    public function getMessages($appointment_id) {
        $appointmentModel = $this->model('AppointmentModel');
        $appt = $appointmentModel->getById($appointment_id);

        if ($appt && (Session::get('user_id') == $appt->patient_id || Session::get('user_id') == $appt->doctor_user_id)) {
            $messages = $appointmentModel->getMessages($appointment_id);
            echo json_encode(['messages' => $messages, 'current_user_id' => Session::get('user_id')]);
        } else {
            echo json_encode(['error' => 'Unauthorized']);
        }
        exit;
    }

    public function saveNotes() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && Session::get('user_role_id') == 2) {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'error' => 'CSRF Failed']);
                exit;
            }

            $appointment_id = (int)$_POST['appointment_id'];
            $notes = $_POST['notes']; // Raw text, we will escape on output

            $appointmentModel = $this->model('AppointmentModel');
            $appt = $appointmentModel->getById($appointment_id);

            if ($appt && $appt->doctor_user_id == Session::get('user_id')) {
                if ($appointmentModel->updatePrivateNotes($appointment_id, $notes)) {
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
    }

    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'error' => 'CSRF Token Validation Failed']);
                exit;
            }

            $appointment_id = (int)$_POST['appointment_id'];
            $message = trim($_POST['message']);

            if (empty($message)) {
                echo json_encode(['success' => false, 'error' => 'Message is empty']);
                exit;
            }

            $appointmentModel = $this->model('AppointmentModel');
            $appt = $appointmentModel->getById($appointment_id);

            if ($appt && $appt->status == 'approved' && (Session::get('user_id') == $appt->patient_id || Session::get('user_id') == $appt->doctor_user_id)) {
                if ($appointmentModel->addMessage($appointment_id, Session::get('user_id'), Security::escape($message))) {
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
            echo json_encode(['success' => false, 'error' => 'Unauthorized or failed to send']);
            exit;
        }
    }
}
