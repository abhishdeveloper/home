<?php

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        Session::init();
        $this->userModel = $this->model('UserModel');
    }

    public function register() {
        // Redirect if already logged in
        if (Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/pages/index');
            exit;
        }

        $data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'role_id' => 3, // Default to Patient
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data['name'] = trim($_POST['name']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            $data['confirm_password'] = trim($_POST['confirm_password']);
            $data['role_id'] = (int) $_POST['role_id'];

            // Validation
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }

            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter name';
            }

            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            if (empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

                if ($this->userModel->register($data)) {
                    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'redirect' => URL_ROOT . '/auth/login']);
                        exit;
                    }
                    header('Location: ' . URL_ROOT . '/auth/login');
                    exit;
                } else {
                    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => 'Something went wrong']);
                        exit;
                    }
                    die('Something went wrong');
                }
            } else {
                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'errors' => [
                        'name_err' => $data['name_err'],
                        'email_err' => $data['email_err'],
                        'password_err' => $data['password_err'],
                        'confirm_password_err' => $data['confirm_password_err']
                    ]]);
                    exit;
                }
            }
        }

        $this->view('auth/register', $data);
    }

    public function login() {
        // Redirect if already logged in
        if (Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/pages/index');
            exit;
        }

        $data = [
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);

            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            if ($this->userModel->findUserByEmail($data['email'])) {
                // User found
            } else {
                $data['email_err'] = 'No user found';
            }

            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'errors' => ['password_err' => $data['password_err']]]);
                        exit;
                    }
                    $this->view('auth/login', $data);
                }
            } else {
                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'errors' => [
                        'email_err' => $data['email_err'],
                        'password_err' => $data['password_err']
                    ]]);
                    exit;
                }
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user) {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            Session::set('user_id', $user->id);
            Session::set('user_email', $user->email);
            Session::set('user_name', $user->name);
            Session::set('user_role_id', $user->role_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => URL_ROOT . '/pages/index']);
            exit;
        }
        Session::set('user_id', $user->id);
        Session::set('user_email', $user->email);
        Session::set('user_name', $user->name);
        Session::set('user_role_id', $user->role_id);
        header('Location: ' . URL_ROOT . '/pages/index');
        exit;
    }

    public function logout() {
        Session::destroy();
        header('Location: ' . URL_ROOT . '/auth/login');
        exit;
    }

    public function googleLogin() {
        // Build the Google OAuth URL
        $settingsModel = $this->model('SettingsModel');
        $client_id = $settingsModel->getSetting('google_client_id');

        if (empty($client_id)) {
            die('Google Login is not configured yet. Please contact the administrator.');
        }

        $redirect_uri = URL_ROOT . '/auth/googleCallback';
        $scope = 'email profile';

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?';
        $url .= 'client_id=' . urlencode($client_id);
        $url .= '&redirect_uri=' . urlencode($redirect_uri);
        $url .= '&response_type=code';
        $url .= '&scope=' . urlencode($scope);
        $url .= '&access_type=online';

        header('Location: ' . $url);
        exit;
    }

    public function googleCallback() {
        if (!isset($_GET['code'])) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }

        $settingsModel = $this->model('SettingsModel');
        $client_id = $settingsModel->getSetting('google_client_id');
        $client_secret = $settingsModel->getSetting('google_client_secret');
        $redirect_uri = URL_ROOT . '/auth/googleCallback';

        $code = $_GET['code'];

        // Exchange code for access token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code',
            'code' => $code
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $token_data = json_decode($response, true);

        if (isset($token_data['error'])) {
            die('Error getting Google access token: ' . htmlspecialchars($token_data['error_description'] ?? $token_data['error']));
        }

        $access_token = $token_data['access_token'];

        // Get user info
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_info_response = curl_exec($ch);
        curl_close($ch);

        $google_user = json_decode($user_info_response, true);

        if (!isset($google_user['id'])) {
            die('Error getting Google user info.');
        }

        $google_id = $google_user['id'];
        $email = $google_user['email'];
        $name = $google_user['name'];

        // Check if user exists by Google ID
        $existingUser = $this->userModel->findUserByGoogleId($google_id);

        if ($existingUser) {
            $this->createUserSession($existingUser);
        } else {
            // Check if email already exists but not linked to Google
            $existingEmailUser = $this->userModel->findUserByEmail($email);
            if ($existingEmailUser) {
                // Link accounts by updating google_id
                $this->userModel->linkGoogleAccount($existingEmailUser->id, $google_id);

                $this->createUserSession($existingEmailUser);
            } else {
                // New user via Google - Register them
                $newUserId = $this->userModel->registerGoogleUser($name, $email, $google_id);
                if ($newUserId) {
                    // Set a temporary session flag to force role selection
                    Session::set('pending_role_user_id', $newUserId);
                    Session::set('pending_role_name', $name);

                    header('Location: ' . URL_ROOT . '/auth/chooseRole');
                    exit;
                } else {
                    die('Error registering new Google user.');
                }
            }
        }
    }

    public function chooseRole() {
        $pending_user_id = Session::get('pending_role_user_id');

        if (!$pending_user_id) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $role_id = (int)$_POST['role_id'];
            if (in_array($role_id, [2, 3])) { // Doctor/Clinic or Patient
                if ($this->userModel->updateRole($pending_user_id, $role_id)) {
                    // Role updated, now log them in fully
                    // We need to fetch the updated user to create standard session
                    $user = $this->userModel->findUserById($pending_user_id);

                    Session::destroy(); // Clear pending session
                    Session::init();    // Start fresh
                    $this->createUserSession($user);
                }
            } else {
                $data['error'] = 'Invalid role selected.';
            }
        }

        $data = [
            'name' => Session::get('pending_role_name')
        ];

        $this->view('auth/chooseRole', $data);
    }
}
