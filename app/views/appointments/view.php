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
        <div class="header">
            <div>
                <h2>Appointment on <?= date('M d, Y', strtotime($data['appointment']->appointment_date)) ?> at <?= date('h:i A', strtotime($data['appointment']->appointment_time)) ?></h2>
                <p>
                    <strong>Patient:</strong> <?= Security::escape($data['appointment']->patient_name) ?> |
                    <strong>Clinic:</strong> <?= Security::escape($data['appointment']->clinic_name) ?>
                </p>
            </div>
            <div>
                <span class="status <?= $data['appointment']->status ?>"><?= ucfirst($data['appointment']->status) ?></span>
                <br><br>
                <a href="<?= URL_ROOT ?>/appointment" class="btn" style="background:#6c757d;">Back to List</a>
            </div>
        </div>

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
    </div>
</body>
</html>
