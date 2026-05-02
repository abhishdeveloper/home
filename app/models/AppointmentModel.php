<?php
class AppointmentModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getBookedTimes($clinic_id, $date) {
        // Fetch times for pending and approved appointments on a specific date
        $this->db->query('SELECT appointment_time FROM appointments WHERE clinic_id = :clinic_id AND appointment_date = :date AND status IN ("pending", "approved")');
        $this->db->bind(':clinic_id', $clinic_id);
        $this->db->bind(':date', $date);

        $results = $this->db->resultSet();
        $bookedTimes = [];
        foreach($results as $row) {
            // Strip seconds for easier comparison
            $bookedTimes[] = substr($row->appointment_time, 0, 5);
        }
        return $bookedTimes;
    }

    public function createAppointment($data) {
        $this->db->query('INSERT INTO appointments (patient_id, clinic_id, appointment_date, appointment_time, status) VALUES (:patient_id, :clinic_id, :appointment_date, :appointment_time, :status)');
        $this->db->bind(':patient_id', $data['patient_id']);
        $this->db->bind(':clinic_id', $data['clinic_id']);
        $this->db->bind(':appointment_date', $data['appointment_date']);
        $this->db->bind(':appointment_time', $data['appointment_time']);
        $this->db->bind(':status', 'pending');

        return $this->db->execute();
    }

    public function getPatientAppointments($patient_user_id) {
        $this->db->query('SELECT a.*, cp.clinic_name FROM appointments a JOIN clinic_profiles cp ON a.clinic_id = cp.id WHERE a.patient_id = :patient_id ORDER BY a.appointment_date DESC, a.appointment_time DESC');
        $this->db->bind(':patient_id', $patient_user_id);
        return $this->db->resultSet();
    }

    public function getClinicAppointments($clinic_id) {
        // Fetch appointments with patient name and their average rating
        $this->db->query('
            SELECT a.*, u.name as patient_name,
            (SELECT AVG(rating) FROM patient_reviews WHERE patient_id = a.patient_id) as patient_rating,
            (SELECT COUNT(id) FROM patient_reviews WHERE patient_id = a.patient_id) as patient_review_count
            FROM appointments a
            JOIN patient_profiles pp ON a.patient_id = pp.user_id
            JOIN users u ON pp.user_id = u.id
            WHERE a.clinic_id = :clinic_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query('SELECT a.*, u.name as patient_name, cp.clinic_name, cp.user_id as doctor_user_id FROM appointments a JOIN patient_profiles pp ON a.patient_id = pp.user_id JOIN users u ON pp.user_id = u.id JOIN clinic_profiles cp ON a.clinic_id = cp.id WHERE a.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateStatus($id, $status) {
        $this->db->query('UPDATE appointments SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Dashboard Stats
    public function getClinicDashboardStats($clinic_id) {
        $this->db->query('
            SELECT
                COUNT(*) as total_appointments,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_requests,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as upcoming_appointments
            FROM appointments
            WHERE clinic_id = :clinic_id
        ');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->single();
    }

    public function getPatientDashboardStats($patient_user_id) {
        $this->db->query('
            SELECT
                COUNT(*) as total_appointments,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as upcoming_appointments
            FROM appointments
            WHERE patient_id = :patient_id
        ');
        $this->db->bind(':patient_id', $patient_user_id);
        return $this->db->single();
    }

    public function getUpcomingPatientAppointments($patient_user_id, $limit = 5) {
        $this->db->query('
            SELECT a.*, cp.clinic_name
            FROM appointments a
            JOIN clinic_profiles cp ON a.clinic_id = cp.id
            WHERE a.patient_id = :patient_id AND a.status = "approved" AND a.appointment_date >= CURDATE()
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
            LIMIT :limit
        ');
        $this->db->bind(':patient_id', $patient_user_id);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Chat Messages
    public function getMessages($appointment_id) {
        $this->db->query('SELECT am.*, u.name as sender_name FROM appointment_messages am JOIN users u ON am.sender_id = u.id WHERE am.appointment_id = :appointment_id ORDER BY am.created_at ASC');
        $this->db->bind(':appointment_id', $appointment_id);
        return $this->db->resultSet();
    }

    public function addMessage($appointment_id, $sender_id, $message) {
        $this->db->query('INSERT INTO appointment_messages (appointment_id, sender_id, message) VALUES (:appointment_id, :sender_id, :message)');
        $this->db->bind(':appointment_id', $appointment_id);
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':message', $message);
        return $this->db->execute();
    }
}
