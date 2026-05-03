<?php require_once APP_ROOT . '/app/views/inc/header.php'; ?>

<style>
    .hero {
        text-align: center; padding: 100px 20px; border-radius: 24px; margin-bottom: 50px;
        background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.5), 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        position: relative; overflow: hidden;
    }
    .hero::before {
        content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, transparent 60%);
        animation: pulse 15s linear infinite; pointer-events: none;
    }
    @keyframes pulse { 0% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.1); opacity: 0.8; } 100% { transform: scale(1); opacity: 0.5; } }

    .hero h1 { font-size: 3.5em; color: var(--text-dark); margin-bottom: 15px; font-weight: 800; letter-spacing: -1px; position: relative;}
    .hero h1 span { background: linear-gradient(135deg, var(--primary), #8B5CF6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .hero p { font-size: 1.25em; color: var(--text-muted); max-width: 650px; margin: 0 auto 40px auto; position: relative;}
    .search-box { display: flex; max-width: 600px; margin: 0 auto; gap: 10px; }
    .search-box input, .search-box select { padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em; }
    .search-box input { flex: 2; }
    .search-box select { flex: 1; }
    .search-box button { padding: 12px 25px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }

    .section-title { text-align: center; margin-bottom: 30px; font-size: 2em; color: #333; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
</style>

<div class="hero animate-fade-up">
    <h1>Find the Right <span>Doctor</span>.<br>Book Instantly.</h1>
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

<div class="card hover-lift" style="margin-top: 60px; text-align: center; background: linear-gradient(135deg, var(--primary), #8B5CF6); color: #fff; padding: 50px 20px; border-radius: 24px; border: none;">
    <h2 style="color: #fff; font-size: 2.5em; margin-bottom: 10px; font-weight: 800; letter-spacing: -0.5px;">Are you a Doctor or Clinic?</h2>
    <p style="font-size: 1.2em; margin-bottom: 30px; opacity: 0.9; max-width: 600px; margin-left: auto; margin-right: auto;">Join our platform to manage your appointments, offer video consultations, and build a beautiful online presence in minutes.</p>
    <a href="<?= URL_ROOT ?>/auth/register" class="btn-primary" style="background: #fff; color: var(--primary) !important; font-size: 1.1em; padding: 15px 30px;">Create Clinic Profile &rarr;</a>
</div>

<?php require_once APP_ROOT . '/app/views/inc/footer.php'; ?>
