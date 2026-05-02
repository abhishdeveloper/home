<?php
class ClinicModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getProfileByUserId($user_id) {
        $this->db->query('SELECT * FROM clinic_profiles WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function getProfileBySlug($slug) {
        $this->db->query('SELECT * FROM clinic_profiles WHERE slug = :slug AND is_published = 1');
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    public function checkSlugExists($slug, $exclude_user_id = null) {
        $query = 'SELECT id FROM clinic_profiles WHERE slug = :slug';
        if ($exclude_user_id) {
            $query .= ' AND user_id != :user_id';
        }
        $this->db->query($query);
        $this->db->bind(':slug', $slug);
        if ($exclude_user_id) {
            $this->db->bind(':user_id', $exclude_user_id);
        }
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    public function createProfile($data) {
        $this->db->query('INSERT INTO clinic_profiles (user_id, slug, clinic_name, specialty_id, theme_id, primary_color, address, phone, whatsapp, facebook, instagram) VALUES (:user_id, :slug, :clinic_name, :specialty_id, :theme_id, :primary_color, :address, :phone, :whatsapp, :facebook, :instagram)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':clinic_name', $data['clinic_name']);
        $this->db->bind(':specialty_id', $data['specialty_id']);
        $this->db->bind(':theme_id', $data['theme_id']);
        $this->db->bind(':primary_color', $data['primary_color']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':whatsapp', $data['whatsapp']);
        $this->db->bind(':facebook', $data['facebook']);
        $this->db->bind(':instagram', $data['instagram']);

        return $this->db->execute();
    }

    public function updateProfile($data) {
        $this->db->query('UPDATE clinic_profiles SET slug = :slug, clinic_name = :clinic_name, specialty_id = :specialty_id, theme_id = :theme_id, primary_color = :primary_color, address = :address, phone = :phone, whatsapp = :whatsapp, facebook = :facebook, instagram = :instagram WHERE user_id = :user_id');
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':clinic_name', $data['clinic_name']);
        $this->db->bind(':specialty_id', $data['specialty_id']);
        $this->db->bind(':theme_id', $data['theme_id']);
        $this->db->bind(':primary_color', $data['primary_color']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':whatsapp', $data['whatsapp']);
        $this->db->bind(':facebook', $data['facebook']);
        $this->db->bind(':instagram', $data['instagram']);
        $this->db->bind(':user_id', $data['user_id']);

        return $this->db->execute();
    }

    public function setPublished($user_id, $status) {
        $this->db->query('UPDATE clinic_profiles SET is_published = :status WHERE user_id = :user_id');
        $this->db->bind(':status', $status);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Pages Management
    public function getPages($clinic_id) {
        $this->db->query('SELECT * FROM clinic_pages WHERE clinic_id = :clinic_id ORDER BY is_home DESC, created_at ASC');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->resultSet();
    }

    public function getPageBySlug($clinic_id, $slug) {
        $this->db->query('SELECT * FROM clinic_pages WHERE clinic_id = :clinic_id AND slug = :slug AND status = "published"');
        $this->db->bind(':clinic_id', $clinic_id);
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    public function createPage($data) {
        $this->db->query('INSERT INTO clinic_pages (clinic_id, title, slug, content, is_home, status) VALUES (:clinic_id, :title, :slug, :content, :is_home, :status)');
        $this->db->bind(':clinic_id', $data['clinic_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':is_home', $data['is_home'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'draft');
        return $this->db->execute();
    }

    public function searchClinics($specialty_id = null) {
        $query = 'SELECT cp.*, s.name as specialty_name FROM clinic_profiles cp LEFT JOIN specialties s ON cp.specialty_id = s.id WHERE cp.is_published = 1';
        if ($specialty_id) {
            $query .= ' AND cp.specialty_id = :specialty_id';
        }
        $this->db->query($query);
        if ($specialty_id) {
            $this->db->bind(':specialty_id', $specialty_id);
        }
        return $this->db->resultSet();
    }
}
