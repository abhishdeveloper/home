<?php

class AdminController extends Controller {
    private $settingsModel;

    public function __construct() {
        // Init session
        Session::init();

        // Check if user is logged in and is an Admin (role_id 1)
        if (!Session::get('user_id') || Session::get('user_role_id') != 1) {
            // Redirect to login
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }

        $this->settingsModel = $this->model('SettingsModel');
    }

    public function settings() {
        $data = [
            'title' => 'Admin Settings',
            'client_id' => $this->settingsModel->getSetting('google_client_id'),
            'client_secret' => $this->settingsModel->getSetting('google_client_secret'),
            'success_msg' => '',
            'error_msg' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                $data['error_msg'] = 'Invalid CSRF token.';
                $this->view('admin/settings', $data);
                return;
            }

            $client_id = trim($_POST['client_id']);
            $client_secret = trim($_POST['client_secret']);

            if ($this->settingsModel->updateSetting('google_client_id', $client_id) &&
                $this->settingsModel->updateSetting('google_client_secret', $client_secret)) {
                $data['success_msg'] = 'Settings updated successfully.';
                $data['client_id'] = $client_id;
                $data['client_secret'] = $client_secret;
            } else {
                $data['error_msg'] = 'Failed to update settings.';
            }
        }

        $this->view('admin/settings', $data);
    }
}
