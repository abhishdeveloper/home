<?php
class PagesController extends Controller {
    public function __construct() {
    }

    public function index() {
        $data = [
            'title' => 'Welcome to ' . SITE_NAME,
            'description' => 'The best platform for Doctors and Clinics to manage their online presence.'
        ];

        $this->view('pages/index', $data);
    }
}
