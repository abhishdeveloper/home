<?php require_once APP_ROOT . '/app/views/inc/header.php'; ?>

<div class="card">
    <h1 style="color: #007bff;">For Doctors & Clinics: How it Works</h1>

    <div style="margin-top: 30px;">
        <h3>1. Create an Account & Profile</h3>
        <p>Register as a "Doctor / Clinic". During onboarding, you will define your unique URL slug (e.g., `<?= URL_ROOT ?>/clinic/view/your-name`), select a color theme, and upload your logo.</p>

        <h3 style="margin-top: 25px;">2. Build Your Website (Mini-CMS)</h3>
        <p>Head to your Dashboard and click "Add New Page". Our platform includes a powerful rich-text editor.</p>
        <ul>
            <li>Click <strong>Insert Template</strong> to instantly load beautiful, pre-designed layouts for your Home page, Services, or Contact info.</li>
            <li>Customize the text, change colors, add images, and hit "Save".</li>
            <li>Be sure to click <strong>Publish Site</strong> on your dashboard to make your site live to the public.</li>
        </ul>

        <h3 style="margin-top: 25px;">3. Manage Your Schedule</h3>
        <p>Click "Manage Schedule" in your dashboard to set your daily operating hours (e.g., Mon-Fri 09:00 - 17:00) and your appointment slot duration (e.g., 30 minutes). The system will automatically calculate available slots for patients and prevent double-booking.</p>

        <h3 style="margin-top: 25px;">4. Handle Appointments & Video Calls</h3>
        <p>When a patient requests a slot, you will receive an email. Log in and "Approve" the appointment. Approving it will unlock a secure chat room and a Jitsi Video Call link for telehealth consultations.</p>

        <h3 style="margin-top: 25px;">5. Generate Prescriptions</h3>
        <p>After clicking "Mark Completed", you can use the Prescription Generator. Add medicines and save them to your quick-fill inventory for faster prescribing next time. The patient will instantly get a printable PDF-friendly copy with your logo.</p>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= URL_ROOT ?>/auth/register" class="btn-primary">Create Your Clinic Profile Now</a>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/inc/footer.php'; ?>
