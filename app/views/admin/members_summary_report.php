<?php
$title = 'Members Summary';
$reportTotal = count($rows);

$fmtDate = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d', $t) : '';
};

$isDatasetEmpty = $reportTotal === 0;

$pkgOpts = [];
foreach ($rows as $r) {
    $p = trim((string) ($r['package_name'] ?? ''));
    if ($p !== '' && !in_array($p, $pkgOpts, true)) {
        $pkgOpts[] = $p;
    }
}
sort($pkgOpts);

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<div class="admin-main msr-page">
<div class="admin-topbar msr-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Members Summary - ALL</div>
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
<div class="admin-content msr-content">
    <div class="container-fluid">
        <div class="msr-report-panel">
            <div class="msr-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="msr-search-block flex-grow-1" style="min-width: 280px; max-width: 520px;">
                    <div class="input-group msr-search-group">
                        <input type="text" id="msrSearch" class="form-control msr-input" placeholder="Search here..." <?= $isDatasetEmpty ? 'disabled' : '' ?>>
                        <button class="btn btn-light border msr-clear flex-shrink-0" type="button" id="msrClearSearch" aria-label="Clear" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="fa fa-times"></i></button>
                        <button class="btn msr-btn-search flex-shrink-0" type="button" id="msrSearchBtn" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="msr-actions flex-shrink-0">
                    <button type="button" class="btn msr-btn-filter d-flex align-items-center" id="msrOpenFilter" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>
            <div class="msr-show-row mb-3">
                <span class="msr-show-wrap"><label class="me-2 mb-0">Show</label><select id="msrShowEntries" class="form-select form-select-sm d-inline-block w-auto msr-select" <?= $isDatasetEmpty ? 'disabled' : '' ?>><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>
            <ul class="nav nav-tabs msr-tabs mb-0">
                <li class="nav-item"><span class="nav-link active">All <small>(<?= (int) $reportTotal ?>)</small></span></li>
            </ul>
            <div class="table-responsive msr-table-wrap">
                <table class="table table-bordered mb-0 msr-table">
                    <thead>
                        <tr>
                            <th>Matri Id</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Package</th>
                            <th>Membership Status</th>
                            <th>Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody id="msrTbody">
                        <?php if ($isDatasetEmpty): ?>
                        <tr class="msr-empty-row"><td colspan="6">No Record found</td></tr>
                        <?php else: ?>
                        <?php foreach ($rows as $R):
                            $searchBlob = strtolower(implode(' ', [
                                $R['matri_id'] ?? '',
                                $R['username'] ?? '',
                                $R['email'] ?? '',
                                $R['package_name'] ?? '',
                                $R['membership_status'] ?? '',
                                $fmtDate($R['expiry_date'] ?? null),
                            ]));
                            $pkgVal = strtolower(trim((string) ($R['package_name'] ?? '')));
                            ?>
                        <tr class="msr-row" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-pkg="<?= htmlspecialchars($pkgVal, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) ($R['matri_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['username'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['email'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['package_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['membership_status'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($fmtDate($R['expiry_date'] ?? null)) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="msr-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="msrInfo"><?= $isDatasetEmpty ? 'Showing 1 to 0 of 0 entries' : 'Showing 0 to 0 of 0 entries' ?></div>
                <nav class="msr-pagination-wrap" id="msrPaginationWrap" style="display:none;"><ul class="pagination pagination-sm mb-0" id="msrPagination"></ul></nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="msrFilterPopup" class="msr-popup-overlay" style="display:none;">
    <div class="msr-popup">
        <div class="msr-popup-header"><h3 class="msr-popup-title">Filter</h3><span class="msr-popup-close" id="msrCloseFilter">&times;</span></div>
        <div class="msr-popup-body">
            <label class="form-label small">Package</label>
            <select id="msrFilterPkg" class="form-select form-select-sm">
                <option value="">All</option>
                <?php foreach ($pkgOpts as $p): ?>
                    <option value="<?= htmlspecialchars(strtolower($p)) ?>"><?= htmlspecialchars($p) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="msr-popup-footer">
            <button type="button" class="btn msr-btn-search btn-sm" id="msrApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="msrCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .msr-page .msr-content { padding: 20px 18px; background: #e8e8e8; }
    .msr-report-panel { background: #fff; border: 1px solid #d5d5d5; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.07); padding: 20px 22px 8px; }
    .msr-page .msr-topbar { justify-content: space-between; padding-left: 12px; padding-right: 16px; background: #fff; border-bottom: 1px solid #e5e5e5; }
    .msr-page .admin-topbar-title { font-size: 15px; font-weight: 600; color: #111; }
    .msr-search-group { display: flex; flex-wrap: nowrap; width: 100%; }
    .msr-input { flex: 1 1 auto; min-width: 0; height: 38px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; }
    .msr-clear { height: 38px; padding: 0 12px; border-left: 0; color: #666; background: #fff; }
    .msr-btn-search { background: #0096C7 !important; border: 1px solid #0096C7 !important; color: #fff !important; height: 38px; font-size: 13px; padding: 0 18px; font-weight: 600; border-radius: 0 4px 4px 0; }
    .msr-btn-filter { background: #5BC0DE !important; border: 1px solid #4fb3d4 !important; color: #fff !important; height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; }
    .msr-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .msr-select { height: 32px; font-size: 13px; min-width: 72px; }
    .msr-tabs { border-bottom: 1px solid #c8c8c8; padding-top: 4px; gap: 6px; }
    .msr-tabs .nav-link { background: #ebebeb; border: 1px solid #c8c8c8; border-bottom: 0; border-radius: 4px 4px 0 0; color: #333; font-size: 12px; font-weight: 700; padding: 9px 18px; min-width: 120px; text-align: center; cursor: default; }
    .msr-tabs .nav-link.active { background: #fff; color: #0096C7; border-top: 3px solid #5BC0DE; border-color: #c8c8c8 #c8c8c8 #fff; margin-bottom: -1px; padding-top: 7px; }
    .msr-tabs .nav-link.active small { color: #0096C7; }
    .msr-table-wrap { border: 1px solid #d0d0d0; border-top: 0; border-radius: 0 0 4px 4px; }
    .msr-table { font-size: 13px; color: #333; }
    .msr-table thead th { background: #888888 !important; color: #fff !important; font-weight: 700; border-color: #7a7a7a !important; padding: 12px 14px; }
    .msr-table tbody td { padding: 11px 14px; border-color: #ddd; background: #fff; }
    .msr-table tbody tr:nth-child(even):not(.msr-empty-row):not(.msr-filter-empty) td { background: #f4f4f4; }
    tr.msr-empty-row td, tr.msr-filter-empty td { background: #f8d7da !important; color: #721c24 !important; font-weight: 600; text-align: center; padding: 14px; }
    .msr-footer { border-top: 1px solid #eaeaea; }
    .msr-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .msr-popup-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1050; display: flex; align-items: center; justify-content: center; }
    .msr-popup { background: #fff; border-radius: 6px; width: 400px; max-width: 92%; box-shadow: 0 8px 28px rgba(0,0,0,.18); }
    .msr-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #e8e8e8; }
    .msr-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .msr-popup-close { cursor: pointer; font-size: 22px; color: #666; }
    .msr-popup-body { padding: 16px 18px; }
    .msr-popup-footer { padding: 12px 18px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #eee; }
</style>

<script>
(function(){
    var tbody = document.getElementById('msrTbody');
    var searchInput = document.getElementById('msrSearch');
    var showSel = document.getElementById('msrShowEntries');
    var infoEl = document.getElementById('msrInfo');
    var pagWrap = document.getElementById('msrPaginationWrap');
    var pagUl = document.getElementById('msrPagination');
    var pkgFilter = '';
    var currentPage = 1;
    function rows() { return Array.from(document.querySelectorAll('.msr-row')); }
    function filtered() {
        var q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        return rows().filter(function(row) {
            if (pkgFilter && (row.getAttribute('data-pkg') || '') !== pkgFilter) return false;
            return !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
        });
    }
    function pageSizeRaw() { var v = parseInt(showSel && showSel.value ? showSel.value : '10', 10); return isNaN(v) ? 10 : v; }
    function renderPagination(total, per, pages, page) {
        pagUl.innerHTML = '';
        if (pages <= 1) { pagWrap.style.display = 'none'; return; }
        pagWrap.style.display = 'block';
        function li(label, p, disabled, active) {
            var l = document.createElement('li');
            l.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
            var a = document.createElement('a'); a.className = 'page-link'; a.href = '#'; a.textContent = label;
            if (!disabled && !active) a.addEventListener('click', function(e) { e.preventDefault(); currentPage = p; render(); });
            l.appendChild(a); return l;
        }
        pagUl.appendChild(li('Previous', Math.max(1, page - 1), page <= 1, false));
        for (var p = 1; p <= pages; p++) pagUl.appendChild(li(String(p), p, false, p === page));
        pagUl.appendChild(li('Next', Math.min(pages, page + 1), page >= pages, false));
    }
    function removeFE() { var x = tbody.querySelector('.msr-filter-empty'); if (x) x.remove(); }
    function showFE() { removeFE(); var tr = document.createElement('tr'); tr.className = 'msr-filter-empty msr-empty-row'; tr.innerHTML = '<td colspan="6">No Record found</td>'; tbody.appendChild(tr); }
    function render() {
        if (rows().length === 0) return;
        removeFE();
        var all = filtered();
        var total = all.length;
        var rawPer = pageSizeRaw();
        var showAll = rawPer >= 9999;
        var per = showAll ? total : Math.max(1, rawPer);
        var pages = showAll || total === 0 ? 1 : Math.max(1, Math.ceil(total / per));
        if (currentPage > pages) currentPage = pages;
        if (currentPage < 1) currentPage = 1;
        rows().forEach(function(row) { row.style.display = 'none'; });
        if (total > 0) {
            var start, end;
            if (showAll) { all.forEach(function(row) { row.style.display = ''; }); start = 1; end = total; }
            else { var off = (currentPage - 1) * per; var slice = all.slice(off, off + per); slice.forEach(function(row) { row.style.display = ''; }); start = off + 1; end = off + slice.length; }
            infoEl.textContent = 'Showing ' + start + ' to ' + end + ' of ' + total + ' entries';
            renderPagination(total, per, pages, currentPage);
        } else { showFE(); infoEl.textContent = 'Showing 0 to 0 of 0 entries'; pagWrap.style.display = 'none'; }
    }
    function resetPage() { currentPage = 1; render(); }
    if (rows().length === 0) return;
    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('msrSearchBtn') && document.getElementById('msrSearchBtn').addEventListener('click', resetPage);
    document.getElementById('msrClearSearch') && document.getElementById('msrClearSearch').addEventListener('click', function() { searchInput.value = ''; resetPage(); });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });
    document.getElementById('msrOpenFilter') && document.getElementById('msrOpenFilter').addEventListener('click', function() { document.getElementById('msrFilterPopup').style.display = 'flex'; });
    function cf() { document.getElementById('msrFilterPopup').style.display = 'none'; }
    document.getElementById('msrCloseFilter') && document.getElementById('msrCloseFilter').addEventListener('click', cf);
    document.getElementById('msrCancelFilter') && document.getElementById('msrCancelFilter').addEventListener('click', cf);
    document.getElementById('msrApplyFilter') && document.getElementById('msrApplyFilter').addEventListener('click', function() {
        var s = document.getElementById('msrFilterPkg'); pkgFilter = s && s.value ? s.value : ''; cf(); resetPage();
    });
    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
