<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - <?= SITE_NAME ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 500px; margin: 5vh auto; background: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="password"] { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .alert-success { padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px; border-radius: 4px; }
        .alert-error { padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="<?= URL_ROOT ?>/profile" style="color: #666; text-decoration: none;">&larr; Back to Settings</a>
        </div>

        <h2>Change Password</h2>

        <?php if (!empty($data['success'])): ?>
            <div class="alert-success"><?= Security::escape($data['success']) ?></div>
        <?php endif; ?>
        <?php if (!empty($data['error'])): ?>
            <div class="alert-error"><?= Security::escape($data['error']) ?></div>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/profile/changePassword" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
</body>
</html>
