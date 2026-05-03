<?php require_once APP_ROOT . '/app/views/inc/header.php'; ?>

<div class="card">
    <h1 style="color: #007bff;">For Patients: How it Works</h1>

    <div style="margin-top: 30px;">
        <h3>1. Search the Directory</h3>
        <p>Use the search bar on our homepage to find doctors by name, or filter by specialty (e.g., Neurology, Gynecology). If no doctors are available in your selected specialty, our smart system will recommend highly-rated Ayurveda specialists as an alternative.</p>

        <h3 style="margin-top: 25px;">2. View Clinic Profiles</h3>
        <p>Click on a clinic to view their custom mini-website. You can read about their services, meet their team, and see verified ratings and reviews from other patients to ensure you are getting the best care.</p>

        <h3 style="margin-top: 25px;">3. Book an Appointment</h3>
        <p>Log in as a Patient. On the doctor's profile, select a date from the calendar. The system will display the doctor's available time slots for that day. Click a time and confirm your request.</p>

        <h3 style="margin-top: 25px;">4. Consult via Video & Chat</h3>
        <p>Once the doctor approves your request, you will receive an email. Head to "My Appointments" in your dashboard to access the secure chat lobby and the "Join Video Call" link for telemedicine appointments.</p>

        <h3 style="margin-top: 25px;">5. Download Your Prescription</h3>
        <p>After your consultation, the doctor will generate a digital prescription. You can view, download, or print this directly from your appointment dashboard.</p>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= URL_ROOT ?>/directory" class="btn-primary">Find a Doctor Now</a>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/inc/footer.php'; ?>
