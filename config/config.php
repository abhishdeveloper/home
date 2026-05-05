<?php
// config/config.php

define('APP_ROOT', dirname(dirname(__FILE__)));

// Dynamically determine URL_ROOT to support production domains and subdirectories
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? "https://" : "http://";
$domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scriptDir = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) : '';
$scriptDir = $scriptDir === '/' ? '' : $scriptDir;
define('URL_ROOT', $protocol . $domainName . $scriptDir);

define('SITE_NAME', 'Clinic Directory');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'clinic_directory');

// Security Configurations
define('SESSION_LIFETIME', 86400); // 1 day
