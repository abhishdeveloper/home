<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; }
        .nav a { margin-left: 10px; color: #fff; text-decoration: none; padding: 8px 15px; border-radius: 4px; display: inline-block; margin-bottom: 5px; }
        .card-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .metric-card { flex: 1; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); text-align: center; }
        .metric-card h3 { margin: 0; font-size: 2em; color: #007bff; }
        .metric-card p { margin: 5px 0 0 0; color: #666; font-size: 0.9em; text-transform: uppercase; letter-spacing: 1px;}
        .card { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .appt-item { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #eee; align-items: center; flex-wrap: wrap; gap: 10px;}
        .appt-item:last-child { border-bottom: none; }
        .btn-sm { padding: 5px 10px; background: #28a745; color: #fff; text-decoration: none; border-radius: 3px; font-size: 0.9em; white-space: nowrap;}

        @media (max-width: 768px) {
            .header { flex-direction: column; text-align: center; gap: 15px; }
            .user-info { flex-direction: column; }
            .nav { display: flex; flex-direction: column; width: 100%; }
            .nav a { margin-left: 0; text-align: center; }
            .card-row { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-info">
                <img src="<?= !empty($data['user']->avatar_url) ? URL_ROOT . Security::escape($data['user']->avatar_url) : 'https://via.placeholder.com/60' ?>" class="avatar" alt="User Avatar">
                <div>
                    <h2 style="margin:0;">Hello, <?= Security::escape($data['user']->name) ?></h2>
                    <p style="margin:5px 0 0 0; color:#666;">Patient Portal</p>
                </div>
            </div>
            <div class="nav">
                <a href="<?= URL_ROOT ?>/directory" style="background:#007bff;">🔍 Find a Doctor</a>
                <a href="<?= URL_ROOT ?>/appointment" style="background:#17a2b8;">📅 All Appointments</a>
                <a href="<?= URL_ROOT ?>/profile" style="background:#6c757d;">⚙️ Settings</a>
                <a href="<?= URL_ROOT ?>/auth/logout" style="background:#dc3545;">Logout</a>
            </div>
        </div>

        <div class="card-row">
            <div class="metric-card">
                <h3><?= $data['stats']->total_appointments ?? 0 ?></h3>
                <p>Total Appointments</p>
            </div>
            <div class="metric-card">
                <h3 style="color:#28a745;"><?= $data['stats']->upcoming_appointments ?? 0 ?></h3>
                <p>Upcoming Appointments</p>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top: 0; border-bottom: 2px solid #007bff; padding-bottom: 10px; display: inline-block;">Upcoming Approved Appointments</h3>

            <?php if (empty($data['upcoming_appointments'])): ?>
                <p style="color: #666; margin-top: 20px;">You have no upcoming appointments scheduled.</p>
                <a href="<?= URL_ROOT ?>/directory" style="color: #007bff; font-weight: bold; text-decoration: none;">&rarr; Browse directory to book one</a>
            <?php else: ?>
                <div style="margin-top: 15px;">
                    <?php foreach ($data['upcoming_appointments'] as $appt): ?>
                        <div class="appt-item">
                            <div>
                                <strong style="font-size: 1.1em;"><?= Security::escape($appt->clinic_name) ?></strong><br>
                                <span style="color: #666; font-size: 0.9em;">
                                    📅 <?= date('l, M d, Y', strtotime($appt->appointment_date)) ?> at
                                    ⏰ <?= date('h:i A', strtotime($appt->appointment_time)) ?>
                                </span>
                            </div>
                            <div>
                                <a href="<?= URL_ROOT ?>/appointment/view/<?= $appt->id ?>" class="btn-sm">Enter Virtual Lobby</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
