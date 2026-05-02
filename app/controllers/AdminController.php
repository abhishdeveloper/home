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
            'smtp_host' => $this->settingsModel->getSetting('smtp_host'),
            'smtp_user' => $this->settingsModel->getSetting('smtp_user'),
            'smtp_pass' => $this->settingsModel->getSetting('smtp_pass'),
            'smtp_port' => $this->settingsModel->getSetting('smtp_port'),
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
            $smtp_host = trim($_POST['smtp_host']);
            $smtp_user = trim($_POST['smtp_user']);
            $smtp_pass = trim($_POST['smtp_pass']);
            $smtp_port = trim($_POST['smtp_port']);

            if ($this->settingsModel->updateSetting('google_client_id', $client_id) &&
                $this->settingsModel->updateSetting('google_client_secret', $client_secret) &&
                $this->settingsModel->updateSetting('smtp_host', $smtp_host) &&
                $this->settingsModel->updateSetting('smtp_user', $smtp_user) &&
                $this->settingsModel->updateSetting('smtp_pass', $smtp_pass) &&
                $this->settingsModel->updateSetting('smtp_port', $smtp_port)
                ) {
                $data['success_msg'] = 'Settings updated successfully.';
                $data['client_id'] = $client_id;
                $data['client_secret'] = $client_secret;
                $data['smtp_host'] = $smtp_host;
                $data['smtp_user'] = $smtp_user;
                $data['smtp_pass'] = $smtp_pass;
                $data['smtp_port'] = $smtp_port;
            } else {
                $data['error_msg'] = 'Failed to update settings.';
            }
        }

        $this->view('admin/settings', $data);
    }
}
