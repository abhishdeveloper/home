<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .status { padding: 4px 8px; border-radius: 3px; font-size: 0.9em; font-weight: bold; }
        .status.pending { background: #ffc107; color: #212529; }
        .status.approved { background: #28a745; color: #fff; }
        .status.rejected { background: #dc3545; color: #fff; }
        .status.completed { background: #17a2b8; color: #fff; }
        .btn { display: inline-block; padding: 6px 12px; background: #007bff; color: #fff; text-decoration: none; border-radius: 3px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>My Appointments</h2>
            <a href="<?= URL_ROOT ?>/directory" class="btn" style="background:#6c757d;">Book New Appointment</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Clinic/Doctor</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['appointments'])): ?>
                    <tr><td colspan="4">No appointments found.</td></tr>
                <?php else: ?>
                    <?php foreach ($data['appointments'] as $appt): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($appt->appointment_date)) ?> at <?= date('h:i A', strtotime($appt->appointment_time)) ?></td>
                            <td><?= Security::escape($appt->clinic_name) ?></td>
                            <td><span class="status <?= $appt->status ?>"><?= ucfirst($appt->status) ?></span></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/appointment/view/<?= $appt->id ?>" class="btn">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
