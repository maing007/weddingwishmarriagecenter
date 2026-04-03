<?php
$title = 'Unsubscribe Member';
$reportTotal = count($rows);

$fmtDateTime = static function ($raw): string {
    if ($raw === null || $raw === '') {
        return '';
    }
    $t = strtotime((string) $raw);

    return $t ? date('Y-m-d H:i:s', $t) : '';
};

$isDatasetEmpty = $reportTotal === 0;

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<div class="admin-main mun-page">
<div class="admin-topbar mun-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Unsubscribe Members - ALL</div>
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
<div class="admin-content mun-content">
    <div class="container-fluid">
        <div class="mun-report-panel">
            <div class="mun-toolbar d-flex flex-wrap align-items-stretch justify-content-between gap-2 mb-3">
                <div class="mun-search-block flex-grow-1" style="min-width: 280px; max-width: 520px;">
                    <div class="input-group mun-search-group">
                        <input type="text" id="munSearch" class="form-control mun-input" placeholder="Search here..." <?= $isDatasetEmpty ? 'disabled' : '' ?>>
                        <button class="btn btn-light border mun-clear flex-shrink-0" type="button" id="munClearSearch" aria-label="Clear" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="fa fa-times"></i></button>
                        <button class="btn mun-btn-search flex-shrink-0" type="button" id="munSearchBtn" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
                <div class="mun-actions flex-shrink-0">
                    <button type="button" class="btn mun-btn-filter d-flex align-items-center" id="munOpenFilter" <?= $isDatasetEmpty ? 'disabled' : '' ?>><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </div>
            <div class="mun-show-row mb-3">
                <span class="mun-show-wrap"><label class="me-2 mb-0">Show</label><select id="munShowEntries" class="form-select form-select-sm d-inline-block w-auto mun-select" <?= $isDatasetEmpty ? 'disabled' : '' ?>><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>
            <ul class="nav nav-tabs mun-tabs mb-0">
                <li class="nav-item"><span class="nav-link active">All <small>(<?= (int) $reportTotal ?>)</small></span></li>
            </ul>
            <div class="table-responsive mun-table-wrap">
                <table class="table table-bordered mb-0 mun-table">
                    <thead>
                        <tr>
                            <th>Matri Id</th>
                            <th>Email</th>
                            <th class="mun-th-sort" id="munSortDt" role="button" tabindex="0">Unsubscribed At <span class="mun-sort-arrows ms-1" aria-hidden="true"><i class="fa fa-sort-up"></i><i class="fa fa-sort-down"></i></span></th>
                            <th>Channel</th>
                        </tr>
                    </thead>
                    <tbody id="munTbody">
                        <?php if ($isDatasetEmpty): ?>
                        <tr class="mun-empty-row"><td colspan="4">No Record found</td></tr>
                        <?php else: ?>
                        <?php foreach ($rows as $R):
                            $ua = $R['unsubscribed_at'] ?? '';
                            $ts = $ua !== '' && strtotime((string) $ua) ? strtotime((string) $ua) : 0;
                            $searchBlob = strtolower(implode(' ', [
                                $R['matri_id'] ?? '',
                                $R['email'] ?? '',
                                $fmtDateTime($ua),
                                $R['channel'] ?? '',
                            ]));
                            $chVal = strtolower(trim((string) ($R['channel'] ?? '')));
                            ?>
                        <tr class="mun-row" data-sort-ts="<?= (int) $ts ?>" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-channel="<?= htmlspecialchars($chVal, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) ($R['matri_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($R['email'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($fmtDateTime($ua)) ?></td>
                            <td><?= htmlspecialchars((string) ($R['channel'] ?? '')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mun-footer mt-3 pt-3 pb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="small text-muted" id="munInfo"><?= $isDatasetEmpty ? 'Showing 1 to 0 of 0 entries' : 'Showing 0 to 0 of 0 entries' ?></div>
                <nav class="mun-pagination-wrap" id="munPaginationWrap" style="display:none;"><ul class="pagination pagination-sm mb-0" id="munPagination"></ul></nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<div id="munFilterPopup" class="mun-popup-overlay" style="display:none;">
    <div class="mun-popup">
        <div class="mun-popup-header"><h3 class="mun-popup-title">Filter</h3><span class="mun-popup-close" id="munCloseFilter">&times;</span></div>
        <div class="mun-popup-body">
            <?php
            $chOpts = [];
            foreach ($rows as $r) {
                $c = trim((string) ($r['channel'] ?? ''));
                if ($c !== '' && !in_array($c, $chOpts, true)) {
                    $chOpts[] = $c;
                }
            }
            sort($chOpts);
            ?>
            <label class="form-label small">Channel</label>
            <select id="munFilterCh" class="form-select form-select-sm">
                <option value="">All</option>
                <?php foreach ($chOpts as $c): ?>
                    <option value="<?= htmlspecialchars(strtolower($c)) ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mun-popup-footer">
            <button type="button" class="btn mun-btn-search btn-sm" id="munApplyFilter">Apply</button>
            <button type="button" class="btn btn-secondary btn-sm" id="munCancelFilter">Close</button>
        </div>
    </div>
</div>

<style>
    .mun-page .mun-content { padding: 20px 18px; background: #e8e8e8; }
    .mun-report-panel { background: #fff; border: 1px solid #d5d5d5; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.07); padding: 20px 22px 8px; }
    .mun-page .mun-topbar { justify-content: space-between; padding-left: 12px; padding-right: 16px; background: #fff; border-bottom: 1px solid #e5e5e5; }
    .mun-page .admin-topbar-title { font-size: 15px; font-weight: 600; color: #111; }
    .mun-search-group { display: flex; flex-wrap: nowrap; width: 100%; }
    .mun-input { flex: 1 1 auto; min-width: 0; height: 38px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; }
    .mun-clear { height: 38px; padding: 0 12px; border-left: 0; color: #666; background: #fff; }
    .mun-btn-search { background: #0096C7 !important; border: 1px solid #0096C7 !important; color: #fff !important; height: 38px; font-size: 13px; padding: 0 18px; font-weight: 600; border-radius: 0 4px 4px 0; }
    .mun-btn-filter { background: #5BC0DE !important; border: 1px solid #4fb3d4 !important; color: #fff !important; height: 38px; font-size: 13px; padding: 0 16px; font-weight: 600; border-radius: 4px; }
    .mun-show-wrap { font-size: 13px; color: #333; display: inline-flex; align-items: center; }
    .mun-select { height: 32px; font-size: 13px; min-width: 72px; }
    .mun-tabs { border-bottom: 1px solid #c8c8c8; padding-top: 4px; gap: 6px; }
    .mun-tabs .nav-link { background: #ebebeb; border: 1px solid #c8c8c8; border-bottom: 0; border-radius: 4px 4px 0 0; color: #333; font-size: 12px; font-weight: 700; padding: 9px 18px; min-width: 120px; text-align: center; cursor: default; }
    .mun-tabs .nav-link.active { background: #fff; color: #0096C7; border-top: 3px solid #5BC0DE; border-color: #c8c8c8 #c8c8c8 #fff; margin-bottom: -1px; padding-top: 7px; }
    .mun-tabs .nav-link.active small { color: #0096C7; }
    .mun-table-wrap { border: 1px solid #d0d0d0; border-top: 0; border-radius: 0 0 4px 4px; }
    .mun-table { font-size: 13px; color: #333; }
    .mun-table thead th { background: #888888 !important; color: #fff !important; font-weight: 700; border-color: #7a7a7a !important; padding: 12px 14px; }
    .mun-th-sort { cursor: pointer; user-select: none; white-space: nowrap; }
    .mun-sort-arrows { display: inline-flex; flex-direction: column; font-size: 9px; line-height: 0.7; vertical-align: middle; }
    .mun-sort-arrows .fa { display: block; }
    .mun-table tbody td { padding: 11px 14px; border-color: #ddd; background: #fff; }
    .mun-table tbody tr:nth-child(even):not(.mun-empty-row):not(.mun-filter-empty) td { background: #f4f4f4; }
    tr.mun-empty-row td, tr.mun-filter-empty td { background: #f8d7da !important; color: #721c24 !important; font-weight: 600; text-align: center; padding: 14px; }
    .mun-footer { border-top: 1px solid #eaeaea; }
    .mun-pagination-wrap .pagination .page-link { padding: 0.25rem 0.65rem; font-size: 12px; }
    .mun-popup-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1050; display: flex; align-items: center; justify-content: center; }
    .mun-popup { background: #fff; border-radius: 6px; width: 400px; max-width: 92%; box-shadow: 0 8px 28px rgba(0,0,0,.18); }
    .mun-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #e8e8e8; }
    .mun-popup-title { margin: 0; font-size: 16px; font-weight: 600; }
    .mun-popup-close { cursor: pointer; font-size: 22px; color: #666; }
    .mun-popup-body { padding: 16px 18px; }
    .mun-popup-footer { padding: 12px 18px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #eee; }
</style>

<script>
(function(){
    var tbody = document.getElementById('munTbody');
    var searchInput = document.getElementById('munSearch');
    var showSel = document.getElementById('munShowEntries');
    var infoEl = document.getElementById('munInfo');
    var pagWrap = document.getElementById('munPaginationWrap');
    var pagUl = document.getElementById('munPagination');
    var chFilter = '';
    var currentPage = 1;
    var dtSortDesc = true;
    function rows() { return Array.from(document.querySelectorAll('.mun-row')); }
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
            if (chFilter && (row.getAttribute('data-channel') || '') !== chFilter) return false;
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
    function removeFE() { var x = tbody.querySelector('.mun-filter-empty'); if (x) x.remove(); }
    function showFE() { removeFE(); var tr = document.createElement('tr'); tr.className = 'mun-filter-empty mun-empty-row'; tr.innerHTML = '<td colspan="4">No Record found</td>'; tbody.appendChild(tr); }
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
    document.getElementById('munSortDt') && document.getElementById('munSortDt').addEventListener('click', function() { dtSortDesc = !dtSortDesc; sortRows(); resetPage(); });
    searchInput && searchInput.addEventListener('keyup', resetPage);
    document.getElementById('munSearchBtn') && document.getElementById('munSearchBtn').addEventListener('click', resetPage);
    document.getElementById('munClearSearch') && document.getElementById('munClearSearch').addEventListener('click', function() { searchInput.value = ''; resetPage(); });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });
    document.getElementById('munOpenFilter') && document.getElementById('munOpenFilter').addEventListener('click', function() { document.getElementById('munFilterPopup').style.display = 'flex'; });
    function cf() { document.getElementById('munFilterPopup').style.display = 'none'; }
    document.getElementById('munCloseFilter') && document.getElementById('munCloseFilter').addEventListener('click', cf);
    document.getElementById('munCancelFilter') && document.getElementById('munCancelFilter').addEventListener('click', cf);
    document.getElementById('munApplyFilter') && document.getElementById('munApplyFilter').addEventListener('click', function() {
        var s = document.getElementById('munFilterCh'); chFilter = s && s.value ? s.value : ''; cf(); resetPage();
    });
    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
