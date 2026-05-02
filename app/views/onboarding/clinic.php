<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Setup - <?= SITE_NAME ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 5vh auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"], .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .form-group input[type="color"] { width: 50px; height: 30px; padding: 0; }
        .btn { display: inline-block; padding: 10px 15px; background: #333; color: #fff; border: none; cursor: pointer; width: 100%; text-align: center; }
        .error { color: red; font-size: 0.9em; margin-top: 5px; display: block; }
        .row { display: flex; gap: 15px; flex-wrap: wrap; }
        .col { flex: 1; min-width: 250px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Set up Your Clinic Profile</h2>
        <?php if (!empty($data['error'])): ?>
            <span class="error"><?= Security::escape($data['error']) ?></span>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/onboarding/clinic" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="form-group">
                <label for="clinic_name">Clinic / Doctor Name *</label>
                <input type="text" name="clinic_name" id="clinic_name" value="<?= Security::escape($data['clinic_name'] ?? '') ?>" required>
                <span class="error"><?= $data['clinic_name_err'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label for="slug">Profile URL Slug * (e.g., your-clinic-name)</label>
                <input type="text" name="slug" id="slug" value="<?= Security::escape($data['slug'] ?? '') ?>" required>
                <span class="error"><?= $data['slug_err'] ?? '' ?></span>
            </div>

            <div class="row">
                <div class="col form-group">
                    <label for="specialty_id">Specialty</label>
                    <select name="specialty_id" id="specialty_id">
                        <option value="">Select Specialty</option>
                        <?php foreach ($data['specialties'] as $spec): ?>
                            <option value="<?= $spec->id ?>" <?= ($data['specialty_id'] == $spec->id) ? 'selected' : '' ?>><?= Security::escape($spec->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col form-group">
                    <label for="theme_id">Theme Template</label>
                    <select name="theme_id" id="theme_id">
                        <?php foreach ($data['themes'] as $theme): ?>
                            <option value="<?= $theme->id ?>" <?= ($data['theme_id'] == $theme->id) ? 'selected' : '' ?>><?= Security::escape($theme->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col form-group">
                    <label for="primary_color">Primary Theme Color</label>
                    <input type="color" name="primary_color" id="primary_color" value="<?= Security::escape($data['primary_color'] ?? '#007bff') ?>">
                </div>
                <div class="col form-group">
                    <label for="logo">Clinic Logo (Optional)</label>
                    <input type="file" name="logo" id="logo" accept="image/*">
                </div>
            </div>

            <div class="row">
                <div class="col form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" rows="3"><?= Security::escape($data['address'] ?? '') ?></textarea>
                </div>
                <div class="col form-group">
                    <label for="consultation_fee">Consultation Fee (₹)</label>
                    <input type="number" name="consultation_fee" id="consultation_fee" value="<?= Security::escape($data['consultation_fee'] ?? '0') ?>" min="0" step="0.01">
                    <small style="color: #666; display: block; margin-top: 5px;">This fee is charged to patients when booking an appointment.</small>
                </div>
            </div>

            <div class="row">
                <div class="col form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="text" name="phone" id="phone" value="<?= Security::escape($data['phone'] ?? '') ?>" required>
                </div>
                <div class="col form-group">
                    <label for="whatsapp">WhatsApp Number</label>
                    <input type="text" name="whatsapp" id="whatsapp" value="<?= Security::escape($data['whatsapp'] ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col form-group">
                    <label for="facebook">Facebook Link</label>
                    <input type="text" name="facebook" id="facebook" value="<?= Security::escape($data['facebook'] ?? '') ?>">
                </div>
                <div class="col form-group">
                    <label for="instagram">Instagram Link</label>
                    <input type="text" name="instagram" id="instagram" value="<?= Security::escape($data['instagram'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn">Save Profile & Continue to Dashboard</button>
        </form>
    </div>
</body>
</html>
