<?php

class SitemapController extends Controller {
    public function index() {
        $clinicModel = $this->model('ClinicModel');

        // Fetch all published clinics
        $clinics = $clinicModel->searchClinics(); // Returns all if no params given

        header('Content-Type: text/xml; charset=utf-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static Pages
        $staticPages = [
            '',
            '/directory',
            '/pages/services',
            '/pages/pricing',
            '/pages/contact',
            '/pages/faq',
            '/pages/privacy',
            '/pages/howItWorksDoctor',
            '/pages/howItWorksPatient'
        ];

        foreach ($staticPages as $page) {
            echo "  <url>\n";
            echo "    <loc>" . URL_ROOT . $page . "</loc>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }

        // Dynamic Clinic Profiles & Their Custom Pages
        foreach ($clinics as $clinic) {
            $baseSlug = '/clinic/view/' . htmlspecialchars($clinic->slug);

            // Clinic Base Profile
            echo "  <url>\n";
            echo "    <loc>" . URL_ROOT . $baseSlug . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($clinic->updated_at)) . "</lastmod>\n";
            echo "    <changefreq>daily</changefreq>\n";
            echo "    <priority>0.9</priority>\n";
            echo "  </url>\n";

            // Clinic Custom Pages
            $pages = $clinicModel->getPages($clinic->id);
            foreach ($pages as $p) {
                if ($p->status == 'published' && !$p->is_home) { // Home is covered by baseSlug
                    echo "  <url>\n";
                    echo "    <loc>" . URL_ROOT . $baseSlug . '/' . htmlspecialchars($p->slug) . "</loc>\n";
                    echo "    <lastmod>" . date('Y-m-d', strtotime($p->updated_at)) . "</lastmod>\n";
                    echo "    <changefreq>weekly</changefreq>\n";
                    echo "    <priority>0.7</priority>\n";
                    echo "  </url>\n";
                }
            }
        }

        echo '</urlset>';
    }
}
