<?php
$title = "Manage Accepted Member - ALL";
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

<style>
.admin-content{padding:14px;background:#efefef}.page-head{font-size:13px;font-weight:700;color:#535353;margin-bottom:8px}
.top-controls{background:#f8f8f8;padding:14px 14px 16px;border:1px solid #d7d7d7;border-radius:3px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.controls-row .btn{font-size:12px;padding:6px 14px;border-radius:3px;line-height:1.2}.controls-row .btn-primary{background:#0e98d3;border-color:#0e98d3}
.controls-row .input-group{display:flex;flex-wrap:nowrap;width:100%}.controls-row .input-group .form-control{height:34px;font-size:12px;border-color:#d8d8d8;min-width:0;flex:1 1 auto}.controls-row .input-group .btn{height:34px}
.am-top-row,.am-bottom-row{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:nowrap}
.am-search-wrap{flex:0 1 560px;max-width:560px;min-width:360px}.am-show-wrap{flex:0 0 auto}
.search-clear-btn{padding:6px 10px;background:#fff;color:#777}.show-entry-wrap{display:inline-flex;align-items:center}
.custom-tabs{border-bottom:1px solid #d7d7d7;padding-top:6px;gap:8px;display:flex;flex-wrap:wrap;margin-bottom:0}
.custom-tabs .nav-link{background:#56c8ed;border:1px solid #48bde4;border-bottom:0;border-radius:3px 3px 0 0;color:#fff;font-size:11px;font-weight:700;padding:8px 14px;min-width:112px;text-align:center}
.custom-tabs .nav-link small{display:block;font-size:10px;font-weight:600;color:#fff}
.table-wrap{background:#fff;border:1px solid #d7d7d7}
#acceptedTable thead th{background:#7f7f7f;color:#fff;font-size:12px;font-weight:600;padding:8px}
#acceptedTable tbody td{font-size:12px;color:#5a5a5a;padding:8px;background:#f7f7f7}
#acceptedTable tbody tr:nth-child(even) td{background:#f1f1f1}
@media(max-width:991px){.am-top-row,.am-bottom-row{flex-wrap:wrap}.am-search-wrap{flex:1 1 100%;max-width:100%;min-width:0}}
</style>

<script>
const rowSearch=document.getElementById('rowSearch');const showEntries=document.getElementById('showEntries');const rows=Array.from(document.querySelectorAll('.searchable-row'));
function updateRows(){const q=(rowSearch?.value||'').toLowerCase();const limit=parseInt(showEntries?.value||'9999',10);const filtered=rows.filter(r=>r.innerText.toLowerCase().includes(q));rows.forEach(r=>r.style.display='none');filtered.slice(0,limit).forEach(r=>r.style.display='table-row');}
rowSearch?.addEventListener('keyup',updateRows);showEntries?.addEventListener('change',updateRows);
document.getElementById('clearSearchBtn')?.addEventListener('click',()=>{if(rowSearch)rowSearch.value='';updateRows();});
document.getElementById('selectAllRows')?.addEventListener('change',function(){document.querySelectorAll('.row-checkbox').forEach(cb=>cb.checked=this.checked);});
updateRows();
</script>

<?php require __DIR__.'/partials/footer.php'; ?>
