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

        .metric-cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .metric-card { flex: 1; background: var(--white); padding: 25px; border-radius: 12px; box-shadow: var(--card-shadow); text-align: center; min-width: 200px; transition: var(--transition); border: 1px solid rgba(0,0,0,0.02);}
        .metric-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .metric-card h3 { margin: 0; font-size: 2.2em; color: var(--primary); font-weight: 700; }
        .metric-card p { margin: 8px 0 0 0; color: var(--text-muted); font-size: 0.85em; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;}

        .table-responsive { overflow-x: auto; width: 100%; }

        @media (max-width: 768px) {
            .header { flex-direction: column; text-align: center; gap: 15px; }
            .header nav { justify-content: center; }
            .metric-cards { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; background: #fff; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <img src="<?= !empty($data['user']->avatar_url) ? URL_ROOT . Security::escape($data['user']->avatar_url) : 'https://via.placeholder.com/60' ?>" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                <div>
                    <h2 style="margin: 0;">Welcome, <?= Security::escape($data['profile']->clinic_name) ?></h2>
                    <p style="margin: 5px 0 0 0;">Public URL: <a href="<?= URL_ROOT ?>/clinic/view/<?= Security::escape($data['profile']->slug) ?>" target="_blank"><?= URL_ROOT ?>/clinic/view/<?= Security::escape($data['profile']->slug) ?></a></p>
                </div>
            </div>
            <nav style="display: flex; flex-wrap: wrap; gap: 5px; justify-content: flex-end; max-width: 500px;">
                <a href="<?= URL_ROOT ?>/appointment" class="btn" style="background:#17a2b8;">Appointments</a>
                <a href="<?= URL_ROOT ?>/clinicDashboard/schedule" class="btn btn-success">Manage Schedule</a>
                <a href="<?= URL_ROOT ?>/billing" class="btn" style="background:#6f42c1;">Billing & Upgrade</a>
                <a href="<?= URL_ROOT ?>/onboarding/clinic" class="btn btn-warning">Clinic Settings</a>
                <a href="<?= URL_ROOT ?>/profile" class="btn" style="background:#6c757d;">Account Settings</a>
                <a href="<?= URL_ROOT ?>/auth/logout" class="btn" style="background:#dc3545;">Logout</a>
            </nav>
        </div>

        <div class="metric-cards">
            <div class="metric-card">
                <h3><?= $data['stats']->total_appointments ?? 0 ?></h3>
                <p>Total Appointments</p>
            </div>
            <div class="metric-card">
                <h3 style="color: #ffc107;"><?= $data['stats']->pending_requests ?? 0 ?></h3>
                <p>Pending Requests</p>
            </div>
            <div class="metric-card">
                <h3 style="color: #28a745;"><?= $data['stats']->upcoming_appointments ?? 0 ?></h3>
                <p>Upcoming Appts</p>
            </div>
            <div class="metric-card">
                <h3 style="color: #f39c12;">⭐ <?= number_format($data['rating']->avg_rating ?? 0, 1) ?></h3>
                <p><?= $data['rating']->total_reviews ?? 0 ?> Reviews</p>
            </div>
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
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 15px;">
                <h3 style="margin: 0;">Your Pages</h3>
                <a href="<?= URL_ROOT ?>/clinicDashboard/createPage" class="btn btn-success">+ Add New Page</a>
            </div>

            <div class="table-responsive">
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
                                    <td style="min-width: 150px;">
                                        <a href="<?= URL_ROOT ?>/clinicDashboard/editPage/<?= $page->id ?>" class="btn btn-warning" style="padding: 5px 10px;">Edit</a>
                                        <form action="<?= URL_ROOT ?>/clinicDashboard/deletePage/<?= $page->id ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                            <button type="submit" class="btn btn-danger" style="padding: 5px 10px; background: #dc3545;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
