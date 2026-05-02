<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 style="color: #28a745;">Ayurvedic Prakruti Pariksha (Body Constitution)</h2>
    <p>Select the option that best describes you.</p>

    <form action="<?= URL_ROOT ?>/assessment/prakruti" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
            <p><strong>1. Body Frame / Build</strong></p>
            <label><input type="radio" name="q[1]" value="v" required> Thin, bony, prominent joints</label><br>
            <label><input type="radio" name="q[1]" value="p"> Medium, well-proportioned</label><br>
            <label><input type="radio" name="q[1]" value="k"> Broad, heavy, sturdy</label>
        </div>

        <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
            <p><strong>2. Skin Texture</strong></p>
            <label><input type="radio" name="q[2]" value="v" required> Dry, rough, cool to touch</label><br>
            <label><input type="radio" name="q[2]" value="p"> Warm, oily, prone to acne/freckles</label><br>
            <label><input type="radio" name="q[2]" value="k"> Thick, moist, smooth, cool</label>
        </div>

        <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
            <p><strong>3. Sleep Pattern</strong></p>
            <label><input type="radio" name="q[3]" value="v" required> Light, easily interrupted</label><br>
            <label><input type="radio" name="q[3]" value="p"> Moderate, sound but short</label><br>
            <label><input type="radio" name="q[3]" value="k"> Deep, heavy, prolonged</label>
        </div>

        <!-- Adding just a few for demonstration -->

        <button type="submit" class="btn-primary" style="background: #28a745; width: 100%;">Calculate & Save Prakruti</button>
    </form>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
