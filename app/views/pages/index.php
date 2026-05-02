<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<style>
    .hero { text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px;}
    .hero h1 { font-size: 2.5em; color: #007bff; margin-bottom: 10px; }
    .hero p { font-size: 1.2em; color: #555; max-width: 600px; margin: 0 auto 30px auto; }
    .search-box { display: flex; max-width: 600px; margin: 0 auto; gap: 10px; }
    .search-box input, .search-box select { padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em; }
    .search-box input { flex: 2; }
    .search-box select { flex: 1; }
    .search-box button { padding: 12px 25px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }

    .section-title { text-align: center; margin-bottom: 30px; font-size: 2em; color: #333; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
</style>

<div class="hero">
    <h1>Find the Right Doctor. Book Instantly.</h1>
    <p><?= Security::escape($data['description']) ?></p>

    <form action="<?= URL_ROOT ?>/directory" method="GET" class="search-box">
        <input type="text" name="q" placeholder="Search doctors, clinics, or conditions...">
        <select name="specialty">
            <option value="">All Specialties</option>
            <?php foreach($data['specialties'] as $spec): ?>
                <option value="<?= $spec->id ?>"><?= Security::escape($spec->name) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>
</div>

<h2 class="section-title">Featured Doctors & Clinics</h2>
<div class="grid">
    <?php if (empty($data['featured_clinics'])): ?>
        <p style="text-align: center; grid-column: 1 / -1;">No clinics are currently featured. Check back soon!</p>
    <?php else: ?>
        <?php foreach ($data['featured_clinics'] as $clinic): ?>
            <div class="card" style="text-align: center;">
                <?php if(!empty($clinic->logo_url)): ?>
                    <img src="<?= URL_ROOT . Security::escape($clinic->logo_url) ?>" alt="Logo" style="width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:15px;">
                <?php else: ?>
                    <div style="width:80px; height:80px; border-radius:50%; background:#eee; margin:0 auto 15px auto; display:flex; align-items:center; justify-content:center; color:#999;">No Logo</div>
                <?php endif; ?>
                <h3 style="margin: 0 0 10px 0;"><?= Security::escape($clinic->clinic_name) ?></h3>
                <p style="color: #666; margin: 0 0 15px 0;"><?= Security::escape($clinic->specialty_name ?? 'General Practice') ?></p>
                <div style="color: #f39c12; margin-bottom: 15px;">
                    ⭐ <?= number_format($clinic->avg_rating ?? 0, 1) ?> (<?= $clinic->total_reviews ?? 0 ?> Reviews)
                </div>
                <a href="<?= URL_ROOT ?>/clinic/view/<?= Security::escape($clinic->slug) ?>" class="btn-outline" style="display: inline-block;">View Profile & Book</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="card" style="margin-top: 40px; text-align: center; background: #007bff; color: #fff;">
    <h2 style="color: #fff;">Are you a Doctor or Clinic?</h2>
    <p style="font-size: 1.1em; margin-bottom: 20px;">Join our platform to manage your appointments, offer video consultations, and build a beautiful online presence in minutes.</p>
    <a href="<?= URL_ROOT ?>/auth/register" class="btn-primary" style="background: #fff; color: #007bff !important;">Create Clinic Profile</a>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
