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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --secondary: #10B981;
            --secondary-hover: #059669;
            --bg-color: #F3F4F6;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --white: #FFFFFF;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; }
        img { max-width: 100%; height: auto; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0; padding: 0;
            background: var(--bg-color);
            color: var(--text-dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp 0.6s ease-out forwards; }

        /* Navbar - Glassmorphism */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding: 15px 5%;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
        }
        .navbar .logo {
            font-size: 1.6em; font-weight: 700; color: var(--primary);
            text-decoration: none; letter-spacing: -0.5px;
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .navbar .nav-links { display: flex; gap: 25px; align-items: center; flex-wrap: wrap; }
        .navbar a { color: var(--text-muted); text-decoration: none; font-weight: 500; transition: var(--transition);}
        .navbar a:hover { color: var(--primary); }

        /* Global Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #8B5CF6);
            color: var(--white) !important;
            padding: 10px 24px; border-radius: 9999px;
            font-weight: 500; border: none; cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
            transition: var(--transition);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }
        .btn-outline {
            border: 2px solid var(--primary); color: var(--primary) !important;
            padding: 8px 24px; border-radius: 9999px; font-weight: 500;
            background: transparent; transition: var(--transition); cursor: pointer;
        }
        .btn-outline:hover {
            background: var(--primary); color: var(--white) !important;
            transform: translateY(-2px);
        }

        /* General Container */
        .main-container { max-width: 1200px; margin: 50px auto; padding: 0 20px; min-height: 65vh; animation: fadeUp 0.5s ease-out;}

        /* Cards */
        .card {
            background: var(--white); padding: 30px; border-radius: 16px;
            box-shadow: var(--card-shadow); margin-bottom: 25px;
            border: 1px solid rgba(0,0,0,0.02);
            transition: var(--transition);
        }
        .card.hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Form Elements */
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="tel"], select, textarea {
            width: 100%; padding: 12px 16px; border: 1px solid #D1D5DB; border-radius: 8px;
            font-family: 'Inter', sans-serif; font-size: 1rem; color: var(--text-dark);
            transition: var(--transition); background: #F9FAFB;
        }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); background: var(--white);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 15px; text-align: center; padding: 20px 5%; }
            .navbar .nav-links { justify-content: center; gap: 12px; }
            .main-container { margin: 30px auto; padding: 0 15px; }
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
