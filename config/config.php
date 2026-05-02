<?php
// config/config.php

define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', 'http://localhost:8000'); // You can change this to your domain later
define('SITE_NAME', 'Clinic Directory');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'clinic_directory');

// Security Configurations
define('SESSION_LIFETIME', 86400); // 1 day
