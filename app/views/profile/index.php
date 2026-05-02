<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - <?= SITE_NAME ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 5vh auto; background: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"], .form-group input[type="email"] { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .alert-success { padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px; border-radius: 4px; }
        .alert-error { padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 20px; border-radius: 4px; }
        .avatar-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Account Settings</h2>
            <?php if(Session::get('user_role_id') == 2): ?>
                <a href="<?= URL_ROOT ?>/clinicDashboard" style="color: #666;">&larr; Dashboard</a>
            <?php else: ?>
                <a href="<?= URL_ROOT ?>/patientDashboard" style="color: #666;">&larr; Dashboard</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($data['success'])): ?>
            <div class="alert-success"><?= Security::escape($data['success']) ?></div>
        <?php endif; ?>
        <?php if (!empty($data['error'])): ?>
            <div class="alert-error"><?= Security::escape($data['error']) ?></div>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/profile" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div style="text-align: center; margin-bottom: 30px;">
                <img src="<?= !empty($data['avatar_url']) ? URL_ROOT . Security::escape($data['avatar_url']) : 'https://via.placeholder.com/100' ?>" class="avatar-preview" alt="Avatar">
                <br>
                <input type="file" name="avatar" accept="image/*">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" value="<?= Security::escape($data['email']) ?>" disabled style="background: #e9ecef;">
                <small style="color: #666;">Account Type: <?= $data['role_name'] ?></small>
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= Security::escape($data['name']) ?>" required>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                <button type="submit" class="btn" style="background: #28a745;">Save Changes</button>
                <a href="<?= URL_ROOT ?>/profile/changePassword" style="color: #007bff; font-weight: bold;">Change Password</a>
            </div>
        </form>
    </div>
</body>
</html>
