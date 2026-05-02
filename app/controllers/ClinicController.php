<?php

class ClinicController extends Controller {
    public function view($slug = '', $page_slug = '') {
        if (empty($slug)) {
            header('Location: ' . URL_ROOT . '/directory');
            exit;
        }

        $clinicModel = $this->model('ClinicModel');
        $themeModel = $this->model('ThemeModel');

        $profile = $clinicModel->getProfileBySlug($slug);

        if (!$profile) {
            die("Clinic profile not found or is currently unpublished.");
        }

        $theme = $themeModel->getById($profile->theme_id);

        $pages = $clinicModel->getPages($profile->id);

        $currentPage = null;
        $homePage = null;

        // Find requested page and homepage
        foreach ($pages as $p) {
            if ($p->status == 'published') {
                if ($p->is_home) {
                    $homePage = $p;
                }
                if ($page_slug && $p->slug == $page_slug) {
                    $currentPage = $p;
                }
            }
        }

        // If no specific page requested, default to home page
        if (!$currentPage && empty($page_slug)) {
            $currentPage = $homePage;
        }

        // If still no page, and they have published pages, pick the first one
        if (!$currentPage) {
            foreach ($pages as $p) {
                if ($p->status == 'published') {
                    $currentPage = $p;
                    break;
                }
            }
        }

        $data = [
            'profile' => $profile,
            'theme' => $theme,
            'pages' => $pages, // to build nav
            'current_page' => $currentPage
        ];

        $this->view('clinic/public_view', $data);
    }
}
