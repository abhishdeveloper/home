<?php

class ReviewModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Clinic Reviews
    public function getClinicReviewByAppointment($appointment_id) {
        $this->db->query('SELECT * FROM clinic_reviews WHERE appointment_id = :appointment_id');
        $this->db->bind(':appointment_id', $appointment_id);
        return $this->db->single();
    }

    public function addClinicReview($data) {
        $this->db->query('INSERT INTO clinic_reviews (appointment_id, patient_id, clinic_id, rating, review_text) VALUES (:appointment_id, :patient_id, :clinic_id, :rating, :review_text)');
        $this->db->bind(':appointment_id', $data['appointment_id']);
        $this->db->bind(':patient_id', $data['patient_id']);
        $this->db->bind(':clinic_id', $data['clinic_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':review_text', $data['review_text']);
        return $this->db->execute();
    }

    public function getReviewsForClinic($clinic_id) {
        $this->db->query('SELECT cr.*, u.name as patient_name FROM clinic_reviews cr JOIN users u ON cr.patient_id = u.id WHERE cr.clinic_id = :clinic_id ORDER BY cr.created_at DESC');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->resultSet();
    }

    public function getClinicAverageRating($clinic_id) {
        $this->db->query('SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM clinic_reviews WHERE clinic_id = :clinic_id');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->single();
    }


    // Patient Reviews
    public function getPatientReviewByAppointment($appointment_id) {
        $this->db->query('SELECT * FROM patient_reviews WHERE appointment_id = :appointment_id');
        $this->db->bind(':appointment_id', $appointment_id);
        return $this->db->single();
    }

    public function addPatientReview($data) {
        $this->db->query('INSERT INTO patient_reviews (appointment_id, clinic_id, patient_id, rating, review_text) VALUES (:appointment_id, :clinic_id, :patient_id, :rating, :review_text)');
        $this->db->bind(':appointment_id', $data['appointment_id']);
        $this->db->bind(':clinic_id', $data['clinic_id']);
        $this->db->bind(':patient_id', $data['patient_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':review_text', $data['review_text']);
        return $this->db->execute();
    }

    public function getPatientAverageRating($patient_user_id) {
        $this->db->query('SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM patient_reviews WHERE patient_id = :patient_id');
        $this->db->bind(':patient_id', $patient_user_id);
        return $this->db->single();
    }
}
