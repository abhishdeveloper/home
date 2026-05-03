<?php require_once APP_ROOT . '/app/views/inc/header.php'; ?>

<style>
    .alert { padding: 15px; background: #fff3cd; color: #856404; margin-bottom: 20px; border-radius: 4px; border: 1px solid #ffeeba;}
    .clinic-card { background: var(--white); padding: 30px; margin-bottom: 25px; border-radius: 16px; box-shadow: var(--card-shadow); display: flex; gap: 25px; align-items: center; transition: var(--transition); border: 1px solid rgba(0,0,0,0.02);}
    .clinic-card:hover { transform: translateY(-3px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    .clinic-logo { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid var(--bg-color); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .clinic-info { flex: 1; }
    .clinic-info h3 { margin: 0 0 8px 0; color: var(--text-dark); font-size: 1.6em; font-weight: 700; letter-spacing: -0.5px;}
    .clinic-info p { margin: 4px 0; color: var(--text-muted); font-size: 1.05em; }
    .clinic-actions { text-align: right; }
    .rating-badge { display: inline-block; background: #fdf2d0; color: #f39c12; padding: 5px 10px; border-radius: 20px; font-weight: bold; margin-bottom: 10px;}

    .search-filter { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; gap: 15px;}
    .search-filter input, .search-filter select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em; }
    .search-filter input { flex: 2; }
    .search-filter select { flex: 1; }
    .search-filter button { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }

    @media (max-width: 768px) {
        .search-filter { flex-direction: column; }
        .clinic-card { flex-direction: column; text-align: center; }
        .clinic-actions { text-align: center; }
    }
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

<?php require_once APP_ROOT . '/app/views/inc/footer.php'; ?>
