<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Prescription - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }

        .medicine-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .medicine-row input { flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 3px; }
        .medicine-row .med-notes { flex: 2; }
        .btn { display: inline-block; padding: 10px 15px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 3px; text-decoration: none; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        .alert-error { padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 15px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Generate Prescription</h2>
                <p><strong>Patient:</strong> <?= Security::escape($data['appointment']->patient_name) ?> | <strong>Date:</strong> <?= date('M d, Y', strtotime($data['appointment']->appointment_date)) ?></p>
            </div>
            <a href="<?= URL_ROOT ?>/appointment/view/<?= $data['appointment']->id ?>" class="btn" style="background:#6c757d;">Back</a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="alert-error"><?= Security::escape($data['error']) ?></div>
        <?php endif; ?>

        <!-- Datalist for Quick Fill -->
        <datalist id="saved_medicines">
            <?php foreach($data['saved_medicines'] as $med): ?>
                <option value="<?= Security::escape($med->name) ?>" data-dosage="<?= Security::escape($med->default_dosage) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        <form action="<?= URL_ROOT ?>/prescription/generate/<?= $data['appointment']->id ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <h3>Medicines</h3>
            <div id="medicines_container">
                <?php
                    $existingMeds = [];
                    if ($data['prescription']) {
                        $existingMeds = json_decode($data['prescription']->medicines_json, true);
                    }
                    if (empty($existingMeds)) {
                        $existingMeds = [['name' => '', 'dosage' => '', 'notes' => '']];
                    }
                ?>
                <?php foreach($existingMeds as $idx => $med): ?>
                    <div class="medicine-row">
                        <input type="text" name="med_name[]" placeholder="Medicine Name" list="saved_medicines" class="med-name-input" value="<?= Security::escape($med['name']) ?>">
                        <input type="text" name="med_dosage[]" placeholder="Dosage (e.g. 1-0-1)" class="med-dosage-input" value="<?= Security::escape($med['dosage']) ?>">
                        <input type="text" name="med_notes[]" placeholder="Instructions/Notes" class="med-notes" value="<?= Security::escape($med['notes']) ?>">
                        <button type="button" class="btn btn-danger remove-med">X</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="add_med_btn" class="btn" style="background:#6c757d; margin-bottom: 20px;">+ Add Medicine</button>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>
                    <input type="checkbox" name="save_to_inventory" value="1" checked>
                    Save new medicines to my quick-fill inventory
                </label>
            </div>

            <div class="form-group">
                <label>General Notes / Advice</label>
                <textarea name="general_notes" rows="5"><?= $data['prescription'] ? Security::escape($data['prescription']->general_notes) : '' ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Save & Preview Prescription</button>
        </form>
    </div>

    <script>
        document.getElementById('add_med_btn').addEventListener('click', function() {
            const container = document.getElementById('medicines_container');
            const row = document.createElement('div');
            row.className = 'medicine-row';
            row.innerHTML = `
                <input type="text" name="med_name[]" placeholder="Medicine Name" list="saved_medicines" class="med-name-input">
                <input type="text" name="med_dosage[]" placeholder="Dosage (e.g. 1-0-1)" class="med-dosage-input">
                <input type="text" name="med_notes[]" placeholder="Instructions/Notes" class="med-notes">
                <button type="button" class="btn btn-danger remove-med">X</button>
            `;
            container.appendChild(row);
        });

        document.getElementById('medicines_container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-med')) {
                e.target.parentElement.remove();
            }
        });

        // Quick fill dosage logic
        document.getElementById('medicines_container').addEventListener('change', function(e) {
            if (e.target.classList.contains('med-name-input')) {
                const val = e.target.value;
                const options = document.getElementById('saved_medicines').options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === val) {
                        const dosageInput = e.target.parentElement.querySelector('.med-dosage-input');
                        if (!dosageInput.value) { // only fill if empty
                            dosageInput.value = options[i].getAttribute('data-dosage');
                        }
                        break;
                    }
                }
            }
        });
    </script>
</body>
</html>
