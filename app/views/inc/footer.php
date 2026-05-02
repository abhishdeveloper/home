</div> <!-- Close .main-container -->

<style>
    .footer { background: #2c3e50; color: #ecf0f1; padding: 50px 5% 20px 5%; font-size: 0.9em; margin-top: 50px;}
    .footer-grid { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px; max-width: 1200px; margin: 0 auto; border-bottom: 1px solid #34495e; padding-bottom: 30px;}
    .footer-col { flex: 1; min-width: 200px; }
    .footer-col h3 { color: #fff; margin-bottom: 20px; }
    .footer-col p { margin: 5px 0; color: #bdc3c7; }
    .footer-col ul { list-style: none; padding: 0; margin: 0; }
    .footer-col ul li { margin-bottom: 10px; }
    .footer-col a { color: #bdc3c7; text-decoration: none; transition: color 0.3s; }
    .footer-col a:hover { color: #fff; }
    .footer-bottom { text-align: center; padding-top: 20px; color: #7f8c8d; }

    @media (max-width: 768px) {
        .footer-grid { flex-direction: column; text-align: center; }
        .footer-col { min-width: 100%; }
        .footer { padding: 40px 20px 20px 20px; }
    }
</style>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-col">
            <h3><?= SITE_NAME ?></h3>
            <p>Connecting patients with top medical professionals seamlessly.</p>
            <p>Empowering clinics with powerful digital tools.</p>
        </div>

        <div class="footer-col">
            <h3>Patients</h3>
            <ul>
                <li><a href="<?= URL_ROOT ?>/directory">Find a Doctor</a></li>
                <li><a href="<?= URL_ROOT ?>/pages/howItWorksPatient">How to Book an Appointment</a></li>
                <li><a href="<?= URL_ROOT ?>/auth/register">Create Patient Account</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Doctors & Clinics</h3>
            <ul>
                <li><a href="<?= URL_ROOT ?>/pages/services">Platform Services</a></li>
                <li><a href="<?= URL_ROOT ?>/pages/pricing">Pricing & Upgrades</a></li>
                <li><a href="<?= URL_ROOT ?>/pages/howItWorksDoctor">How to Create Your Profile</a></li>
                <li><a href="<?= URL_ROOT ?>/auth/register">Register Your Clinic</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Company</h3>
            <ul>
                <li><a href="<?= URL_ROOT ?>/pages/contact">Contact Us</a></li>
                <li><a href="<?= URL_ROOT ?>/pages/faq">FAQs</a></li>
                <li><a href="<?= URL_ROOT ?>/pages/privacy">Privacy Policy</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
    </div>
</footer>

</body>
</html>
