<?php require_once APP_ROOT . '/app/views/inc/header.php'; ?>

<div class="card" style="text-align: center;">
    <h1 style="color: #007bff;">Simple, Transparent Pricing</h1>
    <p style="font-size: 1.1em; color: #555; margin-bottom: 40px;">Choose the plan that best fits your clinic's needs.</p>

    <div style="display: flex; gap: 30px; justify-content: center; flex-wrap: wrap;">
        <!-- Basic Plan -->
        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 30px; flex: 1; max-width: 350px;">
            <h3>Basic Profile</h3>
            <div style="font-size: 2.5em; font-weight: bold; margin: 20px 0;">Free</div>
            <ul style="text-align: left; margin-bottom: 30px; line-height: 1.8;">
                <li>✓ Directory Listing</li>
                <li>✓ Appointment Booking</li>
                <li>✓ Video Consultations</li>
                <li>✓ Digital Prescriptions</li>
                <li>✓ Mini-CMS with Templates</li>
                <li>❌ Universal Platform Branding</li>
            </ul>
            <a href="<?= URL_ROOT ?>/auth/register" class="btn-outline" style="display: block;">Get Started</a>
        </div>

        <!-- Premium Plan -->
        <div style="border: 2px solid #007bff; border-radius: 8px; padding: 30px; flex: 1; max-width: 350px; position: relative; box-shadow: 0 10px 20px rgba(0,123,255,0.1);">
            <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #007bff; color: #fff; padding: 5px 15px; border-radius: 20px; font-size: 0.8em; font-weight: bold;">RECOMMENDED</div>
            <h3>Premium White-Label</h3>
            <div style="font-size: 2.5em; font-weight: bold; margin: 20px 0; color: #007bff;">₹999 <span style="font-size: 0.4em; color:#666;">/ one-time</span></div>
            <ul style="text-align: left; margin-bottom: 30px; line-height: 1.8;">
                <li>✓ All Basic Features</li>
                <li>✓ Complete White-Label Experience</li>
                <li>✓ Removal of Universal Header Ad</li>
                <li>✓ Removal of Universal Footer Ad</li>
                <li>✓ Priority Directory Listing</li>
            </ul>
            <a href="<?= URL_ROOT ?>/auth/register" class="btn-primary" style="display: block;">Register & Upgrade</a>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/inc/footer.php'; ?>
