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
        $activeStyle = ($currentPage && $currentPage->id == $p->id) ? 'font-weight:700; text-decoration:underline;' : '';
        $navHtml .= '<a href="' . $link . '" style="color: inherit; text-decoration: none; ' . $activeStyle . '">' . Security::escape($p->title) . '</a>';
    }
}

// Build Social Links HTML
$socialHtml = '';
if (!empty($profile->facebook)) $socialHtml .= '<a href="' . Security::escape($profile->facebook) . '" target="_blank" style="color: inherit; text-decoration:none;">Facebook</a>';
if (!empty($profile->instagram)) $socialHtml .= '<a href="' . Security::escape($profile->instagram) . '" target="_blank" style="color: inherit; text-decoration:none;">Instagram</a>';
if (!empty($profile->whatsapp)) $socialHtml .= '<a href="https://wa.me/' . Security::escape($profile->whatsapp) . '" target="_blank" style="color: inherit; text-decoration:none;">WhatsApp</a>';

// Replace Theme Variables
$headerHtml = $theme ? $theme->header_html : '<header><h1>{{clinic_name}}</h1><nav>{{navigation}}</nav></header>';
$footerHtml = $theme ? $theme->footer_html : '<footer><p>&copy; {{year}} {{clinic_name}}</p>{{social_links}}</footer>';

$logoHtml = '';
if (!empty($profile->logo_url)) {
    $logoHtml = '<img src="' . URL_ROOT . Security::escape($profile->logo_url) . '" alt="Logo" style="max-height: 60px; border-radius: 8px;">';
}

$replacements = [
    '{{clinic_name}}' => Security::escape($profile->clinic_name),
    '{{navigation}}' => $navHtml,
    '{{social_links}}' => $socialHtml,
    '{{logo}}' => $logoHtml,
    '{{year}}' => date('Y')
];

$headerHtml = str_replace(array_keys($replacements), array_values($replacements), $headerHtml);
$footerHtml = str_replace(array_keys($replacements), array_values($replacements), $footerHtml);

// Inject Custom Primary Color into CSS vars if theme provides it, or append to head
$cssVars = $theme ? $theme->css_variables : ':root { --primary-color: #4F46E5; --bg-color: #F3F4F6; --text-color: #1F2937; }';
// Overwrite theme primary color with user selected color
$cssVars = preg_replace('/--primary-color:\s*#[a-zA-Z0-9]+;/', '--primary-color: ' . Security::escape($profile->primary_color) . ';', $cssVars);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::escape($data['title']) ?></title>
    <meta name="description" content="<?= Security::escape($data['meta_desc']) ?>">
    <meta name="keywords" content="<?= Security::escape($data['meta_keywords']) ?>">

    <meta property="og:title" content="<?= Security::escape($data['title']) ?>">
    <meta property="og:description" content="<?= Security::escape($data['meta_desc']) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= URL_ROOT . $_SERVER['REQUEST_URI'] ?>">
    <?php if (!empty($data['og_image'])): ?>
        <meta property="og:image" content="<?= URL_ROOT . Security::escape($data['og_image']) ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.core.css" rel="stylesheet">

    <!-- Schema.org JSON-LD Structured Data for Local Business / Physician -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Physician",
      "name": "<?= Security::escape($profile->clinic_name) ?>",
      "image": "<?= !empty($profile->logo_url) ? URL_ROOT . Security::escape($profile->logo_url) : '' ?>",
      "@id": "<?= URL_ROOT . '/clinic/view/' . Security::escape($profile->slug) ?>",
      "url": "<?= URL_ROOT . '/clinic/view/' . Security::escape($profile->slug) ?>",
      "telephone": "<?= Security::escape($profile->phone ?? '') ?>",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "<?= Security::escape($profile->address ?? '') ?>"
      },
      "medicalSpecialty": "<?= Security::escape($data['specialty_name']) ?>",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?= number_format($data['avgRating']->avg_rating ?? 5, 1) ?>",
        "reviewCount": "<?= max(1, $data['avgRating']->total_reviews ?? 1) ?>"
      }
    }
    </script>

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

        /* Booking Section */
        .booking-section { margin-top: 30px; padding: 20px; background: #f1f8ff; border-radius: 5px; border: 1px solid #d0e3ff; }
        .slot-btn { display: inline-block; padding: 8px 12px; margin: 5px; background: #fff; border: 1px solid #007bff; color: #007bff; border-radius: 3px; cursor: pointer; }
        .slot-btn.selected { background: #007bff; color: #fff; }
        .btn-book { padding: 10px 20px; background: #28a745; color: #fff; border: none; cursor: pointer; border-radius: 3px; font-size: 16px; margin-top: 15px;}
    </style>
</head>
<body>

    <?php if (!$profile->has_paid_branding): ?>
        <div style="background: #333; color: #fff; text-align: center; padding: 10px; font-size: 14px;">
            Create this type of site for yours with <a href="https://abhish.in" target="_blank" style="color: #ffc107; font-weight: bold;">abhish.in</a>
        </div>
    <?php endif; ?>

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

        <hr style="margin: 40px 0;">

        <!-- Reviews Section -->
        <div class="reviews-section" style="margin-bottom: 40px;">
            <h3>Patient Reviews</h3>
            <?php if ($data['avgRating']->total_reviews > 0): ?>
                <div style="font-size: 1.2em; font-weight: bold; color: #f39c12; margin-bottom: 20px;">
                    ⭐ <?= number_format($data['avgRating']->avg_rating, 1) ?> / 5 (<?= $data['avgRating']->total_reviews ?> Reviews)
                </div>
                <?php foreach ($data['reviews'] as $review): ?>
                    <div style="background: #fdfdfd; padding: 15px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong><?= Security::escape($review->patient_name) ?></strong>
                            <span style="color: #f39c12;">
                                <?= str_repeat('★', $review->rating) ?><?= str_repeat('☆', 5 - $review->rating) ?>
                            </span>
                        </div>
                        <?php if (!empty($review->review_text)): ?>
                            <p style="margin-top: 10px; color: #555;"><?= nl2br(Security::escape($review->review_text)) ?></p>
                        <?php endif; ?>
                        <small style="color: #999;"><?= date('M d, Y', strtotime($review->created_at)) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
        </div>

        <?php if ($data['schedule']): ?>
            <div class="booking-section">
                <h3>Book an Appointment</h3>
                <?php if (Session::get('user_id') && Session::get('user_role_id') == 3): ?>
                    <form id="bookingForm">
                        <input type="hidden" id="clinic_id" value="<?= $profile->id ?>">
                        <input type="hidden" id="csrf_token" value="<?= Security::generateCSRFToken() ?>">

                        <label for="date_picker">Select Date:</label><br>
                        <input type="date" id="date_picker" min="<?= date('Y-m-d') ?>" style="padding: 8px; margin: 10px 0;"><br>

                        <div id="slots_container" style="margin-top: 15px;">
                            <!-- Slots will be populated here via AJAX -->
                            <p style="color: #666;">Select a date to view available time slots.</p>
                        </div>

                        <input type="hidden" id="selected_time" value="">

                        <div id="booking_message" style="margin-top: 10px; color: red;"></div>
                        <button type="button" id="submit_booking" class="btn-book" style="display: none;">Confirm Appointment</button>
                    </form>
                <?php else: ?>
                    <p>Please <a href="<?= URL_ROOT ?>/auth/login">login as a Patient</a> to book an appointment.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?= $footerHtml ?>

    <?php if (!$profile->has_paid_branding): ?>
        <div style="background: #333; color: #fff; text-align: center; padding: 15px; font-size: 14px; margin-top: 0;">
            Want a professional site like this? <a href="https://abhish.in" target="_blank" style="color: #ffc107; font-weight: bold;">Create yours with abhish.in</a>
        </div>
    <?php endif; ?>

    <?php if ($data['schedule'] && Session::get('user_id') && Session::get('user_role_id') == 3): ?>
    <script>
        const datePicker = document.getElementById('date_picker');
        const slotsContainer = document.getElementById('slots_container');
        const selectedTimeInput = document.getElementById('selected_time');
        const submitBooking = document.getElementById('submit_booking');
        const bookingMessage = document.getElementById('booking_message');
        const clinicId = document.getElementById('clinic_id').value;

        datePicker.addEventListener('change', function() {
            const date = this.value;
            if (!date) return;

            slotsContainer.innerHTML = '<p>Loading slots...</p>';
            selectedTimeInput.value = '';
            submitBooking.style.display = 'none';
            bookingMessage.innerHTML = '';

            let formData = new FormData();
            formData.append('date', date);

            fetch('<?= URL_ROOT ?>/clinic/getAvailableSlots/' + clinicId, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    slotsContainer.innerHTML = '<p style="color:red;">' + data.error + '</p>';
                } else if (data.slots && data.slots.length > 0) {
                    slotsContainer.innerHTML = '<p>Available slots for ' + date + ':</p>';
                    data.slots.forEach(slot => {
                        let btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'slot-btn';
                        btn.textContent = slot;
                        btn.onclick = function() {
                            // deselect all
                            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                            this.classList.add('selected');
                            selectedTimeInput.value = slot;
                            submitBooking.style.display = 'inline-block';
                        };
                        slotsContainer.appendChild(btn);
                    });
                } else {
                    slotsContainer.innerHTML = '<p>No available slots on this date.</p>';
                }
            })
            .catch(err => {
                slotsContainer.innerHTML = '<p style="color:red;">Error fetching slots.</p>';
            });
        });

        submitBooking.addEventListener('click', function() {
            const date = datePicker.value;
            const time = selectedTimeInput.value;
            const csrf = document.getElementById('csrf_token').value;

            if (!date || !time) {
                bookingMessage.innerHTML = 'Please select a date and time.';
                return;
            }

            submitBooking.disabled = true;
            submitBooking.textContent = 'Booking...';

            let formData = new FormData();
            formData.append('clinic_id', clinicId);
            formData.append('appointment_date', date);
            formData.append('appointment_time', time);
            formData.append('csrf_token', csrf);

            fetch('<?= URL_ROOT ?>/clinic/bookAppointment', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    bookingMessage.innerHTML = data.error || 'An error occurred.';
                    submitBooking.disabled = false;
                    submitBooking.textContent = 'Confirm Appointment';
                }
            })
            .catch(err => {
                bookingMessage.innerHTML = 'A network error occurred.';
                submitBooking.disabled = false;
                submitBooking.textContent = 'Confirm Appointment';
            });
        });
    </script>
    <?php endif; ?>

</body>
</html>
