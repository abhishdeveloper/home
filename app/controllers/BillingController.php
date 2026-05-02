<?php

class BillingController extends Controller {
    public function __construct() {
        Session::init();

        // Must be logged in as a Clinic/Doctor
        if (!Session::get('user_id') || Session::get('user_role_id') != 2) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function index() {
        $clinicModel = $this->model('ClinicModel');
        $profile = $clinicModel->getProfileByUserId(Session::get('user_id'));

        if (!$profile) {
            header('Location: ' . URL_ROOT . '/onboarding/clinic');
            exit;
        }

        $data = [
            'profile' => $profile
        ];

        $this->view('clinic/billing', $data);
    }

    public function processDemoPayment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            $clinicModel = $this->model('ClinicModel');
            $profile = $clinicModel->getProfileByUserId(Session::get('user_id'));

            if ($profile && !$profile->has_paid_branding) {
                // Simulate gateway success
                $amount = 999.00;

                // Record the transaction
                $clinicModel->recordTransaction($profile->id, $amount);

                // Upgrade the account
                $clinicModel->upgradeBranding($profile->id);

                // Send success email
                $userModel = $this->model('UserModel');
                $user = $userModel->findUserById(Session::get('user_id'));
                if ($user) {
                    $subject = "Upgrade Successful - Branding Removed";
                    $body = "<h2>Thank you for upgrading!</h2><p>Your payment of ₹{$amount} was successful.</p><p>The universal platform branding has now been removed from your public clinic profile.</p>";
                    EmailHelper::sendEmail($user->email, $subject, $body);
                }
            }

            header('Location: ' . URL_ROOT . '/billing?success=1');
            exit;
        }
    }
}
