<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 style="color: #17a2b8;">Psychological State Examination</h2>
    <p>Over the last 2 weeks, how often have you been bothered by any of the following problems?</p>

    <form action="<?= URL_ROOT ?>/assessment/psychological" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <?php
        $questions = [
            "Little interest or pleasure in doing things",
            "Feeling down, depressed, or hopeless",
            "Trouble falling or staying asleep, or sleeping too much",
            "Feeling tired or having little energy"
        ];
        foreach ($questions as $i => $q):
        ?>
        <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
            <p><strong><?= $i+1 ?>. <?= $q ?></strong></p>
            <label style="margin-right: 15px;"><input type="radio" name="q[<?= $i ?>]" value="0" required> Not at all</label>
            <label style="margin-right: 15px;"><input type="radio" name="q[<?= $i ?>]" value="1"> Several days</label>
            <label style="margin-right: 15px;"><input type="radio" name="q[<?= $i ?>]" value="2"> More than half the days</label>
            <label><input type="radio" name="q[<?= $i ?>]" value="3"> Nearly every day</label>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn-primary" style="background: #17a2b8; width: 100%;">Calculate & Save Profile</button>
    </form>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
