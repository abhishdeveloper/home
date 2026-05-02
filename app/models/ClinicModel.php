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

    public function getProfileById($id) {
        $this->db->query('SELECT * FROM clinic_profiles WHERE id = :id');
        $this->db->bind(':id', $id);
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
        $this->db->query('INSERT INTO clinic_profiles (user_id, slug, clinic_name, specialty_id, theme_id, primary_color, logo_url, address, phone, whatsapp, facebook, instagram, consultation_fee) VALUES (:user_id, :slug, :clinic_name, :specialty_id, :theme_id, :primary_color, :logo_url, :address, :phone, :whatsapp, :facebook, :instagram, :consultation_fee)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':clinic_name', $data['clinic_name']);
        $this->db->bind(':specialty_id', $data['specialty_id']);
        $this->db->bind(':theme_id', $data['theme_id']);
        $this->db->bind(':primary_color', $data['primary_color']);
        $this->db->bind(':logo_url', $data['logo_url'] ?? null);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':whatsapp', $data['whatsapp']);
        $this->db->bind(':facebook', $data['facebook']);
        $this->db->bind(':instagram', $data['instagram']);
        $this->db->bind(':consultation_fee', $data['consultation_fee'] ?? 0.00);

        return $this->db->execute();
    }

    public function updateProfile($data) {
        $query = 'UPDATE clinic_profiles SET slug = :slug, clinic_name = :clinic_name, specialty_id = :specialty_id, theme_id = :theme_id, primary_color = :primary_color, address = :address, phone = :phone, whatsapp = :whatsapp, facebook = :facebook, instagram = :instagram, consultation_fee = :consultation_fee';
        if (isset($data['logo_url'])) {
            $query .= ', logo_url = :logo_url';
        }
        $query .= ' WHERE user_id = :user_id';

        $this->db->query($query);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':clinic_name', $data['clinic_name']);
        $this->db->bind(':specialty_id', $data['specialty_id']);
        $this->db->bind(':theme_id', $data['theme_id']);
        $this->db->bind(':primary_color', $data['primary_color']);
        if (isset($data['logo_url'])) {
            $this->db->bind(':logo_url', $data['logo_url']);
        }
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':whatsapp', $data['whatsapp']);
        $this->db->bind(':facebook', $data['facebook']);
        $this->db->bind(':instagram', $data['instagram']);
        $this->db->bind(':consultation_fee', $data['consultation_fee'] ?? 0.00);
        $this->db->bind(':user_id', $data['user_id']);

        return $this->db->execute();
    }

    public function setPublished($user_id, $status) {
        $this->db->query('UPDATE clinic_profiles SET is_published = :status WHERE user_id = :user_id');
        $this->db->bind(':status', $status);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function upgradeBranding($clinic_id) {
        $this->db->query('UPDATE clinic_profiles SET has_paid_branding = 1 WHERE id = :clinic_id');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->execute();
    }

    public function recordTransaction($clinic_id, $amount) {
        $this->db->query('INSERT INTO billing_transactions (clinic_id, amount, status) VALUES (:clinic_id, :amount, "completed")');
        $this->db->bind(':clinic_id', $clinic_id);
        $this->db->bind(':amount', $amount);
        return $this->db->execute();
    }

    // Pages Management
    public function getPages($clinic_id) {
        $this->db->query('SELECT * FROM clinic_pages WHERE clinic_id = :clinic_id ORDER BY is_home DESC, created_at ASC');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->resultSet();
    }

    public function getPageBySlug($clinic_id, $slug, $published_only = true) {
        $query = 'SELECT * FROM clinic_pages WHERE clinic_id = :clinic_id AND slug = :slug';
        if ($published_only) {
            $query .= ' AND status = "published"';
        }
        $this->db->query($query);
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

    public function getPageById($page_id) {
        $this->db->query('SELECT * FROM clinic_pages WHERE id = :id');
        $this->db->bind(':id', $page_id);
        return $this->db->single();
    }

    public function updatePage($data) {
        $this->db->query('UPDATE clinic_pages SET title = :title, slug = :slug, content = :content, is_home = :is_home, status = :status WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':is_home', $data['is_home'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'draft');
        return $this->db->execute();
    }

    public function deletePage($page_id, $clinic_id) {
        $this->db->query('DELETE FROM clinic_pages WHERE id = :id AND clinic_id = :clinic_id');
        $this->db->bind(':id', $page_id);
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->execute();
    }

    public function searchClinics($specialty_id = null, $search_query = null) {
        $query = 'SELECT cp.*, s.name as specialty_name,
                  (SELECT AVG(rating) FROM clinic_reviews WHERE clinic_id = cp.id) as avg_rating
                  FROM clinic_profiles cp
                  LEFT JOIN specialties s ON cp.specialty_id = s.id
                  WHERE cp.is_published = 1';

        if ($specialty_id) {
            $query .= ' AND cp.specialty_id = :specialty_id';
        }
        if ($search_query) {
            $query .= ' AND (cp.clinic_name LIKE :search OR cp.address LIKE :search)';
        }

        $this->db->query($query);

        if ($specialty_id) {
            $this->db->bind(':specialty_id', $specialty_id);
        }
        if ($search_query) {
            $this->db->bind(':search', '%' . $search_query . '%');
        }

        return $this->db->resultSet();
    }

    public function getFeaturedClinics($limit = 3) {
        // Fetch published clinics ordered by their average rating
        $this->db->query('
            SELECT cp.*, s.name as specialty_name,
            (SELECT AVG(rating) FROM clinic_reviews WHERE clinic_id = cp.id) as avg_rating,
            (SELECT COUNT(id) FROM clinic_reviews WHERE clinic_id = cp.id) as total_reviews
            FROM clinic_profiles cp
            LEFT JOIN specialties s ON cp.specialty_id = s.id
            WHERE cp.is_published = 1
            ORDER BY avg_rating DESC, total_reviews DESC
            LIMIT :limit
        ');
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}
