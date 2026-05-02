<?php
class ScheduleModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getScheduleByClinicId($clinic_id) {
        $this->db->query('SELECT * FROM clinic_schedules WHERE clinic_id = :clinic_id');
        $this->db->bind(':clinic_id', $clinic_id);
        return $this->db->single();
    }

    public function createOrUpdateSchedule($data) {
        $existing = $this->getScheduleByClinicId($data['clinic_id']);

        if ($existing) {
            $query = 'UPDATE clinic_schedules SET
                        slot_duration = :slot_duration,
                        monday_start = :mon_start, monday_end = :mon_end,
                        tuesday_start = :tue_start, tuesday_end = :tue_end,
                        wednesday_start = :wed_start, wednesday_end = :wed_end,
                        thursday_start = :thu_start, thursday_end = :thu_end,
                        friday_start = :fri_start, friday_end = :fri_end,
                        saturday_start = :sat_start, saturday_end = :sat_end,
                        sunday_start = :sun_start, sunday_end = :sun_end
                      WHERE clinic_id = :clinic_id';
        } else {
            $query = 'INSERT INTO clinic_schedules
                        (clinic_id, slot_duration, monday_start, monday_end, tuesday_start, tuesday_end, wednesday_start, wednesday_end, thursday_start, thursday_end, friday_start, friday_end, saturday_start, saturday_end, sunday_start, sunday_end)
                      VALUES
                        (:clinic_id, :slot_duration, :mon_start, :mon_end, :tue_start, :tue_end, :wed_start, :wed_end, :thu_start, :thu_end, :fri_start, :fri_end, :sat_start, :sat_end, :sun_start, :sun_end)';
        }

        $this->db->query($query);
        $this->db->bind(':clinic_id', $data['clinic_id']);
        $this->db->bind(':slot_duration', $data['slot_duration']);

        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        foreach ($days as $day) {
            $this->db->bind(':' . $day . '_start', !empty($data[$day . '_start']) ? $data[$day . '_start'] : null);
            $this->db->bind(':' . $day . '_end', !empty($data[$day . '_end']) ? $data[$day . '_end'] : null);
        }

        return $this->db->execute();
    }
}
