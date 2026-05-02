<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile - <?= SITE_NAME ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 500px; margin: 5vh auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 10px 15px; background: #333; color: #fff; border: none; cursor: pointer; width: 100%; text-align: center; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Complete Your Profile (Patient)</h2>
        <?php if (!empty($data['error'])): ?>
            <span class="error"><?= Security::escape($data['error']) ?></span>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/onboarding/patient" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" name="phone" id="phone" value="<?= Security::escape($data['phone'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" name="age" id="age" value="<?= Security::escape($data['age'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" rows="3"><?= Security::escape($data['address'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="preferred_specialty_id">Preferred Doctor Specialty</label>
                <select name="preferred_specialty_id" id="preferred_specialty_id">
                    <option value="">Any / Don't Know</option>
                    <?php foreach ($data['specialties'] as $spec): ?>
                        <option value="<?= $spec->id ?>" <?= ($data['preferred_specialty_id'] == $spec->id) ? 'selected' : '' ?>><?= Security::escape($spec->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn">Save & Continue</button>
        </form>
    </div>
</body>
</html>
