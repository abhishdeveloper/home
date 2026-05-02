<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="card">
    <h1 style="color: #007bff;">Contact Us</h1>
    <p>We are here to help! Whether you are a patient looking for assistance with booking, or a doctor needing help setting up your profile, feel free to reach out to us.</p>

    <div style="margin-top: 30px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h3>Get in Touch</h3>
        <p><strong>Email:</strong> support@<?= strtolower(str_replace(' ', '', SITE_NAME)) ?>.com</p>
        <p><strong>Phone:</strong> +91 8199861552</p>
        <p><strong>Address:</strong> Dr. Naresh Dalal Hospital, Jhajjar, Haryana</p>
    </div>

    <h3 style="margin-top: 40px;">Send a Message</h3>
    <form style="max-width: 600px;">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Name</label>
            <input type="text" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Email</label>
            <input type="email" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Message</label>
            <textarea rows="5" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
        </div>
        <button type="button" class="btn-primary" onclick="alert('This is a demo. Your message was not sent.');">Send Message</button>
    </form>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
