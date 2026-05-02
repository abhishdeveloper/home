<?php

class AssessmentController extends Controller {
    public function __construct() {
        Session::init();

        // Must be logged in as a patient to take assessments
        if (!Session::get('user_id') || Session::get('user_role_id') != 3) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit;
        }
    }

    public function prakruti() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $vata = 0; $pitta = 0; $kapha = 0;

            // Simple tally of selected radio buttons
            if (isset($_POST['q'])) {
                foreach ($_POST['q'] as $val) {
                    if ($val == 'v') $vata++;
                    if ($val == 'p') $pitta++;
                    if ($val == 'k') $kapha++;
                }
            }

            // Determine dominance
            $scores = ['Vata' => $vata, 'Pitta' => $pitta, 'Kapha' => $kapha];
            arsort($scores);
            $dominant = array_key_first($scores);

            $results = [
                'scores' => $scores,
                'dominant_dosha' => $dominant,
                'date_taken' => date('Y-m-d H:i:s')
            ];

            $this->model('PatientModel')->updateAssessment(Session::get('user_id'), 'prakruti_assessment', json_encode($results));

            header('Location: ' . URL_ROOT . '/patientDashboard?msg=Prakruti Assessment Saved');
            exit;
        }

        $this->view('patient/assessments/prakruti');
    }

    public function psychological() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $score = 0;
            if (isset($_POST['q'])) {
                foreach ($_POST['q'] as $val) {
                    $score += (int)$val;
                }
            }

            // Simple severity mapping (0-27 scale based on 9 questions 0-3)
            $severity = 'Minimal';
            if ($score >= 5) $severity = 'Mild';
            if ($score >= 10) $severity = 'Moderate';
            if ($score >= 15) $severity = 'Moderately Severe';
            if ($score >= 20) $severity = 'Severe';

            $results = [
                'score' => $score,
                'severity' => $severity,
                'date_taken' => date('Y-m-d H:i:s')
            ];

            $this->model('PatientModel')->updateAssessment(Session::get('user_id'), 'psychological_assessment', json_encode($results));

            header('Location: ' . URL_ROOT . '/patientDashboard?msg=Psychological Assessment Saved');
            exit;
        }

        $this->view('patient/assessments/psychological');
    }

    public function personality() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Simplified Big 5 trait extraction
            $traits = [
                'Openness' => (int)($_POST['openness'] ?? 0),
                'Conscientiousness' => (int)($_POST['conscientiousness'] ?? 0),
                'Extraversion' => (int)($_POST['extraversion'] ?? 0),
                'Agreeableness' => (int)($_POST['agreeableness'] ?? 0),
                'Neuroticism' => (int)($_POST['neuroticism'] ?? 0),
            ];

            $results = [
                'traits' => $traits,
                'date_taken' => date('Y-m-d H:i:s')
            ];

            $this->model('PatientModel')->updateAssessment(Session::get('user_id'), 'personality_assessment', json_encode($results));

            header('Location: ' . URL_ROOT . '/patientDashboard?msg=Personality Assessment Saved');
            exit;
        }

        $this->view('patient/assessments/personality');
    }
}
