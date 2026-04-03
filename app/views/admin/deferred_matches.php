<?php
$title = 'Deferred Matches';
$reportTotal = count($deferredRows);

$fmtDate = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d H:i:s', $t) : '';
};

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

$staffOptions = [];
foreach ($deferredRows as $r) {
    $s = trim((string) ($r['staff_name'] ?? ''));
    if ($s !== '' && !in_array($s, $staffOptions, true)) {
        $staffOptions[] = $s;
    }
}
sort($staffOptions);
?>

<div class="admin-main dm-page">
<div class="admin-topbar dm-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Deferred Matches - ALL</div>
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
<div class="admin-content dm-content">
    <div class="container-fluid">
        <div class="dm-report-panel">
            <div class="dm-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="dm-search-block flex-grow-1" style="min-width: 280px; max-width: 640px;">
                    <div class="input-group dm-search-group">
                        <input type="text" id="dmSearch" class="form-control dm-input" placeholder="Search here...">
                        <button class="btn btn-light border dm-clear flex-shrink-0" type="button" id="dmClearSearch" aria-label="Clear"><i class="fa fa-times"></i></button>
                        <button class="btn dm-btn-search flex-shrink-0" type="button" id="dmSearchBtn"><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="dm-filter-block flex-shrink-0 d-flex align-items-stretch">
                    <button type="button" class="btn dm-btn-filter d-flex align-items-center" id="dmOpenFilter"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>

            <div class="dm-show-row mb-3">
                <span class="dm-show-wrap"><label class="me-2 mb-0">Show</label><select id="dmShowEntries" class="form-select form-select-sm d-inline-block w-auto dm-select"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>

            <ul class="nav nav-tabs dm-tabs mb-0">
                <li class="nav-item">
                    <span class="nav-link active">All <small>(<?= (int) $reportTotal ?>)</small></span>
                </li>
            </ul>

            <div class="table-responsive dm-table-wrap">
                <table class="table table-bordered mb-0 dm-table">
                    <thead>
                        <tr>
                            <th>My Matri Id</th>
                            <th>My Name</th>
                            <th>Other Matri Id</th>
                            <th>Other Name</th>
                            <th>Staff</th>
                            <th class="dm-th-sort">Date <i class="fa fa-sort-desc ms-1" aria-hidden="true"></i></th>
                            <th>Auto Deferred</th>
                        </tr>
                    </thead>
                    <tbody id="dmTbody">
                        <?php foreach ($deferredRows as $R):
                            $autoDef = trim((string) ($R['auto_deferred'] ?? ''));
                            $searchBlob = strtolower(implode(' ', [
                                $R['my_matri_id'] ?? '',
                                $R['my_name'] ?? '',
                                $R['other_matri_id'] ?? '',
                                $R['other_name'] ?? '',
                                $R['staff_name'] ?? '',
                                $fmtDate($R['deferred_at'] ?? null),
                                $autoDef,
                            ]));
                            $staffVal = trim((string) ($R['staff_name'] ?? ''));
                            ?>
                        <tr class="dm-row" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-staff="<?= htmlspecialchars(strtolower($staffVal), ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) ($R['my_matri_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['my_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['other_matri_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['other_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($staffVal) ?></td>
                            <td><?= htmlspecialchars($fmtDate($R['deferred_at'] ?? null)) ?></td>
                            <td><?= $autoDef !== '' ? htmlspecialchars($autoDef) : '' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="dm-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="dmInfo">Showing 0 to 0 of 0 entries</div>
                <nav class="dm-pagination-wrap" id="dmPaginationWrap" style="display:none;" aria-label="Table pages">
                    <ul class="pagination pagination-sm mb-0" id="dmPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="dmFilterPopup" class="dm-popup-overlay" style="display:none;">
    <div class="dm-popup">
        <div class="dm-popup-header"><h3 class="dm-popup-title">Filter</h3><span class="dm-popup-close" id="dmCloseFilter">&times;</span></div>
        <div class="dm-popup-body">
            <div class="mb-3">
                <label class="form-label small">Staff</label>
                <select id="dmFilterStaff" class="form-select form-select-sm">
                    <option value="">All staff</option>
                    <?php foreach ($staffOptions as $st): ?>
                        <option value="<?= htmlspecialchars(strtolower($st)) ?>"><?= htmlspecialchars($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="dm-popup-footer">
            <button type="button" class="btn dm-btn-search btn-sm" id="dmApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="dmCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .dm-page .dm-content { padding: 20px 18px; background: #e8e8e8; }
    .dm-report-panel {
        background: #fff;
        border: 1px solid #d5d5d5;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        padding: 20px 22px 8px;
    }
    .dm-page .dm-topbar {
        justify-content: space-between;
        padding-left: 12px;
        padding-right: 16px;
        background: #fff;
        border-bottom: 1px solid #e5e5e5;
    }
    .dm-page .admin-topbar-title {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        letter-spacing: 0.01em;
    }
    .dm-toolbar { margin-bottom: 12px !important; }
    .dm-search-group {
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        width: 100%;
    }
    .dm-input {
        flex: 1 1 auto;
        min-width: 0;
        height: 38px;
        font-size: 13px;
        border: 1px solid #ccc;
        border-radius: 4px 0 0 4px;
    }
    .dm-clear {
        height: 38px;
        padding: 0 12px;
        border-left: 0;
        color: #666;
        background: #fff;
    }
    .dm-btn-search {
        background: #0096C7 !important;
        border: 1px solid #0096C7 !important;
        color: #fff !important;
        height: 38px;
        font-size: 13px;
        padding: 0 18px;
        font-weight: 600;
        border-radius: 0 4px 4px 0;
        white-space: nowrap;
    }
    .dm-btn-search:hover { filter: brightness(0.95); color: #fff !important; }
    .dm-btn-filter {
        background: #5BC0DE !important;
        border: 1px solid #4fb3d4 !important;
        color: #fff !important;
        height: 38px;
        font-size: 13px;
        padding: 0 16px;
        font-weight: 600;
        border-radius: 4px;
        white-space: nowrap;
    }
    .dm-btn-filter:hover { filter: brightness(0.96); color: #fff !important; }
    .dm-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .dm-select { height: 32px; font-size: 13px; min-width: 72px; }
    .dm-tabs {
        border-bottom: 1px solid #c8c8c8;
        padding-top: 4px;
        gap: 6px;
    }
    .dm-tabs .nav-link {
        background: #ebebeb;
        border: 1px solid #c8c8c8;
        border-bottom: 0;
        border-radius: 4px 4px 0 0;
        color: #333;
        font-size: 12px;
        font-weight: 700;
        padding: 9px 18px;
        min-width: 120px;
        text-align: center;
        cursor: default;
    }
    .dm-tabs .nav-link.active {
        background: #fff;
        color: #0096C7;
        border-top: 3px solid #5BC0DE;
        border-color: #c8c8c8 #c8c8c8 #fff;
        margin-bottom: -1px;
        padding-top: 7px;
    }
    .dm-tabs .nav-link small { font-size: 11px; font-weight: 600; color: #555; }
    .dm-tabs .nav-link.active small { color: #0096C7; }
    .dm-table-wrap {
        border: 1px solid #d0d0d0;
        border-top: 0;
        border-radius: 0 0 4px 4px;
    }
    .dm-table { font-size: 13px; color: #333; }
    .dm-table thead th {
        background: #888888 !important;
        color: #fff !important;
        font-weight: 700;
        border-color: #7a7a7a !important;
        padding: 12px 14px;
        vertical-align: middle;
    }
    .dm-th-sort { white-space: nowrap; }
    .dm-table tbody td {
        padding: 11px 14px;
        vertical-align: middle;
        border-color: #ddd;
        background: #fff;
    }
    .dm-table tbody tr:nth-child(even) td { background: #f4f4f4; }
    .dm-footer { border-top: 1px solid #eaeaea; }
    .dm-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .dm-popup-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dm-popup {
        background: #fff;
        border-radius: 6px;
        width: 400px;
        max-width: 92%;
        box-shadow: 0 8px 28px rgba(0,0,0,.18);
    }
    .dm-popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid #e8e8e8;
    }
    .dm-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .dm-popup-close { cursor: pointer; font-size: 22px; line-height: 1; color: #666; }
    .dm-popup-body { padding: 16px 18px; }
    .dm-popup-footer {
        padding: 12px 18px 16px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        border-top: 1px solid #eee;
    }
    @media (max-width: 767px) {
        .dm-toolbar .dm-filter-block { width: 100%; }
        .dm-toolbar .dm-btn-filter { width: 100%; justify-content: center; }
    }
</style>

<script>
(function(){
    var searchInput = document.getElementById('dmSearch');
    var showSel = document.getElementById('dmShowEntries');
    var infoEl = document.getElementById('dmInfo');
    var pagWrap = document.getElementById('dmPaginationWrap');
    var pagUl = document.getElementById('dmPagination');
    var staffFilter = '';
    var currentPage = 1;

    function rows() { return Array.from(document.querySelectorAll('.dm-row')); }

    function filtered() {
        var q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        return rows().filter(function(r) {
            if (staffFilter && (r.getAttribute('data-staff') || '') !== staffFilter) return false;
            return !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
        });
    }

    function pageSizeRaw() {
        var v = parseInt(showSel && showSel.value ? showSel.value : '10', 10);
        return isNaN(v) ? 10 : v;
    }

    function renderPagination(total, per, pages, page) {
        pagUl.innerHTML = '';
        if (pages <= 1) {
            pagWrap.style.display = 'none';
            return;
        }
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

        rows().forEach(function(r) { r.style.display = 'none'; });

        var start = 0;
        var end = 0;
        if (total > 0) {
            if (showAll) {
                all.forEach(function(r) { r.style.display = ''; });
                start = 1;
                end = total;
            } else {
                var off = (currentPage - 1) * per;
                var slice = all.slice(off, off + per);
                slice.forEach(function(r) { r.style.display = ''; });
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

    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('dmSearchBtn') && document.getElementById('dmSearchBtn').addEventListener('click', resetPage);
    document.getElementById('dmClearSearch') && document.getElementById('dmClearSearch').addEventListener('click', function() {
        searchInput.value = '';
        resetPage();
    });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });

    document.getElementById('dmOpenFilter') && document.getElementById('dmOpenFilter').addEventListener('click', function() {
        document.getElementById('dmFilterPopup').style.display = 'flex';
    });
    function closeFilter() {
        document.getElementById('dmFilterPopup').style.display = 'none';
    }
    document.getElementById('dmCloseFilter') && document.getElementById('dmCloseFilter').addEventListener('click', closeFilter);
    document.getElementById('dmCancelFilter') && document.getElementById('dmCancelFilter').addEventListener('click', closeFilter);
    document.getElementById('dmApplyFilter') && document.getElementById('dmApplyFilter').addEventListener('click', function() {
        var sel = document.getElementById('dmFilterStaff');
        staffFilter = sel && sel.value ? sel.value : '';
        closeFilter();
        resetPage();
    });

    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
