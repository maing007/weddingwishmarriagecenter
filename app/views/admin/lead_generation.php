<?php
$title = 'Manage Lead Generation';
$na = static function ($v) {
    if ($v === null || $v === '') {
        return 'N/A';
    }

    return (string) $v;
};
$fmtDt = static function ($v) use ($na) {
    if ($v === null || $v === '') {
        return 'N/A';
    }
    $t = strtotime($v);

    return $t ? date('F j, Y g:i A', $t) : 'N/A';
};
$fmtDateOnly = static function ($v) use ($na) {
    if ($v === null || $v === '') {
        return 'N/A';
    }
    $t = strtotime($v);

    return $t ? date('F j, Y', $t) : 'N/A';
};

$tabLabels = [
    'all' => 'ALL',
    'in_process' => 'IN-PROCESS',
    'registered' => 'REGISTERED',
    'closed' => 'CLOSED',
];
$headSuffix = $tabLabels[$tab] ?? 'ALL';

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

<div class="admin-main lead-gen-page">
<div class="admin-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Lead Generation - <?= htmlspecialchars($headSuffix) ?></div>
    </div>
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
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success py-2 small"><?= htmlspecialchars($_SESSION['flash_success']);
            unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger py-2 small"><?= htmlspecialchars($_SESSION['flash_error']);
            unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>

        <div class="top-controls">
            <div class="controls-row controls-row-top mm-top-row">
                <div class="mm-search-wrap">
                    <div class="input-group">
                        <input type="text" id="leadSearch" class="form-control" placeholder="Search here...">
                        <button class="btn btn-light border search-clear-btn" type="button" id="clearSearchBtn"><i class="fa fa-times"></i></button>
                        <button class="btn btn-primary" type="button" id="searchBtn"><i class="bi bi-search"></i> Search</button>
                    </div>
                </div>
                <div class="mm-actions-wrap text-end lg-actions-top">
                    <a href="<?= BASE_URL ?>/admin/lead-generation/add" class="btn btn-danger text-white btn-sm lg-btn-add"><i class="fa fa-plus"></i> Add New</a>
                    <button class="btn btn-primary text-white btn-sm" type="button" onclick="openFilter1Popup()"><i class="bi bi-funnel"></i> Filter1</button>
                    <button class="btn btn-info text-white btn-sm" type="button" onclick="openFilterPopup()"><i class="bi bi-funnel-fill"></i> Filter</button>
                </div>
            </div>

            <div class="controls-row controls-row-mid lg-mid-row mt-2">
                <div class="lg-bulk-box">
                    <div class="form-check d-inline-flex align-items-center gap-2 mb-0">
                        <input class="form-check-input" type="checkbox" id="selectAllLeads">
                        <label class="form-check-label" for="selectAllLeads">Select All</label>
                    </div>
                </div>
                <form id="bulkInterestForm" method="POST" action="<?= BASE_URL ?>/admin/lead-generation/bulk-interest" class="d-inline-flex align-items-center gap-2 flex-wrap lg-bulk-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="lead_ids" id="bulk_lead_ids" value="">
                    <input type="hidden" name="return_tab" value="<?= htmlspecialchars($tab) ?>">
                    <label class="mb-0 small text-muted">Select Interest</label>
                    <select name="new_interest" id="bulkNewInterest" class="form-select form-select-sm w-auto">
                        <option value="In-Process-M">In-Process-M</option>
                        <option value="Registered">Registered</option>
                        <option value="Closed-M">Closed-M</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-change-interest" id="btnChangeInterest">Change Interest</button>
                </form>
            </div>

            <div class="controls-row controls-row-bottom mm-bottom-row mt-3">
                <div class="mm-show-wrap"><div class="show-entry-wrap"><label class="me-2 mb-0">Show</label><select id="showEntries" class="form-select d-inline-block w-auto"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></div></div>
                <div class="mm-sort-wrap text-end"><div class="sort-wrap"><label class="me-2 mb-0">Sort</label><select id="sortLeads" class="form-select d-inline-block w-auto"><option value="latest_desc">Latest Descending</option><option value="latest_asc">Latest Ascending</option><option value="name_asc">Name A-Z</option><option value="name_desc">Name Z-A</option></select></div></div>
            </div>

            <ul class="nav nav-tabs custom-tabs mt-3">
                <li class="nav-item"><a class="nav-link <?= $tab === 'all' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/lead-generation?interest_tab=all">All <small>(<?= (int) $countAll ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= $tab === 'in_process' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/lead-generation?interest_tab=in_process">In-Process-M <small>(<?= (int) $countInProcess ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= $tab === 'registered' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/lead-generation?interest_tab=registered">Registered <small>(<?= (int) $countRegistered ?>)</small></a></li>
                <li class="nav-item"><a class="nav-link <?= $tab === 'closed' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/lead-generation?interest_tab=closed">Closed-M <small>(<?= (int) $countClosed ?>)</small></a></li>
            </ul>
        </div>

        <div id="leadList" class="mt-4">
            <?php foreach ($leads as $lead): ?>
            <div class="lead-card searchable-card"
                 data-date="<?= strtotime($lead['created_at'] ?? 'now') ?>"
                 data-name="<?= htmlspecialchars(strtolower($lead['full_name'] ?? '')) ?>"
                 data-interest="<?= htmlspecialchars(strtolower($lead['interest_name'] ?? '')) ?>"
                 data-team="<?= htmlspecialchars(strtolower($lead['team_assign'] ?? '')) ?>"
                 data-importance="<?= htmlspecialchars(strtolower($lead['importance'] ?? '')) ?>"
                 data-country="<?= htmlspecialchars(strtolower($lead['country'] ?? '')) ?>">
                <div class="lead-card-header">
                    <div class="lead-left-title">
                        <input type="checkbox" class="lead-checkbox" value="<?= (int) $lead['id'] ?>" form="bulkInterestForm">
                        <h5><?= htmlspecialchars($lead['full_name'] ?? '') ?></h5>
                    </div>
                </div>
                <div class="lead-three-col">
                    <div class="details-column details-grid lg-col-left">
                        <p><strong>Gender</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['gender'] ?? '')) ?></span></p>
                        <p><strong>Lead Id</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['lead_code'] ?? '')) ?></span></p>
                        <p><strong>Country Name</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['country'] ?? '')) ?></span></p>
                        <p><strong>City Name</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['city'] ?? '')) ?></span></p>
                        <p><strong>Phone No. 2</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['phone2'] ?? '')) ?></span></p>
                        <p><strong>Phone No. 4</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['phone4'] ?? '')) ?></span></p>
                        <p><strong>Interest Name</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['interest_name'] ?? '')) ?></span></p>
                        <p><strong>Team Assign</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['team_assign'] ?? '')) ?></span></p>
                        <p><strong>Importance</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na($lead['importance'] ?? '')) ?></span></p>
                        <p><strong>Reg Matri Id</strong><span>:</span><span class="lg-val"><?= htmlspecialchars($na(matri_id_display(trim((string) ($lead['reg_matri_id'] ?? ''))))) ?></span></p>
                    </div>
                    <div class="details-column details-grid lg-col-right">
                        <p><strong>Email</strong><span>:</span> <?= htmlspecialchars($na($lead['email'] ?? '')) ?></p>
                        <p><strong>Address</strong><span>:</span> <?= htmlspecialchars($na($lead['address'] ?? '')) ?></p>
                        <p><strong>State Name</strong><span>:</span> <?= htmlspecialchars($na($lead['state'] ?? '')) ?></p>
                        <p><strong>Phone No. 1</strong><span>:</span> <?= htmlspecialchars($na($lead['phone1'] ?? '')) ?></p>
                        <p><strong>Phone No. 3</strong><span>:</span> <?= htmlspecialchars($na($lead['phone3'] ?? '')) ?></p>
                        <p><strong>Reg Date</strong><span>:</span> <?= htmlspecialchars($fmtDt($lead['reg_date'] ?? '')) ?></p>
                        <p><strong>Source Name</strong><span>:</span> <?= htmlspecialchars($na($lead['source_name'] ?? '')) ?></p>
                        <p><strong>Next Followup Date</strong><span>:</span> <?= htmlspecialchars($fmtDateOnly($lead['next_followup'] ?? '')) ?></p>
                        <p><strong>Created By</strong><span>:</span> <?= htmlspecialchars($na($lead['created_by'] ?? '')) ?></p>
                        <p><strong>Staff Username</strong><span>:</span> <?= htmlspecialchars($na($lead['staff_username'] ?? '')) ?></p>
                    </div>
                </div>
                <div class="action-row">
                    <button type="button" class="btn-action btn-action-teal" onclick="openLeadCommentPopup(<?= (int) $lead['id'] ?>, '<?= htmlspecialchars($lead['full_name'] ?? '', ENT_QUOTES) ?>')">Add Comment</button>
                    <button type="button" class="btn-action btn-action-amber" onclick="openLeadViewCommentsPopup(<?= (int) $lead['id'] ?>, '<?= htmlspecialchars($lead['full_name'] ?? '', ENT_QUOTES) ?>')">View Comments</button>
                    <a class="btn-action btn-action-cyan" href="<?= BASE_URL ?>/admin/lead-generation/edit?id=<?= (int) $lead['id'] ?>">Edit Lead</a>
                    <a class="btn-action btn-action-teal" href="<?= BASE_URL ?>/admin/lead-generation/task?lead_id=<?= (int) $lead['id'] ?>">Open Task</a>
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
        <div class="popup-header"><h3>Filter</h3><span class="close-popup" onclick="closeFilterPopup()">&times;</span></div>
        <div class="popup-body">
            <div class="form-group"><label>Interest</label><select class="form-control" id="advFilterInterest"><option value="">All</option><option value="In-Process-M">In-Process-M</option><option value="Registered">Registered</option><option value="Closed-M">Closed-M</option></select></div>
            <div class="form-group"><label>Team (contains)</label><input type="text" class="form-control" id="advFilterTeam" placeholder=""></div>
            <div class="form-group"><label>Importance</label><select class="form-control" id="advFilterImportance"><option value="">All</option><option value="Important">Important</option><option value="Normal">Normal</option><option value="Low">Low</option></select></div>
            <div class="form-group"><label>Country (contains)</label><input type="text" class="form-control" id="advFilterCountry" placeholder=""></div>
        </div>
        <div class="popup-footer"><button type="button" class="btn-submit" onclick="applyAdvFilter()">Apply</button><button type="button" class="btn-cancel" onclick="closeFilterPopup()">Close</button></div>
    </div>
</div>

<div id="filter1Popup" class="custom-popup-overlay" style="display:none;">
    <div class="custom-popup">
        <div class="popup-header"><h3>Filter1 — Interest</h3><span class="close-popup" onclick="closeFilter1Popup()">&times;</span></div>
        <div class="popup-body">
            <div class="form-group"><label>Quick filter by interest</label><select class="form-control" id="filter1Interest">
                <option value="">All (clear)</option>
                <option value="in-process-m">In-Process-M</option>
                <option value="registered">Registered</option>
                <option value="closed-m">Closed-M</option>
            </select></div>
        </div>
        <div class="popup-footer"><button type="button" class="btn-submit" onclick="applyFilter1()">Apply</button><button type="button" class="btn-cancel" onclick="closeFilter1Popup()">Close</button></div>
    </div>
</div>

<div id="commentPopup" class="custom-popup-overlay" style="display:none;"><div class="custom-popup custom-popup-lg"><div class="popup-header"><h3 id="commentPopupTitle">Add Comment</h3><span class="close-popup" onclick="closeLeadCommentPopup()">&times;</span></div><form method="POST" action="<?= BASE_URL ?>/admin/lead-generation/comment"><div class="popup-body"><input type="hidden" name="lead_id" id="comment_lead_id"><div class="form-group"><label>Type</label><select class="form-control" name="comment_type"><option value="general">General</option><option value="follow_up">Follow Up</option><option value="warning">Warning</option><option value="approval_note">Approval Note</option></select></div><div class="form-group"><label>Comment</label><textarea class="form-control" name="comment" rows="6" required></textarea></div></div><div class="popup-footer"><button type="submit" class="btn-submit">Save Comment</button><button type="button" class="btn-cancel" onclick="closeLeadCommentPopup()">Cancel</button></div></form></div></div>
<div id="viewCommentsPopup" class="custom-popup-overlay" style="display:none;"><div class="custom-popup custom-popup-xl"><div class="popup-header"><h3 id="viewCommentPopupTitle">View Comments</h3><span class="close-popup" onclick="closeLeadViewCommentsPopup()">&times;</span></div><div class="popup-body"><input type="hidden" id="view_comment_lead_id"><div class="row g-2 mb-3"><div class="col-md-4"><label>Type</label><select id="filter_comment_type" class="form-control"><option value="">All</option><option value="general">General</option><option value="follow_up">Follow Up</option><option value="warning">Warning</option><option value="approval_note">Approval Note</option></select></div><div class="col-md-4"><label>From</label><input type="date" id="filter_comment_from" class="form-control"></div><div class="col-md-4"><label>To</label><input type="date" id="filter_comment_to" class="form-control"></div></div><button type="button" class="btn btn-primary btn-sm mb-3" onclick="loadLeadComments()">Apply Filter</button><div id="commentsResults" style="max-height:380px;overflow:auto;"></div></div><div class="popup-footer"><button type="button" class="btn-cancel" onclick="closeLeadViewCommentsPopup()">Close</button></div></div></div>

<script>
const searchInput=document.getElementById("leadSearch");const sortSelect=document.getElementById("sortLeads");const showEntries=document.getElementById("showEntries");let advFilterState={interest:"",team:"",importance:"",country:""};
function cardMatchesAdv(c){if(advFilterState.interest){const v=(c.dataset.interest||"").toLowerCase().replace(/\s/g,"");if(v!==advFilterState.interest)return false;}if(advFilterState.team){if(!(c.dataset.team||"").includes(advFilterState.team))return false;}if(advFilterState.importance){if((c.dataset.importance||"")!==advFilterState.importance)return false;}if(advFilterState.country){if(!(c.dataset.country||"").includes(advFilterState.country))return false;}return true;}
let f1Interest="";
function cardMatchesF1(c){if(!f1Interest)return true;return(c.dataset.interest||"").replace(/\s/g,"")===f1Interest;}
function getCards(){return Array.from(document.querySelectorAll(".searchable-card"));}
function updateList(){const search=(searchInput?.value||"").toLowerCase();const limit=parseInt(showEntries?.value||"9999",10);const cards=getCards();let filtered=cards.filter((c)=>c.innerText.toLowerCase().includes(search)&&cardMatchesAdv(c)&&cardMatchesF1(c));const sort=sortSelect?.value||"latest_desc";filtered.sort((a,b)=>{if(sort==="latest_desc")return b.dataset.date-a.dataset.date;if(sort==="latest_asc")return a.dataset.date-b.dataset.date;if(sort==="name_asc")return a.dataset.name.localeCompare(b.dataset.name);if(sort==="name_desc")return b.dataset.name.localeCompare(a.dataset.name);return 0;});cards.forEach((c)=>c.style.display="none");filtered.slice(0,limit).forEach((c)=>c.style.display="block");}
searchInput?.addEventListener("keyup",updateList);sortSelect?.addEventListener("change",updateList);showEntries?.addEventListener("change",updateList);
document.getElementById("clearSearchBtn")?.addEventListener("click",()=>{if(searchInput)searchInput.value="";updateList();});
document.getElementById("searchBtn")?.addEventListener("click",updateList);
document.getElementById("selectAllLeads")?.addEventListener("change",function(){const vis=getCards().filter(c=>c.style.display!=="none");vis.forEach(cb=>{const x=cb.querySelector(".lead-checkbox");if(x)x.checked=this.checked;});});
document.getElementById("btnChangeInterest")?.addEventListener("click",function(){const ids=[...document.querySelectorAll(".lead-checkbox:checked")].map(c=>c.value);if(!ids.length){alert("Select at least one lead.");return;}document.getElementById("bulk_lead_ids").value=ids.join(",");document.getElementById("bulkInterestForm").submit();});
function openFilterPopup(){document.getElementById("filterPopup").style.display="flex";}function closeFilterPopup(){document.getElementById("filterPopup").style.display="none";}
function applyAdvFilter(){const i=document.getElementById("advFilterInterest").value;advFilterState.interest=i?i.toLowerCase().replace(/\s/g,""):"";advFilterState.team=(document.getElementById("advFilterTeam").value||"").toLowerCase();advFilterState.importance=(document.getElementById("advFilterImportance").value||"").toLowerCase();advFilterState.country=(document.getElementById("advFilterCountry").value||"").toLowerCase();closeFilterPopup();updateList();}
function openFilter1Popup(){document.getElementById("filter1Popup").style.display="flex";}function closeFilter1Popup(){document.getElementById("filter1Popup").style.display="none";}
function applyFilter1(){const v=document.getElementById("filter1Interest").value;f1Interest=v;closeFilter1Popup();updateList();}
function openLeadCommentPopup(leadId,name){document.getElementById("comment_lead_id").value=leadId;document.getElementById("commentPopupTitle").innerText="Add Comment — "+name;document.getElementById("commentPopup").style.display="flex";}
function closeLeadCommentPopup(){document.getElementById("commentPopup").style.display="none";}
function openLeadViewCommentsPopup(leadId,name){document.getElementById("view_comment_lead_id").value=leadId;document.getElementById("viewCommentPopupTitle").innerText="View Comments — "+name;document.getElementById("viewCommentsPopup").style.display="flex";loadLeadComments();}
function closeLeadViewCommentsPopup(){document.getElementById("viewCommentsPopup").style.display="none";}
function escapeHtml(str){return (str||"").replace(/[&<>"']/g,(m)=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#039;"}[m]));}
function loadLeadComments(){const leadId=document.getElementById("view_comment_lead_id").value;const type=document.getElementById("filter_comment_type").value;const from=document.getElementById("filter_comment_from").value;const to=document.getElementById("filter_comment_to").value;const url=`<?= BASE_URL ?>/admin/lead-generation/comments-json?lead_id=${encodeURIComponent(leadId)}&type=${encodeURIComponent(type)}&date_from=${encodeURIComponent(from)}&date_to=${encodeURIComponent(to)}`;fetch(url).then(r=>r.json()).then(data=>{const wrap=document.getElementById("commentsResults");if(!data.ok||!data.rows||data.rows.length===0){wrap.innerHTML="<div class=\"alert alert-warning mb-0\">No comments found.</div>";return;}wrap.innerHTML=data.rows.map((row)=>`<div class=\"comment-item\"><div class=\"comment-meta\"><strong>${escapeHtml(row.admin_name||"Admin")}</strong> | ${escapeHtml(row.comment_type||"general")} | ${escapeHtml(row.created_at||"")}</div><div>${escapeHtml(row.comment||"")}</div></div>`).join("");}).catch(()=>{document.getElementById("commentsResults").innerHTML="<div class=\"alert alert-danger mb-0\">Unable to load comments.</div>";});}
updateList();
</script>

<?php require __DIR__.'/partials/footer.php'; ?>
