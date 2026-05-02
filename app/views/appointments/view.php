<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .status { padding: 4px 8px; border-radius: 3px; font-size: 0.9em; font-weight: bold; }
        .status.pending { background: #ffc107; color: #212529; }
        .status.approved { background: #28a745; color: #fff; }
        .status.rejected { background: #dc3545; color: #fff; }
        .status.completed { background: #17a2b8; color: #fff; }

        /* Chat Box */
        .chat-container { border: 1px solid #ddd; border-radius: 5px; margin-top: 20px; }
        .chat-messages { height: 300px; overflow-y: scroll; padding: 15px; background: #f9f9f9; }
        .message { margin-bottom: 10px; padding: 10px; border-radius: 5px; max-width: 80%; }
        .message.mine { background: #007bff; color: #fff; margin-left: auto; }
        .message.other { background: #e9ecef; color: #333; margin-right: auto; }
        .message-sender { font-size: 0.8em; margin-bottom: 5px; opacity: 0.8; }
        .chat-input-area { display: flex; border-top: 1px solid #ddd; }
        .chat-input-area input { flex: 1; padding: 10px; border: none; outline: none; }
        .chat-input-area button { padding: 10px 20px; background: #28a745; color: #fff; border: none; cursor: pointer; }

        .btn { display: inline-block; padding: 10px 15px; background: #007bff; color: #fff; text-decoration: none; border-radius: 3px; font-size: 0.9em; margin-top: 10px;}
    </style>
</head>
<body>
    <div class="container">
        <style>
            .appt-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
            .appt-layout { display: flex; gap: 20px; flex-wrap: wrap; }
            .col-left { flex: 2; min-width: 300px; }
            .col-right { flex: 1; min-width: 250px; background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 15px; }
            @media (max-width: 768px) {
                .appt-header { flex-direction: column; text-align: center; }
                .appt-header > div { text-align: center !important; }
            }
        </style>

        <div class="appt-header">
            <div>
                <h2 style="margin: 0 0 5px 0;">Appointment on <?= date('M d, Y', strtotime($data['appointment']->appointment_date)) ?> at <?= date('h:i A', strtotime($data['appointment']->appointment_time)) ?></h2>
                <p style="margin: 0;">
                    <strong>Patient:</strong> <?= Security::escape($data['appointment']->patient_name) ?> |
                    <strong>Clinic:</strong> <?= Security::escape($data['appointment']->clinic_name) ?>
                </p>
                <div style="margin-top: 10px;">
                    <?php if (Session::get('user_role_id') == 2): ?>
                        <a href="tel:<?= Security::escape($data['appointment']->patient_phone) ?>" class="btn" style="background:#17a2b8; padding: 5px 10px; border-radius:20px; font-size: 0.85em; display: inline-block;">📞 Call Patient (<?= Security::escape($data['appointment']->patient_phone) ?>)</a>
                    <?php elseif (Session::get('user_role_id') == 3): ?>
                        <a href="tel:<?= Security::escape($data['appointment']->clinic_phone) ?>" class="btn" style="background:#17a2b8; padding: 5px 10px; border-radius:20px; font-size: 0.85em; display: inline-block;">📞 Call Clinic (<?= Security::escape($data['appointment']->clinic_phone) ?>)</a>
                    <?php endif; ?>
                </div>
            </div>
            <div style="text-align: right;">
                <span class="status <?= $data['appointment']->status ?>"><?= ucfirst($data['appointment']->status) ?></span>
                <br><br>
                <a href="<?= URL_ROOT ?>/appointment" class="btn" style="background:#6c757d;">Back to List</a>
            </div>
        </div>

        <div class="appt-layout">

        <!-- Left Column: Chat & Video -->
        <div class="col-left">
        <?php if ($data['appointment']->status == 'approved'): ?>
            <div style="background: #e2e3e5; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;" id="video_section">
                <h3>Video Consultation</h3>
                <p>Join the secure video call room for your appointment.</p>
                <!-- Using Jitsi Meet External API directly in a new window or iframe -->
                <?php
                    // Create a unique, predictable room name using a hash of the appointment ID and a secret salt
                    $roomName = "ClinicDirectoryAppt_" . md5($data['appointment']->id . "ClinicDirectorySecretSalt2024");
                    $jitsiUrl = "https://meet.jit.si/" . $roomName . "#config.prejoinPageEnabled=false";
                ?>
                <a href="<?= $jitsiUrl ?>" target="_blank" class="btn" style="background: #dc3545;">Join Video Call</a>
            </div>

            <div class="chat-container">
                <div class="chat-messages" id="chat_messages">
                    <!-- Loaded via AJAX -->
                </div>
                <div class="chat-input-area">
                    <input type="hidden" id="chat_csrf" value="<?= Security::generateCSRFToken() ?>">
                    <input type="text" id="chat_input" placeholder="Type a message...">
                    <button type="button" id="send_btn">Send</button>
                </div>
            </div>

            <script>
                const appointmentId = <?= $data['appointment']->id ?>;
                const chatMessages = document.getElementById('chat_messages');
                const chatInput = document.getElementById('chat_input');
                const sendBtn = document.getElementById('send_btn');

                function fetchMessages() {
                    fetch('<?= URL_ROOT ?>/appointment/getMessages/' + appointmentId)
                    .then(res => res.json())
                    .then(data => {
                        if (data.messages) {
                            let html = '';
                            data.messages.forEach(msg => {
                                let isMine = (msg.sender_id == data.current_user_id);
                                html += `<div class="message ${isMine ? 'mine' : 'other'}">
                                            <div class="message-sender">${isMine ? 'You' : msg.sender_name}</div>
                                            <div>${msg.message}</div>
                                         </div>`;
                            });
                            // Only update if content changed to prevent scrolling issues, or just scroll to bottom
                            let isScrolledToBottom = chatMessages.scrollHeight - chatMessages.clientHeight <= chatMessages.scrollTop + 1;
                            chatMessages.innerHTML = html;
                            if (isScrolledToBottom) {
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        }
                    });
                }

                // Poll every 3 seconds
                setInterval(fetchMessages, 3000);
                fetchMessages(); // initial load

                sendBtn.addEventListener('click', function() {
                    let text = chatInput.value.trim();
                    let csrf = document.getElementById('chat_csrf').value;
                    if (!text) return;

                    let formData = new FormData();
                    formData.append('appointment_id', appointmentId);
                    formData.append('message', text);
                    formData.append('csrf_token', csrf);

                    fetch('<?= URL_ROOT ?>/appointment/sendMessage', {
                        method: 'POST',
                        body: formData
                    }).then(res => res.json())
                      .then(data => {
                        if(data.success) {
                            chatInput.value = '';
                            fetchMessages();
                        } else {
                            alert(data.error || 'Failed to send message.');
                        }
                    });
                });

                chatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') sendBtn.click();
                });
            </script>
        <?php elseif ($data['appointment']->status == 'completed'): ?>
            <div style="background: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; color: #155724;">
                <h3>Appointment Completed</h3>
                <p>This appointment has concluded.</p>
                <?php if (Session::get('user_role_id') == 2): ?>
                    <a href="<?= URL_ROOT ?>/prescription/generate/<?= $data['appointment']->id ?>" class="btn" style="background:#28a745;">Manage Prescription</a>
                <?php else: ?>
                    <a href="<?= URL_ROOT ?>/prescription/view/<?= $data['appointment']->id ?>" class="btn">View / Download Prescription</a>
                <?php endif; ?>
            </div>

            <!-- Review Section -->
            <div style="background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
                <?php if (Session::get('user_role_id') == 3): ?>
                    <!-- Patient reviewing Clinic -->
                    <?php if ($data['clinicReview']): ?>
                        <h4>Your Review of the Clinic</h4>
                        <p><strong>Rating:</strong> <?= $data['clinicReview']->rating ?> / 5 Stars</p>
                        <p><?= nl2br(Security::escape($data['clinicReview']->review_text)) ?></p>
                    <?php else: ?>
                        <h4>Rate Your Experience with <?= Security::escape($data['appointment']->clinic_name) ?></h4>
                        <form action="<?= URL_ROOT ?>/review/submit" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="appointment_id" value="<?= $data['appointment']->id ?>">
                            <div style="margin-bottom: 10px;">
                                <label>Rating:</label>
                                <select name="rating" required>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <textarea name="review_text" rows="3" style="width: 100%;" placeholder="Leave a review... (Optional)"></textarea>
                            </div>
                            <button type="submit" class="btn">Submit Review</button>
                        </form>
                    <?php endif; ?>
                <?php elseif (Session::get('user_role_id') == 2): ?>
                    <!-- Clinic reviewing Patient -->
                    <?php if ($data['patientReview']): ?>
                        <h4>Your Rating of the Patient</h4>
                        <p><strong>Rating:</strong> <?= $data['patientReview']->rating ?> / 5 Stars</p>
                        <p><?= nl2br(Security::escape($data['patientReview']->review_text)) ?></p>
                    <?php else: ?>
                        <h4>Rate <?= Security::escape($data['appointment']->patient_name) ?></h4>
                        <p style="font-size: 0.9em; color: #666;">Private rating for internal clinic records/reputation tracking.</p>
                        <form action="<?= URL_ROOT ?>/review/submit" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="appointment_id" value="<?= $data['appointment']->id ?>">
                            <div style="margin-bottom: 10px;">
                                <label>Rating:</label>
                                <select name="rating" required>
                                    <option value="5">5 - Excellent Patient</option>
                                    <option value="4">4 - Good</option>
                                    <option value="3">3 - Average</option>
                                    <option value="2">2 - Difficult</option>
                                    <option value="1">1 - No Show / Very Difficult</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <textarea name="review_text" rows="3" style="width: 100%;" placeholder="Internal notes... (Optional)"></textarea>
                            </div>
                            <button type="submit" class="btn">Save Rating</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <p>Chat and Video capabilities will be available once the appointment is approved by the clinic.</p>
        <?php endif; ?>
        </div> <!-- End Left Column -->

        <!-- Right Column: Medical Attachments & Notes -->
        <div class="col-right">
            <h3 style="margin-top: 0; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">Medical Records & Attachments</h3>
            <p style="font-size: 0.9em; color: #666;">Upload past prescriptions, lab reports, or images relevant to this consultation.</p>

            <?php if (!empty($data['attachment_error'])): ?>
                <div style="color: red; font-size: 0.9em; margin-bottom: 10px;"><?= Security::escape($data['attachment_error']) ?></div>
            <?php endif; ?>

            <?php if (in_array($data['appointment']->status, ['pending', 'approved'])): ?>
                <form action="<?= URL_ROOT ?>/appointment/view/<?= $data['appointment']->id ?>" method="POST" enctype="multipart/form-data" style="margin-bottom: 20px; background: #f9f9f9; padding: 10px; border-radius: 4px;">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="file" name="attachment" style="margin-bottom: 10px; width: 100%;" required>
                    <button type="submit" class="btn" style="padding: 5px 10px; font-size: 0.85em; background: #28a745;">Upload File</button>
                </form>
            <?php endif; ?>

            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (empty($data['attachments'])): ?>
                    <li style="font-size: 0.9em; color: #999;">No attachments uploaded yet.</li>
                <?php else: ?>
                    <?php foreach ($data['attachments'] as $att): ?>
                        <li style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <a href="<?= URL_ROOT . Security::escape($att->file_url) ?>" target="_blank" style="text-decoration: none; color: #007bff; font-weight: bold;">
                                📄 <?= Security::escape($att->file_name) ?>
                            </a>
                            <div style="font-size: 0.8em; color: #666;">Uploaded by <?= Security::escape($att->uploader_name) ?> on <?= date('M d', strtotime($att->created_at)) ?></div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <?php if (Session::get('user_role_id') == 2): // Doctor Private Notes & Assessments ?>
                <div style="margin-top: 40px; background: #eef5ff; border: 1px solid #cce5ff; border-radius: 5px; padding: 15px;">
                    <h3 style="margin-top: 0; color: #004085; font-size: 1.1em; border-bottom: 2px solid #004085; padding-bottom: 5px;">Patient Medical Assessments</h3>

                    <?php
                    $pp = $data['patientProfile'];
                    if (!$pp || (empty($pp->prakruti_assessment) && empty($pp->psychological_assessment) && empty($pp->personality_assessment))):
                    ?>
                        <p style="font-size: 0.9em; color: #666;">The patient has not completed any medical assessments yet.</p>
                    <?php else: ?>
                        <?php if (!empty($pp->prakruti_assessment)): $pr = json_decode($pp->prakruti_assessment); ?>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #28a745;">Ayurvedic Prakruti:</strong><br>
                                Dominant Dosha: <strong><?= Security::escape($pr->dominant_dosha) ?></strong><br>
                                <span style="font-size: 0.85em; color: #555;">(Vata: <?= $pr->scores->Vata ?? 0 ?>, Pitta: <?= $pr->scores->Pitta ?? 0 ?>, Kapha: <?= $pr->scores->Kapha ?? 0 ?>)</span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pp->psychological_assessment)): $ps = json_decode($pp->psychological_assessment); ?>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #17a2b8;">Psychological State:</strong><br>
                                Severity: <strong><?= Security::escape($ps->severity) ?></strong><br>
                                <span style="font-size: 0.85em; color: #555;">(Score: <?= $ps->score ?>/12)</span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pp->personality_assessment)): $pe = json_decode($pp->personality_assessment); ?>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #6f42c1;">Personality Profile (Big 5):</strong><br>
                                <div style="font-size: 0.85em; color: #555; display: grid; grid-template-columns: 1fr 1fr;">
                                    <?php foreach($pe->traits as $trait => $score): ?>
                                        <div><?= Security::escape($trait) ?>: <?= (int)$score ?>/5</div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 20px; background: #fff8e1; border: 1px solid #ffeeba; border-radius: 5px; padding: 15px;">
                    <h3 style="margin-top: 0; color: #856404; font-size: 1.1em;">Private Medical Notes</h3>
                    <p style="font-size: 0.85em; color: #666;">These notes are only visible to you.</p>
                    <form id="notesForm">
                        <textarea id="private_notes" rows="6" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: monospace; resize: vertical;"><?= Security::escape($data['appointment']->private_notes ?? '') ?></textarea>
                        <button type="button" id="saveNotesBtn" class="btn" style="background: #28a745; margin-top: 10px; padding: 5px 10px; font-size: 0.85em;">Save Notes</button>
                        <span id="notesStatus" style="font-size: 0.8em; color: green; margin-left: 10px; display: none;">Saved!</span>
                    </form>
                </div>
                <script>
                    document.getElementById('saveNotesBtn').addEventListener('click', function() {
                        let text = document.getElementById('private_notes').value;
                        let csrf = document.getElementById('chat_csrf') ? document.getElementById('chat_csrf').value : '<?= Security::generateCSRFToken() ?>'; // fallback if chat is hidden
                        let statusSpan = document.getElementById('notesStatus');

                        let formData = new FormData();
                        formData.append('appointment_id', <?= $data['appointment']->id ?>);
                        formData.append('notes', text);
                        formData.append('csrf_token', csrf);

                        fetch('<?= URL_ROOT ?>/appointment/saveNotes', {
                            method: 'POST',
                            body: formData
                        }).then(res => res.json())
                          .then(data => {
                            if(data.success) {
                                statusSpan.style.display = 'inline';
                                setTimeout(() => { statusSpan.style.display = 'none'; }, 2000);
                            } else {
                                alert(data.error || 'Failed to save notes.');
                            }
                        });
                    });
                </script>
            <?php endif; ?>

        </div> <!-- End Right Column -->

        </div> <!-- End Flex Container -->
    </div>
</body>
</html>
