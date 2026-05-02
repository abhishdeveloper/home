<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page - <?= SITE_NAME ?></title>
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
            <h2>Edit Page: <?= Security::escape($data['title']) ?></h2>
            <a href="<?= URL_ROOT ?>/clinicDashboard" style="color: #666;">Back to Dashboard</a>
        </div>

        <?php if (!empty($data['error'])): ?>
            <span class="error"><?= Security::escape($data['error']) ?></span>
        <?php endif; ?>

        <form action="<?= URL_ROOT ?>/clinicDashboard/editPage/<?= $data['id'] ?>" method="POST" id="pageForm">
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
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link', 'image'],
                    ['clean']
                ]
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
