<?php
class PrescriptionModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getByAppointmentId($appointment_id) {
        $this->db->query('SELECT * FROM prescriptions WHERE appointment_id = :appointment_id');
        $this->db->bind(':appointment_id', $appointment_id);
        return $this->db->single();
    }

    public function savePrescription($data) {
        $existing = $this->getByAppointmentId($data['appointment_id']);

        if ($existing) {
            $this->db->query('UPDATE prescriptions SET medicines_json = :medicines_json, general_notes = :general_notes WHERE appointment_id = :appointment_id');
        } else {
            $this->db->query('INSERT INTO prescriptions (appointment_id, medicines_json, general_notes) VALUES (:appointment_id, :medicines_json, :general_notes)');
        }

        $this->db->bind(':appointment_id', $data['appointment_id']);
        $this->db->bind(':medicines_json', $data['medicines_json']);
        $this->db->bind(':general_notes', $data['general_notes']);

        return $this->db->execute();
    }

    // Medicine Inventory
    public function getClinicMedicines($clinic_id) {
        $this->db->query('SELECT * FROM medicines_inventory WHERE clinic_id = :clinic_id ORDER BY name ASC');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->resultSet();
    }

    public function saveMedicine($clinic_id, $name, $dosage) {
        // Check if exists
        $this->db->query('SELECT id FROM medicines_inventory WHERE clinic_id = :clinic_id AND name = :name');
        $this->db->bind(':clinic_id', $clinic_id);
        $this->db->bind(':name', $name);
        $exists = $this->db->single();

        if ($exists) {
            return true; // Already exists
        }

        $this->db->query('INSERT INTO medicines_inventory (clinic_id, name, default_dosage) VALUES (:clinic_id, :name, :dosage)');
        $this->db->bind(':clinic_id', $clinic_id);
        $this->db->bind(':name', $name);
        $this->db->bind(':dosage', $dosage);
        return $this->db->execute();
    }
}
