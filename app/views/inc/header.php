<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Dynamic SEO Meta Tags -->
    <title><?= isset($data['title']) ? Security::escape($data['title']) . ' - ' . SITE_NAME : SITE_NAME ?></title>
    <meta name="description" content="<?= isset($data['meta_desc']) ? Security::escape($data['meta_desc']) : 'Find top-rated doctors and clinics near you. Book appointments instantly.' ?>">
    <meta name="keywords" content="<?= isset($data['meta_keywords']) ? Security::escape($data['meta_keywords']) : 'doctor, clinic, medical directory, book appointment, healthcare' ?>">

    <!-- Open Graph (Social Media) -->
    <meta property="og:title" content="<?= isset($data['title']) ? Security::escape($data['title']) . ' - ' . SITE_NAME : SITE_NAME ?>">
    <meta property="og:description" content="<?= isset($data['meta_desc']) ? Security::escape($data['meta_desc']) : 'Find top-rated doctors and clinics near you. Book appointments instantly.' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= URL_ROOT . $_SERVER['REQUEST_URI'] ?>">
    <?php if (isset($data['og_image'])): ?>
        <meta property="og:image" content="<?= URL_ROOT . Security::escape($data['og_image']) ?>">
    <?php endif; ?>

    <style>
        * { box-sizing: border-box; }
        img { max-width: 100%; height: auto; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f4f7f6; color: #333; line-height: 1.6; }

        /* Navbar */
        .navbar { background: #fff; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;}
        .navbar .logo { font-size: 1.5em; font-weight: bold; color: #007bff; text-decoration: none; }
        .navbar .nav-links { display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .navbar a { color: #555; text-decoration: none; font-weight: 500; transition: color 0.3s;}
        .navbar a:hover { color: #007bff; }
        .btn-primary { background: #007bff; color: #fff !important; padding: 8px 20px; border-radius: 20px; }
        .btn-primary:hover { background: #0056b3; }
        .btn-outline { border: 1px solid #007bff; color: #007bff !important; padding: 8px 20px; border-radius: 20px; }
        .btn-outline:hover { background: #007bff; color: #fff !important; }

        /* General Container */
        .main-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; min-height: 60vh;}

        /* Cards */
        .card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px;}

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 15px; text-align: center; }
            .navbar .nav-links { justify-content: center; gap: 10px; }
            .main-container { margin: 20px auto; padding: 0 15px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="<?= URL_ROOT ?>/pages/index" class="logo"><?= SITE_NAME ?></a>
    <div class="nav-links">
        <a href="<?= URL_ROOT ?>/pages/index">Home</a>
        <a href="<?= URL_ROOT ?>/directory">Directory</a>
        <a href="<?= URL_ROOT ?>/pages/services">Services</a>
        <a href="<?= URL_ROOT ?>/pages/pricing">Pricing</a>

        <?php if (Session::get('user_id')): ?>
            <?php if(Session::get('user_role_id') == 2): ?>
                <a href="<?= URL_ROOT ?>/clinicDashboard" class="btn-primary">Dashboard</a>
            <?php endif; ?>
            <?php if(Session::get('user_role_id') == 3): ?>
                <a href="<?= URL_ROOT ?>/patientDashboard" class="btn-primary">My Portal</a>
            <?php endif; ?>
            <?php if(Session::get('user_role_id') == 1): ?>
                <a href="<?= URL_ROOT ?>/admin/settings" class="btn-primary">Admin Panel</a>
            <?php endif; ?>
            <a href="<?= URL_ROOT ?>/auth/logout" class="btn-outline">Logout</a>
        <?php else: ?>
            <a href="<?= URL_ROOT ?>/auth/login" class="btn-outline">Login</a>
            <a href="<?= URL_ROOT ?>/auth/register" class="btn-primary">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="main-container">
