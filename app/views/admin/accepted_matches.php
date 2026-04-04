<?php
$title = "Manage Accepted Member - ALL";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

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
        <div class="page-head">Manage Accepted Member - ALL</div>

        <div class="top-controls">
            <div class="controls-row controls-row-top am-top-row">
                <div class="am-search-wrap">
                    <div class="input-group">
                        <input type="text" id="rowSearch" class="form-control" placeholder="Search here...">
                        <button class="btn btn-light border search-clear-btn" type="button" id="clearSearchBtn"><i class="fa fa-times"></i></button>
                        <button class="btn btn-primary" type="button"><i class="bi bi-search"></i> Search</button>
                    </div>
                </div>
            </div>
            <div class="controls-row controls-row-bottom am-bottom-row mt-3">
                <div class="am-show-wrap"><div class="show-entry-wrap"><label class="me-2 mb-0">Show</label><select id="showEntries" class="form-select d-inline-block w-auto"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></div></div>
            </div>
            <ul class="nav nav-tabs custom-tabs mt-3">
                <li class="nav-item"><a class="nav-link active" href="<?= BASE_URL ?>/admin/accepted-matches">All <small>(<?= (int)count($rows) ?>)</small></a></li>
            </ul>
        </div>

        <div class="table-wrap mt-3">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0" id="acceptedTable">
                    <thead>
                        <tr>
                            <th style="width:42px;"><input type="checkbox" id="selectAllRows"></th>
                            <th>My Id</th>
                            <th>Other Id</th>
                            <th>Staff</th>
                            <th>Team</th>
                            <th>Action Date</th>
                            <th style="width:72px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr class="searchable-row">
                            <td><input type="checkbox" class="row-checkbox"></td>
                            <td><?= htmlspecialchars(trim(($r['my_first_name'] ?? '-') . ' ' . ($r['my_last_name'] ?? '')) . ' - (NG' . (int)($r['assigned_to'] ?? 0) . ')') ?></td>
                            <td><?= htmlspecialchars(trim(($r['other_first_name'] ?? '-') . ' ' . ($r['other_last_name'] ?? '')) . ' - (NG' . (int)($r['assigned_member'] ?? 0) . ')') ?></td>
                            <td><?= htmlspecialchars((string)($r['staff_name'] ?? 'admin')) ?></td>
                            <td><?= htmlspecialchars((string)($r['team_name'] ?? 'admin')) ?></td>
                            <td><?= htmlspecialchars((string)($r['accepted_at'] ?? $r['updated_at'] ?? '-')) ?></td>
                            <td class="text-center"><a href="javascript:void(0)" class="text-danger"><i class="fa fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<script>
const rowSearch=document.getElementById('rowSearch');const showEntries=document.getElementById('showEntries');const rows=Array.from(document.querySelectorAll('.searchable-row'));
function updateRows(){const q=(rowSearch?.value||'').toLowerCase();const limit=parseInt(showEntries?.value||'9999',10);const filtered=rows.filter(r=>r.innerText.toLowerCase().includes(q));rows.forEach(r=>r.style.display='none');filtered.slice(0,limit).forEach(r=>r.style.display='table-row');}
rowSearch?.addEventListener('keyup',updateRows);showEntries?.addEventListener('change',updateRows);
document.getElementById('clearSearchBtn')?.addEventListener('click',()=>{if(rowSearch)rowSearch.value='';updateRows();});
document.getElementById('selectAllRows')?.addEventListener('change',function(){document.querySelectorAll('.row-checkbox').forEach(cb=>cb.checked=this.checked);});
updateRows();
</script>

<?php require __DIR__.'/partials/footer.php'; ?>
