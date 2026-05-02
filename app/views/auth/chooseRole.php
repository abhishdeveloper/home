<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 10px 15px; background: #333; color: #fff; border: none; cursor: pointer; width: 100%; text-align: center; font-size: 16px; }
        .welcome { text-align: center; margin-bottom: 20px; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome">
            <h2>Welcome, <?= Security::escape($data['name'] ?? 'User') ?>!</h2>
            <p>You've successfully signed in with Google. To complete your setup, please tell us who you are.</p>
        </div>

        <?php if (!empty($data['error'])): ?>
            <span class="error"><?= Security::escape($data['error']) ?></span>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/auth/chooseRole" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="form-group">
                <label for="role_id">I am a...</label>
                <select name="role_id" id="role_id" required>
                    <option value="" disabled selected>Select your role</option>
                    <option value="3">Patient</option>
                    <option value="2">Doctor / Clinic</option>
                </select>
            </div>

            <button type="submit" class="btn">Complete Setup</button>
        </form>
    </div>
</body>
</html>
