<?php

class ClinicDashboardController extends Controller {
    private $clinicModel;

    public function __construct() {
        Session::init();

        if (!Session::get('user_id') || Session::get('user_role_id') != 2) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }

        $this->clinicModel = $this->model('ClinicModel');

        // Ensure they have finished onboarding
        if (!$this->clinicModel->getProfileByUserId(Session::get('user_id'))) {
            header('Location: ' . URL_ROOT . '/onboarding/clinic');
            exit;
        }
    }

    public function index() {
        $profile = $this->clinicModel->getProfileByUserId(Session::get('user_id'));
        $pages = $this->clinicModel->getPages($profile->id);

        $data = [
            'profile' => $profile,
            'pages' => $pages
        ];

        $this->view('clinic/dashboard', $data);
    }

    public function schedule() {
        $profile = $this->clinicModel->getProfileByUserId(Session::get('user_id'));
        $scheduleModel = $this->model('ScheduleModel');

        $schedule = $scheduleModel->getScheduleByClinicId($profile->id);

        $data = [
            'profile' => $profile,
            'schedule' => $schedule,
            'success' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $postData = [
                'clinic_id' => $profile->id,
                'slot_duration' => (int)$_POST['slot_duration']
            ];

            $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
            foreach ($days as $day) {
                // Check if they toggled the "Closed" checkbox (if we have one), else just grab the time
                $postData[$day . '_start'] = !empty($_POST[$day . '_start']) ? $_POST[$day . '_start'] : null;
                $postData[$day . '_end'] = !empty($_POST[$day . '_end']) ? $_POST[$day . '_end'] : null;
            }

            if ($scheduleModel->createOrUpdateSchedule($postData)) {
                $data['success'] = 'Schedule updated successfully.';
                // Refresh data
                $data['schedule'] = $scheduleModel->getScheduleByClinicId($profile->id);
            } else {
                $data['error'] = 'Failed to update schedule.';
            }
        }

        $this->view('clinic/schedule', $data);
    }

    public function togglePublish() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $profile = $this->clinicModel->getProfileByUserId(Session::get('user_id'));
            $newStatus = $profile->is_published ? 0 : 1;

            $this->clinicModel->setPublished(Session::get('user_id'), $newStatus);

            header('Location: ' . URL_ROOT . '/clinicDashboard');
            exit;
        }
    }

    public function createPage() {
        $profile = $this->clinicModel->getProfileByUserId(Session::get('user_id'));

        $data = [
            'title' => '',
            'slug' => '',
            'content' => '',
            'is_home' => 0,
            'status' => 'draft',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $data['title'] = trim(htmlspecialchars($_POST['title'] ?? ''));
            $data['slug'] = trim(strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $_POST['slug'] ?? '')));
            // Content must remain raw HTML to support the WYSIWYG editor. It is escaped on display conditionally.
            $data['content'] = $_POST['content'] ?? '';
            $data['is_home'] = isset($_POST['is_home']) ? 1 : 0;
            $data['status'] = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';

            if (empty($data['title']) || empty($data['slug'])) {
                $data['error'] = 'Title and Slug are required.';
            } else {
                // Check for duplicate slug before inserting to prevent PDOException
                $existingPage = $this->clinicModel->getPageBySlug($profile->id, $data['slug'], false);

                if ($existingPage) {
                    $data['error'] = 'A page with this URL slug already exists. Please choose a different slug.';
                } else {
                    $pageData = [
                        'clinic_id' => $profile->id,
                        'title' => $data['title'],
                        'slug' => $data['slug'],
                        'content' => $data['content'],
                        'is_home' => $data['is_home'],
                        'status' => $data['status']
                    ];

                    if ($this->clinicModel->createPage($pageData)) {
                        header('Location: ' . URL_ROOT . '/clinicDashboard');
                        exit;
                    } else {
                        $data['error'] = 'Failed to create page. An unexpected error occurred.';
                    }
                }
            }
        }

        $this->view('clinic/createPage', $data);
    }
}
