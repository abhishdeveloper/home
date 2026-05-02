<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APP_ROOT . '/app/helpers/PHPMailer/Exception.php';
require_once APP_ROOT . '/app/helpers/PHPMailer/PHPMailer.php';
require_once APP_ROOT . '/app/helpers/PHPMailer/SMTP.php';

class EmailHelper {
    public static function sendEmail($to, $subject, $body) {
        $settingsModel = new SettingsModel();

        $host = $settingsModel->getSetting('smtp_host');
        $user = $settingsModel->getSetting('smtp_user');
        $pass = $settingsModel->getSetting('smtp_pass');
        $port = $settingsModel->getSetting('smtp_port');

        if (empty($host) || empty($user) || empty($pass)) {
            // SMTP not configured, quietly fail or log it
            error_log("Email sending failed: SMTP settings not configured.");
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER; // Enable verbose debug output if needed
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $user;
            $mail->Password   = $pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = !empty($port) ? (int)$port : 587;

            //Recipients
            $mail->setFrom($user, SITE_NAME);
            $mail->addAddress($to);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
