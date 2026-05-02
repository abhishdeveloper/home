<?php
class PrescriptionController extends Controller {
    public function __construct() {
        Session::init();
        if (!Session::get('user_id')) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function generate($appointment_id) {
        if (Session::get('user_role_id') != 2) { // Must be Doctor/Clinic
            die("Unauthorized access.");
        }

        $appointmentModel = $this->model('AppointmentModel');
        $prescriptionModel = $this->model('PrescriptionModel');
        $clinicModel = $this->model('ClinicModel');

        $appt = $appointmentModel->getById($appointment_id);
        $profile = $clinicModel->getProfileByUserId(Session::get('user_id'));

        if (!$appt || !$profile || $appt->clinic_id != $profile->id) {
            die("Appointment not found or unauthorized.");
        }

        if ($appt->status != 'completed') {
            die("Cannot generate prescription. Appointment is not completed.");
        }

        $prescription = $prescriptionModel->getByAppointmentId($appointment_id);
        $savedMedicines = $prescriptionModel->getClinicMedicines($profile->id);

        $data = [
            'appointment' => $appt,
            'prescription' => $prescription,
            'saved_medicines' => $savedMedicines,
            'success' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
                die('CSRF Token Validation Failed');
            }

            // Save new medicines to inventory if requested
            if (isset($_POST['save_to_inventory']) && $_POST['save_to_inventory'] == '1') {
                if (!empty($_POST['med_name'])) {
                    for ($i = 0; $i < count($_POST['med_name']); $i++) {
                        $medName = trim($_POST['med_name'][$i]);
                        $medDosage = trim($_POST['med_dosage'][$i]);
                        if (!empty($medName)) {
                            $prescriptionModel->saveMedicine($profile->id, $medName, $medDosage);
                        }
                    }
                }
            }

            // Build JSON for prescription
            $medicinesArr = [];
            if (!empty($_POST['med_name'])) {
                for ($i = 0; $i < count($_POST['med_name']); $i++) {
                    $medName = trim($_POST['med_name'][$i]);
                    $medDosage = trim($_POST['med_dosage'][$i]);
                    $medNotes = trim($_POST['med_notes'][$i]);

                    if (!empty($medName)) {
                        $medicinesArr[] = [
                            'name' => $medName,
                            'dosage' => $medDosage,
                            'notes' => $medNotes
                        ];
                    }
                }
            }

            $prescData = [
                'appointment_id' => $appointment_id,
                'medicines_json' => json_encode($medicinesArr),
                'general_notes' => trim($_POST['general_notes'])
            ];

            if ($prescriptionModel->savePrescription($prescData)) {
                header('Location: ' . URL_ROOT . '/prescription/view/' . $appointment_id);
                exit;
            } else {
                $data['error'] = 'Failed to save prescription.';
            }
        }

        $this->view('prescriptions/generate', $data);
    }

    public function view($appointment_id) {
        $appointmentModel = $this->model('AppointmentModel');
        $prescriptionModel = $this->model('PrescriptionModel');

        $appt = $appointmentModel->getById($appointment_id);

        if (!$appt) {
            die("Appointment not found.");
        }

        // Must be the assigned patient or the assigned doctor
        if (Session::get('user_id') != $appt->patient_id && Session::get('user_id') != $appt->doctor_user_id) {
            die("Unauthorized access.");
        }

        $prescription = $prescriptionModel->getByAppointmentId($appointment_id);

        if (!$prescription) {
            die("Prescription not found.");
        }

        $medicines = json_decode($prescription->medicines_json, true);

        $data = [
            'appointment' => $appt,
            'prescription' => $prescription,
            'medicines' => $medicines
        ];

        // This view will be print-friendly
        $this->view('prescriptions/view', $data);
    }
}
