<?php

class OnboardingController extends Controller {
    public function __construct() {
        Session::init();

        if (!Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function index() {
        $role_id = Session::get('user_role_id');

        if ($role_id == 3) {
            // Patient
            $patientModel = $this->model('PatientModel');
            if (!$patientModel->getProfileByUserId(Session::get('user_id'))) {
                header('Location: ' . URL_ROOT . '/onboarding/patient');
                exit;
            } else {
                header('Location: ' . URL_ROOT . '/directory');
                exit;
            }
        } elseif ($role_id == 2) {
            // Clinic/Doctor
            $clinicModel = $this->model('ClinicModel');
            if (!$clinicModel->getProfileByUserId(Session::get('user_id'))) {
                header('Location: ' . URL_ROOT . '/onboarding/clinic');
                exit;
            } else {
                header('Location: ' . URL_ROOT . '/clinicDashboard');
                exit;
            }
        } elseif ($role_id == 1) {
            // Admin
            header('Location: ' . URL_ROOT . '/admin/settings');
            exit;
        } else {
            header('Location: ' . URL_ROOT . '/pages/index');
            exit;
        }
    }

    public function patient() {
        if (Session::get('user_role_id') != 3) {
            header('Location: ' . URL_ROOT . '/onboarding');
            exit;
        }

        $patientModel = $this->model('PatientModel');
        $specialtyModel = $this->model('SpecialtyModel');

        // Check if already has profile
        $existingProfile = $patientModel->getProfileByUserId(Session::get('user_id'));

        $data = [
            'age' => $existingProfile ? $existingProfile->age : '',
            'address' => $existingProfile ? $existingProfile->address : '',
            'preferred_specialty_id' => $existingProfile ? $existingProfile->preferred_specialty_id : '',
            'specialties' => $specialtyModel->getAll(),
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $profileData = [
                'user_id' => Session::get('user_id'),
                'age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
                'address' => trim(htmlspecialchars($_POST['address'] ?? '')),
                'preferred_specialty_id' => !empty($_POST['preferred_specialty_id']) ? (int)$_POST['preferred_specialty_id'] : null
            ];

            if ($existingProfile) {
                if ($patientModel->updateProfile($profileData)) {
                    header('Location: ' . URL_ROOT . '/directory');
                    exit;
                } else {
                    $data['error'] = 'Something went wrong updating profile.';
                }
            } else {
                if ($patientModel->createProfile($profileData)) {
                    header('Location: ' . URL_ROOT . '/directory');
                    exit;
                } else {
                    $data['error'] = 'Something went wrong creating profile.';
                }
            }
        }

        $this->view('onboarding/patient', $data);
    }

    public function clinic() {
        if (Session::get('user_role_id') != 2) {
            header('Location: ' . URL_ROOT . '/onboarding');
            exit;
        }

        $clinicModel = $this->model('ClinicModel');
        $specialtyModel = $this->model('SpecialtyModel');
        $themeModel = $this->model('ThemeModel');

        $existingProfile = $clinicModel->getProfileByUserId(Session::get('user_id'));

        $data = [
            'slug' => $existingProfile ? $existingProfile->slug : '',
            'clinic_name' => $existingProfile ? $existingProfile->clinic_name : '',
            'specialty_id' => $existingProfile ? $existingProfile->specialty_id : '',
            'theme_id' => $existingProfile ? $existingProfile->theme_id : '',
            'primary_color' => $existingProfile ? $existingProfile->primary_color : '#007bff',
            'address' => $existingProfile ? $existingProfile->address : '',
            'phone' => $existingProfile ? $existingProfile->phone : '',
            'whatsapp' => $existingProfile ? $existingProfile->whatsapp : '',
            'facebook' => $existingProfile ? $existingProfile->facebook : '',
            'instagram' => $existingProfile ? $existingProfile->instagram : '',
            'specialties' => $specialtyModel->getAll(),
            'themes' => $themeModel->getAll(),
            'error' => '',
            'slug_err' => '',
            'clinic_name_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $data['slug'] = trim(strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $_POST['slug'] ?? '')));
            $data['clinic_name'] = trim(htmlspecialchars($_POST['clinic_name'] ?? ''));
            $data['specialty_id'] = !empty($_POST['specialty_id']) ? (int)$_POST['specialty_id'] : null;
            $data['theme_id'] = !empty($_POST['theme_id']) ? (int)$_POST['theme_id'] : null;
            $data['primary_color'] = trim(htmlspecialchars($_POST['primary_color'] ?? ''));
            $data['address'] = trim(htmlspecialchars($_POST['address'] ?? ''));
            $data['phone'] = trim(htmlspecialchars($_POST['phone'] ?? ''));
            $data['whatsapp'] = trim(htmlspecialchars($_POST['whatsapp'] ?? ''));
            $data['facebook'] = trim(htmlspecialchars($_POST['facebook'] ?? ''));
            $data['instagram'] = trim(htmlspecialchars($_POST['instagram'] ?? ''));

            // Validate
            if (empty($data['slug'])) {
                $data['slug_err'] = 'Please enter a unique URL slug.';
            } else {
                if ($clinicModel->checkSlugExists($data['slug'], Session::get('user_id'))) {
                    $data['slug_err'] = 'This URL slug is already taken.';
                }
            }

            if (empty($data['clinic_name'])) {
                $data['clinic_name_err'] = 'Please enter clinic/doctor name.';
            }

            if (empty($data['slug_err']) && empty($data['clinic_name_err'])) {
                $profileData = [
                    'user_id' => Session::get('user_id'),
                    'slug' => $data['slug'],
                    'clinic_name' => $data['clinic_name'],
                    'specialty_id' => $data['specialty_id'],
                    'theme_id' => $data['theme_id'],
                    'primary_color' => $data['primary_color'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'whatsapp' => $data['whatsapp'],
                    'facebook' => $data['facebook'],
                    'instagram' => $data['instagram']
                ];

                if ($existingProfile) {
                    if ($clinicModel->updateProfile($profileData)) {
                        header('Location: ' . URL_ROOT . '/clinicDashboard');
                        exit;
                    }
                } else {
                    if ($clinicModel->createProfile($profileData)) {
                        header('Location: ' . URL_ROOT . '/clinicDashboard');
                        exit;
                    }
                }
            }
        }

        $this->view('onboarding/clinic', $data);
    }
}
