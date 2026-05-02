<?php
class PagesController extends Controller {
    public function __construct() {
    }

    public function index() {
        $clinicModel = $this->model('ClinicModel');
        $specialtyModel = $this->model('SpecialtyModel');

        $featured = $clinicModel->getFeaturedClinics(3);
        $specialties = $specialtyModel->getAll();

        $data = [
            'title' => 'Welcome to ' . SITE_NAME,
            'description' => 'Connect with top doctors and manage your clinic seamlessly online.',
            'meta_desc' => 'Connect with top doctors and manage your clinic seamlessly online. Book appointments, video calls, and access digital prescriptions securely.',
            'featured_clinics' => $featured,
            'specialties' => $specialties
        ];

        $this->view('pages/index', $data);
    }

    public function services() {
        $this->view('pages/services', [
            'title' => 'Our Services',
            'meta_desc' => 'Explore the medical and telehealth services offered on our platform, from directory listings to full clinic CMS builders.'
        ]);
    }

    public function pricing() {
        $this->view('pages/pricing', [
            'title' => 'Pricing & Upgrades',
            'meta_desc' => 'Transparent pricing plans for doctors and clinics. Upgrade to a premium white-label experience.'
        ]);
    }

    public function contact() {
        $this->view('pages/contact', [
            'title' => 'Contact Us',
            'meta_desc' => 'Get in touch with our team for support regarding your patient or clinic account.'
        ]);
    }

    public function faq() {
        $this->view('pages/faq', [
            'title' => 'Frequently Asked Questions',
            'meta_desc' => 'Find answers to common questions about booking appointments, managing clinic profiles, and telemedicine.'
        ]);
    }

    public function privacy() {
        $this->view('pages/privacy', [
            'title' => 'Privacy Policy',
            'meta_desc' => 'Read our privacy policy to understand how we securely handle your medical data and personal information.'
        ]);
    }

    public function howItWorksDoctor() {
        $this->view('pages/how_doctor', [
            'title' => 'For Doctors: How it Works',
            'meta_desc' => 'A step-by-step guide for clinics and doctors to set up profiles, create mini-sites, and manage telehealth appointments.'
        ]);
    }

    public function howItWorksPatient() {
        $this->view('pages/how_patient', [
            'title' => 'For Patients: How it Works',
            'meta_desc' => 'A step-by-step guide for patients to find the right doctor, book appointments, and attend secure video calls.'
        ]);
    }
}
