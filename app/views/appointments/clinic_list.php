<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Appointments - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .status { padding: 4px 8px; border-radius: 3px; font-size: 0.9em; font-weight: bold; }
        .status.pending { background: #ffc107; color: #212529; }
        .status.approved { background: #28a745; color: #fff; }
        .status.rejected { background: #dc3545; color: #fff; }
        .status.completed { background: #17a2b8; color: #fff; }
        .btn { display: inline-block; padding: 6px 12px; background: #007bff; color: #fff; text-decoration: none; border-radius: 3px; font-size: 0.9em; border: none; cursor: pointer; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Appointment Requests</h2>
            <a href="<?= URL_ROOT ?>/clinicDashboard" class="btn" style="background:#6c757d;">Back to Dashboard</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Patient Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['appointments'])): ?>
                    <tr><td colspan="4">No appointments found.</td></tr>
                <?php else: ?>
                    <?php foreach ($data['appointments'] as $appt): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($appt->appointment_date)) ?> at <?= date('h:i A', strtotime($appt->appointment_time)) ?></td>
                            <td>
                                <?= Security::escape($appt->patient_name) ?>
                                <?php if ($appt->patient_review_count > 0): ?>
                                    <br><small style="color: #f39c12;">⭐ <?= number_format($appt->patient_rating, 1) ?> (<?= $appt->patient_review_count ?>)</small>
                                <?php else: ?>
                                    <br><small style="color: #999;">No ratings</small>
                                <?php endif; ?>
                            </td>
                            <td><span class="status <?= $appt->status ?>"><?= ucfirst($appt->status) ?></span></td>
                            <td>
                                <a href="<?= URL_ROOT ?>/appointment/view/<?= $appt->id ?>" class="btn">View & Chat</a>

                                <?php if ($appt->status == 'pending'): ?>
                                    <form action="<?= URL_ROOT ?>/appointment/updateStatus" method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="appointment_id" value="<?= $appt->id ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                    <form action="<?= URL_ROOT ?>/appointment/updateStatus" method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="appointment_id" value="<?= $appt->id ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </form>
                                <?php elseif ($appt->status == 'approved'): ?>
                                    <form action="<?= URL_ROOT ?>/appointment/updateStatus" method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="appointment_id" value="<?= $appt->id ?>">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn" style="background:#17a2b8;">Mark Completed</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
