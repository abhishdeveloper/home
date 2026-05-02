<?php
class ThemeModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query('SELECT * FROM themes ORDER BY id ASC');
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query('SELECT * FROM themes WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}
