<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - <?= Security::escape($data['appointment']->patient_name) ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #e0e0e0; color: #333; }
        .prescription-paper { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.2); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 20px; }
        .clinic-info h1 { margin: 0; color: #007bff; }
        .clinic-info p { margin: 5px 0; font-size: 0.9em; color: #666; }
        .patient-info { border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between;}
        .rx-symbol { font-size: 2em; font-weight: bold; margin-bottom: 15px; font-style: italic; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 10px; border-bottom: 1px dotted #ccc; text-align: left; }
        .table th { background: #f8f9fa; }
        .notes-section { margin-top: 30px; }
        .footer { margin-top: 50px; text-align: right; border-top: 1px solid #ddd; padding-top: 20px; }

        .no-print { text-align: center; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 3px; cursor: pointer; border: none; font-size: 16px; }

        @media print {
            body { background: #fff; padding: 0; }
            .prescription-paper { box-shadow: none; max-width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn">Print / Save as PDF</button>
        <?php if (Session::get('user_role_id') == 2): ?>
            <a href="<?= URL_ROOT ?>/prescription/generate/<?= $data['appointment']->id ?>" class="btn" style="background:#6c757d; margin-left:10px;">Edit Prescription</a>
        <?php endif; ?>
        <a href="<?= URL_ROOT ?>/appointment/view/<?= $data['appointment']->id ?>" class="btn" style="background:#6c757d; margin-left:10px;">Back to Appointment</a>
    </div>

    <div class="prescription-paper">
        <div class="header">
            <div class="clinic-info">
                <?php if (!empty($data['clinic_profile']->logo_url)): ?>
                    <img src="<?= URL_ROOT . Security::escape($data['clinic_profile']->logo_url) ?>" alt="Clinic Logo" style="max-height: 80px; margin-bottom: 10px;">
                <?php endif; ?>
                <h1><?= Security::escape($data['appointment']->clinic_name) ?></h1>
                <?php if (!empty($data['clinic_profile']->address)): ?>
                    <p><?= Security::escape($data['clinic_profile']->address) ?></p>
                <?php endif; ?>
                <?php if (!empty($data['clinic_profile']->phone)): ?>
                    <p>Tel: <?= Security::escape($data['clinic_profile']->phone) ?></p>
                <?php endif; ?>
            </div>
            <div style="text-align: right; color: #666; font-size: 0.9em;">
                <p><strong>Date:</strong> <?= date('M d, Y') ?></p>
                <p><strong>Appt Ref:</strong> #<?= str_pad($data['appointment']->id, 5, '0', STR_PAD_LEFT) ?></p>
            </div>
        </div>

        <div class="patient-info">
            <div><strong>Patient Name:</strong> <?= Security::escape($data['appointment']->patient_name) ?></div>
            <!-- In a full app we'd grab age/gender from patient_profiles here -->
            <div><strong>Consultation Date:</strong> <?= date('M d, Y', strtotime($data['appointment']->appointment_date)) ?></div>
        </div>

        <div class="rx-symbol">Rx</div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Medicine Name</th>
                    <th style="width: 20%;">Dosage</th>
                    <th style="width: 40%;">Instructions / Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['medicines'])): ?>
                    <tr><td colspan="4">No medicines prescribed.</td></tr>
                <?php else: ?>
                    <?php $count = 1; foreach ($data['medicines'] as $med): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><strong><?= Security::escape($med['name']) ?></strong></td>
                            <td><?= Security::escape($med['dosage']) ?></td>
                            <td><?= Security::escape($med['notes']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($data['prescription']->general_notes)): ?>
            <div class="notes-section">
                <h4>General Notes / Advice:</h4>
                <p><?= nl2br(Security::escape($data['prescription']->general_notes)) ?></p>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p>Signature</p>
            <p><strong>Dr. <?= Security::escape($data['appointment']->clinic_name) ?></strong></p>
        </div>
    </div>
</body>
</html>
