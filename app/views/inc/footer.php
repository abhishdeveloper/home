</div> <!-- Close .main-container -->

<style>
    .footer { background: #111827; color: #E5E7EB; padding: 60px 5% 20px 5%; font-size: 0.95em; margin-top: 60px; border-top: 4px solid var(--primary);}
    .footer-grid { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 40px; max-width: 1200px; margin: 0 auto; border-bottom: 1px solid #374151; padding-bottom: 40px;}
    .footer-col { flex: 1; min-width: 200px; }
    .footer-col h3 { color: #F9FAFB; margin-bottom: 20px; font-weight: 600; font-size: 1.2em; letter-spacing: 0.5px;}
    .footer-col p { margin: 8px 0; color: #9CA3AF; line-height: 1.6;}
    .footer-col ul { list-style: none; padding: 0; margin: 0; }
    .footer-col ul li { margin-bottom: 12px; }
    .footer-col a { color: #9CA3AF; text-decoration: none; transition: var(--transition); }
    .footer-col a:hover { color: var(--primary); padding-left: 5px; }
    .footer-bottom { text-align: center; padding-top: 20px; color: #6B7280; font-size: 0.85em; }

    @media (max-width: 768px) {
        .footer-grid { flex-direction: column; text-align: center; gap: 30px;}
        .footer-col { min-width: 100%; }
        .footer-col a:hover { padding-left: 0; } /* Disable shift on mobile */
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
