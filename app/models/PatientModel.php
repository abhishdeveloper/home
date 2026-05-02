<?php
class PatientModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getProfileByUserId($user_id) {
        $this->db->query('SELECT * FROM patient_profiles WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function createProfile($data) {
        $this->db->query('INSERT INTO patient_profiles (user_id, age, address, preferred_specialty_id) VALUES (:user_id, :age, :address, :preferred_specialty_id)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':age', $data['age']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':preferred_specialty_id', $data['preferred_specialty_id']);

        return $this->db->execute();
    }

    public function updateProfile($data) {
        $this->db->query('UPDATE patient_profiles SET age = :age, address = :address, preferred_specialty_id = :preferred_specialty_id WHERE user_id = :user_id');
        $this->db->bind(':age', $data['age']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':preferred_specialty_id', $data['preferred_specialty_id']);
        $this->db->bind(':user_id', $data['user_id']);

        return $this->db->execute();
    }
}
