<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? SITE_NAME) ?></title>
    <!-- Basic styling for now -->
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #ddd; }
        .nav a { margin-left: 15px; text-decoration: none; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h2><?= SITE_NAME ?></h2>
            <nav class="nav">
                <a href="<?= URL_ROOT ?>/pages/index">Home</a>
                <a href="<?= URL_ROOT ?>/directory">Directory</a>
                <?php if (Session::get('user_id')): ?>
                    <?php if(Session::get('user_role_id') == 2): ?>
                        <a href="<?= URL_ROOT ?>/clinicDashboard">Dashboard</a>
                    <?php endif; ?>
                    <?php if(Session::get('user_role_id') == 3): ?>
                        <a href="<?= URL_ROOT ?>/appointment">My Appointments</a>
                    <?php endif; ?>
                    <a href="<?= URL_ROOT ?>/auth/logout">Logout</a>
                <?php else: ?>
                    <a href="<?= URL_ROOT ?>/auth/login">Login</a>
                    <a href="<?= URL_ROOT ?>/auth/register">Register</a>
                <?php endif; ?>
            </nav>
        </header>

        <main style="padding: 40px 0; text-align: center;">
            <h1><?= htmlspecialchars($data['title'] ?? '') ?></h1>
            <p><?= htmlspecialchars($data['description'] ?? '') ?></p>
        </main>
    </div>
</body>
</html>
