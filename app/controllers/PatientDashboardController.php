<?php

class PatientDashboardController extends Controller {
    public function __construct() {
        Session::init();

        if (!Session::get('user_id') || Session::get('user_role_id') != 3) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function index() {
        $patientModel = $this->model('PatientModel');
        $appointmentModel = $this->model('AppointmentModel');
        $userModel = $this->model('UserModel');

        $user_id = Session::get('user_id');
        $profile = $patientModel->getProfileByUserId($user_id);
        $user = $userModel->findUserById($user_id);

        if (!$profile) {
            header('Location: ' . URL_ROOT . '/onboarding/patient');
            exit;
        }

        $stats = $appointmentModel->getPatientDashboardStats($user_id);
        $upcoming = $appointmentModel->getUpcomingPatientAppointments($user_id, 3);

        $data = [
            'user' => $user,
            'profile' => $profile,
            'stats' => $stats,
            'upcoming_appointments' => $upcoming
        ];

        $this->view('patient/dashboard', $data);
    }
}
