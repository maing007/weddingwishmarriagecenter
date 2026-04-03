<?php
$title = 'Meeting Summary';
$reportTotal = count($meetingRows);

$fmtDate = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d', $t) : '';
};

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$arrangedOpts = $statusOpts = [];
foreach ($meetingRows as $r) {
    $a = trim((string) ($r['arranged_by'] ?? ''));
    if ($a !== '' && !in_array($a, $arrangedOpts, true)) {
        $arrangedOpts[] = $a;
    }
    $st = trim((string) ($r['meeting_status'] ?? ''));
    if ($st !== '' && !in_array($st, $statusOpts, true)) {
        $statusOpts[] = $st;
    }
}
sort($arrangedOpts);
sort($statusOpts);

$isDatasetEmpty = $reportTotal === 0;
?>

<div class="admin-main mts-page">
<div class="admin-topbar mts-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Meeting Summary - ALL</div>
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
<div class="admin-content mts-content">
    <div class="container-fluid">
        <div class="mts-report-panel">
            <div class="mts-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="mts-search-block flex-grow-1" style="min-width: 280px; max-width: 520px;">
                    <div class="input-group mts-search-group">
                        <input type="text" id="mtsSearch" class="form-control mts-input" placeholder="Search here..." <?= $isDatasetEmpty ? 'disabled' : '' ?>>
                        <button class="btn btn-light border mts-clear flex-shrink-0" type="button" id="mtsClearSearch" aria-label="Clear" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="fa fa-times"></i></button>
                        <button class="btn mts-btn-search flex-shrink-0" type="button" id="mtsSearchBtn" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="mts-actions flex-shrink-0 d-flex align-items-stretch">
                    <button type="button" class="btn mts-btn-filter d-flex align-items-center" id="mtsOpenFilter" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>

            <div class="mts-show-row mb-3">
                <span class="mts-show-wrap"><label class="me-2 mb-0">Show</label><select id="mtsShowEntries" class="form-select form-select-sm d-inline-block w-auto mts-select" <?= $isDatasetEmpty ? 'disabled' : '' ?>><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>

            <ul class="nav nav-tabs mts-tabs mb-0">
                <li class="nav-item">
                    <span class="nav-link active">All <small>(<?= (int) $reportTotal ?>)</small></span>
                </li>
            </ul>

            <div class="table-responsive mts-table-wrap">
                <table class="table table-bordered mb-0 mts-table">
                    <thead>
                        <tr>
                            <th class="mts-th-select">
                                <label class="d-flex align-items-center justify-content-center gap-2 mb-0">
                                    <input type="checkbox" id="mtsSelectAll" class="mts-chk-master" title="Select all visible" aria-label="Select all visible" <?= $isDatasetEmpty ? 'disabled' : '' ?>>
                                    <span>Select</span>
                                </label>
                            </th>
                            <th class="mts-th-sort" id="mtsSortDate" role="button" tabindex="0">Meeting Date <span class="mts-sort-arrows ms-1" aria-hidden="true"><i class="fa fa-sort-up"></i><i class="fa fa-sort-down"></i></span></th>
                            <th>Meeting Number</th>
                            <th>Arranged By</th>
                            <th>TI Name</th>
                            <th>Customer Support</th>
                            <th>Client Id</th>
                            <th>Client</th>
                            <th>Match Id</th>
                            <th>Match</th>
                            <th>Meeting Status</th>
                            <th>Meeting Outcome</th>
                            <th>Staff Approval</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody id="mtsTbody">
                        <?php if ($isDatasetEmpty): ?>
                        <tr class="mts-empty-row"><td colspan="14">No Record found</td></tr>
                        <?php else: ?>
                        <?php foreach ($meetingRows as $R):
                            $md = $R['meeting_date'] ?? '';
                            $ts = $md !== '' && strtotime((string) $md) ? strtotime((string) $md) : 0;
                            $searchBlob = strtolower(implode(' ', [
                                $fmtDate($md),
                                $R['meeting_number'] ?? '',
                                $R['arranged_by'] ?? '',
                                $R['ti_name'] ?? '',
                                $R['customer_support'] ?? '',
                                $R['client_id'] ?? '',
                                $R['client_name'] ?? '',
                                $R['match_id'] ?? '',
                                $R['match_name'] ?? '',
                                $R['meeting_status'] ?? '',
                                $R['meeting_outcome'] ?? '',
                                $R['staff_approval'] ?? '',
                                $R['payment'] ?? '',
                            ]));
                            $arrVal = strtolower(trim((string) ($R['arranged_by'] ?? '')));
                            $statVal = strtolower(trim((string) ($R['meeting_status'] ?? '')));
                            ?>
                        <tr class="mts-row" data-sort-ts="<?= (int) $ts ?>" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-arranged="<?= htmlspecialchars($arrVal, ENT_QUOTES, 'UTF-8') ?>" data-status="<?= htmlspecialchars($statVal, ENT_QUOTES, 'UTF-8') ?>">
                            <td class="text-center"><input type="checkbox" class="mts-chk-row" value="<?= (int) ($R['id'] ?? 0) ?>" aria-label="Select row"></td>
                            <td><?= htmlspecialchars($fmtDate($md)) ?></td>
                            <td><?= htmlspecialchars((string) ($R['meeting_number'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['arranged_by'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['ti_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['customer_support'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['client_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['client_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['match_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['match_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['meeting_status'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['meeting_outcome'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['staff_approval'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['payment'] ?? '')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mts-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="mtsInfo"><?= $isDatasetEmpty ? 'Showing 1 to 0 of 0 entries' : 'Showing 0 to 0 of 0 entries' ?></div>
                <nav class="mts-pagination-wrap" id="mtsPaginationWrap" style="display:none;" aria-label="Table pages">
                    <ul class="pagination pagination-sm mb-0" id="mtsPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="mtsFilterPopup" class="mts-popup-overlay" style="display:none;">
    <div class="mts-popup">
        <div class="mts-popup-header"><h3 class="mts-popup-title">Filter</h3><span class="mts-popup-close" id="mtsCloseFilter">&times;</span></div>
        <div class="mts-popup-body">
            <div class="mb-3">
                <label class="form-label small">Arranged By</label>
                <select id="mtsFilterArranged" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($arrangedOpts as $a): ?>
                        <option value="<?= htmlspecialchars(strtolower($a)) ?>"><?= htmlspecialchars($a) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small">Meeting Status</label>
                <select id="mtsFilterStatus" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($statusOpts as $s): ?>
                        <option value="<?= htmlspecialchars(strtolower($s)) ?>"><?= htmlspecialchars($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mts-popup-footer">
            <button type="button" class="btn mts-btn-search btn-sm" id="mtsApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="mtsCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .mts-page .mts-content { padding: 20px 18px; background: #e8e8e8; }
    .mts-report-panel {
        background: #fff;
        border: 1px solid #d5d5d5;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        padding: 20px 22px 8px;
    }
    .mts-page .mts-topbar {
        justify-content: space-between;
        padding-left: 12px;
        padding-right: 16px;
        background: #fff;
        border-bottom: 1px solid #e5e5e5;
    }
    .mts-page .admin-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        letter-spacing: 0.01em;
    }
    .mts-toolbar { margin-bottom: 12px !important; }
    .mts-search-group { display: flex; flex-wrap: nowrap; align-items: stretch; width: 100%; }
    .mts-input {
        flex: 1 1 auto; min-width: 0; height: 38px; font-size: 13px;
        border: 1px solid #ccc; border-radius: 4px 0 0 4px;
    }
    .mts-clear { height: 38px; padding: 0 12px; border-left: 0; color: #666; background: #fff; }
    .mts-btn-search {
        background: #0096C7 !important; border: 1px solid #0096C7 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 18px; font-weight: 600;
        border-radius: 0 4px 4px 0; white-space: nowrap;
    }
    .mts-btn-search:hover { filter: brightness(0.95); color: #fff !important; }
    .mts-btn-filter {
        background: #5BC0DE !important; border: 1px solid #4fb3d4 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; white-space: nowrap;
    }
    .mts-btn-filter:hover { filter: brightness(0.96); color: #fff !important; }
    .mts-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .mts-select { height: 32px; font-size: 13px; min-width: 72px; }
    .mts-tabs { border-bottom: 1px solid #c8c8c8; padding-top: 4px; gap: 6px; }
    .mts-tabs .nav-link {
        background: #ebebeb; border: 1px solid #c8c8c8; border-bottom: 0; border-radius: 4px 4px 0 0;
        color: #333; font-size: 12px; font-weight: 700; padding: 9px 18px; min-width: 120px; text-align: center; cursor: default;
    }
    .mts-tabs .nav-link.active {
        background: #fff; color: #0096C7;
        border-top: 3px solid #5BC0DE;
        border-color: #c8c8c8 #c8c8c8 #fff; margin-bottom: -1px; padding-top: 7px;
    }
    .mts-tabs .nav-link small { font-size: 11px; font-weight: 600; color: #555; }
    .mts-tabs .nav-link.active small { color: #0096C7; }
    .mts-table-wrap { border: 1px solid #d0d0d0; border-top: 0; border-radius: 0 0 4px 4px; }
    .mts-table { font-size: 13px; color: #333; }
    .mts-table thead th {
        background: #808080 !important; color: #fff !important; font-weight: 700;
        border-color: #737373 !important; padding: 12px 14px; vertical-align: middle;
    }
    .mts-th-select { white-space: nowrap; min-width: 88px; }
    .mts-th-select .mts-chk-master { margin: 0; }
    .mts-th-sort { cursor: pointer; user-select: none; white-space: nowrap; }
    .mts-sort-arrows {
        display: inline-flex; flex-direction: column; font-size: 9px; line-height: 0.7;
        vertical-align: middle; opacity: 0.95;
    }
    .mts-sort-arrows .fa { display: block; }
    .mts-table tbody td { padding: 11px 14px; vertical-align: middle; border-color: #ddd; background: #fff; }
    .mts-table tbody tr:nth-child(even):not(.mts-empty-row) td { background: #f4f4f4; }
    tr.mts-empty-row td {
        background: #f8d7da !important;
        color: #721c24 !important;
        font-weight: 600;
        text-align: center;
        padding: 14px;
    }
    .mts-chk-row { margin: 0; }
    .mts-footer { border-top: 1px solid #eaeaea; }
    .mts-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .mts-popup-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1050;
        display: flex; align-items: center; justify-content: center;
    }
    .mts-popup { background: #fff; border-radius: 6px; width: 400px; max-width: 92%; box-shadow: 0 8px 28px rgba(0,0,0,.18); }
    .mts-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #e8e8e8; }
    .mts-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .mts-popup-close { cursor: pointer; font-size: 22px; line-height: 1; color: #666; }
    .mts-popup-body { padding: 16px 18px; }
    .mts-popup-footer { padding: 12px 18px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #eee; }
    @media (max-width: 767px) {
        .mts-toolbar .mts-actions { width: 100%; }
        .mts-toolbar .mts-actions .btn { width: 100%; justify-content: center; }
    }
</style>

<script>
(function(){
    var tbody = document.getElementById('mtsTbody');
    var searchInput = document.getElementById('mtsSearch');
    var showSel = document.getElementById('mtsShowEntries');
    var infoEl = document.getElementById('mtsInfo');
    var pagWrap = document.getElementById('mtsPaginationWrap');
    var pagUl = document.getElementById('mtsPagination');
    var selectAll = document.getElementById('mtsSelectAll');
    var arrangedFilter = '', statusFilter = '';
    var currentPage = 1;
    var dateSortDesc = true;

    function rows() { return Array.from(document.querySelectorAll('.mts-row')); }

    function sortRowsInDom() {
        var r = rows();
        r.sort(function(a, b) {
            var ta = parseInt(a.getAttribute('data-sort-ts'), 10) || 0;
            var tb = parseInt(b.getAttribute('data-sort-ts'), 10) || 0;
            return dateSortDesc ? (tb - ta) : (ta - tb);
        });
        r.forEach(function(row) { tbody.appendChild(row); });
    }

    function visibleCheckboxes() {
        var out = [];
        rows().forEach(function(row) {
            if (row.style.display !== 'none') {
                var c = row.querySelector('.mts-chk-row');
                if (c) out.push(c);
            }
        });
        return out;
    }

    function syncSelectAll() {
        if (!selectAll || selectAll.disabled) return;
        var vis = visibleCheckboxes();
        if (vis.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;
        }
        var n = vis.filter(function(c) { return c.checked; }).length;
        selectAll.checked = n === vis.length;
        selectAll.indeterminate = n > 0 && n < vis.length;
    }

    function filtered() {
        var q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        return rows().filter(function(row) {
            if (arrangedFilter && (row.getAttribute('data-arranged') || '') !== arrangedFilter) return false;
            if (statusFilter && (row.getAttribute('data-status') || '') !== statusFilter) return false;
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

    function setFilterEmptyRow(show) {
        var empty = tbody.querySelector('.mts-filter-empty');
        if (show) {
            if (!empty) {
                empty = document.createElement('tr');
                empty.className = 'mts-filter-empty mts-empty-row';
                empty.innerHTML = '<td colspan="14">No Record found</td>';
                tbody.appendChild(empty);
            }
            empty.style.display = '';
        } else if (empty) {
            empty.remove();
        }
    }

    function render() {
        if (rows().length === 0) {
            return;
        }

        setFilterEmptyRow(false);

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

        if (total === 0) {
            setFilterEmptyRow(true);
            infoEl.textContent = 'Showing 0 to 0 of 0 entries';
            pagWrap.style.display = 'none';
        } else {
            infoEl.textContent = 'Showing ' + start + ' to ' + end + ' of ' + total + ' entries';
            renderPagination(total, per, pages, currentPage);
        }
        syncSelectAll();
    }

    function resetPage() { currentPage = 1; render(); }

    if (rows().length === 0) {
        return;
    }

    document.getElementById('mtsSortDate') && document.getElementById('mtsSortDate').addEventListener('click', function() {
        dateSortDesc = !dateSortDesc;
        sortRowsInDom();
        resetPage();
    });

    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('mtsSearchBtn') && document.getElementById('mtsSearchBtn').addEventListener('click', resetPage);
    document.getElementById('mtsClearSearch') && document.getElementById('mtsClearSearch').addEventListener('click', function() {
        searchInput.value = '';
        resetPage();
    });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });

    selectAll && selectAll.addEventListener('change', function() {
        var on = selectAll.checked;
        visibleCheckboxes().forEach(function(c) { c.checked = on; });
        syncSelectAll();
    });

    tbody && tbody.addEventListener('change', function(e) {
        if (e.target && e.target.classList && e.target.classList.contains('mts-chk-row')) {
            syncSelectAll();
        }
    });

    document.getElementById('mtsOpenFilter') && document.getElementById('mtsOpenFilter').addEventListener('click', function() {
        document.getElementById('mtsFilterPopup').style.display = 'flex';
    });
    function closeFilter() { document.getElementById('mtsFilterPopup').style.display = 'none'; }
    document.getElementById('mtsCloseFilter') && document.getElementById('mtsCloseFilter').addEventListener('click', closeFilter);
    document.getElementById('mtsCancelFilter') && document.getElementById('mtsCancelFilter').addEventListener('click', closeFilter);
    document.getElementById('mtsApplyFilter') && document.getElementById('mtsApplyFilter').addEventListener('click', function() {
        var a = document.getElementById('mtsFilterArranged');
        var s = document.getElementById('mtsFilterStatus');
        arrangedFilter = a && a.value ? a.value : '';
        statusFilter = s && s.value ? s.value : '';
        closeFilter();
        resetPage();
    });

    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
