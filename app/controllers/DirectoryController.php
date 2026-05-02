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

        // Check for active search from GET parameters
        $search_query = isset($_GET['q']) ? trim($_GET['q']) : null;
        $search_specialty_id = isset($_GET['specialty']) && !empty($_GET['specialty']) ? (int)$_GET['specialty'] : null;

        if ($search_query !== null || $search_specialty_id !== null) {
            // User is actively searching
            $clinics = $clinicModel->searchClinics($search_specialty_id, $search_query);

            if (empty($clinics)) {
                $fallback_msg = 'No clinics found matching your search criteria.';
            }
        } else {
            // Default view based on patient preferences
            if ($preferred_specialty_id) {
                $clinics = $clinicModel->searchClinics($preferred_specialty_id);
                if (empty($clinics)) {
                    // Fallback to Ayurveda
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
        }

        // Advanced SEO Meta Tags
        $metaTitle = 'Find Top Doctors and Clinics Near You | Directory';
        $metaDesc = 'Search our comprehensive medical directory to find top-rated doctors and clinics. Book appointments instantly online for free.';

        if ($search_specialty_id) {
            $specialties = $specialtyModel->getAll();
            $specName = '';
            foreach ($specialties as $spec) {
                if ($spec->id == $search_specialty_id) {
                    $specName = $spec->name;
                    break;
                }
            }
            if ($specName) {
                $metaTitle = "Best {$specName} Doctors and Clinics | Directory";
                $metaDesc = "Find and book top-rated {$specName} specialists near you. Read patient reviews and schedule your visit instantly.";
            }
        }

        $data = [
            'clinics' => $clinics,
            'fallback_msg' => $fallback_msg,
            'specialties' => $specialtyModel->getAll(),
            'current_q' => $search_query ?? '',
            'current_specialty' => $search_specialty_id ?? '',
            'title' => $metaTitle,
            'meta_desc' => $metaDesc
        ];

        $this->view('directory/index', $data);
    }
}
