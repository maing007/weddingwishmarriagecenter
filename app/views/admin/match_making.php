<?php
$title = "Member Match - ALL";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
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
        <div class="page-head">Member Match - ALL</div>

        <div class="top-controls">
            <div class="controls-row controls-row-top mm-top-row">
                <div class="mm-search-wrap">
                    <div class="input-group">
                        <input type="text" id="userSearch" class="form-control" placeholder="Search here...">
                        <button class="btn btn-light border search-clear-btn" type="button" id="clearSearchBtn"><i class="fa fa-times"></i></button>
                        <button class="btn btn-primary" type="button"><i class="bi bi-search"></i> Search</button>
                    </div>
                </div>
                <div class="mm-actions-wrap text-end">
                    <button class="btn btn-info text-white" type="button" onclick="openFilterPopup()"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </div>
            <div class="controls-row controls-row-mid mm-mid-row mt-2">
                <div class="mm-select-wrap"><div class="form-check d-inline-flex align-items-center gap-2"><input class="form-check-input" type="checkbox" id="selectAllUsers"><label class="form-check-label" for="selectAllUsers">Select All</label></div></div>
            </div>
            <div class="controls-row controls-row-bottom mm-bottom-row mt-3">
                <div class="mm-show-wrap"><div class="show-entry-wrap"><label class="me-2 mb-0">Show</label><select id="showEntries" class="form-select d-inline-block w-auto"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></div></div>
                <div class="mm-sort-wrap text-end"><div class="sort-wrap"><label class="me-2 mb-0">Sort</label><select id="sortUsers" class="form-select d-inline-block w-auto"><option value="latest_desc">Latest Descending</option><option value="latest_asc">Latest Ascending</option><option value="name_asc">Name A-Z</option><option value="name_desc">Name Z-A</option></select></div></div>
            </div>

            <ul class="nav nav-tabs custom-tabs mt-3">
                <li class="nav-item"><a class="nav-link <?= ($filter ?? 'all') === 'all' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/match-making?status_filter=all">All <small>(<?= (int)count($allUsers) ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= ($filter ?? 'all') === 'approved' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/match-making?status_filter=approved">Approved List <small>(<?= (int)$approvedCount ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= ($filter ?? 'all') === 'unapproved' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/match-making?status_filter=unapproved">Unapproved List <small>(<?= (int)$unapprovedCount ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= ($filter ?? 'all') === 'suspended' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/match-making?status_filter=suspended">Suspended List <small>(<?= (int)$suspendedCount ?>)</small></a></li>
            </ul>
        </div>

        <div id="userList" class="mt-4">
            <?php foreach ($users as $u): ?>
            <div class="user-card searchable-card" data-date="<?= strtotime($u['created_at']) ?>" data-name="<?= strtolower($u['first_name'].' '.$u['last_name']) ?>">
                <div class="user-card-header">
                    <div class="user-left-title"><input type="checkbox" class="user-checkbox" value="<?= (int)$u['id'] ?>"><h5><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?> (NG<?= (int)$u['id'] ?>)</h5></div>
                    <div class="approved-badge status-<?= strtolower((string)($u['status'] ?? 'unapproved')) ?>"><?= strtoupper((string)($u['status'] ?? 'UNAPPROVED')) ?></div>
                </div>
                <div class="counter-row">
                    <a class="counter-box blue text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=opened">Opened (<?= (int)($u['opened_count'] ?? 0) ?>)</a>
                    <a class="counter-box yellow text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=deferred">Deferred (<?= (int)($u['deferred_count'] ?? 0) ?>)</a>
                    <a class="counter-box red text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=declined">Declined (<?= (int)($u['declined_count'] ?? 0) ?>)</a>
                    <a class="counter-box cyan text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=meeting">Meeting (<?= (int)($u['meeting_count'] ?? 0) ?>)</a>
                    <a class="counter-box green text-decoration-none" href="<?= BASE_URL ?>/admin/users/interactions?id=<?= (int)$u['id'] ?>&action=accepted">Accepted (<?= (int)($u['accepted_count'] ?? 0) ?>)</a>
                </div>
                <div class="user-main-content">
                    <div class="profile-image-box"><img src="<?= BASE_URL . (!empty($u['avatar']) ? $u['avatar'] : '/assets/images/default-avatar.png') ?>" alt=""></div>
                    <div class="details-column details-grid">
                        <p><strong>Gender</strong><span>:</span> <?= htmlspecialchars($u['gender'] ?? '-') ?></p>
                        <p><strong>Mobile</strong><span>:</span> <?= htmlspecialchars($u['phone'] ?? '-') ?></p>
                        <p><strong>Religion Name</strong><span>:</span> <?= htmlspecialchars($u['religion'] ?? '-') ?></p>
                        <p><strong>Caste Name</strong><span>:</span> <?= htmlspecialchars($u['caste'] ?? '-') ?></p>
                        <p><strong>Mother Tongue</strong><span>:</span> <?= htmlspecialchars($u['mother_tongue'] ?? '-') ?></p>
                        <p><strong>Marital Status</strong><span>:</span> <?= htmlspecialchars($u['marital_status'] ?? '-') ?></p>
                        <p><strong>Plan Name</strong><span>:</span> N/A</p>
                        <p><strong>Plan Expired On</strong><span>:</span> N/A</p>
                        <p><strong>Added By</strong><span>:</span> Admin</p>
                    </div>
                    <div class="details-column details-grid">
                        <p><strong>Email</strong><span>:</span> <?= htmlspecialchars($u['email']) ?></p>
                        <p><strong>Country Name</strong><span>:</span> <?= htmlspecialchars($u['country'] ?? 'Pakistan') ?></p>
                        <p><strong>State Name</strong><span>:</span> <?= htmlspecialchars($u['state'] ?? '-') ?></p>
                        <p><strong>City Name</strong><span>:</span> <?= htmlspecialchars($u['city'] ?? '-') ?></p>
                        <p><strong>Birthdate</strong><span>:</span> <?= htmlspecialchars($u['dob'] ?? '-') ?></p>
                        <p><strong>Registered On</strong><span>:</span> <?= htmlspecialchars($u['created_at']) ?></p>
                        <p><strong>Last Login</strong><span>:</span> N/A</p>
                        <p><strong>Partner Contact Pdf</strong><span>:</span> No</p>
                        <p><strong>Final Rishta Fee</strong><span>:</span> 50000</p>
                    </div>
                </div>
                <div class="action-row">
                    <a class="btn-action blue" href="<?= BASE_URL ?>/admin/users/open-task?id=<?= (int)$u['id'] ?>">Open Task</a>
                    <a class="btn-action lightblue" href="<?= BASE_URL ?>/admin/member-evaluation?id=<?= (int)$u['id'] ?>">MEF</a>
                    <button type="button" class="btn-action blue" onclick="openCommentPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">Add Comment</button>
                    <button type="button" class="btn-action yellow-btn" onclick="openViewCommentsPopup(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['first_name'].' '.$u['last_name'], ENT_QUOTES) ?>')">View Comment</button>
                    <a class="btn-action blue" href="<?= BASE_URL ?>/admin/users/profile-view?id=<?= (int)$u['id'] ?>">View Profile</a>
                    <a class="btn-action lightblue" href="<?= BASE_URL ?>/admin/users/edit-steps?id=<?= (int)$u['id'] ?>">Edit Profile</a>
                    <a class="btn-action green" target="_blank" href="<?= BASE_URL ?>/admin/users/profile-pdf-template?id=<?= (int)$u['id'] ?>">Profile (PDF)</a>
                    <a class="btn-action lightblue" href="<?= BASE_URL ?>/admin/users/open-task?id=<?= (int)$u['id'] ?>">Team List (1)</a>
                    <form method="post" action="<?= BASE_URL ?>/admin/users/send-email-confirmation"><input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>"><button class="btn-action blue" type="submit">Confirm email</button></form>
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
        <div class="popup-body"><div class="form-group"><label>Status</label><select class="form-control" id="statusFilter"><option value="">All</option><option value="approved">Approved</option><option value="unapproved">Unapproved</option><option value="suspended">Suspended</option></select></div></div>
        <div class="popup-footer"><button type="button" class="btn-submit" onclick="applyFilterStatus()">Apply</button><button type="button" class="btn-cancel" onclick="closeFilterPopup()">Close</button></div>
    </div>
</div>
<div id="commentPopup" class="custom-popup-overlay" style="display:none;"><div class="custom-popup custom-popup-lg"><div class="popup-header"><h3 id="commentPopupTitle">Add Comment</h3><span class="close-popup" onclick="closeCommentPopup()">&times;</span></div><form method="POST" action="<?= BASE_URL ?>/admin/users/comment"><div class="popup-body"><input type="hidden" name="user_id" id="comment_user_id"><div class="form-group"><label>Type</label><select class="form-control" name="comment_type"><option value="general">General</option><option value="follow_up">Follow Up</option><option value="warning">Warning</option><option value="approval_note">Approval Note</option></select></div><div class="form-group"><label>Comment</label><textarea class="form-control" name="comment" rows="6" required></textarea></div></div><div class="popup-footer"><button type="submit" class="btn-submit">Save Comment</button><button type="button" class="btn-cancel" onclick="closeCommentPopup()">Cancel</button></div></form></div></div>
<div id="viewCommentsPopup" class="custom-popup-overlay" style="display:none;"><div class="custom-popup custom-popup-xl"><div class="popup-header"><h3 id="viewCommentPopupTitle">View Comments</h3><span class="close-popup" onclick="closeViewCommentsPopup()">&times;</span></div><div class="popup-body"><input type="hidden" id="view_comment_user_id"><div class="row g-2 mb-3"><div class="col-md-4"><label>Type</label><select id="filter_comment_type" class="form-control"><option value="">All</option><option value="general">General</option><option value="follow_up">Follow Up</option><option value="warning">Warning</option><option value="approval_note">Approval Note</option></select></div><div class="col-md-4"><label>From</label><input type="date" id="filter_comment_from" class="form-control"></div><div class="col-md-4"><label>To</label><input type="date" id="filter_comment_to" class="form-control"></div></div><button type="button" class="btn btn-primary btn-sm mb-3" onclick="loadProfileComments()">Apply Filter</button><div id="commentsResults" style="max-height:380px;overflow:auto;"></div></div><div class="popup-footer"><button type="button" class="btn-cancel" onclick="closeViewCommentsPopup()">Close</button></div></div></div>

<style>
    .admin-content{padding:14px;background:#efefef}.page-head{font-size:13px;font-weight:700;color:#535353;margin-bottom:8px}
    .top-controls{background:#f8f8f8;padding:14px 14px 16px;border:1px solid #d7d7d7;border-radius:3px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
    .controls-row .btn{font-size:12px;padding:6px 14px;border-radius:3px;line-height:1.2}.controls-row .btn-info{background:#44c0df;border-color:#44c0df}.controls-row .btn-primary{background:#0e98d3;border-color:#0e98d3}
    .controls-row .input-group{display:flex;flex-wrap:nowrap;width:100%}.controls-row .input-group .form-control{height:34px;font-size:12px;border-color:#d8d8d8;min-width:0;flex:1 1 auto}.controls-row .input-group .btn{height:34px}
    .controls-row-top{margin-bottom:10px}.controls-row-mid{margin-top:2px;margin-bottom:12px}.controls-row-bottom{margin-top:4px;margin-bottom:10px}
    .mm-top-row,.mm-mid-row,.mm-bottom-row{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:nowrap}
    .mm-search-wrap{flex:0 1 560px;max-width:560px;min-width:360px}.mm-actions-wrap,.mm-sort-wrap{flex:0 0 auto;white-space:nowrap}.mm-select-wrap,.mm-show-wrap{flex:0 0 auto}
    .search-clear-btn{padding:6px 10px;background:#fff;color:#777}.show-entry-wrap,.sort-wrap{display:inline-flex;align-items:center}
    .custom-tabs{border-bottom:1px solid #d7d7d7;padding-top:6px;gap:8px;display:flex;flex-wrap:wrap;margin-bottom:0}
    .custom-tabs .nav-link{background:#e9e9e9;border:1px solid #d9d9d9;border-bottom:0;border-radius:3px 3px 0 0;color:#333;font-size:11px;font-weight:700;padding:8px 14px;min-width:112px;text-align:center}
    .custom-tabs .nav-link small{display:block;font-size:10px;font-weight:600;color:#666}.custom-tabs .nav-link.active{background:#56c8ed;color:#fff;border-color:#48bde4}.custom-tabs .nav-link.active small{color:#fff}
    .user-card{background:#f3f3f3;border:1px solid #d9d9d9;border-radius:2px;padding:10px;margin-bottom:14px}
    .user-card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;border-bottom:1px solid #e1e1e1;padding-bottom:6px}
    .user-left-title h5{margin:0;font-size:32px;font-size:30px;font-weight:500;color:#4e4e4e}
    .approved-badge{font-size:18px;font-size:16px;font-weight:700;padding:5px 14px;border-radius:3px}.status-approved{background:#e6f7ef;color:#1aa260}.status-unapproved{background:#fff4e5;color:#ff9800}.status-suspended{background:#fdecec;color:#d9534f}
    .counter-row{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px}.counter-box{flex:1;min-width:120px;padding:5px 8px;color:#fff;text-align:center;border-radius:0;font-size:11px}
    .blue{background:#1399c8}.yellow{background:#efc145}.red{background:#d96958}.cyan{background:#45bdd2}.green{background:#2fc66f}
    .user-main-content{display:flex;gap:12px}.profile-image-box img{width:120px;height:120px;object-fit:cover;border:1px solid #ddd}
    .details-column{flex:1;min-width:230px}.details-grid p{display:grid;grid-template-columns:130px 12px 1fr;margin:0 0 5px;font-size:11px;color:#565656}
    .action-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;justify-content:flex-end}
    .btn-action{padding:6px 12px;border:none;color:#fff;border-radius:2px;text-decoration:none;font-size:11px;min-width:110px;text-align:center}.lightblue{background:#54c3da}.yellow-btn{background:#efc145;color:#6f5100}
    .custom-popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;justify-content:center;align-items:center;z-index:1050}
    .custom-popup{background:#fff;padding:20px;border-radius:8px;width:400px;max-width:90%;box-shadow:0 4px 15px rgba(0,0,0,.2)}.custom-popup-lg{width:620px}.custom-popup-xl{width:900px}
    .popup-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px}.close-popup{cursor:pointer;font-size:24px}.popup-body .form-group{margin-bottom:15px}.popup-footer{display:flex;justify-content:flex-end;gap:10px}
    .btn-submit{background:#0e98d3;color:#fff;border:0;padding:7px 14px;border-radius:3px}.btn-cancel{background:#e4e4e4;color:#333;border:0;padding:7px 14px;border-radius:3px}
    .comment-item{border:1px solid #e4e4e4;background:#fbfbfb;padding:10px;border-radius:4px;margin-bottom:10px}.comment-meta{font-size:11px;color:#666;margin-bottom:6px}
    @media(max-width:991px){.mm-top-row,.mm-mid-row,.mm-bottom-row{flex-wrap:wrap}.mm-search-wrap{flex:1 1 100%;max-width:100%;min-width:0}.mm-actions-wrap,.mm-sort-wrap{width:100%;text-align:left}}
</style>

<script>
const searchInput = document.getElementById("userSearch");const sortSelect = document.getElementById("sortUsers");const showEntries = document.getElementById("showEntries");const cards = Array.from(document.querySelectorAll(".searchable-card"));
function updateList(){const search=(searchInput?.value||"").toLowerCase();const limit=parseInt(showEntries?.value||"9999",10);const filtered=cards.filter((c)=>c.innerText.toLowerCase().includes(search));const sort=sortSelect?.value||"latest_desc";filtered.sort((a,b)=>{if(sort==="latest_desc")return b.dataset.date-a.dataset.date;if(sort==="latest_asc")return a.dataset.date-b.dataset.date;if(sort==="name_asc")return a.dataset.name.localeCompare(b.dataset.name);if(sort==="name_desc")return b.dataset.name.localeCompare(a.dataset.name);return 0;});cards.forEach((c)=>c.style.display="none");filtered.slice(0,limit).forEach((c)=>c.style.display="block");}
searchInput?.addEventListener("keyup",updateList);sortSelect?.addEventListener("change",updateList);showEntries?.addEventListener("change",updateList);
document.getElementById("clearSearchBtn")?.addEventListener("click",()=>{if(searchInput)searchInput.value="";updateList();});
document.getElementById("selectAllUsers")?.addEventListener("change",function(){document.querySelectorAll(".user-checkbox").forEach(cb=>cb.checked=this.checked);});
function openFilterPopup(){document.getElementById("filterPopup").style.display="flex";}function closeFilterPopup(){document.getElementById("filterPopup").style.display="none";}
function applyFilterStatus(){const st=document.getElementById('statusFilter').value;const target=st?`<?= BASE_URL ?>/admin/match-making?status_filter=${encodeURIComponent(st)}`:`<?= BASE_URL ?>/admin/match-making`;window.location.href=target;}
function openCommentPopup(userId,userName){document.getElementById("comment_user_id").value=userId;document.getElementById("commentPopupTitle").innerText="Add Comment - "+userName;document.getElementById("commentPopup").style.display="flex";}
function closeCommentPopup(){document.getElementById("commentPopup").style.display="none";}
function openViewCommentsPopup(userId,userName){document.getElementById("view_comment_user_id").value=userId;document.getElementById("viewCommentPopupTitle").innerText="View Comments - "+userName;document.getElementById("viewCommentsPopup").style.display="flex";loadProfileComments();}
function closeViewCommentsPopup(){document.getElementById("viewCommentsPopup").style.display="none";}
function escapeHtml(str){return (str||'').replace(/[&<>"']/g,(m)=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
function loadProfileComments(){const userId=document.getElementById('view_comment_user_id').value;const type=document.getElementById('filter_comment_type').value;const from=document.getElementById('filter_comment_from').value;const to=document.getElementById('filter_comment_to').value;const url=`<?= BASE_URL ?>/admin/users/comments-json?user_id=${encodeURIComponent(userId)}&type=${encodeURIComponent(type)}&date_from=${encodeURIComponent(from)}&date_to=${encodeURIComponent(to)}`;fetch(url).then(r=>r.json()).then(data=>{const wrap=document.getElementById('commentsResults');if(!data.ok||!data.rows||data.rows.length===0){wrap.innerHTML='<div class="alert alert-warning mb-0">No comments found.</div>';return;}wrap.innerHTML=data.rows.map((row)=>`<div class="comment-item"><div class="comment-meta"><strong>${escapeHtml(row.admin_name||'Admin')}</strong> | ${escapeHtml(row.comment_type||'general')} | ${escapeHtml(row.created_at||'')}</div><div>${escapeHtml(row.comment||'')}</div></div>`).join('');}).catch(()=>{document.getElementById('commentsResults').innerHTML='<div class="alert alert-danger mb-0">Unable to load comments.</div>';});}
updateList();
</script>

<?php require __DIR__.'/partials/footer.php'; ?>
