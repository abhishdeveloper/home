<?php
class SpecialtyModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query('SELECT * FROM specialties ORDER BY name ASC');
        return $this->db->resultSet();
    }
}
