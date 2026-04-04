<?php
$title = 'Members All Activity';
$reportTotal = count($rows);

$fmtDateTime = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d H:i:s', $t) : '';
};

$isDatasetEmpty = $reportTotal === 0;

$actOpts = [];
foreach ($rows as $r) {
    $a = trim((string) ($r['activity'] ?? ''));
    if ($a !== '' && !in_array($a, $actOpts, true)) {
        $actOpts[] = $a;
    }
}
sort($actOpts);

$exportUrl = BASE_URL . '/admin/reports/members/members-all-activity/export';

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<div class="admin-main mma-page">
<div class="admin-topbar mma-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Members All Activity - ALL</div>
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
<div class="admin-content mma-content">
    <div class="container-fluid">
        <div class="mma-report-panel">
            <div class="mma-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="mma-search-block flex-grow-1" style="min-width: 280px; max-width: 520px;">
                    <div class="input-group mma-search-group">
                        <input type="text" id="mmaSearch" class="form-control mma-input" placeholder="Search here..." <?= $isDatasetEmpty ? 'disabled' : '' ?>>
                        <button class="btn btn-light border mma-clear flex-shrink-0" type="button" id="mmaClearSearch" aria-label="Clear" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="fa fa-times"></i></button>
                        <button class="btn mma-btn-search flex-shrink-0" type="button" id="mmaSearchBtn" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="mma-actions flex-shrink-0 d-flex align-items-stretch gap-2">
                    <a href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn mma-btn-export d-flex align-items-center"><i class="bi bi-download me-1"></i> Export</a>
                    <button type="button" class="btn mma-btn-filter d-flex align-items-center" id="mmaOpenFilter" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>
            <div class="mma-show-row mb-3">
                <span class="mma-show-wrap"><label class="me-2 mb-0">Show</label><select id="mmaShowEntries" class="form-select form-select-sm d-inline-block w-auto mma-select" <?= $isDatasetEmpty ? 'disabled' : '' ?>><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>
            <ul class="nav nav-tabs mma-tabs mb-0">
                <li class="nav-item"><span class="nav-link active">All <small>(<?= (int) $reportTotal ?>)</small></span></li>
            </ul>
            <div class="table-responsive mma-table-wrap">
                <table class="table table-bordered mb-0 mma-table">
                    <thead>
                        <tr>
                            <th class="mma-th-sort" id="mmaSortDt" role="button" tabindex="0">Date Time <span class="mma-sort-arrows ms-1" aria-hidden="true"><i class="fa fa-sort-up"></i><i class="fa fa-sort-down"></i></span></th>
                            <th>Matri Id</th>
                            <th>Member Name</th>
                            <th>Activity</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody id="mmaTbody">
                        <?php if ($isDatasetEmpty): ?>
                        <tr class="mma-empty-row"><td colspan="5">No Record found</td></tr>
                        <?php else: ?>
                        <?php foreach ($rows as $R):
                            $at = $R['activity_at'] ?? '';
                            $ts = $at !== '' && strtotime((string) $at) ? strtotime((string) $at) : 0;
                            $searchBlob = strtolower(implode(' ', [
                                $fmtDateTime($at),
                                $R['matri_id'] ?? '',
                                $R['member_name'] ?? '',
                                $R['activity'] ?? '',
                                $R['detail'] ?? '',
                            ]));
                            $actVal = strtolower(trim((string) ($R['activity'] ?? '')));
                            ?>
                        <tr class="mma-row" data-sort-ts="<?= (int) $ts ?>" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-activity="<?= htmlspecialchars($actVal, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars($fmtDateTime($at)) ?></td>
                            <td><?= htmlspecialchars(matri_id_display((string) ($R['matri_id'] ?? ''))) ?></td>
                            <td><?= htmlspecialchars((string) ($R['member_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['activity'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['detail'] ?? '')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mma-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="mmaInfo"><?= $isDatasetEmpty ? 'Showing 1 to 0 of 0 entries' : 'Showing 0 to 0 of 0 entries' ?></div>
                <nav class="mma-pagination-wrap" id="mmaPaginationWrap" style="display:none;"><ul class="pagination pagination-sm mb-0" id="mmaPagination"></ul></nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="mmaFilterPopup" class="mma-popup-overlay" style="display:none;">
    <div class="mma-popup">
        <div class="mma-popup-header"><h3 class="mma-popup-title">Filter</h3><span class="mma-popup-close" id="mmaCloseFilter">&times;</span></div>
        <div class="mma-popup-body">
            <label class="form-label small">Activity</label>
            <select id="mmaFilterAct" class="form-select form-select-sm">
                <option value="">All</option>
                <?php foreach ($actOpts as $a): ?>
                    <option value="<?= htmlspecialchars(strtolower($a)) ?>"><?= htmlspecialchars($a) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mma-popup-footer">
            <button type="button" class="btn mma-btn-search btn-sm" id="mmaApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="mmaCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .mma-page .mma-content { padding: 20px 18px; background: #e8e8e8; }
    .mma-report-panel { background: #fff; border: 1px solid #d5d5d5; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.07); padding: 20px 22px 8px; }
    .mma-page .mma-topbar { justify-content: space-between; padding-left: 12px; padding-right: 16px; background: #fff; border-bottom: 1px solid #e5e5e5; }
    .mma-page .admin-topbar-title { font-size: 15px; font-weight: 600; color: #111; }
    .mma-search-group { display: flex; flex-wrap: nowrap; width: 100%; }
    .mma-input { flex: 1 1 auto; min-width: 0; height: 38px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; }
    .mma-clear { height: 38px; padding: 0 12px; border-left: 0; color: #666; background: #fff; }
    .mma-btn-search { background: #0096C7 !important; border: 1px solid #0096C7 !important; color: #fff !important; height: 38px; font-size: 13px; padding: 0 18px; font-weight: 600; border-radius: 0 4px 4px 0; }
    .mma-btn-export { background: #f0ad4e !important; border: 1px solid #eea236 !important; color: #fff !important; height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; }
    .mma-btn-filter { background: #5BC0DE !important; border: 1px solid #4fb3d4 !important; color: #fff !important; height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; }
    .mma-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .mma-select { height: 32px; font-size: 13px; min-width: 72px; }
    .mma-tabs { border-bottom: 1px solid #c8c8c8; padding-top: 4px; gap: 6px; }
    .mma-tabs .nav-link { background: #ebebeb; border: 1px solid #c8c8c8; border-bottom: 0; border-radius: 4px 4px 0 0; color: #333; font-size: 12px; font-weight: 700; padding: 9px 18px; min-width: 120px; text-align: center; cursor: default; }
    .mma-tabs .nav-link.active { background: #fff; color: #0096C7; border-top: 3px solid #5BC0DE; border-color: #c8c8c8 #c8c8c8 #fff; margin-bottom: -1px; padding-top: 7px; }
    .mma-tabs .nav-link.active small { color: #0096C7; }
    .mma-table-wrap { border: 1px solid #d0d0d0; border-top: 0; border-radius: 0 0 4px 4px; }
    .mma-table { font-size: 13px; color: #333; }
    .mma-table thead th { background: #888888 !important; color: #fff !important; font-weight: 700; border-color: #7a7a7a !important; padding: 12px 14px; }
    .mma-th-sort { cursor: pointer; user-select: none; white-space: nowrap; }
    .mma-sort-arrows { display: inline-flex; flex-direction: column; font-size: 9px; line-height: 0.7; vertical-align: middle; }
    .mma-sort-arrows .fa { display: block; }
    .mma-table tbody td { padding: 11px 14px; border-color: #ddd; background: #fff; }
    .mma-table tbody tr:nth-child(even):not(.mma-empty-row):not(.mma-filter-empty) td { background: #f4f4f4; }
    tr.mma-empty-row td, tr.mma-filter-empty td { background: #f8d7da !important; color: #721c24 !important; font-weight: 600; text-align: center; padding: 14px; }
    .mma-footer { border-top: 1px solid #eaeaea; }
    .mma-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .mma-popup-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1050; display: flex; align-items: center; justify-content: center; }
    .mma-popup { background: #fff; border-radius: 6px; width: 400px; max-width: 92%; box-shadow: 0 8px 28px rgba(0,0,0,.18); }
    .mma-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #e8e8e8; }
    .mma-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .mma-popup-close { cursor: pointer; font-size: 22px; color: #666; }
    .mma-popup-body { padding: 16px 18px; }
    .mma-popup-footer { padding: 12px 18px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #eee; }
    @media (max-width: 767px) { .mma-toolbar .mma-actions { width: 100%; flex-direction: column; } .mma-toolbar .mma-actions .btn { width: 100%; justify-content: center; } }
</style>

<script>
(function(){
    var tbody = document.getElementById('mmaTbody');
    var searchInput = document.getElementById('mmaSearch');
    var showSel = document.getElementById('mmaShowEntries');
    var infoEl = document.getElementById('mmaInfo');
    var pagWrap = document.getElementById('mmaPaginationWrap');
    var pagUl = document.getElementById('mmaPagination');
    var actFilter = '';
    var currentPage = 1;
    var dtSortDesc = true;
    function rows() { return Array.from(document.querySelectorAll('.mma-row')); }
    function sortRows() {
        var r = rows();
        r.sort(function(a, b) {
            var ta = parseInt(a.getAttribute('data-sort-ts'), 10) || 0;
            var tb = parseInt(b.getAttribute('data-sort-ts'), 10) || 0;
            return dtSortDesc ? (tb - ta) : (ta - tb);
        });
        r.forEach(function(row) { tbody.appendChild(row); });
    }
    function filtered() {
        var q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        return rows().filter(function(row) {
            if (actFilter && (row.getAttribute('data-activity') || '') !== actFilter) return false;
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
    function removeFE() { var x = tbody.querySelector('.mma-filter-empty'); if (x) x.remove(); }
    function showFE() { removeFE(); var tr = document.createElement('tr'); tr.className = 'mma-filter-empty mma-empty-row'; tr.innerHTML = '<td colspan="5">No Record found</td>'; tbody.appendChild(tr); }
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
    document.getElementById('mmaSortDt') && document.getElementById('mmaSortDt').addEventListener('click', function() { dtSortDesc = !dtSortDesc; sortRows(); resetPage(); });
    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('mmaSearchBtn') && document.getElementById('mmaSearchBtn').addEventListener('click', resetPage);
    document.getElementById('mmaClearSearch') && document.getElementById('mmaClearSearch').addEventListener('click', function() { searchInput.value = ''; resetPage(); });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });
    document.getElementById('mmaOpenFilter') && document.getElementById('mmaOpenFilter').addEventListener('click', function() { document.getElementById('mmaFilterPopup').style.display = 'flex'; });
    function cf() { document.getElementById('mmaFilterPopup').style.display = 'none'; }
    document.getElementById('mmaCloseFilter') && document.getElementById('mmaCloseFilter').addEventListener('click', cf);
    document.getElementById('mmaCancelFilter') && document.getElementById('mmaCancelFilter').addEventListener('click', cf);
    document.getElementById('mmaApplyFilter') && document.getElementById('mmaApplyFilter').addEventListener('click', function() {
        var s = document.getElementById('mmaFilterAct'); actFilter = s && s.value ? s.value : ''; cf(); resetPage();
    });
    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
