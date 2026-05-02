<?php

class ReviewController extends Controller {
    public function __construct() {
        Session::init();
        if (!Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $appointment_id = (int)$_POST['appointment_id'];
            $rating = (int)$_POST['rating'];
            $review_text = trim(htmlspecialchars($_POST['review_text'] ?? ''));

            if ($rating < 1 || $rating > 5) {
                die("Invalid rating.");
            }

            $appointmentModel = $this->model('AppointmentModel');
            $appt = $appointmentModel->getById($appointment_id);

            if (!$appt || $appt->status != 'completed') {
                die("Appointment not found or not completed.");
            }

            $reviewModel = $this->model('ReviewModel');
            $role_id = Session::get('user_role_id');

            if ($role_id == 3 && $appt->patient_id == Session::get('user_id')) {
                // Patient reviewing clinic
                if (!$reviewModel->getClinicReviewByAppointment($appointment_id)) {
                    $data = [
                        'appointment_id' => $appointment_id,
                        'patient_id' => Session::get('user_id'),
                        'clinic_id' => $appt->clinic_id,
                        'rating' => $rating,
                        'review_text' => $review_text
                    ];
                    $reviewModel->addClinicReview($data);
                }
            } elseif ($role_id == 2 && $appt->doctor_user_id == Session::get('user_id')) {
                // Clinic reviewing patient
                if (!$reviewModel->getPatientReviewByAppointment($appointment_id)) {
                    $data = [
                        'appointment_id' => $appointment_id,
                        'clinic_id' => $appt->clinic_id,
                        'patient_id' => $appt->patient_id,
                        'rating' => $rating,
                        'review_text' => $review_text
                    ];
                    $reviewModel->addPatientReview($data);
                }
            } else {
                die("Unauthorized.");
            }

            header('Location: ' . URL_ROOT . '/appointment/view/' . $appointment_id);
            exit;
        }
    }
}
