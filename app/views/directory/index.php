<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<style>
    .alert { padding: 15px; background: #fff3cd; color: #856404; margin-bottom: 20px; border-radius: 4px; border: 1px solid #ffeeba;}
    .clinic-card { background: #fff; padding: 25px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; gap: 20px; align-items: center;}
    .clinic-logo { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #f4f7f6; }
    .clinic-info { flex: 1; }
    .clinic-info h3 { margin: 0 0 5px 0; color: #333; font-size: 1.5em;}
    .clinic-info p { margin: 3px 0; color: #666; }
    .clinic-actions { text-align: right; }
    .rating-badge { display: inline-block; background: #fdf2d0; color: #f39c12; padding: 5px 10px; border-radius: 20px; font-weight: bold; margin-bottom: 10px;}

    .search-filter { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; gap: 15px;}
    .search-filter input, .search-filter select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em; }
    .search-filter input { flex: 2; }
    .search-filter select { flex: 1; }
    .search-filter button { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
</style>

<h2>Doctor Directory</h2>

<form action="<?= URL_ROOT ?>/directory" method="GET" class="search-filter">
    <input type="text" name="q" placeholder="Search by name or address..." value="<?= Security::escape($data['current_q']) ?>">
    <select name="specialty">
        <option value="">All Specialties</option>
        <?php foreach($data['specialties'] as $spec): ?>
            <option value="<?= $spec->id ?>" <?= ($data['current_specialty'] == $spec->id) ? 'selected' : '' ?>><?= Security::escape($spec->name) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filter Results</button>
</form>

<?php if (!empty($data['fallback_msg'])): ?>
    <div class="alert"><?= Security::escape($data['fallback_msg']) ?></div>
<?php endif; ?>

<?php if (empty($data['clinics'])): ?>
    <div class="card" style="text-align: center; padding: 50px;">
        <h3>No doctors found.</h3>
        <p style="color:#666;">Try adjusting your search criteria or removing filters.</p>
    </div>
<?php else: ?>
    <?php foreach ($data['clinics'] as $clinic): ?>
        <div class="clinic-card">
            <?php if(!empty($clinic->logo_url)): ?>
                <img src="<?= URL_ROOT . Security::escape($clinic->logo_url) ?>" class="clinic-logo" alt="Logo">
            <?php else: ?>
                <div class="clinic-logo" style="background:#eee; display:flex; align-items:center; justify-content:center; color:#999; font-size: 0.8em;">No Logo</div>
            <?php endif; ?>

            <div class="clinic-info">
                <h3><?= Security::escape($clinic->clinic_name) ?></h3>
                <p><strong><?= Security::escape($clinic->specialty_name ?? 'General Practice') ?></strong></p>
                <p>📍 <?= Security::escape($clinic->address ?? 'Address not provided') ?></p>
            </div>

            <div class="clinic-actions">
                <div class="rating-badge">⭐ <?= number_format($clinic->avg_rating ?? 0, 1) ?></div>
                <br>
                <a href="<?= URL_ROOT ?>/clinic/view/<?= Security::escape($clinic->slug) ?>" class="btn-primary" style="display:inline-block; margin-top: 10px;">View Profile & Book</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
