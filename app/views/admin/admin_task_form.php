<?php
$title = 'Open Task';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<div class="admin-main">
    <div class="admin-topbar">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-profile" id="adminProfileTrigger">
            <div class="admin-profile-box"><span><?= htmlspecialchars($this->displayadminname()) ?></span><i class="fa fa-user"></i></div>
            <div class="admin-dropdown" id="adminDropdown">
                <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
                <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>
    <main class="admin-page">
        <div class="admin-content">
            <div class="container-fluid">
                <a href="<?= BASE_URL ?>/admin/users" class="btn btn-sm btn-primary mb-3">Back</a>
                <div class="card">
                    <div class="card-header">
                        <strong>Create Task for <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['second_name'] ?? ''))) ?></strong>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= BASE_URL ?>/admin/users/open-task" enctype="multipart/form-data">
                            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3"><label>Task Name</label><input class="form-control" name="task_name" required></div>
                                <div class="col-md-6 mb-3"><label>Assign To Admin Team</label><select class="form-control" name="assigned_admin_id"><option value="">Select</option><?php foreach ($admins as $ad): ?><option value="<?= (int)$ad['id'] ?>"><?= htmlspecialchars($ad['name']) ?></option><?php endforeach; ?></select></div>
                                <div class="col-md-4 mb-3"><label>Status</label><select class="form-control" name="status"><option value="open">Open</option><option value="in_progress">In Progress</option><option value="done">Done</option></select></div>
                                <div class="col-md-4 mb-3"><label>Activity</label><input class="form-control" name="activity"></div>
                                <div class="col-md-4 mb-3"><label>Main Topic</label><input class="form-control" name="main_topic"></div>
                                <div class="col-md-6 mb-3"><label>Task Meeting</label><input class="form-control" name="task_meeting"></div>
                                <div class="col-md-3 mb-3"><label>Date From</label><input type="date" class="form-control" name="date_from"></div>
                                <div class="col-md-3 mb-3"><label>Date To</label><input type="date" class="form-control" name="date_to"></div>
                                <div class="col-md-4 mb-3"><label>Priority</label><select class="form-control" name="priority"><option value="">Select</option><option>Low</option><option>Medium</option><option>High</option><option>Critical</option></select></div>
                                <div class="col-md-8 mb-3"><label>Visible To (staff IDs comma separated)</label><input class="form-control" name="visible_to" placeholder="e.g. 2,4,6"></div>
                                <div class="col-12 mb-3"><label>Details</label><textarea class="form-control" name="details" rows="5"></textarea></div>
                                <div class="col-md-6 mb-3"><label>Image</label><input type="file" class="form-control" name="image" accept="image/*"></div>
                                <div class="col-md-6 mb-3"><label>Attachment</label><input type="file" class="form-control" name="attachment"></div>
                                <div class="col-12 mb-3"><label>Admin Comment</label><textarea class="form-control" name="admin_comment" rows="4"></textarea></div>
                            </div>
                            <button class="btn btn-success" type="submit">Create Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
