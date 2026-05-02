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
            'featured_clinics' => $featured,
            'specialties' => $specialties
        ];

        $this->view('pages/index', $data);
    }

    public function services() {
        $this->view('pages/services', ['title' => 'Our Services']);
    }

    public function pricing() {
        $this->view('pages/pricing', ['title' => 'Pricing & Upgrades']);
    }

    public function contact() {
        $this->view('pages/contact', ['title' => 'Contact Us']);
    }

    public function faq() {
        $this->view('pages/faq', ['title' => 'Frequently Asked Questions']);
    }

    public function privacy() {
        $this->view('pages/privacy', ['title' => 'Privacy Policy']);
    }

    public function howItWorksDoctor() {
        $this->view('pages/how_doctor', ['title' => 'For Doctors: How it Works']);
    }

    public function howItWorksPatient() {
        $this->view('pages/how_patient', ['title' => 'For Patients: How it Works']);
    }
}
