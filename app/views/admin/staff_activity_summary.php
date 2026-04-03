<?php
$title = 'Staff Activity Summary';
$reportTotal = count($summaryRows);

$fmtDate = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d H:i:s', $t) : '';
};

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$staffOpts = $deptOpts = [];
foreach ($summaryRows as $r) {
    $vs = trim((string) ($r['staff_name'] ?? ''));
    if ($vs !== '' && !in_array($vs, $staffOpts, true)) {
        $staffOpts[] = $vs;
    }
    $vd = trim((string) ($r['department_name'] ?? ''));
    if ($vd !== '' && !in_array($vd, $deptOpts, true)) {
        $deptOpts[] = $vd;
    }
}
sort($staffOpts);
sort($deptOpts);

$exportUrl = BASE_URL . '/admin/reports/staff-management/staff-activity-summary/export';
?>

<div class="admin-main sa-page">
<div class="admin-topbar sa-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Activity Summary - ALL</div>
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
<div class="admin-content sa-content">
    <div class="container-fluid">
        <div class="sa-report-panel">
            <div class="sa-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="sa-search-block flex-grow-1" style="min-width: 280px; max-width: 520px;">
                    <div class="input-group sa-search-group">
                        <input type="text" id="ssSearch" class="form-control sa-input" placeholder="Search here...">
                        <button class="btn btn-light border sa-clear flex-shrink-0" type="button" id="ssClearSearch" aria-label="Clear"><i class="fa fa-times"></i></button>
                        <button class="btn sa-btn-search flex-shrink-0" type="button" id="ssSearchBtn"><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="sa-actions flex-shrink-0 d-flex align-items-stretch gap-2">
                    <a href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn sa-btn-export d-flex align-items-center"><i class="bi bi-download me-1"></i> Export</a>
                    <button type="button" class="btn sa-btn-filter d-flex align-items-center" id="ssOpenFilter"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>

            <div class="sa-show-row mb-3">
                <span class="sa-show-wrap"><label class="me-2 mb-0">Show</label><select id="ssShowEntries" class="form-select form-select-sm d-inline-block w-auto sa-select"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>

            <ul class="nav nav-tabs sa-tabs mb-0">
                <li class="nav-item">
                    <span class="nav-link active">All <small>(<?= (int) $reportTotal ?>)</small></span>
                </li>
            </ul>

            <div class="table-responsive sa-table-wrap">
                <table class="table table-bordered mb-0 sa-table">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Team Name</th>
                            <th>Department Name</th>
                            <th>Total Activities</th>
                            <th class="sa-th-sort" id="ssSortLast" role="button" tabindex="0">Last Activity <span class="sa-sort-arrows ms-1" aria-hidden="true"><i class="fa fa-sort-up"></i><i class="fa fa-sort-down"></i></span></th>
                        </tr>
                    </thead>
                    <tbody id="ssTbody">
                        <?php foreach ($summaryRows as $R):
                            $last = $R['last_activity_at'] ?? '';
                            $ts = $last !== '' && strtotime((string) $last) ? strtotime((string) $last) : 0;
                            $searchBlob = strtolower(implode(' ', [
                                $R['staff_name'] ?? '',
                                $R['team_name'] ?? '',
                                $R['department_name'] ?? '',
                                (string) ($R['total_activities'] ?? ''),
                                $fmtDate($last),
                            ]));
                            $staffVal = strtolower(trim((string) ($R['staff_name'] ?? '')));
                            $deptVal = strtolower(trim((string) ($R['department_name'] ?? '')));
                            ?>
                        <tr class="ss-row" data-sort-ts="<?= (int) $ts ?>" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-staff="<?= htmlspecialchars($staffVal, ENT_QUOTES, 'UTF-8') ?>" data-dept="<?= htmlspecialchars($deptVal, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) ($R['staff_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['team_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['department_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['total_activities'] ?? '0')) ?></td>
                            <td><?= htmlspecialchars($fmtDate($last)) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="sa-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="ssInfo">Showing 0 to 0 of 0 entries</div>
                <nav class="sa-pagination-wrap" id="ssPaginationWrap" style="display:none;" aria-label="Table pages">
                    <ul class="pagination pagination-sm mb-0" id="ssPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="ssFilterPopup" class="sa-popup-overlay" style="display:none;">
    <div class="sa-popup">
        <div class="sa-popup-header"><h3 class="sa-popup-title">Filter</h3><span class="sa-popup-close" id="ssCloseFilter">&times;</span></div>
        <div class="sa-popup-body">
            <div class="mb-3">
                <label class="form-label small">Staff Name</label>
                <select id="ssFilterStaff" class="form-select form-select-sm">
                    <option value="">All staff</option>
                    <?php foreach ($staffOpts as $st): ?>
                        <option value="<?= htmlspecialchars(strtolower($st)) ?>"><?= htmlspecialchars($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small">Department Name</label>
                <select id="ssFilterDept" class="form-select form-select-sm">
                    <option value="">All departments</option>
                    <?php foreach ($deptOpts as $d): ?>
                        <option value="<?= htmlspecialchars(strtolower($d)) ?>"><?= htmlspecialchars($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="sa-popup-footer">
            <button type="button" class="btn sa-btn-search btn-sm" id="ssApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="ssCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .sa-page .sa-content { padding: 20px 18px; background: #e8e8e8; }
    .sa-report-panel {
        background: #fff;
        border: 1px solid #d5d5d5;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        padding: 20px 22px 8px;
    }
    .sa-page .sa-topbar {
        justify-content: space-between;
        padding-left: 12px;
        padding-right: 16px;
        background: #fff;
        border-bottom: 1px solid #e5e5e5;
    }
    .sa-page .admin-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        letter-spacing: 0.01em;
    }
    .sa-toolbar { margin-bottom: 12px !important; }
    .sa-search-group { display: flex; flex-wrap: nowrap; align-items: stretch; width: 100%; }
    .sa-input {
        flex: 1 1 auto; min-width: 0; height: 38px; font-size: 13px;
        border: 1px solid #ccc; border-radius: 4px 0 0 4px;
    }
    .sa-clear { height: 38px; padding: 0 12px; border-left: 0; color: #666; background: #fff; }
    .sa-btn-search {
        background: #0096C7 !important; border: 1px solid #0096C7 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 18px; font-weight: 600;
        border-radius: 0 4px 4px 0; white-space: nowrap;
    }
    .sa-btn-search:hover { filter: brightness(0.95); color: #fff !important; }
    .sa-btn-export {
        background: #f0ad4e !important; border: 1px solid #eea236 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; white-space: nowrap;
    }
    .sa-btn-export:hover { filter: brightness(0.96); color: #fff !important; }
    .sa-btn-filter {
        background: #5BC0DE !important; border: 1px solid #4fb3d4 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; white-space: nowrap;
    }
    .sa-btn-filter:hover { filter: brightness(0.96); color: #fff !important; }
    .sa-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .sa-select { height: 32px; font-size: 13px; min-width: 72px; }
    .sa-tabs { border-bottom: 1px solid #c8c8c8; padding-top: 4px; gap: 6px; }
    .sa-tabs .nav-link {
        background: #ebebeb; border: 1px solid #c8c8c8; border-bottom: 0; border-radius: 4px 4px 0 0;
        color: #333; font-size: 12px; font-weight: 700; padding: 9px 18px; min-width: 120px; text-align: center; cursor: default;
    }
    .sa-tabs .nav-link.active {
        background: #fff; color: #0096C7;
        border-top: 3px solid #5BC0DE;
        border-color: #c8c8c8 #c8c8c8 #fff; margin-bottom: -1px; padding-top: 7px;
    }
    .sa-tabs .nav-link small { font-size: 11px; font-weight: 600; color: #555; }
    .sa-tabs .nav-link.active small { color: #0096C7; }
    .sa-table-wrap { border: 1px solid #d0d0d0; border-top: 0; border-radius: 0 0 4px 4px; }
    .sa-table { font-size: 13px; color: #333; }
    .sa-table thead th {
        background: #777777 !important; color: #fff !important; font-weight: 700;
        border-color: #6a6a6a !important; padding: 12px 14px; vertical-align: middle;
    }
    .sa-th-sort { cursor: pointer; user-select: none; white-space: nowrap; }
    .sa-sort-arrows {
        display: inline-flex;
        flex-direction: column;
        font-size: 9px;
        line-height: 0.7;
        vertical-align: middle;
        opacity: 0.95;
    }
    .sa-sort-arrows .fa { display: block; }
    .sa-table tbody td { padding: 11px 14px; vertical-align: middle; border-color: #ddd; background: #fff; }
    .sa-table tbody tr:nth-child(even) td { background: #f4f4f4; }
    .sa-footer { border-top: 1px solid #eaeaea; }
    .sa-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .sa-popup-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1050;
        display: flex; align-items: center; justify-content: center;
    }
    .sa-popup { background: #fff; border-radius: 6px; width: 400px; max-width: 92%; box-shadow: 0 8px 28px rgba(0,0,0,.18); }
    .sa-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #e8e8e8; }
    .sa-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .sa-popup-close { cursor: pointer; font-size: 22px; line-height: 1; color: #666; }
    .sa-popup-body { padding: 16px 18px; }
    .sa-popup-footer { padding: 12px 18px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #eee; }
    @media (max-width: 767px) {
        .sa-toolbar .sa-actions { width: 100%; flex-direction: column; }
        .sa-toolbar .sa-actions .btn { width: 100%; justify-content: center; }
    }
</style>

<script>
(function(){
    var tbody = document.getElementById('ssTbody');
    var searchInput = document.getElementById('ssSearch');
    var showSel = document.getElementById('ssShowEntries');
    var infoEl = document.getElementById('ssInfo');
    var pagWrap = document.getElementById('ssPaginationWrap');
    var pagUl = document.getElementById('ssPagination');
    var staffFilter = '', deptFilter = '';
    var currentPage = 1;
    var dateSortDesc = true;

    function rows() { return Array.from(document.querySelectorAll('.ss-row')); }

    function sortRowsInDom() {
        var r = rows();
        r.sort(function(a, b) {
            var ta = parseInt(a.getAttribute('data-sort-ts'), 10) || 0;
            var tb = parseInt(b.getAttribute('data-sort-ts'), 10) || 0;
            return dateSortDesc ? (tb - ta) : (ta - tb);
        });
        r.forEach(function(row) { tbody.appendChild(row); });
    }

    function filtered() {
        var q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        return rows().filter(function(row) {
            if (staffFilter && (row.getAttribute('data-staff') || '') !== staffFilter) return false;
            if (deptFilter && (row.getAttribute('data-dept') || '') !== deptFilter) return false;
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

    function render() {
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
        }

        infoEl.textContent = total === 0
            ? 'Showing 0 to 0 of 0 entries'
            : ('Showing ' + start + ' to ' + end + ' of ' + total + ' entries');

        renderPagination(total, per, pages, currentPage);
    }

    function resetPage() { currentPage = 1; render(); }

    document.getElementById('ssSortLast') && document.getElementById('ssSortLast').addEventListener('click', function() {
        dateSortDesc = !dateSortDesc;
        sortRowsInDom();
        resetPage();
    });

    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('ssSearchBtn') && document.getElementById('ssSearchBtn').addEventListener('click', resetPage);
    document.getElementById('ssClearSearch') && document.getElementById('ssClearSearch').addEventListener('click', function() {
        searchInput.value = '';
        resetPage();
    });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });

    document.getElementById('ssOpenFilter') && document.getElementById('ssOpenFilter').addEventListener('click', function() {
        document.getElementById('ssFilterPopup').style.display = 'flex';
    });
    function closeFilter() { document.getElementById('ssFilterPopup').style.display = 'none'; }
    document.getElementById('ssCloseFilter') && document.getElementById('ssCloseFilter').addEventListener('click', closeFilter);
    document.getElementById('ssCancelFilter') && document.getElementById('ssCancelFilter').addEventListener('click', closeFilter);
    document.getElementById('ssApplyFilter') && document.getElementById('ssApplyFilter').addEventListener('click', function() {
        var s = document.getElementById('ssFilterStaff');
        var d = document.getElementById('ssFilterDept');
        staffFilter = s && s.value ? s.value : '';
        deptFilter = d && d.value ? d.value : '';
        closeFilter();
        resetPage();
    });

    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
