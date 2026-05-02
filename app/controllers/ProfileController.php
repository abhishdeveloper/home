<?php

class ProfileController extends Controller {
    public function __construct() {
        Session::init();

        if (!Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function index() {
        $userModel = $this->model('UserModel');
        $user = $userModel->findUserById(Session::get('user_id'));

        $data = [
            'name' => $user->name,
            'avatar_url' => $user->avatar_url,
            'email' => $user->email, // read only
            'role_name' => ($user->role_id == 1) ? 'Admin' : (($user->role_id == 2) ? 'Clinic/Doctor' : 'Patient'),
            'success' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $name = trim(htmlspecialchars($_POST['name'] ?? ''));
            $avatar_url = $user->avatar_url;

            if (empty($name)) {
                $data['error'] = 'Name cannot be empty.';
            } else {
                // Handle Avatar Upload
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = APP_ROOT . '/public/images/avatars/';
                    $fileTmp = $_FILES['avatar']['tmp_name'];
                    $fileName = basename($_FILES['avatar']['name']);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($fileExt, $allowedExts)) {
                        $newFileName = md5(time() . $fileName) . '.' . $fileExt;
                        $destPath = $uploadDir . $newFileName;
                        if (move_uploaded_file($fileTmp, $destPath)) {
                            $avatar_url = '/images/avatars/' . $newFileName;
                        }
                    } else {
                        $data['error'] = 'Invalid image format. Allowed: JPG, PNG, GIF, WEBP.';
                    }
                }

                if (empty($data['error'])) {
                    if ($userModel->updateUserInfo(Session::get('user_id'), $name, $avatar_url)) {
                        Session::set('user_name', $name); // Update session
                        $data['success'] = 'Profile updated successfully.';
                        $data['name'] = $name;
                        $data['avatar_url'] = $avatar_url;
                    } else {
                        $data['error'] = 'Failed to update profile.';
                    }
                }
            }
        }

        $this->view('profile/index', $data);
    }

    public function changePassword() {
        $userModel = $this->model('UserModel');
        $user = $userModel->findUserById(Session::get('user_id'));

        // If they use Google Auth, they might not have a password
        if (empty($user->password)) {
            die("You are logged in with Google. You cannot change your password here.");
        }

        $data = [
            'success' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $data['error'] = 'Please fill out all fields.';
            } elseif ($new_password !== $confirm_password) {
                $data['error'] = 'New passwords do not match.';
            } elseif (strlen($new_password) < 6) {
                $data['error'] = 'New password must be at least 6 characters.';
            } elseif (!password_verify($current_password, $user->password)) {
                $data['error'] = 'Current password is incorrect.';
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                if ($userModel->updatePassword(Session::get('user_id'), $hashed_password)) {
                    $data['success'] = 'Password changed successfully.';
                } else {
                    $data['error'] = 'Something went wrong.';
                }
            }
        }

        $this->view('profile/change_password', $data);
    }
}
