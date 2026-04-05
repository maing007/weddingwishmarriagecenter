<?php
$title = "All Users";
$pageHead = 'ManageMember - ALL';
if (!empty($activeFilterLabel) && $activeFilterLabel !== 'All Members') {
    $pageHead = 'ManageMember - ' . $activeFilterLabel;
}
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
require_once __DIR__ . '/../../helpers/admin_member_display.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">
<style>
    .income-fee-page-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        margin-left: 8px;
        margin-right: auto;
        letter-spacing: 0.01em;
    }
    .admin-topbar.income-fee-topbar {
        justify-content: flex-start;
        padding-left: 12px;
        padding-right: 16px;
    }
    .income-fee-topbar .admin-profile {
        margin-left: auto;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<?php
$allCount = is_array($users ?? null) ? count($users) : 0;
$approvedCount = 0;
$unapprovedCount = 0;
$suspendedCount = 0;
$regQueueCount = 0;
foreach (($users ?? []) as $tmpUser) {
    $st = strtolower((string)($tmpUser['status'] ?? 'approved'));
    $queued = (int) ($tmpUser['registration_fee_queued'] ?? 0);
    if ($queued === 1) {
        $regQueueCount++;
    }
    if ($st === 'approved') {
        $approvedCount++;
    } elseif ($st === 'unapproved') {
        if ($queued !== 1) {
            $unapprovedCount++;
        }
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
<div class="admin-topbar income-fee-topbar">
    <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu">
        <i class="fa fa-bars"></i>
    </button>
    <div class="income-fee-page-title"><?= htmlspecialchars($pageHead, ENT_QUOTES, 'UTF-8') ?></div>
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
<main class="admin-page manage-users-page">
<div class="admin-content manage-users-content">
    <div class="container-fluid">
        <div class="page-head"><?= htmlspecialchars($pageHead, ENT_QUOTES, 'UTF-8') ?></div>

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
                        <button type="button" class="status-pill sp-approved" title="Queues member on Registration Fee page for plan assignment" onclick="submitBulkStatus('approved')">Approved</button>
                        <button type="button" class="status-pill sp-unapproved" onclick="submitBulkStatus('unapproved')">Unapproved</button>
                        <button type="button" class="status-pill sp-suspended" onclick="submitBulkStatus('suspended')"><i class="fa fa-user-times"></i> Suspended</button>
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
                    <a class="nav-link tab-filter" data-tab="reg_queue">Reg. fee queue <small>(<?= (int)$regQueueCount ?>)</small></a>
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
                 data-registration-queued="<?= (int)($u['registration_fee_queued'] ?? 0) ?>"
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
                        <h5><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?> (<?= htmlspecialchars(matri_id_display((string) ($u['matri_id'] ?? ''), (int) $u['id'])) ?>)</h5>
                    </div>
                    <div class="approved-badge status-<?= strtolower($u['status'] ?? 'approved') ?>">
                        <?php if (strtolower((string)($u['status'] ?? '')) === 'approved'): ?>
                            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                        <?php elseif (strtolower((string)($u['status'] ?? '')) === 'suspended'): ?>
                            <i class="fa fa-user-times" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?= strtoupper(htmlspecialchars((string)($u['status'] ?? 'approved'))) ?>
                    </div>
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
                    <?php $cardUser = $u;
                    require __DIR__ . '/partials/member_card_photo_block.php'; ?>

                    <div class="details-column details-grid">
                        <p><strong>Gender</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['gender'] ?? '')) ?></p>
                        <p><strong>Mobile</strong><span>:</span> <?= htmlspecialchars(admin_member_mobile_display($u)) ?></p>
                        <p><strong>Religion Name</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['religion'] ?? '')) ?></p>
                        <p><strong>Caste Name</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['caste'] ?? '')) ?></p>
                        <p><strong>Mother Tongue</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['mother_tongue'] ?? '')) ?></p>
                        <p><strong>Marital Status</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['marital_status'] ?? '')) ?></p>
                        <p><strong>Plan Name</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['active_plan_name'] ?? '')) ?></p>
                        <p><strong>Plan Expired On</strong><span>:</span> <?= htmlspecialchars(admin_member_date_display($u['plan_expires_at'] ?? null)) ?></p>
                        <p><strong>Added By</strong><span>:</span> <?= htmlspecialchars(admin_member_added_by_display($u)) ?></p>
                        <p><strong>Uuid</strong><span>:</span> <?= htmlspecialchars(admin_member_uuid_display((int) $u['id'])) ?></p>
                    </div>

                    <div class="details-column details-grid">
                        <p><strong>Email</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['email'] ?? '')) ?></p>
                        <p><strong>Country Name</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['country'] ?? '')) ?></p>
                        <p><strong>State Name</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['state'] ?? '')) ?></p>
                        <p><strong>City Name</strong><span>:</span> <?= htmlspecialchars(admin_member_na($u['city'] ?? '')) ?></p>
                        <p><strong>Birthdate</strong><span>:</span> <?= htmlspecialchars(admin_member_birth_display($u['dob'] ?? null)) ?></p>
                        <p><strong>Registered On</strong><span>:</span> <?= htmlspecialchars(admin_member_datetime_display($u['created_at'] ?? null)) ?></p>
                        <p><strong>Last Login</strong><span>:</span> <?= htmlspecialchars(admin_member_datetime_display($u['last_login'] ?? null)) ?></p>
                        <p><strong>Partner Contact Pdf</strong><span>:</span> <?= htmlspecialchars(admin_member_partner_pdf_display($u)) ?></p>
                        <p><strong>Final Rishta Fee</strong><span>:</span> <?= htmlspecialchars(admin_member_final_fee_display($u['final_fee'] ?? null)) ?></p>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="action-row">
                    <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/open-task?id=<?= (int)$u['id'] ?>">Open Task</a>
                    <button type="button" class="btn-action btn-action-cyan" onclick="openDynamicTeamPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">
                        View Team</button>
                    <button type="button" class="btn-action btn-action-teal" onclick="openCommentPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">Add Comment</button>
                    <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/profile-view?id=<?= (int)$u['id'] ?>">View Profile</a>
                    <button type="button" class="btn-action btn-action-amber" onclick="openViewCommentsPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">View Comments</button>
                    <a class="btn-action btn-action-cyan" href="<?= BASE_URL ?>/admin/users/edit-steps?id=<?= (int)$u['id'] ?>">Edit Profile</a>
                    <a class="btn-action btn-action-cyan" target="_blank" href="<?= BASE_URL ?>/profile/<?= (int)$u['id'] ?>">Profile Link</a>
                    <a class="btn-action btn-action-green" target="_blank" href="<?= BASE_URL ?>/admin/users/profile-pdf-template?id=<?= (int)$u['id'] ?>">Profile PDF</a>
                    <form method="post" action="<?= BASE_URL ?>/admin/users/send-email-confirmation" class="btn-action-form">
                        <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                        <button class="btn-action btn-action-teal" type="submit">Email Confirmation</button>
                    </form>
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

<!-- DYNAMIC ASSIGN TEAM POPUP -->
<div id="dynamicTeamPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-lg">
        <div class="popup-header">
            <h3 id="dynamicTeamPopupTitle">Dynamic assign team</h3>
            <span class="close-popup" onclick="closeDynamicTeamPopup()">&times;</span>
        </div>
        <div class="popup-body">
            <div id="dynamicTeamMeta" class="mb-3 small text-muted"></div>
            <div id="dynamicTeamResults"></div>
        </div>
        <div class="popup-footer">
            <button type="button" class="btn-cancel" onclick="closeDynamicTeamPopup()">Close</button>
        </div>
    </div>
</div>
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
        if (activeTab === "reg_queue") {
            if (parseInt(card.dataset.registrationQueued || "0", 10) !== 1) return false;
        } else if (activeTab === "unapproved") {
            if (status !== "unapproved" || parseInt(card.dataset.registrationQueued || "0", 10) === 1) return false;
        } else if (activeTab !== "all" && status !== activeTab) return false;
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
function openDynamicTeamPopup(userId, userName){
    document.getElementById("dynamicTeamPopupTitle").innerText = "Dynamic assign team — " + userName;
    document.getElementById("dynamicTeamMeta").innerHTML = "";
    document.getElementById("dynamicTeamResults").innerHTML = '<div class="text-muted">Loading…</div>';
    document.getElementById("dynamicTeamPopup").style.display = "flex";
    const url = `<?= BASE_URL ?>/admin/users/member-dynamic-team-json?user_id=${encodeURIComponent(userId)}`;
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const meta = document.getElementById("dynamicTeamMeta");
            const wrap = document.getElementById("dynamicTeamResults");
            if (!data.ok) {
                wrap.innerHTML = '<div class="alert alert-warning mb-0">Unable to load team.</div>';
                return;
            }
            let metaParts = [];
            if (data.team_name) {
                metaParts.push("<strong>Team</strong>: " + escapeHtml(data.team_name));
            }
            if (data.primary_lead_name) {
                metaParts.push("<strong>Assigned lead</strong>: " + escapeHtml(data.primary_lead_name));
            }
            meta.innerHTML = metaParts.length ? metaParts.join(" &nbsp;|&nbsp; ") : "<span>Staff linked to tasks, matches, or team grouping from this profile.</span>";
            if (!data.rows || data.rows.length === 0) {
                wrap.innerHTML = '<div class="alert alert-warning mb-0">No assigned team staff found. Set <strong>lead</strong> on the member profile or assign tasks / matches.</div>';
                return;
            }
            const rowsHtml = data.rows.map(function(row){
                const dept = escapeHtml(row.department || "—");
                let desig = escapeHtml(row.designation || "Staff");
                if (row.is_primary) {
                    desig += ' <span class="badge bg-success ms-1">Primary lead</span>';
                }
                const name = escapeHtml(row.name || "");
                const contact = row.contact ? "(" + escapeHtml(row.contact) + ")" : "";
                const off = row.official ? '<span class="badge bg-success ms-1">Official</span>' : "";
                return `<tr><td>${dept}</td><td>${desig}</td><td>${name} ${contact} ${off}</td></tr>`;
            }).join("");
            wrap.innerHTML = `<div class="table-responsive"><table class="table table-sm table-striped align-middle mb-0" style="background:#fff;border:1px solid #e9ecef;border-radius:6px;">
                <thead><tr><th>Department</th><th>Designation</th><th>Name</th></tr></thead>
                <tbody>${rowsHtml}</tbody></table></div>`;
        })
        .catch(function(){
            document.getElementById("dynamicTeamResults").innerHTML = '<div class="alert alert-danger mb-0">Unable to load team.</div>';
        });
}
function closeDynamicTeamPopup(){
    document.getElementById("dynamicTeamPopup").style.display = "none";
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
    let teamPop = document.getElementById("dynamicTeamPopup");
    if (teamPop && e.target === teamPop) {
        teamPop.style.display = "none";
    }
});
</script>

<?php require __DIR__.'/partials/footer.php'; ?>
