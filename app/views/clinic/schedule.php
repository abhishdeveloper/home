<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .form-group label { width: 100px; font-weight: bold; }
        .form-group input[type="time"], .form-group input[type="number"] { padding: 8px; border: 1px solid #ccc; border-radius: 3px; }
        .btn { display: inline-block; padding: 10px 15px; background: #28a745; color: #fff; border: none; cursor: pointer; border-radius: 3px; text-decoration: none; }
        .btn-secondary { background: #6c757d; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .day-row { display: flex; align-items: center; gap: 15px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .day-row label { width: 100px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Manage Schedule & Working Hours</h2>
            <a href="<?= URL_ROOT ?>/clinicDashboard" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if (!empty($data['success'])): ?>
            <div class="alert alert-success"><?= Security::escape($data['success']) ?></div>
        <?php endif; ?>
        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-error"><?= Security::escape($data['error']) ?></div>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/clinicDashboard/schedule" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div style="margin-bottom: 30px; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                <div class="form-group">
                    <label style="width: auto;">Appointment Slot Duration (minutes):</label>
                    <input type="number" name="slot_duration" value="<?= Security::escape($data['schedule']->slot_duration ?? 30) ?>" min="5" step="5" required>
                </div>
                <small style="color: #666;">This determines the length of each appointment slot patients can book.</small>
            </div>

            <h3>Working Hours</h3>
            <p><small>Leave times blank to mark the clinic as closed for that day.</small></p>

            <?php
                $days = [
                    'mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday',
                    'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'
                ];

                foreach ($days as $key => $label):
                    // To handle full database names dynamically
                    $fullDayKeyStart = false;
                    switch($key) {
                        case 'mon': $fullDayKeyStart = 'monday_start'; $fullDayKeyEnd = 'monday_end'; break;
                        case 'tue': $fullDayKeyStart = 'tuesday_start'; $fullDayKeyEnd = 'tuesday_end'; break;
                        case 'wed': $fullDayKeyStart = 'wednesday_start'; $fullDayKeyEnd = 'wednesday_end'; break;
                        case 'thu': $fullDayKeyStart = 'thursday_start'; $fullDayKeyEnd = 'thursday_end'; break;
                        case 'fri': $fullDayKeyStart = 'friday_start'; $fullDayKeyEnd = 'friday_end'; break;
                        case 'sat': $fullDayKeyStart = 'saturday_start'; $fullDayKeyEnd = 'saturday_end'; break;
                        case 'sun': $fullDayKeyStart = 'sunday_start'; $fullDayKeyEnd = 'sunday_end'; break;
                    }

                    $start_time = isset($data['schedule']) ? $data['schedule']->$fullDayKeyStart : '';
                    $end_time = isset($data['schedule']) ? $data['schedule']->$fullDayKeyEnd : '';
            ?>
                <div class="day-row">
                    <label><?= $label ?></label>
                    <span>Start:</span>
                    <input type="time" name="<?= $key ?>_start" value="<?= Security::escape($start_time) ?>">
                    <span>End:</span>
                    <input type="time" name="<?= $key ?>_end" value="<?= Security::escape($end_time) ?>">
                </div>
            <?php endforeach; ?>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn">Save Schedule</button>
            </div>
        </form>
    </div>
</body>
</html>
