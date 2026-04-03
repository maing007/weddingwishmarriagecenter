<?php
$title = "All Users";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<?php
$allCount = is_array($users ?? null) ? count($users) : 0;
$approvedCount = 0;
$unapprovedCount = 0;
$suspendedCount = 0;
foreach (($users ?? []) as $tmpUser) {
    $st = strtolower((string)($tmpUser['status'] ?? 'approved'));
    if ($st === 'approved') {
        $approvedCount++;
    } elseif ($st === 'unapproved') {
        $unapprovedCount++;
    } elseif ($st === 'suspended') {
        $suspendedCount++;
    }
}

$departmentOptions = [];
$teamLeaderOptions = [];
$customerSupportOptions = [];
$teamOptions = [];
foreach (($users ?? []) as $tmpUser) {
    $departmentOptions[] = trim((string)($tmpUser['religion'] ?? ''));
    $teamLeaderOptions[] = trim((string)($tmpUser['gender'] ?? ''));
    $customerSupportOptions[] = trim((string)($tmpUser['country'] ?? ''));
    $teamOptions[] = trim((string)($tmpUser['city'] ?? ''));
}
$departmentOptions = array_values(array_filter(array_unique($departmentOptions)));
$teamLeaderOptions = array_values(array_filter(array_unique($teamLeaderOptions)));
$customerSupportOptions = array_values(array_filter(array_unique($customerSupportOptions)));
$teamOptions = array_values(array_filter(array_unique($teamOptions)));
?>

<div class="admin-main">
<div class="admin-topbar">
    <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
        <i class="fa fa-bars"></i>
    </button>
    <div class="admin-profile" id="adminProfileTrigger">
        <div class="admin-profile-box">
            <span><?= htmlspecialchars($this->displayadminname()) ?></span>
            <i class="fa fa-user"></i>
        </div>
        <div class="admin-dropdown" id="adminDropdown">
            <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
            <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>
<main class="admin-page">
<div class="admin-content">
    <div class="container-fluid">
        <div class="page-head">ManageMember - ALL</div>

        <!-- TOP CONTROL PANEL -->
        <div class="top-controls">
            <div class="applied-filter-row">
                <span class="applied-filter-chip">
                    Showing: <?= htmlspecialchars($activeFilterLabel ?? 'All Members') ?>
                </span>
                <?php if (!empty($_GET['dashboard_filter'])): ?>
                    <a href="<?= BASE_URL ?>/admin/users" class="clear-filter-link">Clear Filter</a>
                <?php endif; ?>
            </div>
            <div class="controls-row controls-row-top users-top-row">
                <div class="users-search-wrap">
                    <div class="input-group">
                        <input type="text" id="userSearch" class="form-control" placeholder="Search here...">
                        <button class="btn btn-light border search-clear-btn" type="button" id="clearSearchBtn" aria-label="Clear search">
                            <i class="fa fa-times"></i>
                        </button>
                        <button class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            Search
                        </button>
                    </div>
                </div>
                <div class="users-actions-wrap text-end">
                    <a href="<?= BASE_URL?>/admin/add-user" class="btn btn-danger me-2">
                        <i class="bi bi-person-plus"></i> Add New
                    </a>
                    <button class="btn btn-info text-white" onclick="openFilterPopup()">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </div>

            <div class="controls-row controls-row-mid users-mid-row mt-2">
                <div class="users-select-wrap">
                    <div class="form-check d-inline-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="selectAllUsers">
                        <label class="form-check-label" for="selectAllUsers">Select All</label>
                    </div>
                </div>
                <div class="users-status-wrap text-end">
                    <div class="status-pill-row">
                        <button type="button" class="status-pill sp-approved" onclick="submitBulkStatus('approved')">Approved</button>
                        <button type="button" class="status-pill sp-unapproved" onclick="submitBulkStatus('unapproved')">Unapproved</button>
                        <button type="button" class="status-pill sp-suspended" onclick="submitBulkStatus('suspended')">Suspended</button>
                    </div>
                </div>
            </div>

            <div class="controls-row controls-row-bottom users-bottom-row mt-3">
                <div class="users-show-wrap">
                    <div class="show-entry-wrap">
                        <label class="me-2 mb-0">Show</label>
                        <select id="showEntries" class="form-select d-inline-block w-auto">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="9999">All</option>
                        </select>
                        <label class="ms-2 mb-0">Entries</label>
                    </div>
                </div>
                <div class="users-sort-wrap text-end">
                    <div class="sort-wrap">
                        <label class="me-2 mb-0">Sort</label>
                        <select id="sortUsers" class="form-select d-inline-block w-auto">
                        <option value="latest_desc">Latest Desc</option>
                        <option value="latest_asc">Latest Asc</option>
                        <option value="name_asc">Name A-Z</option>
                        <option value="name_desc">Name Z-A</option>
                        </select>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs custom-tabs mt-4">
                <li class="nav-item">
                    <a class="nav-link active tab-filter" data-tab="all">All <small>(<?= (int)$allCount ?>)</small></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-filter" data-tab="approved">Approved List <small>(<?= (int)$approvedCount ?>)</small></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-filter" data-tab="unapproved">Unapproved List <small>(<?= (int)$unapprovedCount ?>)</small></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-filter" data-tab="suspended">Suspended List <small>(<?= (int)$suspendedCount ?>)</small></a>
                </li>
            </ul>
        </div>

        <!-- USER LIST -->
        <div id="userList" class="mt-4">
            <?php foreach ($users as $u): ?>
            <div class="user-card searchable-card"
                 data-status="<?= strtolower($u['status'] ?? 'approved') ?>"
                 data-date="<?= strtotime($u['created_at']) ?>"
                 data-name="<?= strtolower($u['first_name'].' '.$u['last_name']) ?>"
                 data-department="<?= strtolower(trim((string)($u['religion'] ?? ''))) ?>"
                 data-team-leader="<?= strtolower(trim((string)($u['gender'] ?? ''))) ?>"
                 data-customer-support="<?= strtolower(trim((string)($u['country'] ?? ''))) ?>"
                 data-team="<?= strtolower(trim((string)($u['city'] ?? ''))) ?>">

                <!-- HEADER -->
                <div class="user-card-header">
                    <div class="user-left-title">
                        <input type="checkbox" class="user-checkbox" value="<?= (int)$u['id'] ?>">
                        <h5><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?> (NG<?= $u['id'] ?>)</h5>
                    </div>
                    <div class="approved-badge status-<?= strtolower($u['status'] ?? 'approved') ?>"><?= strtoupper($u['status'] ?? 'APPROVED') ?></div>
                </div>

                <!-- COUNTER ROW -->
                <div class="counter-row">
                    <a class="counter-box blue text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=opened">Opened (<?= (int)($u['opened_count'] ?? 0) ?>)</a>
                    <a class="counter-box yellow text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=deferred">Deferred (<?= (int)($u['deferred_count'] ?? 0) ?>)</a>
                    <a class="counter-box red text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=declined">Declined (<?= (int)($u['declined_count'] ?? 0) ?>)</a>
                    <a class="counter-box cyan text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=meeting">Meeting (<?= (int)($u['meeting_count'] ?? 0) ?>)</a>
                    <a class="counter-box green text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=accepted">Accepted (<?= (int)($u['accepted_count'] ?? 0) ?>)</a>
                </div>

                <!-- CONTENT -->
                <div class="user-main-content">
                    <div class="profile-image-box">
                        <img src="<?= BASE_URL . (!empty($u['avatar']) ? $u['avatar'] : '/assets/images/default-avatar.png') ?>" alt="">
                    </div>

                    <div class="details-column details-grid">
                        <p><strong>Gender</strong><span>:</span> <?= htmlspecialchars($u['gender'] ?? '-') ?></p>
                        <p><strong>Mobile</strong><span>:</span> <?= htmlspecialchars($u['phone'] ?? '-') ?></p>
                        <p><strong>Religion Name</strong><span>:</span> <?= htmlspecialchars($u['religion'] ?? '-') ?></p>
                        <p><strong>Marital Status</strong><span>:</span> <?= htmlspecialchars($u['marital_status'] ?? '-') ?></p>
                        <p><strong>Added By</strong><span>:</span> Admin</p>
                    </div>

                    <div class="details-column details-grid">
                        <p><strong>Email</strong><span>:</span> <?= htmlspecialchars($u['email']) ?></p>
                        <p><strong>Country Name</strong><span>:</span> <?= htmlspecialchars($u['country'] ?? 'Pakistan') ?></p>
                        <p><strong>City Name</strong><span>:</span> <?= htmlspecialchars($u['city'] ?? 'Lahore') ?></p>
                        <p><strong>Birthdate</strong><span>:</span> <?= htmlspecialchars($u['dob'] ?? '-') ?></p>
                        <p><strong>Registered On</strong><span>:</span> <?= htmlspecialchars($u['created_at']) ?></p>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="action-row">
                    <button type="button" class="btn-action dark" onclick="openCommentPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">Add Comment</button>
                    <button type="button" class="btn-action lightblue" onclick="openViewCommentsPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">View Comments</button>
                    <a class="btn-action blue" href="<?= BASE_URL ?>/admin/users/profile-view?id=<?= (int)$u['id'] ?>">View Profile</a>
                    <a class="btn-action lightblue" href="<?= BASE_URL ?>/admin/users/edit-steps?id=<?= (int)$u['id'] ?>">Edit Profile</a>
                    <a class="btn-action dark" target="_blank" href="<?= BASE_URL ?>/profile/<?= (int)$u['id'] ?>">Profile Link</a>
                    <a class="btn-action green" target="_blank" href="<?= BASE_URL ?>/admin/users/profile-pdf-template?id=<?= (int)$u['id'] ?>">Profile PDF</a>
                    <form method="post" action="<?= BASE_URL ?>/admin/users/send-email-confirmation">
                        <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                        <button class="btn-action blue" type="submit">Email Confirmation</button>
                    </form>
                    <a class="btn-action red" href="<?= BASE_URL ?>/admin/users/open-task?id=<?= (int)$u['id'] ?>">Open Task</a>
                </div>

            </div>

            <!-- HIDDEN PDF -->
            <div id="profile-pdf-<?= $u['id'] ?>" style="position:fixed;top:0;left:0;width:210mm;background:#fff;visibility:hidden;">
                <h2>User Profile</h2>
                <strong>Name:</strong> <?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($u['email']) ?><br>
                <strong>Phone:</strong> <?= htmlspecialchars($u['phone']) ?><br>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
    </div>
</div>
</main>

<!-- FILTER POPUP -->
<div id="filterPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup">

        <div class="popup-header">
            <h3>Filter Data</h3>
            <span class="close-popup" onclick="closeFilterPopup()">&times;</span>
        </div>

        <form id="advancedFilterForm" onsubmit="applyAdvancedFilters(event)">
        <div class="popup-body">
            <div class="form-group">
                <label>Department Filter</label>
                <select class="form-control" id="departmentFilter">
                    <option value="">All Departments</option>
                    <?php foreach ($departmentOptions as $opt): ?>
                        <option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Team Leader Filter</label>
                <select class="form-control" id="teamLeaderFilter">
                    <option value="">All Team Leaders</option>
                    <?php foreach ($teamLeaderOptions as $opt): ?>
                        <option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Customer Support</label>
                <select class="form-control" id="customerSupportFilter">
                    <option value="">All Customer Support</option>
                    <?php foreach ($customerSupportOptions as $opt): ?>
                        <option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Team Filter</label>
                <select class="form-control" id="teamFilter">
                    <option value="">All Teams</option>
                    <?php foreach ($teamOptions as $opt): ?>
                        <option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="popup-footer">
            <button type="submit" class="btn-submit">Submit</button>
            <button type="button" class="btn-cancel" onclick="clearAdvancedFilters()">Reset</button>
            <button type="button" class="btn-cancel" onclick="closeFilterPopup()">Close</button>
        </div>
        </form>
    </div>
</div>

<!-- ADD COMMENT POPUP -->
<div id="commentPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-lg">
        <div class="popup-header">
            <h3 id="commentPopupTitle">Add Comment</h3>
            <span class="close-popup" onclick="closeCommentPopup()">&times;</span>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/admin/users/comment">
            <div class="popup-body">
                <input type="hidden" name="user_id" id="comment_user_id">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" name="comment_type">
                        <option value="general">General</option>
                        <option value="follow_up">Follow Up</option>
                        <option value="warning">Warning</option>
                        <option value="approval_note">Approval Note</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" name="comment" rows="6" placeholder="Write comment..." required></textarea>
                </div>
            </div>
            <div class="popup-footer">
                <button type="submit" class="btn-submit">Save Comment</button>
                <button type="button" class="btn-cancel" onclick="closeCommentPopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW COMMENTS POPUP -->
<div id="viewCommentsPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-xl">
        <div class="popup-header">
            <h3 id="viewCommentPopupTitle">View Comments</h3>
            <span class="close-popup" onclick="closeViewCommentsPopup()">&times;</span>
        </div>
        <div class="popup-body">
            <div class="row g-2 mb-3">
                <input type="hidden" id="view_comment_user_id">
                <div class="col-md-4">
                    <label>Type</label>
                    <select id="filter_comment_type" class="form-control">
                        <option value="">All</option>
                        <option value="general">General</option>
                        <option value="follow_up">Follow Up</option>
                        <option value="warning">Warning</option>
                        <option value="approval_note">Approval Note</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>From</label>
                    <input type="date" id="filter_comment_from" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>To</label>
                    <input type="date" id="filter_comment_to" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="loadProfileComments()">Apply Filter</button>
            <div id="commentsResults" style="max-height:380px;overflow:auto;"></div>
        </div>
        <div class="popup-footer">
            <button type="button" class="btn-cancel" onclick="closeViewCommentsPopup()">Close</button>
        </div>
    </div>
</div>
<!-- STYLES -->
<style>
    .filter-btn{display: none !important;}
    .admin-content{padding:14px;background:#efefef;}
    .page-head{font-size:13px;font-weight:700;color:#535353;margin-bottom:8px;}
    .top-controls{background:#f8f8f8;padding:14px 14px 16px;border:1px solid #d7d7d7;border-radius:3px;box-shadow:0 1px 4px rgba(0,0,0,.05);}
    .controls-row .btn{font-size:12px;padding:6px 14px;border-radius:3px;line-height:1.2;}
    .controls-row .btn-danger{background:#ef4c5a;border-color:#ef4c5a;}
    .controls-row .btn-info{background:#44c0df;border-color:#44c0df;}
    .controls-row .btn-primary{background:#0e98d3;border-color:#0e98d3;}
    .controls-row .input-group{display:flex;flex-wrap:nowrap;width:100%;}
    .controls-row .input-group .form-control{height:34px;font-size:12px;border-color:#d8d8d8;min-width:0;flex:1 1 auto;}
    .controls-row .input-group .btn{height:34px;}
    .controls-row-top{margin-bottom:10px;}
    .controls-row-mid{margin-top:2px;margin-bottom:12px;}
    .controls-row-bottom{margin-top:4px;margin-bottom:10px;}
    .users-top-row{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:nowrap;}
    .users-search-wrap{flex:0 1 560px;max-width:560px;min-width:360px;}
    .users-actions-wrap{flex:0 0 auto;white-space:nowrap;}
    .users-mid-row{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:nowrap;}
    .users-select-wrap{flex:0 0 auto;}
    .users-status-wrap{flex:0 0 auto;white-space:nowrap;}
    .users-bottom-row{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:nowrap;}
    .users-show-wrap{flex:0 0 auto;}
    .users-sort-wrap{flex:0 0 auto;white-space:nowrap;}
    .search-clear-btn{padding:6px 10px;background:#fff;color:#777;}
    .show-entry-wrap,.sort-wrap{display:inline-flex;align-items:center;}
    .status-pill-row{display:inline-flex;gap:8px;align-items:center;flex-wrap:wrap;}
    .status-pill{display:inline-block;padding:5px 12px;border-radius:3px;font-size:11px;font-weight:700;color:#fff;line-height:1;border:0;cursor:pointer;}
    .sp-approved{background:#32c766;}
    .sp-unapproved{background:#f0bc45;color:#6f5100;}
    .sp-suspended{background:#ed4e58;}
    .applied-filter-row{display:flex;align-items:center;gap:12px;margin-bottom:10px;flex-wrap:wrap;}
    .applied-filter-chip{background:#eef6ff;color:#2a6cab;border:1px solid #cfe3fb;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;}
    .clear-filter-link{font-size:11px;color:#d9534f;text-decoration:none;}
    .clear-filter-link:hover{text-decoration:underline;}
    .custom-tabs{border-bottom:1px solid #d7d7d7;padding-top:6px;gap:8px;display:flex;flex-wrap:wrap;margin-bottom:0;}
    .custom-tabs .nav-link{background:#e9e9e9;border:1px solid #d9d9d9;border-bottom:0;border-radius:3px 3px 0 0;color:#333;font-size:11px;font-weight:700;padding:8px 14px;min-width:112px;text-align:center;}
    .custom-tabs .nav-link small{display:block;font-size:10px;font-weight:600;color:#666;}
    .custom-tabs .nav-link.active{background:#56c8ed;color:#fff;border-color:#48bde4;}
    .custom-tabs .nav-link.active small{color:#fff;}
    .user-card{background:#f3f3f3;border:1px solid #d9d9d9;border-radius:2px;padding:10px;margin-bottom:14px;}
    .user-card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;border-bottom:1px solid #e1e1e1;padding-bottom:6px;}
    .user-left-title h5{margin:0;font-size:30px; font-size:16px;font-weight:500;color:#4e4e4e;}
    .approved-badge{font-size:12px;font-weight:700;padding:5px 14px;border-radius:3px;color:#fff;}
    .status-approved{background:#36c66a;}
    .status-unapproved{background:#efc145;color:#6b4f00;}
    .status-suspended{background:#ef4c5a;}
    .counter-row{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;}
    .counter-box{flex:1;min-width:120px;padding:5px 8px;color:white;text-align:center;border-radius:0;font-size:11px;}
    .blue{background:#1399c8} .yellow{background:#efc145;color:#fff} .red{background:#d96958} .cyan{background:#45bdd2} .green{background:#2fc66f}
    .user-main-content{display:flex;gap:12px;}
    .profile-image-box img{width:120px;height:120px;object-fit:cover;border-radius:0;border:1px solid #ddd;}
    .details-column{flex:1;min-width:230px;}
    .details-grid p{display:grid;grid-template-columns:130px 12px 1fr;margin:0 0 5px;font-size:11px;color:#565656;}
    .details-grid p strong{font-weight:600;}
    .action-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;justify-content:flex-end;}
    .btn-action{padding:6px 12px;border:none;color:white;border-radius:2px;text-decoration:none;font-size:11px;min-width:110px;text-align:center;}
    .lightblue{background:#54c3da} .dark{background:#34495e}

.custom-popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background: rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;z-index:1050;}
.custom-popup{background:#fff;padding:20px;border-radius:8px;width:400px;max-width:90%;box-shadow:0 4px 15px rgba(0,0,0,0.2);}
.popup-header{display:flex;justify-content: space-between;align-items:center;margin-bottom:15px;}
.popup-header h3{margin:0;}
.close-popup{cursor:pointer;font-size:24px;}
.popup-body .form-group{margin-bottom:15px;}
.popup-footer{display:flex;justify-content:flex-end;gap:10px;}
.custom-popup-overlay{
    position: fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.5);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:1050;
}

.custom-popup{
    background:#fff;
    padding:20px;
    border-radius:8px;
    width:400px;
    max-width:90%;
}
.custom-popup-lg{width:620px;}
.custom-popup-xl{width:900px;}
.comment-item{border:1px solid #e4e4e4;background:#fbfbfb;padding:10px;border-radius:4px;margin-bottom:10px;}
.comment-meta{font-size:11px;color:#666;margin-bottom:6px;}
@media(max-width:768px){
    .controls-row .btn{margin-top:6px;}
    .status-pill-row{justify-content:flex-start;width:100%;margin-top:4px;}
    .show-entry-wrap,.sort-wrap{width:100%;justify-content:flex-start;}
    .users-top-row,.users-mid-row,.users-bottom-row{flex-wrap:wrap;}
    .users-search-wrap{flex:1 1 100%;max-width:100%;min-width:0;}
    .users-actions-wrap,.users-status-wrap,.users-sort-wrap{width:100%;text-align:left;}
    .controls-row-top,.controls-row-mid,.controls-row-bottom{margin-bottom:8px;}
    .custom-tabs .nav-link{flex:1 1 calc(50% - 8px);}
    .user-main-content{flex-direction:column;}
    .profile-image-box img{width:100%;height:auto;}
    .action-row{justify-content:flex-start;}
}
</style>
<!-- SCRIPTS -->
<script>
const searchInput = document.getElementById("userSearch");
const sortSelect = document.getElementById("sortUsers");
const showEntries = document.getElementById("showEntries");
let activeTab = "all";
// SELECT ALL FUNCTION
document.getElementById("selectAllUsers").addEventListener("change", function () {
    let checked = this.checked;
    document.querySelectorAll(".user-checkbox").forEach(cb => {
        cb.checked = checked;
    });
});
let cards = Array.from(document.querySelectorAll(".searchable-card"));
const advancedFilters = {
    department: "",
    teamLeader: "",
    customerSupport: "",
    team: ""
};

function updateList(){
    let search = searchInput.value.toLowerCase();
    let limit = parseInt(showEntries.value);
    let filtered = cards.filter(card=>{
        let text = card.innerText.toLowerCase();
        let status = card.dataset.status;
        if(!text.includes(search)) return false;
        if(activeTab!="all" && status!=activeTab) return false;
        if (advancedFilters.department && card.dataset.department !== advancedFilters.department) return false;
        if (advancedFilters.teamLeader && card.dataset.teamLeader !== advancedFilters.teamLeader) return false;
        if (advancedFilters.customerSupport && card.dataset.customerSupport !== advancedFilters.customerSupport) return false;
        if (advancedFilters.team && card.dataset.team !== advancedFilters.team) return false;
        return true;
    });

    let sort = sortSelect.value;
    filtered.sort((a,b)=>{
        if(sort=="latest_desc") return b.dataset.date - a.dataset.date;
        if(sort=="latest_asc") return a.dataset.date - b.dataset.date;
        if(sort=="name_asc") return a.dataset.name.localeCompare(b.dataset.name);
        if(sort=="name_desc") return b.dataset.name.localeCompare(a.dataset.name);
    });

    cards.forEach(c=>c.style.display="none");
    filtered.slice(0,limit).forEach(c=>c.style.display="block");
}

searchInput.addEventListener("keyup",updateList);
sortSelect.addEventListener("change",updateList);
showEntries.addEventListener("change",updateList);

document.getElementById("clearSearchBtn").addEventListener("click", function(){
    searchInput.value = "";
    updateList();
});

document.querySelectorAll(".tab-filter").forEach(tab=>{
    tab.addEventListener("click",function(){
        document.querySelectorAll(".tab-filter").forEach(t=>t.classList.remove("active"));
        this.classList.add("active");
        activeTab = this.dataset.tab;
        updateList();
    });
});

updateList();

function downloadProfilePDF(userId){
    const element=document.getElementById("profile-pdf-"+userId);
    html2pdf().from(element).set({margin:0.5,filename:"User-"+userId+".pdf"}).save();
}

function openCommentPopup(userId, userName){
    document.getElementById("comment_user_id").value = userId;
    document.getElementById("commentPopupTitle").innerText = "Add Comment - " + userName;
    document.getElementById("commentPopup").style.display = "flex";
}
function closeCommentPopup(){
    document.getElementById("commentPopup").style.display = "none";
}
function openViewCommentsPopup(userId, userName){
    document.getElementById("view_comment_user_id").value = userId;
    document.getElementById("viewCommentPopupTitle").innerText = "View Comments - " + userName;
    document.getElementById("viewCommentsPopup").style.display = "flex";
    loadProfileComments();
}
function closeViewCommentsPopup(){
    document.getElementById("viewCommentsPopup").style.display = "none";
}
function escapeHtml(str){
    return (str || '').replace(/[&<>"']/g, function(m){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]);
    });
}
function loadProfileComments(){
    const userId = document.getElementById('view_comment_user_id').value;
    const type = document.getElementById('filter_comment_type').value;
    const from = document.getElementById('filter_comment_from').value;
    const to = document.getElementById('filter_comment_to').value;
    const url = `<?= BASE_URL ?>/admin/users/comments-json?user_id=${encodeURIComponent(userId)}&type=${encodeURIComponent(type)}&date_from=${encodeURIComponent(from)}&date_to=${encodeURIComponent(to)}`;
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const wrap = document.getElementById('commentsResults');
            if (!data.ok || !data.rows || data.rows.length === 0) {
                wrap.innerHTML = '<div class="alert alert-warning mb-0">No comments found.</div>';
                return;
            }
            wrap.innerHTML = data.rows.map(function(row){
                return `<div class="comment-item">
                    <div class="comment-meta"><strong>${escapeHtml(row.admin_name || 'Admin')}</strong> | ${escapeHtml(row.comment_type || 'general')} | ${escapeHtml(row.created_at || '')}</div>
                    <div>${escapeHtml(row.comment || '')}</div>
                </div>`;
            }).join('');
        })
        .catch(() => {
            document.getElementById('commentsResults').innerHTML = '<div class="alert alert-danger mb-0">Unable to load comments.</div>';
        });
}
// OPEN FILTER POPUP
function openFilterPopup(){
    document.getElementById("filterPopup").style.display = "flex";
}

// CLOSE FILTER POPUP
function closeFilterPopup(){
    document.getElementById("filterPopup").style.display = "none";
}
function applyAdvancedFilters(e){
    if (e) e.preventDefault();
    advancedFilters.department = (document.getElementById("departmentFilter").value || "").toLowerCase();
    advancedFilters.teamLeader = (document.getElementById("teamLeaderFilter").value || "").toLowerCase();
    advancedFilters.customerSupport = (document.getElementById("customerSupportFilter").value || "").toLowerCase();
    advancedFilters.team = (document.getElementById("teamFilter").value || "").toLowerCase();
    closeFilterPopup();
    updateList();
}
function clearAdvancedFilters(){
    document.getElementById("advancedFilterForm").reset();
    advancedFilters.department = "";
    advancedFilters.teamLeader = "";
    advancedFilters.customerSupport = "";
    advancedFilters.team = "";
    updateList();
}

function submitBulkStatus(statusValue){
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(function(cb){
        return cb.value;
    });

    if (selectedUsers.length === 0) {
        alert('Please select at least one user.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASE_URL ?>/admin/users/bulk-status';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = 'csrf_token';
    csrf.value = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>';
    form.appendChild(csrf);

    const bulkStatus = document.createElement('input');
    bulkStatus.type = 'hidden';
    bulkStatus.name = 'bulk_status';
    bulkStatus.value = statusValue;
    form.appendChild(bulkStatus);

    selectedUsers.forEach(function(id){
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_users[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

window.addEventListener("click", function(e){
    let popup = document.getElementById("filterPopup");
    if(e.target === popup){
        popup.style.display = "none";
    }
});
</script>

<?php require __DIR__.'/partials/footer.php'; ?>
