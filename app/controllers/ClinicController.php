<?php

class ClinicController extends Controller {
    public function view($slug = '', $page_slug = '') {
        if (empty($slug)) {
            header('Location: ' . URL_ROOT . '/directory');
            exit;
        }

        $clinicModel = $this->model('ClinicModel');
        $themeModel = $this->model('ThemeModel');

        $profile = $clinicModel->getProfileBySlug($slug);

        if (!$profile) {
            die("Clinic profile not found or is currently unpublished.");
        }

        $theme = $themeModel->getById($profile->theme_id);

        $pages = $clinicModel->getPages($profile->id);

        $currentPage = null;
        $homePage = null;

        // Find requested page and homepage
        foreach ($pages as $p) {
            if ($p->status == 'published') {
                if ($p->is_home) {
                    $homePage = $p;
                }
                if ($page_slug && $p->slug == $page_slug) {
                    $currentPage = $p;
                }
            }
        }

        // If no specific page requested, default to home page
        if (!$currentPage && empty($page_slug)) {
            $currentPage = $homePage;
        }

        // If still no page, and they have published pages, pick the first one
        if (!$currentPage) {
            foreach ($pages as $p) {
                if ($p->status == 'published') {
                    $currentPage = $p;
                    break;
                }
            }
        }

        $scheduleModel = $this->model('ScheduleModel');
        $schedule = $scheduleModel->getScheduleByClinicId($profile->id);

        $reviewModel = $this->model('ReviewModel');
        $clinicReviews = $reviewModel->getReviewsForClinic($profile->id);
        $avgRating = $reviewModel->getClinicAverageRating($profile->id);

        $data = [
            'profile' => $profile,
            'theme' => $theme,
            'pages' => $pages, // to build nav
            'current_page' => $currentPage,
            'schedule' => $schedule,
            'reviews' => $clinicReviews,
            'avgRating' => $avgRating
        ];

        $this->view('clinic/public_view', $data);
    }

    public function getAvailableSlots($clinic_id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date'])) {
            $date = $_POST['date']; // YYYY-MM-DD

            $scheduleModel = $this->model('ScheduleModel');
            $appointmentModel = $this->model('AppointmentModel');

            $schedule = $scheduleModel->getScheduleByClinicId($clinic_id);

            if (!$schedule) {
                echo json_encode(['error' => 'Clinic schedule not set.']);
                exit;
            }

            // Determine day of week string to match database columns
            $dayOfWeek = strtolower(date('l', strtotime($date)));

            $startCol = $dayOfWeek . '_start';
            $endCol = $dayOfWeek . '_end';

            if (empty($schedule->$startCol) || empty($schedule->$endCol)) {
                echo json_encode(['slots' => []]); // Closed on this day
                exit;
            }

            $startTime = strtotime($schedule->$startCol);
            $endTime = strtotime($schedule->$endCol);
            $duration = $schedule->slot_duration * 60; // Convert to seconds

            $bookedTimes = $appointmentModel->getBookedTimes($clinic_id, $date);

            $availableSlots = [];

            while ($startTime + $duration <= $endTime) {
                $timeString = date('H:i', $startTime);

                // If the time is not already booked, add it
                if (!in_array($timeString, $bookedTimes)) {
                    // Also check if slot is in the past if it's today
                    if ($date > date('Y-m-d') || ($date == date('Y-m-d') && $startTime > time())) {
                        $availableSlots[] = $timeString;
                    }
                }

                $startTime += $duration;
            }

            echo json_encode(['slots' => $availableSlots]);
            exit;
        }
    }

    public function bookAppointment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'error' => 'CSRF Token Validation Failed']);
                exit;
            }

            if (!Session::get('user_id') || Session::get('user_role_id') != 3) {
                echo json_encode(['success' => false, 'error' => 'You must be logged in as a patient to book appointments.']);
                exit;
            }

            $patientModel = $this->model('PatientModel');
            $patientProfile = $patientModel->getProfileByUserId(Session::get('user_id'));

            if (!$patientProfile) {
                echo json_encode(['success' => false, 'error' => 'Please complete your patient profile first.']);
                exit;
            }

            $clinic_id = (int)$_POST['clinic_id'];
            $appointment_date = trim($_POST['appointment_date']);
            $appointment_time = trim($_POST['appointment_time']);

            // Validate that the slot is actually available on the backend
            $scheduleModel = $this->model('ScheduleModel');
            $appointmentModel = $this->model('AppointmentModel');
            $schedule = $scheduleModel->getScheduleByClinicId($clinic_id);

            if (!$schedule) {
                echo json_encode(['success' => false, 'error' => 'Clinic schedule is not set.']);
                exit;
            }

            $dayOfWeek = strtolower(date('l', strtotime($appointment_date)));
            $startCol = $dayOfWeek . '_start';
            $endCol = $dayOfWeek . '_end';

            if (empty($schedule->$startCol) || empty($schedule->$endCol)) {
                echo json_encode(['success' => false, 'error' => 'Clinic is closed on this day.']);
                exit;
            }

            $startTime = strtotime($schedule->$startCol);
            $endTime = strtotime($schedule->$endCol);
            $duration = $schedule->slot_duration * 60;
            $bookedTimes = $appointmentModel->getBookedTimes($clinic_id, $appointment_date);

            $isValidSlot = false;
            while ($startTime + $duration <= $endTime) {
                $timeString = date('H:i', $startTime);
                if ($timeString === substr($appointment_time, 0, 5) && !in_array($timeString, $bookedTimes)) {
                    // Check if time is in the past
                    if ($appointment_date > date('Y-m-d') || ($appointment_date == date('Y-m-d') && $startTime > time())) {
                        $isValidSlot = true;
                    }
                    break;
                }
                $startTime += $duration;
            }

            if (!$isValidSlot) {
                echo json_encode(['success' => false, 'error' => 'Invalid or already booked time slot.']);
                exit;
            }

            $data = [
                'patient_id' => Session::get('user_id'),
                'clinic_id' => $clinic_id,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time
            ];

            if ($appointmentModel->createAppointment($data)) {
                // Email Clinic
                $clinicModel = $this->model('ClinicModel');
                // The $_POST['clinic_id'] is actually the clinic profile ID in our schema
                $profileRow = $clinicModel->getProfileById((int)$_POST['clinic_id']);

                if ($profileRow) {
                    $userModel = $this->model('UserModel');
                    $clinicUser = $userModel->findUserById($profileRow->user_id);
                    if ($clinicUser) {
                        $subject = "New Appointment Request";
                        $body = "<h2>New Appointment Request</h2><p>You have a new appointment request for {$data['appointment_date']} at {$data['appointment_time']}.</p><p>Please log in to your dashboard to approve or reject it.</p>";
                        EmailHelper::sendEmail($clinicUser->email, $subject, $body);
                    }
                }

                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to book appointment.']);
            }
            exit;
        }
    }
}
