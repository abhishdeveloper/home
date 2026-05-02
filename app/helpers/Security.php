<?php
class Security {
    public static function generateCSRFToken() {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function verifyCSRFToken($token) {
        if (Session::get('csrf_token') && hash_equals(Session::get('csrf_token'), $token)) {
            return true;
        }
        return false;
    }

    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
