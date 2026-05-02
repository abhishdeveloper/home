<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Upgrade - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
        .btn { display: inline-block; padding: 15px 30px; background: #28a745; color: #fff; text-decoration: none; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; font-weight: bold; margin-top: 20px;}
        .alert-success { padding: 15px; background: #d4edda; color: #155724; margin-bottom: 20px; border-radius: 4px; }
        .price { font-size: 2.5em; color: #333; margin: 20px 0; }
        .features { text-align: left; margin: 20px auto; max-width: 300px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: left; margin-bottom: 20px;">
            <a href="<?= URL_ROOT ?>/clinicDashboard" style="color: #666; text-decoration: none;">&larr; Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert-success">Thank you! Your payment was successful and your account has been upgraded.</div>
        <?php endif; ?>

        <h2>Premium Profile Upgrade</h2>

        <?php if ($data['profile']->has_paid_branding): ?>
            <div style="padding: 30px; background: #f8f9fa; border-radius: 8px; margin-top: 20px;">
                <h3 style="color: #28a745;">You are a Premium Member</h3>
                <p>The universal platform branding has been successfully removed from your public site.</p>
                <p>Your patients now see a 100% white-labeled experience.</p>
            </div>
        <?php else: ?>
            <p>Remove the universal platform branding ("Create this type of site for yours with abhish.in") from your public clinic profile to offer your patients a completely white-labeled experience.</p>

            <div class="price">₹999 <span style="font-size: 0.4em; color: #666;">/ one-time</span></div>

            <div class="features">
                ✅ <strong>Remove universal header banner</strong><br>
                ✅ <strong>Remove universal footer banner</strong><br>
                ✅ <strong>100% White-labeled profile</strong>
            </div>

            <!-- Demo Payment Form -->
            <form action="<?= URL_ROOT ?>/billing/processDemoPayment" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <button type="submit" class="btn" onclick="return confirm('This will simulate a successful payment gateway callback and instantly upgrade your account. Proceed?');">
                    Pay Now (Demo)
                </button>
            </form>
            <p style="margin-top: 15px; font-size: 0.8em; color: #999;">* This is a simulated demo payment gateway.</p>
        <?php endif; ?>
    </div>
</body>
</html>
