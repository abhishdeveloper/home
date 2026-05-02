<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 style="color: #6f42c1;">Personality Examination (Big Five)</h2>
    <p>Rate yourself on the following traits from 1 (Low) to 5 (High).</p>

    <form action="<?= URL_ROOT ?>/assessment/personality" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <?php
        $traits = ['Openness', 'Conscientiousness', 'Extraversion', 'Agreeableness', 'Neuroticism'];
        foreach ($traits as $trait):
        ?>
        <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;">
            <strong style="width: 150px;"><?= $trait ?></strong>
            <div style="flex: 1; display: flex; justify-content: space-around;">
                <label><input type="radio" name="<?= strtolower($trait) ?>" value="1" required> 1</label>
                <label><input type="radio" name="<?= strtolower($trait) ?>" value="2"> 2</label>
                <label><input type="radio" name="<?= strtolower($trait) ?>" value="3"> 3</label>
                <label><input type="radio" name="<?= strtolower($trait) ?>" value="4"> 4</label>
                <label><input type="radio" name="<?= strtolower($trait) ?>" value="5"> 5</label>
            </div>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn-primary" style="background: #6f42c1; width: 100%;">Calculate & Save Personality Profile</button>
    </form>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
