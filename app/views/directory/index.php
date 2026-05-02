<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Directory - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .alert { padding: 10px; background: #fff3cd; color: #856404; margin-bottom: 20px; border-radius: 4px; }
        .card { background: #fff; padding: 20px; margin-bottom: 15px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .card h3 { margin: 0 0 10px 0; }
        .card p { margin: 5px 0; color: #555; }
        .btn { display: inline-block; padding: 8px 12px; background: #007bff; color: #fff; text-decoration: none; border-radius: 3px; margin-top: 10px; }
        .btn-outline { background: transparent; color: #007bff; border: 1px solid #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Find a Doctor</h2>
            <nav>
                <a href="<?= URL_ROOT ?>/pages/index">Home</a>
                <?php if(Session::get('user_id')): ?>
                    | <a href="<?= URL_ROOT ?>/auth/logout">Logout</a>
                <?php else: ?>
                    | <a href="<?= URL_ROOT ?>/auth/login">Login</a>
                <?php endif; ?>
            </nav>
        </div>

        <?php if (!empty($data['fallback_msg'])): ?>
            <div class="alert"><?= Security::escape($data['fallback_msg']) ?></div>
        <?php endif; ?>

        <?php if (empty($data['clinics'])): ?>
            <p>No clinics currently available.</p>
        <?php else: ?>
            <?php foreach ($data['clinics'] as $clinic): ?>
                <div class="card">
                    <h3><?= Security::escape($clinic->clinic_name) ?></h3>
                    <p><strong>Specialty:</strong> <?= Security::escape($clinic->specialty_name ?? 'General') ?></p>
                    <p><strong>Address:</strong> <?= Security::escape($clinic->address ?? 'N/A') ?></p>

                    <a href="<?= URL_ROOT ?>/clinic/view/<?= Security::escape($clinic->slug) ?>" class="btn btn-outline">View Profile</a>
                    <a href="#" class="btn" onclick="alert('Appointment booking coming soon!'); return false;">Book Appointment</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
