<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::escape($data['title']) ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .alert { padding: 10px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= Security::escape($data['title']) ?></h2>

        <p><a href="<?= URL_ROOT ?>/pages/index">Back to Home</a> | <a href="<?= URL_ROOT ?>/auth/logout">Logout</a></p>

        <?php if (!empty($data['success_msg'])): ?>
            <div class="alert alert-success"><?= Security::escape($data['success_msg']) ?></div>
        <?php endif; ?>

        <?php if (!empty($data['error_msg'])): ?>
            <div class="alert alert-error"><?= Security::escape($data['error_msg']) ?></div>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/admin/settings" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="form-group">
                <label for="client_id">Google Client ID</label>
                <input type="text" name="client_id" id="client_id" value="<?= Security::escape($data['client_id'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="client_secret">Google Client Secret</label>
                <input type="text" name="client_secret" id="client_secret" value="<?= Security::escape($data['client_secret'] ?? '') ?>" required>
            </div>

            <button type="submit">Save Settings</button>
        </form>
    </div>
</body>
</html>
