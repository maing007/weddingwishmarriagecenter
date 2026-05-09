<?php
/** @var string $pageTitle */
/** @var string $feeColumnLabel fee_type-specific column title */
/** @var array $rows */
/** @var string $feeType internal '', not always passed */

$title = $pageTitle;
$reportTotal = count($rows);

$fmtDate = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d', $t) : '';
};

$fmtMoney = static function ($raw): string {
    $n = (float) $raw;
    if (floor($n) == $n) {
        return (string) (int) $n;
    }

    return number_format($n, 2, '.', '');
};

$dispDash = static function ($v): string {
    if ($v === null || $v === '') {
        return '-';
    }
    $s = trim((string) $v);

    return $s === '' ? '-' : $s;
};

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$staffOpts = $payModeOpts = $statusOpts = [];
foreach ($rows as $r) {
    $s = trim((string) ($r['staff_name'] ?? ''));
    if ($s !== '' && !in_array($s, $staffOpts, true)) {
        $staffOpts[] = $s;
    }
    $pm = trim((string) ($r['payment_mode'] ?? ''));
    if ($pm !== '' && !in_array($pm, $payModeOpts, true)) {
        $payModeOpts[] = $pm;
    }
    $st = trim((string) ($r['staff_payment_status'] ?? ''));
    if ($st !== '' && !in_array($st, $statusOpts, true)) {
        $statusOpts[] = $st;
    }
}
sort($staffOpts);
sort($payModeOpts);
sort($statusOpts);
?>

<div class="admin-main ms-page">
<div class="admin-topbar ms-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></div>
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
<div class="admin-content ms-content">
    <div class="container-fluid">
        <div class="ms-report-panel">
            <div class="ms-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="ms-search-block flex-grow-1" style="min-width: 280px; max-width: 520px;">
                    <div class="input-group ms-search-group">
                        <input type="text" id="msSearch" class="form-control ms-input" placeholder="Search here...">
                        <button class="btn btn-light border ms-clear flex-shrink-0" type="button" id="msClearSearch" aria-label="Clear"><i class="fa fa-times"></i></button>
                        <button class="btn ms-btn-search flex-shrink-0" type="button" id="msSearchBtn"><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="ms-actions flex-shrink-0 d-flex align-items-stretch">
                    <button type="button" class="btn ms-btn-filter d-flex align-items-center" id="msOpenFilter"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>

            <div class="ms-show-row mb-3">
                <span class="ms-show-wrap"><label class="me-2 mb-0">Show</label><select id="msShowEntries" class="form-select form-select-sm d-inline-block w-auto ms-select"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>

            <ul class="nav nav-tabs ms-tabs mb-0">
                <li class="nav-item">
                    <span class="nav-link active">All <small>(<?= (int) $reportTotal ?>)</small></span>
                </li>
            </ul>

            <div class="table-responsive ms-table-wrap">
                <table class="table table-bordered mb-0 ms-table">
                    <thead>
                        <tr>
                            <th class="ms-th-select">
                                <label class="d-flex align-items-center justify-content-center gap-2 mb-0">
                                    <input type="checkbox" id="msSelectAll" class="ms-chk-master" title="Select all visible" aria-label="Select all visible">
                                    <span>Select</span>
                                </label>
                            </th>
                            <th class="ms-th-sort" id="msSortAct" role="button" tabindex="0">Activation Date <span class="ms-sort-arrows ms-1" aria-hidden="true"><i class="fa fa-sort-up"></i><i class="fa fa-sort-down"></i></span></th>
                            <th>Staff Name</th>
                            <th>TI Name</th>
                            <th>Matri Id</th>
                            <th>Client Name</th>
                            <th><?= htmlspecialchars($feeColumnLabel, ENT_QUOTES, 'UTF-8') ?></th>
                            <th>Package</th>
                            <th>Payment Mode</th>
                            <th>Staff Payment Status</th>
                            <th>Staff Payment Mode</th>
                            <th>Staff Paid On</th>
                        </tr>
                    </thead>
                    <tbody id="msTbody">
                        <?php foreach ($rows as $R):
                            $act = $R['activation_date'] ?? '';
                            $ts = $act !== '' && strtotime((string) $act) ? strtotime((string) $act) : 0;
                            $paidOn = $fmtDate($R['staff_paid_on'] ?? null);
                            $paidOnDisp = ($paidOn === '') ? '-' : $paidOn;
                            $searchBlob = strtolower(implode(' ', [
                                $fmtDate($act),
                                $R['staff_name'] ?? '',
                                $R['ti_name'] ?? '',
                                $R['matri_id'] ?? '',
                                $R['client_name'] ?? '',
                                $fmtMoney($R['fee_amount'] ?? 0),
                                $R['package'] ?? '',
                                $R['payment_mode'] ?? '',
                                $R['staff_payment_status'] ?? '',
                                $dispDash($R['staff_payment_mode'] ?? ''),
                                $paidOnDisp,
                            ]));
                            $staffVal = strtolower(trim((string) ($R['staff_name'] ?? '')));
                            $modeVal = strtolower(trim((string) ($R['payment_mode'] ?? '')));
                            $statVal = strtolower(trim((string) ($R['staff_payment_status'] ?? '')));
                            ?>
                        <tr class="ms-row" data-sort-ts="<?= (int) $ts ?>" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-staff="<?= htmlspecialchars($staffVal, ENT_QUOTES, 'UTF-8') ?>" data-paymode="<?= htmlspecialchars($modeVal, ENT_QUOTES, 'UTF-8') ?>" data-status="<?= htmlspecialchars($statVal, ENT_QUOTES, 'UTF-8') ?>">
                            <td class="text-center"><input type="checkbox" class="ms-chk-row" value="<?= (int) ($R['id'] ?? 0) ?>" aria-label="Select row"></td>
                            <td><?= htmlspecialchars($fmtDate($act)) ?></td>
                            <td><?= htmlspecialchars((string) ($R['staff_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['ti_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars(matri_id_display((string) ($R['matri_id'] ?? ''), (int) ($R['id'] ?? 0))) ?></td>
                            <td><?= htmlspecialchars((string) ($R['client_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($fmtMoney($R['fee_amount'] ?? 0)) ?></td>
                            <td><?= htmlspecialchars((string) ($R['package'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['payment_mode'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['staff_payment_status'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($dispDash($R['staff_payment_mode'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($paidOnDisp) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="ms-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="msInfo">Showing 0 to 0 of 0 entries</div>
                <nav class="ms-pagination-wrap" id="msPaginationWrap" style="display:none;" aria-label="Table pages">
                    <ul class="pagination pagination-sm mb-0" id="msPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="msFilterPopup" class="ms-popup-overlay" style="display:none;">
    <div class="ms-popup">
        <div class="ms-popup-header"><h3 class="ms-popup-title">Filter</h3><span class="ms-popup-close" id="msCloseFilter">&times;</span></div>
        <div class="ms-popup-body">
            <div class="mb-3">
                <label class="form-label small">Staff Name</label>
                <select id="msFilterStaff" class="form-select form-select-sm">
                    <option value="">All staff</option>
                    <?php foreach ($staffOpts as $st): ?>
                        <option value="<?= htmlspecialchars(strtolower($st)) ?>"><?= htmlspecialchars($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small">Payment Mode</label>
                <select id="msFilterPayMode" class="form-select form-select-sm">
                    <option value="">All modes</option>
                    <?php foreach ($payModeOpts as $pm): ?>
                        <option value="<?= htmlspecialchars(strtolower($pm)) ?>"><?= htmlspecialchars($pm) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small">Staff Payment Status</label>
                <select id="msFilterStatus" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    <?php foreach ($statusOpts as $st): ?>
                        <option value="<?= htmlspecialchars(strtolower($st)) ?>"><?= htmlspecialchars($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="ms-popup-footer">
            <button type="button" class="btn ms-btn-search btn-sm" id="msApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="msCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .ms-page .ms-content { padding: 20px 18px; background: #e8e8e8; }
    .ms-report-panel {
        background: #fff;
        border: 1px solid #d5d5d5;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        padding: 20px 22px 8px;
    }
    .ms-page .ms-topbar {
        justify-content: space-between;
        padding-left: 12px;
        padding-right: 16px;
        background: #fff;
        border-bottom: 1px solid #e5e5e5;
    }
    .ms-page .admin-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        letter-spacing: 0.01em;
    }
    .ms-toolbar { margin-bottom: 12px !important; }
    .ms-search-group { display: flex; flex-wrap: nowrap; align-items: stretch; width: 100%; }
    .ms-input {
        flex: 1 1 auto; min-width: 0; height: 38px; font-size: 13px;
        border: 1px solid #ccc; border-radius: 4px 0 0 4px;
    }
    .ms-clear { height: 38px; padding: 0 12px; border-left: 0; color: #666; background: #fff; }
    .ms-btn-search {
        background: #0096C7 !important; border: 1px solid #0096C7 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 18px; font-weight: 600;
        border-radius: 0 4px 4px 0; white-space: nowrap;
    }
    .ms-btn-search:hover { filter: brightness(0.95); color: #fff !important; }
    .ms-btn-filter {
        background: #5BC0DE !important; border: 1px solid #4fb3d4 !important; color: #fff !important;
        height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; white-space: nowrap;
    }
    .ms-btn-filter:hover { filter: brightness(0.96); color: #fff !important; }
    .ms-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .ms-select { height: 32px; font-size: 13px; min-width: 72px; }
    .ms-tabs { border-bottom: 1px solid #c8c8c8; padding-top: 4px; gap: 6px; }
    .ms-tabs .nav-link {
        background: #ebebeb; border: 1px solid #c8c8c8; border-bottom: 0; border-radius: 4px 4px 0 0;
        color: #333; font-size: 12px; font-weight: 700; padding: 9px 18px; min-width: 120px; text-align: center; cursor: default;
    }
    .ms-tabs .nav-link.active {
        background: #fff; color: #0096C7;
        border-top: 3px solid #5BC0DE;
        border-color: #c8c8c8 #c8c8c8 #fff; margin-bottom: -1px; padding-top: 7px;
    }
    .ms-tabs .nav-link small { font-size: 11px; font-weight: 600; color: #555; }
    .ms-tabs .nav-link.active small { color: #0096C7; }
    .ms-table-wrap { border: 1px solid #d0d0d0; border-top: 0; border-radius: 0 0 4px 4px; }
    .ms-table { font-size: 13px; color: #333; }
    .ms-table thead th {
        background: #777777 !important; color: #fff !important; font-weight: 700;
        border-color: #6a6a6a !important; padding: 12px 14px; vertical-align: middle;
    }
    .ms-th-select { white-space: nowrap; min-width: 88px; }
    .ms-th-select .ms-chk-master { margin: 0; }
    .ms-th-sort { cursor: pointer; user-select: none; white-space: nowrap; }
    .ms-sort-arrows {
        display: inline-flex; flex-direction: column; font-size: 9px; line-height: 0.7;
        vertical-align: middle; opacity: 0.95;
    }
    .ms-sort-arrows .fa { display: block; }
    .ms-table tbody td { padding: 11px 14px; vertical-align: middle; border-color: #ddd; background: #fff; }
    .ms-table tbody tr:nth-child(even) td { background: #f4f4f4; }
    .ms-chk-row { margin: 0; }
    .ms-footer { border-top: 1px solid #eaeaea; }
    .ms-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .ms-popup-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1050;
        display: flex; align-items: center; justify-content: center;
    }
    .ms-popup { background: #fff; border-radius: 6px; width: 400px; max-width: 92%; box-shadow: 0 8px 28px rgba(0,0,0,.18); }
    .ms-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #e8e8e8; }
    .ms-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .ms-popup-close { cursor: pointer; font-size: 22px; line-height: 1; color: #666; }
    .ms-popup-body { padding: 16px 18px; }
    .ms-popup-footer { padding: 12px 18px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #eee; }
    @media (max-width: 767px) {
        .ms-toolbar .ms-actions { width: 100%; }
        .ms-toolbar .ms-actions .btn { width: 100%; justify-content: center; }
    }
</style>

<script>
(function(){
    var tbody = document.getElementById('msTbody');
    var searchInput = document.getElementById('msSearch');
    var showSel = document.getElementById('msShowEntries');
    var infoEl = document.getElementById('msInfo');
    var pagWrap = document.getElementById('msPaginationWrap');
    var pagUl = document.getElementById('msPagination');
    var selectAll = document.getElementById('msSelectAll');
    var staffFilter = '', payModeFilter = '', statusFilter = '';
    var currentPage = 1;
    var dateSortDesc = true;

    function rows() { return Array.from(document.querySelectorAll('.ms-row')); }

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
                var c = row.querySelector('.ms-chk-row');
                if (c) out.push(c);
            }
        });
        return out;
    }

    function syncSelectAll() {
        if (!selectAll) return;
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
            if (staffFilter && (row.getAttribute('data-staff') || '') !== staffFilter) return false;
            if (payModeFilter && (row.getAttribute('data-paymode') || '') !== payModeFilter) return false;
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
        syncSelectAll();
    }

    function resetPage() { currentPage = 1; render(); }

    document.getElementById('msSortAct') && document.getElementById('msSortAct').addEventListener('click', function() {
        dateSortDesc = !dateSortDesc;
        sortRowsInDom();
        resetPage();
    });

    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('msSearchBtn') && document.getElementById('msSearchBtn').addEventListener('click', resetPage);
    document.getElementById('msClearSearch') && document.getElementById('msClearSearch').addEventListener('click', function() {
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
        if (e.target && e.target.classList && e.target.classList.contains('ms-chk-row')) {
            syncSelectAll();
        }
    });

    document.getElementById('msOpenFilter') && document.getElementById('msOpenFilter').addEventListener('click', function() {
        document.getElementById('msFilterPopup').style.display = 'flex';
    });
    function closeFilter() { document.getElementById('msFilterPopup').style.display = 'none'; }
    document.getElementById('msCloseFilter') && document.getElementById('msCloseFilter').addEventListener('click', closeFilter);
    document.getElementById('msCancelFilter') && document.getElementById('msCancelFilter').addEventListener('click', closeFilter);
    document.getElementById('msApplyFilter') && document.getElementById('msApplyFilter').addEventListener('click', function() {
        var s = document.getElementById('msFilterStaff');
        var pm = document.getElementById('msFilterPayMode');
        var st = document.getElementById('msFilterStatus');
        staffFilter = s && s.value ? s.value : '';
        payModeFilter = pm && pm.value ? pm.value : '';
        statusFilter = st && st.value ? st.value : '';
        closeFilter();
        resetPage();
    });

    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
