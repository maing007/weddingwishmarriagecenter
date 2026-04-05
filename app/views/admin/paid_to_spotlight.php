<?php
$title = "Paid to Spotlight";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

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
        <div class="page-head">ManagePaid Member - ALL</div>

        <div class="top-controls">
            <div class="controls-row controls-row-top spotlight-top-row">
                <div class="spotlight-search-wrap">
                    <div class="input-group d-flex">
                        <input type="text" id="userSearch" class="form-control" placeholder="Search here...">
                        <button class="btn btn-light border search-clear-btn" type="button" id="clearSearchBtn" aria-label="Clear search"><i class="fa fa-times"></i></button>
                        <button class="btn btn-primary" type="button"><i class="bi bi-search"></i> Search</button>
                    </div>
                </div>
                <div class="spotlight-actions-wrap text-end">
                    <a href="<?= BASE_URL?>/admin/add-user" class="btn btn-danger me-2"><i class="bi bi-person-plus"></i> Add New</a>
                    <button class="btn btn-info text-white" type="button" onclick="openFilterPopup()"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </div>

            <div class="controls-row controls-row-mid spotlight-mid-row mt-2">
                <div class="spotlight-select-wrap">
                    <div class="form-check d-inline-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" id="selectAllUsers">
                        <label class="form-check-label" for="selectAllUsers">Select All</label>
                    </div>
                </div>
                <div class="spotlight-feature-wrap text-end">
                    <button type="button" class="status-pill sp-featured" onclick="submitFeaturedStatus('featured')">Featured</button>
                    <button type="button" class="status-pill sp-nonfeatured" onclick="submitFeaturedStatus('non_featured')">Non Featured</button>
                </div>
            </div>

            <div class="controls-row controls-row-bottom spotlight-bottom-row mt-3">
                <div class="spotlight-show-wrap">
                    <div class="show-entry-wrap">
                        <label class="me-2 mb-0">Show</label>
                        <select id="showEntries" class="form-select d-inline-block w-auto">
                            <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option>
                        </select>
                        <label class="ms-2 mb-0">Entries</label>
                    </div>
                </div>
                <div class="spotlight-sort-wrap text-end">
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

            <ul class="nav nav-tabs custom-tabs mt-3">
                <li class="nav-item"><a class="nav-link <?= ($filter ?? 'all') === 'all' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/paid-to-spotlight?featured_filter=all">All <small>(<?= (int)count($users) ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= ($filter ?? 'all') === 'featured' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/paid-to-spotlight?featured_filter=featured">Featured <small>(<?= (int)$featuredCount ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= ($filter ?? 'all') === 'non_featured' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/paid-to-spotlight?featured_filter=non_featured">Non Featured <small>(<?= (int)$nonFeaturedCount ?>)</small></a></li>
            </ul>
        </div>

        <div id="userList" class="mt-4">
            <?php foreach ($users as $u): ?>
            <div class="user-card searchable-card"
                 data-date="<?= strtotime($u['created_at']) ?>"
                 data-name="<?= strtolower($u['first_name'].' '.$u['last_name']) ?>"
                 data-department="<?= strtolower(trim((string)($u['religion'] ?? ''))) ?>"
                 data-team-leader="<?= strtolower(trim((string)($u['gender'] ?? ''))) ?>"
                 data-customer-support="<?= strtolower(trim((string)($u['country'] ?? ''))) ?>"
                 data-team="<?= strtolower(trim((string)($u['city'] ?? ''))) ?>">
                <div class="user-card-header">
                    <div class="user-left-title">
                        <input type="checkbox" class="user-checkbox" value="<?= (int)$u['id'] ?>">
                        <h5><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?> (<?= htmlspecialchars(matri_id_display((string) ($u['matri_id'] ?? ''), (int) $u['id'])) ?>)</h5>
                    </div>
                    <div class="approved-badge <?= strtolower((string)($u['featured_status'] ?? 'non_featured')) === 'featured' ? 'status-featured' : 'status-nonfeatured' ?>">
                        <?php if (strtolower((string)($u['featured_status'] ?? 'non_featured')) === 'featured'): ?>
                            <i class="fa fa-star" aria-hidden="true"></i>
                        <?php else: ?>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?= strtoupper(strtolower((string)($u['featured_status'] ?? 'non_featured')) === 'featured' ? 'FEATURED' : 'NON FEATURED') ?>
                    </div>
                </div>

                <div class="counter-row">
                    <a class="counter-box blue text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=opened">Opened (<?= (int)($u['opened_count'] ?? 0) ?>)</a>
                    <a class="counter-box yellow text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=deferred">Deferred (<?= (int)($u['deferred_count'] ?? 0) ?>)</a>
                    <a class="counter-box red text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=declined">Declined (<?= (int)($u['declined_count'] ?? 0) ?>)</a>
                    <a class="counter-box cyan text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=meeting">Meeting (<?= (int)($u['meeting_count'] ?? 0) ?>)</a>
                    <a class="counter-box green text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=accepted">Accepted (<?= (int)($u['accepted_count'] ?? 0) ?>)</a>
                </div>

                <div class="user-main-content">
                    <?php $cardUser = $u;
                    require __DIR__ . '/partials/member_card_photo_block.php'; ?>
                    <div class="details-column details-grid">
                        <p><strong>Gender</strong><span>:</span> <?= htmlspecialchars($u['gender'] ?? '-') ?></p>
                        <p><strong>Mobile</strong><span>:</span> <?= htmlspecialchars($u['phone'] ?? '-') ?></p>
                        <p><strong>Religion Name</strong><span>:</span> <?= htmlspecialchars($u['religion'] ?? '-') ?></p>
                        <p><strong>Added By</strong><span>:</span> Admin</p>
                    </div>
                    <div class="details-column details-grid">
                        <p><strong>Email</strong><span>:</span> <?= htmlspecialchars($u['email']) ?></p>
                        <p><strong>Country Name</strong><span>:</span> <?= htmlspecialchars($u['country'] ?? 'Pakistan') ?></p>
                        <p><strong>City Name</strong><span>:</span> <?= htmlspecialchars($u['city'] ?? 'Lahore') ?></p>
                        <p><strong>Registered On</strong><span>:</span> <?= htmlspecialchars($u['created_at']) ?></p>
                    </div>
                </div>

                <div class="action-row">
                    <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/open-task?id=<?= (int)$u['id'] ?>">Team List</a>
                    <button type="button" class="btn-action btn-action-teal" onclick="openCommentPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">Add Comment</button>
                    <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/users/profile-view?id=<?= (int)$u['id'] ?>">View Profile</a>
                    <button type="button" class="btn-action btn-action-amber" onclick="openViewCommentsPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">View Comment</button>
                    <a class="btn-action btn-action-cyan" href="<?= BASE_URL ?>/admin/users/edit-steps?id=<?= (int)$u['id'] ?>">Edit Profile</a>
                    <a class="btn-action btn-action-green" target="_blank" href="<?= BASE_URL ?>/admin/users/profile-pdf-template?id=<?= (int)$u['id'] ?>">Profile PDF</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</main>
</div>

<div id="filterPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup">
        <div class="popup-header"><h3>Filter Data</h3><span class="close-popup" onclick="closeFilterPopup()">&times;</span></div>
        <form id="advancedFilterForm" onsubmit="applyAdvancedFilters(event)">
            <div class="popup-body">
                <div class="form-group"><label>Department Filter</label><select class="form-control" id="departmentFilter"><option value="">All Departments</option><?php foreach (array_values(array_filter(array_unique(array_map(static function($x){ return trim((string)($x['religion'] ?? '')); }, $users)))) as $opt): ?><option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Team Leader Filter</label><select class="form-control" id="teamLeaderFilter"><option value="">All Team Leaders</option><?php foreach (array_values(array_filter(array_unique(array_map(static function($x){ return trim((string)($x['gender'] ?? '')); }, $users)))) as $opt): ?><option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Customer Support</label><select class="form-control" id="customerSupportFilter"><option value="">All Customer Support</option><?php foreach (array_values(array_filter(array_unique(array_map(static function($x){ return trim((string)($x['country'] ?? '')); }, $users)))) as $opt): ?><option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Team Filter</label><select class="form-control" id="teamFilter"><option value="">All Teams</option><?php foreach (array_values(array_filter(array_unique(array_map(static function($x){ return trim((string)($x['city'] ?? '')); }, $users)))) as $opt): ?><option value="<?= htmlspecialchars(strtolower($opt)) ?>"><?= htmlspecialchars($opt) ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="popup-footer"><button type="submit" class="btn-submit">Submit</button><button type="button" class="btn-cancel" onclick="clearAdvancedFilters()">Reset</button><button type="button" class="btn-cancel" onclick="closeFilterPopup()">Close</button></div>
        </form>
    </div>
</div>

<div id="commentPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-lg">
        <div class="popup-header"><h3 id="commentPopupTitle">Add Comment</h3><span class="close-popup" onclick="closeCommentPopup()">&times;</span></div>
        <form method="POST" action="<?= BASE_URL ?>/admin/users/comment">
            <div class="popup-body">
                <input type="hidden" name="user_id" id="comment_user_id">
                <div class="form-group"><label>Type</label><select class="form-control" name="comment_type"><option value="general">General</option><option value="follow_up">Follow Up</option><option value="warning">Warning</option><option value="approval_note">Approval Note</option></select></div>
                <div class="form-group"><label>Comment</label><textarea class="form-control" name="comment" rows="6" required></textarea></div>
            </div>
            <div class="popup-footer"><button type="submit" class="btn-submit">Save Comment</button><button type="button" class="btn-cancel" onclick="closeCommentPopup()">Cancel</button></div>
        </form>
    </div>
</div>

<div id="viewCommentsPopup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup custom-popup-xl">
        <div class="popup-header"><h3 id="viewCommentPopupTitle">View Comments</h3><span class="close-popup" onclick="closeViewCommentsPopup()">&times;</span></div>
        <div class="popup-body">
            <input type="hidden" id="view_comment_user_id">
            <div class="row g-2 mb-3">
                <div class="col-md-4"><label>Type</label><select id="filter_comment_type" class="form-control"><option value="">All</option><option value="general">General</option><option value="follow_up">Follow Up</option><option value="warning">Warning</option><option value="approval_note">Approval Note</option></select></div>
                <div class="col-md-4"><label>From</label><input type="date" id="filter_comment_from" class="form-control"></div>
                <div class="col-md-4"><label>To</label><input type="date" id="filter_comment_to" class="form-control"></div>
            </div>
            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="loadProfileComments()">Apply Filter</button>
            <div id="commentsResults" style="max-height:380px;overflow:auto;"></div>
        </div>
        <div class="popup-footer"><button type="button" class="btn-cancel" onclick="closeViewCommentsPopup()">Close</button></div>
    </div>
</div>

<script>
document.getElementById("selectAllUsers").addEventListener("change", function () {
    document.querySelectorAll(".user-checkbox").forEach(cb => cb.checked = this.checked);
});
const searchInput = document.getElementById("userSearch");
const sortSelect = document.getElementById("sortUsers");
const showEntries = document.getElementById("showEntries");
const cards = Array.from(document.querySelectorAll(".searchable-card"));
const advancedFilters = { department: "", teamLeader: "", customerSupport: "", team: "" };
function updateList(){
    const search = (searchInput?.value || "").toLowerCase();
    const limit = parseInt(showEntries?.value || "9999", 10);
    let filtered = cards.filter((card) => {
        const text = card.innerText.toLowerCase();
        if (!text.includes(search)) return false;
        if (advancedFilters.department && card.dataset.department !== advancedFilters.department) return false;
        if (advancedFilters.teamLeader && card.dataset.teamLeader !== advancedFilters.teamLeader) return false;
        if (advancedFilters.customerSupport && card.dataset.customerSupport !== advancedFilters.customerSupport) return false;
        if (advancedFilters.team && card.dataset.team !== advancedFilters.team) return false;
        return true;
    });
    const sort = sortSelect?.value || "latest_desc";
    filtered.sort((a, b) => {
        if (sort === "latest_desc") return b.dataset.date - a.dataset.date;
        if (sort === "latest_asc") return a.dataset.date - b.dataset.date;
        if (sort === "name_asc") return a.dataset.name.localeCompare(b.dataset.name);
        if (sort === "name_desc") return b.dataset.name.localeCompare(a.dataset.name);
        return 0;
    });
    cards.forEach((c) => c.style.display = "none");
    filtered.slice(0, limit).forEach((c) => c.style.display = "block");
}
searchInput?.addEventListener("keyup", updateList);
sortSelect?.addEventListener("change", updateList);
showEntries?.addEventListener("change", updateList);
document.getElementById("clearSearchBtn")?.addEventListener("click", function(){ if (searchInput) searchInput.value = ""; updateList(); });
function openFilterPopup(){ document.getElementById("filterPopup").style.display = "flex"; }
function closeFilterPopup(){ document.getElementById("filterPopup").style.display = "none"; }
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
    advancedFilters.department = ""; advancedFilters.teamLeader = ""; advancedFilters.customerSupport = ""; advancedFilters.team = "";
    updateList();
}
function submitFeaturedStatus(statusValue){
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selectedUsers.length === 0) { alert('Please select at least one profile.'); return; }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASE_URL ?>/admin/paid-to-spotlight/bulk-featured';
    form.innerHTML = `<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"><input type="hidden" name="featured_status" value="${statusValue}">`;
    selectedUsers.forEach((id) => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = 'selected_users[]'; input.value = id;
        form.appendChild(input);
    });
    document.body.appendChild(form); form.submit();
}
function openCommentPopup(userId, userName){ document.getElementById("comment_user_id").value = userId; document.getElementById("commentPopupTitle").innerText = "Add Comment - " + userName; document.getElementById("commentPopup").style.display = "flex"; }
function closeCommentPopup(){ document.getElementById("commentPopup").style.display = "none"; }
function openViewCommentsPopup(userId, userName){ document.getElementById("view_comment_user_id").value = userId; document.getElementById("viewCommentPopupTitle").innerText = "View Comments - " + userName; document.getElementById("viewCommentsPopup").style.display = "flex"; loadProfileComments(); }
function closeViewCommentsPopup(){ document.getElementById("viewCommentsPopup").style.display = "none"; }
function escapeHtml(str){ return (str || '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
function loadProfileComments(){
    const userId = document.getElementById('view_comment_user_id').value;
    const type = document.getElementById('filter_comment_type').value;
    const from = document.getElementById('filter_comment_from').value;
    const to = document.getElementById('filter_comment_to').value;
    const url = `<?= BASE_URL ?>/admin/users/comments-json?user_id=${encodeURIComponent(userId)}&type=${encodeURIComponent(type)}&date_from=${encodeURIComponent(from)}&date_to=${encodeURIComponent(to)}`;
    fetch(url).then(r => r.json()).then(data => {
        const wrap = document.getElementById('commentsResults');
        if (!data.ok || !data.rows || data.rows.length === 0) { wrap.innerHTML = '<div class="alert alert-warning mb-0">No comments found.</div>'; return; }
        wrap.innerHTML = data.rows.map((row) => `<div class="comment-item"><div class="comment-meta"><strong>${escapeHtml(row.admin_name || 'Admin')}</strong> | ${escapeHtml(row.comment_type || 'general')} | ${escapeHtml(row.created_at || '')}</div><div>${escapeHtml(row.comment || '')}</div></div>`).join('');
    }).catch(() => { document.getElementById('commentsResults').innerHTML = '<div class="alert alert-danger mb-0">Unable to load comments.</div>'; });
}
updateList();
</script>

<?php require __DIR__.'/partials/footer.php'; ?>
