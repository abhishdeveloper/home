<?php
$profile = $data['profile'];
$theme = $data['theme'];
$currentPage = $data['current_page'];
$pages = $data['pages'];

// Build Navigation HTML
$navHtml = '';
foreach ($pages as $p) {
    if ($p->status == 'published') {
        // If it's home, link to base slug, else link to base/page_slug
        $link = URL_ROOT . '/clinic/view/' . Security::escape($profile->slug) . ($p->is_home ? '' : '/' . Security::escape($p->slug));
        $navHtml .= '<a href="' . $link . '" style="margin: 0 10px;">' . Security::escape($p->title) . '</a>';
    }
}

// Build Social Links HTML
$socialHtml = '';
if (!empty($profile->facebook)) $socialHtml .= '<a href="' . Security::escape($profile->facebook) . '" target="_blank" style="margin: 0 5px;">Facebook</a>';
if (!empty($profile->instagram)) $socialHtml .= '<a href="' . Security::escape($profile->instagram) . '" target="_blank" style="margin: 0 5px;">Instagram</a>';
if (!empty($profile->whatsapp)) $socialHtml .= '<a href="https://wa.me/' . Security::escape($profile->whatsapp) . '" target="_blank" style="margin: 0 5px;">WhatsApp</a>';

// Replace Theme Variables
$headerHtml = $theme ? $theme->header_html : '<header><h1>{{clinic_name}}</h1><nav>{{navigation}}</nav></header>';
$footerHtml = $theme ? $theme->footer_html : '<footer><p>&copy; {{year}} {{clinic_name}}</p>{{social_links}}</footer>';

$replacements = [
    '{{clinic_name}}' => Security::escape($profile->clinic_name),
    '{{navigation}}' => $navHtml,
    '{{social_links}}' => $socialHtml,
    '{{year}}' => date('Y')
];

$headerHtml = str_replace(array_keys($replacements), array_values($replacements), $headerHtml);
$footerHtml = str_replace(array_keys($replacements), array_values($replacements), $footerHtml);

// Inject Custom Primary Color into CSS vars if theme provides it, or append to head
$cssVars = $theme ? $theme->css_variables : ':root { --primary-color: #333; }';
// Overwrite theme primary color with user selected color
$cssVars = preg_replace('/--primary-color:\s*#[a-zA-Z0-9]+;/', '--primary-color: ' . Security::escape($profile->primary_color) . ';', $cssVars);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::escape($profile->clinic_name) ?> - <?= $currentPage ? Security::escape($currentPage->title) : 'Home' ?></title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.core.css" rel="stylesheet">
    <style>
        <?= $cssVars ?>
        body { font-family: sans-serif; margin: 0; padding: 0; background: #f9f9f9; color: var(--text-color, #333); }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        header { background: var(--primary-color); color: #fff; padding: 20px 0; }
        header a { color: #fff; text-decoration: none; }
        footer { background: #333; color: #fff; padding: 20px 0; margin-top: 40px; }
        footer a { color: #ccc; }
        .content { background: #fff; padding: 30px; margin-top: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.05); min-height: 400px; }

        /* Directory Back Link */
        .directory-bar { background: #eee; padding: 10px; text-align: center; font-size: 0.9em; }
        .directory-bar a { color: #333; text-decoration: none; }
    </style>
</head>
<body>

    <div class="directory-bar">
        Powered by <a href="<?= URL_ROOT ?>/directory"><?= SITE_NAME ?></a>
    </div>

    <?= $headerHtml ?>

    <div class="container content">
        <?php if ($currentPage): ?>
            <div class="ql-editor">
                <!-- Outputting raw HTML from WYSIWYG editor. It should ideally be purified on backend using HTMLPurifier, but since it's a CMS built for the user, we render it directly. -->
                <?= $currentPage->content ?>
            </div>
        <?php else: ?>
            <h2>Welcome to <?= Security::escape($profile->clinic_name) ?></h2>
            <p>This clinic hasn't published any pages yet. Please check back later!</p>
            <?php if (!empty($profile->address) || !empty($profile->phone)): ?>
                <hr>
                <h3>Contact Information</h3>
                <?php if (!empty($profile->address)): ?><p><strong>Address:</strong> <?= nl2br(Security::escape($profile->address)) ?></p><?php endif; ?>
                <?php if (!empty($profile->phone)): ?><p><strong>Phone:</strong> <?= Security::escape($profile->phone) ?></p><?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?= $footerHtml ?>

</body>
</html>
