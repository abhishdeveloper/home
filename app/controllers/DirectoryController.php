<?php

class DirectoryController extends Controller {
    public function __construct() {
        Session::init();
    }

    public function index() {
        $clinicModel = $this->model('ClinicModel');
        $patientModel = $this->model('PatientModel');
        $specialtyModel = $this->model('SpecialtyModel');

        $preferred_specialty_id = null;
        $fallback_msg = '';

        if (Session::get('user_id') && Session::get('user_role_id') == 3) {
            $profile = $patientModel->getProfileByUserId(Session::get('user_id'));
            if ($profile) {
                $preferred_specialty_id = $profile->preferred_specialty_id;
            }
        }

        $clinics = [];

        if ($preferred_specialty_id) {
            $clinics = $clinicModel->searchClinics($preferred_specialty_id);
            if (empty($clinics)) {
                // Fallback to Ayurveda (assuming ID 1 from our seed data, but let's fetch it safely)
                $specialties = $specialtyModel->getAll();
                $ayurveda_id = null;
                foreach ($specialties as $spec) {
                    if (strtolower($spec->name) == 'ayurveda') {
                        $ayurveda_id = $spec->id;
                        break;
                    }
                }

                if ($ayurveda_id) {
                    $clinics = $clinicModel->searchClinics($ayurveda_id);
                    $fallback_msg = 'No doctors available in your preferred specialty right now. Showing Ayurveda specialists instead.';
                }
            }
        } else {
            // Show all
            $clinics = $clinicModel->searchClinics();
        }

        $data = [
            'clinics' => $clinics,
            'fallback_msg' => $fallback_msg
        ];

        $this->view('directory/index', $data);
    }
}
