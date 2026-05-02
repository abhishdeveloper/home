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

        /* Booking Section */
        .booking-section { margin-top: 30px; padding: 20px; background: #f1f8ff; border-radius: 5px; border: 1px solid #d0e3ff; }
        .slot-btn { display: inline-block; padding: 8px 12px; margin: 5px; background: #fff; border: 1px solid #007bff; color: #007bff; border-radius: 3px; cursor: pointer; }
        .slot-btn.selected { background: #007bff; color: #fff; }
        .btn-book { padding: 10px 20px; background: #28a745; color: #fff; border: none; cursor: pointer; border-radius: 3px; font-size: 16px; margin-top: 15px;}
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
                if (data.success) {
                    slotsContainer.innerHTML = '<div style="color: green; font-weight: bold; padding: 10px; background: #d4edda; border-radius: 4px;">Appointment requested successfully! You can track its status in your dashboard.</div>';
                    submitBooking.style.display = 'none';
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
