<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Page - <?= SITE_NAME ?></title>
    <!-- Include Quill.js for WYSIWYG -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"], .form-group select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 10px 15px; background: #28a745; color: #fff; border: none; cursor: pointer; border-radius: 3px; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; display: block; }
        #editor { height: 300px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Create New Page</h2>
            <a href="<?= URL_ROOT ?>/clinicDashboard" style="color: #666;">Back to Dashboard</a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <span class="error"><?= Security::escape($data['error']) ?></span>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/clinicDashboard/createPage" method="POST" id="pageForm">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="content" id="hidden_content">

            <div class="form-group">
                <label for="title">Page Title</label>
                <input type="text" name="title" id="title" value="<?= Security::escape($data['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="slug">URL Slug (e.g., about-us)</label>
                <input type="text" name="slug" id="slug" value="<?= Security::escape($data['slug']) ?>" required>
            </div>

            <div class="form-group">
                <label for="is_home">
                    <input type="checkbox" name="is_home" id="is_home" value="1" <?= $data['is_home'] ? 'checked' : '' ?>> Set as Homepage
                </label>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="draft" <?= $data['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $data['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>

            <div class="form-group" style="background: #e9ecef; padding: 10px; border-radius: 4px; display: flex; align-items: center; gap: 15px;">
                <label style="margin:0;">Load Template:</label>
                <select id="templateSelector" style="width: auto; flex: 1;">
                    <option value="">-- Blank Page --</option>
                    <option value="home">Modern Home Page</option>
                    <option value="services">Services List</option>
                    <option value="team">Our Team / Doctors</option>
                    <option value="contact">Contact & Location</option>
                </select>
                <button type="button" id="loadTemplateBtn" class="btn" style="background: #17a2b8; padding: 6px 12px;">Insert Template</button>
            </div>

            <div class="form-group">
                <label>Page Content</label>
                <div id="editor"><?= $data['content'] // Intentionally NOT escaped so we can re-render HTML if form fails validation ?></div>
            </div>

            <button type="submit" class="btn">Save Page</button>
        </form>
    </div>

    <!-- Initialize Quill editor -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'font': [] }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        // Template System
        const templates = {
            'home': `<h1 class="ql-align-center">Welcome to Our Clinic</h1><p class="ql-align-center"><br></p><p class="ql-align-center">We provide world-class healthcare tailored to your needs.</p><p class="ql-align-center"><br></p><hr><h2 class="ql-align-center">Why Choose Us?</h2><p><br></p><ul><li><strong>Expert Care:</strong> Highly qualified specialists.</li><li><strong>Modern Facilities:</strong> State-of-the-art technology.</li><li><strong>Patient First:</strong> We care about your comfort.</li></ul><p><br></p>`,
            'services': `<h2>Our Medical Services</h2><p><br></p><h3>1. General Consultation</h3><p>Comprehensive health checkups and personalized medical advice.</p><p><br></p><h3>2. Diagnostic Services</h3><p>Accurate and fast diagnostic testing.</p><p><br></p><h3>3. Specialized Care</h3><p>Treatment plans designed by experts for specific conditions.</p>`,
            'team': `<h2 class="ql-align-center">Meet Our Specialists</h2><p class="ql-align-center"><br></p><h3>Dr. John Doe</h3><p><em>Chief Medical Officer</em></p><p>Dr. Doe has over 15 years of experience in general medicine and specializes in preventive care.</p><p><br></p><hr><p><br></p><h3>Dr. Jane Smith</h3><p><em>Lead Surgeon</em></p><p>Dr. Smith is a board-certified surgeon with a passion for minimally invasive techniques.</p>`,
            'contact': `<h2>Contact Us</h2><p>If you have any questions or need to schedule an appointment, please reach out to us!</p><p><br></p><h3>Opening Hours:</h3><p>Monday - Friday: 9:00 AM - 5:00 PM</p><p>Saturday: 10:00 AM - 2:00 PM</p><p>Sunday: Closed</p><p><br></p><h3>Location:</h3><p>123 Health Avenue, Medical District.</p><p><strong>Phone: </strong>+1 234 567 890</p><p><strong>Email: </strong>contact@ourclinic.com</p>`
        };

        document.getElementById('loadTemplateBtn').addEventListener('click', function() {
            const selected = document.getElementById('templateSelector').value;
            if (selected && templates[selected]) {
                if (quill.getLength() > 1) { // If editor is not empty
                    if (!confirm('Loading this template will overwrite your current content. Proceed?')) {
                        return;
                    }
                }
                const html = templates[selected];
                quill.clipboard.dangerouslyPasteHTML(html);
            }
        });

        // Populate hidden input before submitting form
        document.getElementById('pageForm').onsubmit = function() {
            var content = document.querySelector('.ql-editor').innerHTML;
            document.getElementById('hidden_content').value = content;
        };
    </script>
</body>
</html>
