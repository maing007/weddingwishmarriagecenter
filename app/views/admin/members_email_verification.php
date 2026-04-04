<?php
$title = 'Members Email Verification';

$fmtDateTime = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d H:i:s', $t) : '';
};

$rows = $rows ?? [];
$cntAll = count($rows);
$cntVerified = count(array_filter($rows, static fn ($r) => !empty($r['is_verified'])));
$cntUnverified = count(array_filter($rows, static fn ($r) => empty($r['is_verified'])));
$cntPaidVerified = count(array_filter($rows, static fn ($r) => !empty($r['is_paid']) && !empty($r['is_verified'])));
$cntPaidUnverified = count(array_filter($rows, static fn ($r) => !empty($r['is_paid']) && empty($r['is_verified'])));
$isDatasetEmpty = $cntAll === 0;

$teamOpts = [];
foreach ($rows as $r) {
    $t = trim((string) ($r['team_assign_id'] ?? ''));
    if ($t !== '' && !in_array($t, $teamOpts, true)) {
        $teamOpts[] = $t;
    }
}
sort($teamOpts);

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<div class="admin-main mbr-page">
<div class="admin-topbar mbr-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title" id="mbrPageTitle">Manage Members - Verified</div>
    </div>
    <div class="admin-profile" id="adminProfileTrigger">
        <div class="admin-profile-box"><span><?= htmlspecialchars($this->displayadminname(), ENT_QUOTES, 'UTF-8') ?></span><i class="fa fa-user"></i></div>
        <div class="admin-dropdown" id="adminDropdown">
            <a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
            <a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>
<main class="admin-page">
<div class="admin-content mbr-content">
    <div class="container-fluid">
        <div class="mbr-report-panel">
            <div class="mbr-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="mbr-search-block flex-grow-1" style="min-width: 280px; max-width: 520px;">
                    <div class="input-group mbr-search-group">
                        <input type="text" id="mbrSearch" class="form-control mbr-input" placeholder="Search here..." <?= $isDatasetEmpty ? 'disabled' : '' ?>>
                        <button class="btn btn-light border mbr-clear flex-shrink-0" type="button" id="mbrClearSearch" aria-label="Clear" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="fa fa-times"></i></button>
                        <button class="btn mbr-btn-search flex-shrink-0" type="button" id="mbrSearchBtn" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="mbr-actions flex-shrink-0 d-flex align-items-stretch">
                    <button type="button" class="btn mbr-btn-filter d-flex align-items-center" id="mbrOpenFilter" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>

            <div class="mbr-show-row mb-3">
                <span class="mbr-show-wrap"><label class="me-2 mb-0">Show</label><select id="mbrShowEntries" class="form-select form-select-sm d-inline-block w-auto mbr-select" <?= $isDatasetEmpty ? 'disabled' : '' ?>><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>

            <ul class="nav nav-tabs mbr-tabs mb-0 flex-wrap" id="mbrTabs">
                <li class="nav-item">
                    <button type="button" class="nav-link mbr-tab" data-tab="all">All <small>(<?= (int) $cntAll ?>)</small></button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link mbr-tab active" data-tab="verified">Verified List <small>(<?= (int) $cntVerified ?>)</small></button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link mbr-tab" data-tab="unverified">Unverified List <small>(<?= (int) $cntUnverified ?>)</small></button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link mbr-tab" data-tab="paid_verified">Paid verified List <small>(<?= (int) $cntPaidVerified ?>)</small></button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link mbr-tab" data-tab="paid_unverified">Paid unverified List <small>(<?= (int) $cntPaidUnverified ?>)</small></button>
                </li>
            </ul>

            <div class="table-responsive mbr-table-wrap">
                <table class="table table-bordered mb-0 mbr-table">
                    <thead>
                        <tr>
                            <th>Matri Id</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th class="mbr-th-sort" id="mbrSortLogin" role="button" tabindex="0">Last Login <span class="mbr-sort-arrows ms-1" aria-hidden="true"><i class="fa fa-sort-up"></i><i class="fa fa-sort-down"></i></span></th>
                            <th>Team Assign Id</th>
                        </tr>
                    </thead>
                    <tbody id="mbrTbody">
                        <?php if ($isDatasetEmpty): ?>
                        <tr class="mbr-empty-row"><td colspan="6">No Record found</td></tr>
                        <?php else: ?>
                        <?php foreach ($rows as $R):
                            $ll = $R['last_login'] ?? null;
                            $ts = ($ll !== null && $ll !== '' && strtotime((string) $ll)) ? strtotime((string) $ll) : 0;
                            $v = !empty($R['is_verified']) ? '1' : '0';
                            $p = !empty($R['is_paid']) ? '1' : '0';
                            $searchBlob = strtolower(implode(' ', [
                                $fmtDateTime($ll),
                                $R['matri_id'] ?? '',
                                $R['username'] ?? '',
                                $R['email'] ?? '',
                                $R['mobile'] ?? '',
                                $R['team_assign_id'] ?? '',
                            ]));
                            $teamVal = strtolower(trim((string) ($R['team_assign_id'] ?? '')));
                            ?>
                        <tr class="mbr-row" data-sort-ts="<?= (int) $ts ?>" data-verified="<?= $v ?>" data-paid="<?= $p ?>" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-team="<?= htmlspecialchars($teamVal, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars(matri_id_display((string) ($R['matri_id'] ?? ''), (int) ($R['id'] ?? 0))) ?></td>
                            <td><?= htmlspecialchars((string) ($R['username'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['email'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['mobile'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($fmtDateTime($ll)) ?></td>
                            <td><?= htmlspecialchars((string) ($R['team_assign_id'] ?? '')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mbr-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="mbrInfo"><?= $isDatasetEmpty ? 'Showing 1 to 0 of 0 entries' : 'Showing 0 to 0 of 0 entries' ?></div>
                <nav class="mbr-pagination-wrap" id="mbrPaginationWrap" style="display:none;" aria-label="Table pages">
                    <ul class="pagination pagination-sm mb-0" id="mbrPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="mbrFilterPopup" class="mbr-popup-overlay" style="display:none;">
    <div class="mbr-popup">
        <div class="mbr-popup-header"><h3 class="mbr-popup-title">Filter</h3><span class="mbr-popup-close" id="mbrCloseFilter">&times;</span></div>
        <div class="mbr-popup-body">
            <div class="mb-3">
                <label class="form-label small">Team Assign Id</label>
                <select id="mbrFilterTeam" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($teamOpts as $tm): ?>
                        <option value="<?= htmlspecialchars(strtolower($tm)) ?>"><?= htmlspecialchars($tm) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mbr-popup-footer">
            <button type="button" class="btn mbr-btn-search btn-sm" id="mbrApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="mbrCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .mbr-page .mbr-content { padding: 20px 18px; background: #e8e8e8; }
    .mbr-report-panel {
        background: #fff;
        border: 1px solid #d5d5d5;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        padding: 20px 22px 8px;
    }
    .mbr-page .mbr-topbar {
        justify-content: space-between;
        padding-left: 12px;
        padding-right: 16px;
        background: #fff;
        border-bottom: 1px solid #e5e5e5;
    }
    .mbr-page .admin-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        letter-spacing: 0.01em;
    }
    .mbr-toolbar { margin-bottom: 12px !important; }
    .mbr-search-group { display: flex; flex-wrap: nowrap; align-items: stretch; width: 100%; }
    .mbr-input {
        flex: 1 1 auto; min-width: 0; height: 38px; font-size: 13px;
        border: 1px solid #ccc; border-radius: 4px 0 0 4px;
    }
    .mbr-clear { height: 38px; padding: 0 12px; border-left: 0; color: #666; background: #fff; }
    .mbr-btn-search {
        background: #0096C7 !important; border: 1px solid #0096C7 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 18px; font-weight: 600;
        border-radius: 0 4px 4px 0; white-space: nowrap;
    }
    .mbr-btn-search:hover { filter: brightness(0.95); color: #fff !important; }
    .mbr-btn-filter {
        background: #5BC0DE !important; border: 1px solid #4fb3d4 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; white-space: nowrap;
    }
    .mbr-btn-filter:hover { filter: brightness(0.96); color: #fff !important; }
    .mbr-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .mbr-select { height: 32px; font-size: 13px; min-width: 72px; }
    .mbr-tabs { border-bottom: 1px solid #c8c8c8; padding-top: 4px; gap: 4px; }
    .mbr-tabs .nav-item { margin-bottom: -1px; }
    .mbr-tabs .nav-link.mbr-tab {
        background: #ebebeb;
        border: 1px solid #c8c8c8;
        border-bottom: 0;
        border-radius: 4px 4px 0 0;
        color: #111;
        font-size: 12px;
        font-weight: 600;
        padding: 9px 14px;
        min-height: 40px;
        cursor: pointer;
    }
    .mbr-tabs .nav-link.mbr-tab:hover { background: #e2e2e2; color: #111; }
    .mbr-tabs .nav-link.mbr-tab.active {
        background: #fff;
        color: #0096C7;
        border-top: 3px solid #0096C7;
        border-color: #c8c8c8 #c8c8c8 #fff;
        margin-bottom: -1px;
        padding-top: 7px;
    }
    .mbr-tabs .nav-link.mbr-tab small { font-size: 11px; font-weight: 600; color: #555; }
    .mbr-tabs .nav-link.mbr-tab.active small { color: #0096C7; }
    .mbr-table-wrap { border: 1px solid #d0d0d0; border-top: 0; border-radius: 0 0 4px 4px; }
    .mbr-table { font-size: 13px; color: #333; }
    .mbr-table thead th {
        background: #888888 !important; color: #fff !important; font-weight: 700;
        border-color: #7a7a7a !important; padding: 12px 14px; vertical-align: middle;
    }
    .mbr-th-sort { cursor: pointer; user-select: none; white-space: nowrap; }
    .mbr-sort-arrows {
        display: inline-flex; flex-direction: column; font-size: 9px; line-height: 0.7;
        vertical-align: middle; opacity: 0.95;
    }
    .mbr-sort-arrows .fa { display: block; }
    .mbr-table tbody td { padding: 11px 14px; vertical-align: middle; border-color: #ddd; background: #fff; }
    .mbr-table tbody tr:nth-child(even):not(.mbr-empty-row):not(.mbr-filter-empty) td { background: #f4f4f4; }
    tr.mbr-empty-row td, tr.mbr-filter-empty td {
        background: #f8d7da !important;
        color: #721c24 !important;
        font-weight: 600;
        text-align: center;
        padding: 14px;
    }
    .mbr-footer { border-top: 1px solid #eaeaea; }
    .mbr-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .mbr-popup-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1050;
        display: flex; align-items: center; justify-content: center;
    }
    .mbr-popup { background: #fff; border-radius: 6px; width: 400px; max-width: 92%; box-shadow: 0 8px 28px rgba(0,0,0,.18); }
    .mbr-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #e8e8e8; }
    .mbr-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .mbr-popup-close { cursor: pointer; font-size: 22px; line-height: 1; color: #666; }
    .mbr-popup-body { padding: 16px 18px; }
    .mbr-popup-footer { padding: 12px 18px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #eee; }
    @media (max-width: 767px) {
        .mbr-toolbar .mbr-actions { width: 100%; }
        .mbr-toolbar .mbr-actions .btn { width: 100%; justify-content: center; }
    }
</style>

<script>
(function(){
    var titles = {
        all: 'Manage Members - ALL',
        verified: 'Manage Members - Verified',
        unverified: 'Manage Members - Unverified',
        paid_verified: 'Manage Members - Paid Verified',
        paid_unverified: 'Manage Members - Paid Unverified'
    };
    var tbody = document.getElementById('mbrTbody');
    var titleEl = document.getElementById('mbrPageTitle');
    var searchInput = document.getElementById('mbrSearch');
    var showSel = document.getElementById('mbrShowEntries');
    var infoEl = document.getElementById('mbrInfo');
    var pagWrap = document.getElementById('mbrPaginationWrap');
    var pagUl = document.getElementById('mbrPagination');
    var activeTab = 'verified';
    var teamFilter = '';
    var currentPage = 1;
    var loginSortDesc = true;

    function rows() { return Array.from(document.querySelectorAll('.mbr-row')); }

    function tabMatch(row) {
        var v = row.getAttribute('data-verified') === '1';
        var p = row.getAttribute('data-paid') === '1';
        switch (activeTab) {
            case 'all': return true;
            case 'verified': return v;
            case 'unverified': return !v;
            case 'paid_verified': return p && v;
            case 'paid_unverified': return p && !v;
            default: return true;
        }
    }

    function sortRowsInDom() {
        var r = rows();
        r.sort(function(a, b) {
            var ta = parseInt(a.getAttribute('data-sort-ts'), 10) || 0;
            var tb = parseInt(b.getAttribute('data-sort-ts'), 10) || 0;
            return loginSortDesc ? (tb - ta) : (ta - tb);
        });
        r.forEach(function(row) { tbody.appendChild(row); });
    }

    function filtered() {
        var q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        return rows().filter(function(row) {
            if (!tabMatch(row)) return false;
            if (teamFilter && (row.getAttribute('data-team') || '') !== teamFilter) return false;
            return !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
        });
    }

    function pageSizeRaw() {
        var v = parseInt(showSel && showSel.value ? showSel.value : '10', 10);
        return isNaN(v) ? 10 : v;
    }

    function renderPagination(total, per, pages, page) {
        pagUl.innerHTML = '';
        if (pages <= 1) { pagWrap.style.display = 'none'; return; }
        pagWrap.style.display = 'block';
        function li(label, p, disabled, active) {
            var l = document.createElement('li');
            l.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
            var a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = label;
            if (!disabled && !active) {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentPage = p;
                    render();
                });
            }
            l.appendChild(a);
            return l;
        }
        pagUl.appendChild(li('Previous', Math.max(1, page - 1), page <= 1, false));
        for (var p = 1; p <= pages; p++) {
            pagUl.appendChild(li(String(p), p, false, p === page));
        }
        pagUl.appendChild(li('Next', Math.min(pages, page + 1), page >= pages, false));
    }

    function removeFilterEmpty() {
        var x = tbody.querySelector('.mbr-filter-empty');
        if (x) x.remove();
    }

    function showFilterEmpty() {
        removeFilterEmpty();
        var tr = document.createElement('tr');
        tr.className = 'mbr-filter-empty mbr-empty-row';
        tr.innerHTML = '<td colspan="6">No Record found</td>';
        tbody.appendChild(tr);
    }

    function render() {
        if (rows().length === 0) {
            return;
        }

        removeFilterEmpty();
        var emptyStatic = tbody.querySelector('.mbr-empty-row');
        if (emptyStatic) emptyStatic.style.display = 'none';

        var all = filtered();
        var total = all.length;
        var rawPer = pageSizeRaw();
        var showAll = rawPer >= 9999;
        var per = showAll ? total : Math.max(1, rawPer);
        var pages = showAll || total === 0 ? 1 : Math.max(1, Math.ceil(total / per));
        if (currentPage > pages) currentPage = pages;
        if (currentPage < 1) currentPage = 1;

        rows().forEach(function(row) { row.style.display = 'none'; });

        var start = 0;
        var end = 0;
        if (total > 0) {
            if (showAll) {
                all.forEach(function(row) { row.style.display = ''; });
                start = 1;
                end = total;
            } else {
                var off = (currentPage - 1) * per;
                var slice = all.slice(off, off + per);
                slice.forEach(function(row) { row.style.display = ''; });
                start = off + 1;
                end = off + slice.length;
            }
            infoEl.textContent = 'Showing ' + start + ' to ' + end + ' of ' + total + ' entries';
            renderPagination(total, per, pages, currentPage);
        } else {
            showFilterEmpty();
            infoEl.textContent = 'Showing 0 to 0 of 0 entries';
            pagWrap.style.display = 'none';
        }
    }

    function resetPage() { currentPage = 1; render(); }

    function setActiveTab(tab) {
        activeTab = tab;
        titleEl.textContent = titles[tab] || titles.all;
        document.querySelectorAll('.mbr-tab').forEach(function(btn) {
            btn.classList.toggle('active', btn.getAttribute('data-tab') === tab);
        });
        resetPage();
    }

    document.querySelectorAll('.mbr-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = btn.getAttribute('data-tab');
            if (tab) setActiveTab(tab);
        });
    });

    if (rows().length === 0) {
        document.querySelectorAll('.mbr-tab').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tab = btn.getAttribute('data-tab');
                if (tab) {
                    activeTab = tab;
                    titleEl.textContent = titles[tab] || titles.all;
                    document.querySelectorAll('.mbr-tab').forEach(function(b) {
                        b.classList.toggle('active', b.getAttribute('data-tab') === tab);
                    });
                }
            });
        });
        return;
    }

    document.getElementById('mbrSortLogin') && document.getElementById('mbrSortLogin').addEventListener('click', function() {
        loginSortDesc = !loginSortDesc;
        sortRowsInDom();
        resetPage();
    });

    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('mbrSearchBtn') && document.getElementById('mbrSearchBtn').addEventListener('click', resetPage);
    document.getElementById('mbrClearSearch') && document.getElementById('mbrClearSearch').addEventListener('click', function() {
        searchInput.value = '';
        resetPage();
    });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });

    document.getElementById('mbrOpenFilter') && document.getElementById('mbrOpenFilter').addEventListener('click', function() {
        document.getElementById('mbrFilterPopup').style.display = 'flex';
    });
    function closeFilter() { document.getElementById('mbrFilterPopup').style.display = 'none'; }
    document.getElementById('mbrCloseFilter') && document.getElementById('mbrCloseFilter').addEventListener('click', closeFilter);
    document.getElementById('mbrCancelFilter') && document.getElementById('mbrCancelFilter').addEventListener('click', closeFilter);
    document.getElementById('mbrApplyFilter') && document.getElementById('mbrApplyFilter').addEventListener('click', function() {
        var t = document.getElementById('mbrFilterTeam');
        teamFilter = t && t.value ? t.value : '';
        closeFilter();
        resetPage();
    });

    titleEl.textContent = titles[activeTab];
    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
