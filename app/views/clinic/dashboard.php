<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Dashboard - <?= SITE_NAME ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .card { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 10px 15px; background: #007bff; color: #fff; text-decoration: none; border: none; border-radius: 3px; cursor: pointer; }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #212529; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Welcome, <?= Security::escape($data['profile']->clinic_name) ?></h2>
                <p>Public URL: <a href="<?= URL_ROOT ?>/clinic/view/<?= Security::escape($data['profile']->slug) ?>" target="_blank"><?= URL_ROOT ?>/clinic/view/<?= Security::escape($data['profile']->slug) ?></a></p>
            </div>
            <nav>
                <a href="<?= URL_ROOT ?>/onboarding/clinic" class="btn btn-warning">Edit Profile Settings</a>
                <a href="<?= URL_ROOT ?>/auth/logout" class="btn">Logout</a>
            </nav>
        </div>

        <div class="card">
            <h3>Site Status</h3>
            <p>Your clinic mini-site is currently: <strong><?= $data['profile']->is_published ? 'Published' : 'Draft' ?></strong></p>
            <form action="<?= URL_ROOT ?>/clinicDashboard/togglePublish" method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <button type="submit" class="btn <?= $data['profile']->is_published ? 'btn-warning' : 'btn-success' ?>">
                    <?= $data['profile']->is_published ? 'Unpublish Site' : 'Publish Site' ?>
                </button>
            </form>

            <?php if ($data['profile']->is_published): ?>
                <div style="margin-top: 15px;">
                    <strong>Share:</strong>
                    <a href="https://api.whatsapp.com/send?text=Check%20out%20my%20clinic%20profile:%20<?= urlencode(URL_ROOT . '/clinic/view/' . $data['profile']->slug) ?>" target="_blank">WhatsApp</a> |
                    <a href="mailto:?subject=Visit%20my%20Clinic&body=Check%20out%20my%20clinic%20profile:%20<?= urlencode(URL_ROOT . '/clinic/view/' . $data['profile']->slug) ?>">Email</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3>Your Pages</h3>
                <a href="<?= URL_ROOT ?>/clinicDashboard/createPage" class="btn btn-success">+ Add New Page</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>URL Slug</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['pages'])): ?>
                        <tr><td colspan="5">No pages created yet. Create a homepage to get started!</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['pages'] as $page): ?>
                            <tr>
                                <td><?= Security::escape($page->title) ?></td>
                                <td>/<?= Security::escape($page->slug) ?></td>
                                <td><?= ucfirst(Security::escape($page->status)) ?></td>
                                <td><?= $page->is_home ? 'Homepage' : 'Inner Page' ?></td>
                                <td>
                                    <!-- Edit function would go here -->
                                    <a href="#">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
