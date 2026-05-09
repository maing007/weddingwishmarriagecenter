<?php
$title = "Team Management";
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$allCount = is_array($admins ?? null) ? count($admins) : 0;
$approvedCount = 0;
$unapprovedCount = 0;
foreach (($admins ?? []) as $tmpAdmin) {
    $st = strtolower((string)($tmpAdmin['status'] ?? ''));
    if ($st === 'approved') {
        $approvedCount++;
    } elseif ($st === 'unapproved') {
        $unapprovedCount++;
    }
}
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
                <div class="page-head">ManageTeam - ALL</div>

                <div class="top-controls">
                    <form method="post" id="teamSearchForm">
                        <div class="controls-row controls-row-top team-top-row">
                            <div class="team-search-wrap">
                                <div class="input-group">
                                    <input type="text" name="search_filed" id="teamSearch" class="form-control" placeholder="Search here..." value="<?= htmlspecialchars($search ?? '') ?>">
                                    <button class="btn btn-light border search-clear-btn" type="button" id="clearSearchBtn" aria-label="Clear search">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="team-actions-wrap text-end">
                                <button class="btn btn-info text-white" type="button" onclick="openFilterPopup()"><i class="bi bi-funnel"></i> Filter</button>
                            </div>
                        </div>

                        <div class="controls-row controls-row-mid team-mid-row mt-2">
                            <div class="team-select-wrap">
                                <div class="form-check d-inline-flex align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All</label>
                                </div>
                            </div>
                            <div class="team-bulk-wrap text-end">
                                <select name="action" class="form-select d-inline-block w-auto me-2">
                                    <option value="approve">Approve</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm" form="bulkActionForm">Apply</button>
                            </div>
                        </div>

                        <div class="controls-row controls-row-bottom team-bottom-row mt-3">
                            <div class="team-show-wrap">
                                <div class="show-entry-wrap">
                                    <label class="me-2 mb-0">Show</label>
                                    <select name="limit_per_page" id="limitPerPage" class="form-select d-inline-block w-auto" onchange="document.getElementById('teamSearchForm').submit()">
                                        <?php foreach ([10, 25, 50, 100] as $val): ?>
                                            <option value="<?= $val ?>" <?= ((int)$limit === (int)$val) ? 'selected' : '' ?>><?= $val ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label class="ms-2 mb-0">Entries</label>
                                </div>
                            </div>
                            <div class="team-sort-wrap text-end">
                                <div class="sort-wrap">
                                    <label class="me-2 mb-0">Sort</label>
                                    <select name="sort_order_js" id="sortOrder" class="form-select d-inline-block w-auto" onchange="document.getElementById('teamSearchForm').submit()">
                                        <option value="created_at-desc" <?= ($sort ?? '') === 'created_at-desc' ? 'selected' : '' ?>>Status Descending</option>
                                        <option value="created_at-asc" <?= ($sort ?? '') === 'created_at-asc' ? 'selected' : '' ?>>Status Ascending</option>
                                        <option value="name-asc" <?= ($sort ?? '') === 'name-asc' ? 'selected' : '' ?>>Name A-Z</option>
                                        <option value="name-desc" <?= ($sort ?? '') === 'name-desc' ? 'selected' : '' ?>>Name Z-A</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <ul class="nav nav-tabs custom-tabs mt-3">
                        <li class="nav-item"><a class="nav-link <?= empty($status) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/team-management">All <small>(<?= (int)$allCount ?>)</small></a></li>
                        <li class="nav-item"><a class="nav-link <?= ($status === 'approved') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/team-management?status=approved">Approved List <small>(<?= (int)$approvedCount ?>)</small></a></li>
                        <li class="nav-item"><a class="nav-link <?= ($status === 'unapproved') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/team-management?status=unapproved">Unapproved List <small>(<?= (int)$unapprovedCount ?>)</small></a></li>
                    </ul>
                </div>

                <form action="<?= BASE_URL ?>/admin/team-management/bulk-action" method="post" id="bulkActionForm">
                    <?php if (!empty($admins)): ?>
                        <?php foreach ($admins as $admin): ?>
                            <?php $adminStatus = strtolower((string)($admin['status'] ?? 'unapproved')); ?>
                            <div class="team-card">
                                <div class="team-card-head">
                                    <div class="team-title-wrap">
                                        <input type="checkbox" class="rowCheckbox" name="ids[]" value="<?= (int)$admin['id'] ?>">
                                        <h5><?= htmlspecialchars($admin['name'] ?? 'N/A') ?></h5>
                                    </div>
                                    <div class="team-status <?= $adminStatus === 'approved' ? 'approved' : 'unapproved' ?>">
                                        <?= strtoupper($adminStatus) ?>
                                    </div>
                                </div>

                                <div class="team-card-body">
                                    <div class="team-col">
                                        <h6>Team Details</h6>
                                        <p><strong>Team Leader</strong><span>:</span> <?= htmlspecialchars($admin['team_leader'] ?? 'N/A') ?></p>
                                        <p><strong>Department</strong><span>:</span> <?= htmlspecialchars($admin['department'] ?? 'N/A') ?></p>
                                        <p><strong>Created On</strong><span>:</span> <?= htmlspecialchars($admin['created_at'] ?? 'N/A') ?></p>
                                        <p><strong>Email</strong><span>:</span> <?= htmlspecialchars($admin['email'] ?? 'N/A') ?></p>
                                        <p><strong>Password</strong><span>:</span> ********</p>
                                        <p><strong>Referral</strong><span>:</span> N/A</p>
                                    </div>

                                    <div class="team-col">
                                        <h6>KPI'S</h6>
                                        <div class="kpi-table">
                                            <div class="kpi-head">
                                                <span></span><span>Total</span><span>Current</span>
                                            </div>
                                            <div><span>Total Members</span><span>N/A</span><span><?= (int)($admin['total_members'] ?? 0) ?></span></div>
                                            <div><span>Matches Opened</span><span>N/A</span><span><?= (int)($admin['matches_opened'] ?? 0) ?></span></div>
                                            <div><span>Matches Declined</span><span>N/A</span><span><?= (int)($admin['matches_declined'] ?? 0) ?></span></div>
                                            <div><span>Matches Accepted</span><span>N/A</span><span><?= (int)($admin['matches_accepted'] ?? 0) ?></span></div>
                                            <div><span>Attendance</span><span>N/A</span><span><?= (int)($admin['attendance'] ?? 0) ?></span></div>
                                        </div>
                                        <div class="team-actions text-end">
                                            <button type="button" class="btn btn-attendance btn-sm" onclick="openAttendanceModal(<?= (int)$admin['id'] ?>)">Attendance</button>
                                            <button type="button" class="btn btn-member btn-sm">Member</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">No records found</div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </main>
</div>

<div id="filterPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup">
        <div class="popup-header">
            <h3>Filter Data</h3>
            <span class="close-popup" onclick="closeFilterPopup()">&times;</span>
        </div>
        <div class="popup-body">
            <div class="form-group">
                <label>Status</label>
                <select id="quickStatusFilter" class="form-control">
                    <option value="">All</option>
                    <option value="approved">Approved</option>
                    <option value="unapproved">Unapproved</option>
                </select>
            </div>
        </div>
        <div class="popup-footer">
            <button type="button" class="btn-submit" onclick="applyStatusFilter()">Apply</button>
            <button type="button" class="btn-cancel" onclick="closeFilterPopup()">Close</button>
        </div>
    </div>
</div>

<div class="modal" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                <h4>This Week Attendance</h4>
                <h2 id="weeklyCount">0</h2>

                <p class="text-muted">You can mark attendance once per day</p>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" onclick="submitAttendance()">Mark Attendance</button>
            </div>

        </div>
    </div>
</div>
<style>
    .admin-content{padding:14px;background:#efefef}
    .page-head{font-size:13px;font-weight:700;color:#535353;margin-bottom:8px}
    .top-controls{background:#f8f8f8;padding:14px 14px 16px;border:1px solid #d7d7d7;border-radius:3px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
    .controls-row .btn{font-size:12px;padding:6px 14px;border-radius:3px;line-height:1.2}
    .controls-row .btn-info{background:#44c0df;border-color:#44c0df}
    .controls-row .btn-primary{background:#0e98d3;border-color:#0e98d3}
    .controls-row .input-group{display:flex;flex-wrap:nowrap;width:100%}
    .controls-row .input-group .form-control{height:34px;font-size:12px;border-color:#d8d8d8;min-width:0;flex:1 1 auto}
    .controls-row .input-group .btn{height:34px}
    .controls-row-top{margin-bottom:10px}
    .controls-row-mid{margin-top:2px;margin-bottom:12px}
    .controls-row-bottom{margin-top:4px;margin-bottom:10px}
    .team-top-row,.team-mid-row,.team-bottom-row{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:nowrap}
    .team-search-wrap{flex:0 1 560px;max-width:560px;min-width:360px}
    .team-actions-wrap,.team-bulk-wrap,.team-sort-wrap{flex:0 0 auto;white-space:nowrap}
    .team-select-wrap,.team-show-wrap{flex:0 0 auto}
    .search-clear-btn{padding:6px 10px;background:#fff;color:#777}
    .show-entry-wrap,.sort-wrap{display:inline-flex;align-items:center}
    .custom-tabs{border-bottom:1px solid #d7d7d7;padding-top:6px;gap:8px;display:flex;flex-wrap:wrap;margin-bottom:0}
    .custom-tabs .nav-link{background:#e9e9e9;border:1px solid #d9d9d9;border-bottom:0;border-radius:3px 3px 0 0;color:#333;font-size:11px;font-weight:700;padding:8px 14px;min-width:112px;text-align:center}
    .custom-tabs .nav-link small{display:block;font-size:10px;font-weight:600;color:#666}
    .custom-tabs .nav-link.active{background:#56c8ed;color:#fff;border-color:#48bde4}
    .custom-tabs .nav-link.active small{color:#fff}
    .team-card{background:#f3f3f3;border:1px solid #d9d9d9;border-radius:2px;padding:10px;margin-top:12px}
    .team-card-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;border-bottom:1px solid #e1e1e1;padding-bottom:8px}
    .team-title-wrap{display:flex;align-items:center;gap:8px}
    .team-title-wrap h5{margin:0;font-size:30px;font-size:16px;font-weight:500;color:#4e4e4e}
    .team-status{font-size:12px;font-weight:700;padding:5px 14px;border-radius:3px}
    .team-status.approved{background:#e6f7ef;color:#1aa260}
    .team-status.unapproved{background:#fff4e5;color:#ff9800}
    .team-card-body{display:flex;gap:16px;flex-wrap:wrap}
    .team-col{flex:1;min-width:300px}
    .team-col h6{font-weight:700;color:#444;margin-bottom:8px}
    .team-col p{display:grid;grid-template-columns:130px 12px 1fr;margin:0 0 5px;font-size:11px;color:#565656}
    .kpi-table{font-size:11px;color:#565656}
    .kpi-table > div{display:grid;grid-template-columns:1.8fr .7fr .7fr;gap:8px;margin-bottom:4px}
    .kpi-head{font-style:italic;color:#666}
    .team-actions .btn{min-width:120px}
    .btn-attendance{background:#2ecc71;color:#fff}
    .btn-member{background:#17a2b8;color:#fff}
    .custom-popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;justify-content:center;align-items:center;z-index:1050}
    .custom-popup{background:#fff;padding:20px;border-radius:8px;width:420px;max-width:90%;box-shadow:0 4px 15px rgba(0,0,0,.2)}
    .popup-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px}
    .popup-header h3{margin:0}
    .close-popup{cursor:pointer;font-size:24px}
    .popup-body .form-group{margin-bottom:15px}
    .popup-footer{display:flex;justify-content:flex-end;gap:10px}
    .btn-submit{background:#0e98d3;color:#fff;border:0;padding:7px 14px;border-radius:3px}
    .btn-cancel{background:#e4e4e4;color:#333;border:0;padding:7px 14px;border-radius:3px}
    @media(max-width:991px){
        .team-top-row,.team-mid-row,.team-bottom-row{flex-wrap:wrap}
        .team-search-wrap{flex:1 1 100%;max-width:100%;min-width:0}
        .team-actions-wrap,.team-bulk-wrap,.team-sort-wrap{width:100%;text-align:left}
    }
    @media(max-width:768px){
        .team-card-body{flex-direction:column}
        .team-col{min-width:0}
    }
</style>

<script>
    window.selectedUserId = null;
    window.isSubmitting = false;

    function openFilterPopup(){ document.getElementById("filterPopup").style.display = "flex"; }
    function closeFilterPopup(){ document.getElementById("filterPopup").style.display = "none"; }
    function applyStatusFilter(){
        const status = document.getElementById('quickStatusFilter').value;
        const target = status ? `<?= BASE_URL ?>/admin/team-management?status=${encodeURIComponent(status)}` : `<?= BASE_URL ?>/admin/team-management`;
        window.location.href = target;
    }

    document.getElementById('selectAll')?.addEventListener('click', function() {
        document.querySelectorAll('.rowCheckbox').forEach((cb) => cb.checked = this.checked);
    });

    document.getElementById('clearSearchBtn')?.addEventListener('click', function(){
        const input = document.getElementById('teamSearch');
        if (input) input.value = '';
        document.getElementById('teamSearchForm').submit();
    });

    window.openAttendanceModal = function(id) {
        window.selectedUserId = id;
        fetch("<?= BASE_URL ?>/admin/team-management/attendance-data", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById("weeklyCount").innerText = data.weekly ?? 0;
            const modalElement = document.getElementById('attendanceModal');
            if (!modalElement || typeof bootstrap === 'undefined') return;
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        })
        .catch(() => { alert("Error loading attendance"); });
    };

    window.submitAttendance = function() {
        if (!window.selectedUserId || window.isSubmitting) return;
        window.isSubmitting = true;
        fetch("<?= BASE_URL ?>/admin/team-management/mark-attendance", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(window.selectedUserId)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                document.getElementById("weeklyCount").innerText = data.weekly ?? document.getElementById("weeklyCount").innerText;
                alert("Attendance marked successfully");
            } else {
                alert(data.message || "Failed to mark attendance");
            }
            window.isSubmitting = false;
        })
        .catch(() => {
            alert("Error saving attendance");
            window.isSubmitting = false;
        });
    };
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>