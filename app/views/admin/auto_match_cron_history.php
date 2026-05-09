<?php
$title = 'Auto Match Cron History';
$reportTotal = count($cronRows);
$cronStatusTabs = AutoMatchCronHistoryModel::cronStatusTabs();
$amchTabCounts = ['all' => $reportTotal];
foreach ($cronStatusTabs as $_t) {
    $amchTabCounts[$_t] = 0;
}
foreach ($cronRows as $_r) {
    $_s = trim((string) ($_r['status'] ?? ''));
    if (isset($amchTabCounts[$_s])) {
        $amchTabCounts[$_s]++;
    }
}
$amchStatusBadgeClass = static function (string $s): string {
    $s = trim($s);
    $map = [
        'Completed' => 'success',
        'Running' => 'primary',
        'Failed' => 'danger',
    ];

    return $map[$s] ?? 'secondary';
};
$fmtDt = static function (?string $raw): string {
    if ($raw === null || $raw === '') {
        return 'N/A';
    }
    $t = strtotime($raw);

    return $t ? date('M j, Y g:i A', $t) : 'N/A';
};
$na = static function ($v) {
    if ($v === null || $v === '') {
        return 'N/A';
    }

    return (string) $v;
};

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-manage-members.css">

<div class="admin-main lead-gen-report-page amch-page">
<div class="admin-topbar">
    <div class="admin-topbar-left">
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
        <div class="admin-topbar-title">Manage Auto Match Cron History - ALL</div>
    </div>
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
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show py-2 small mb-3">
                <?= htmlspecialchars($_SESSION['flash_success']);
        unset($_SESSION['flash_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 small mb-3">
                <?= htmlspecialchars($_SESSION['flash_error']);
        unset($_SESSION['flash_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="report-panel">
            <div class="report-page-heading mb-3">Manage Auto Match Cron History - ALL</div>

            <div class="report-controls-top">
                <div class="report-search-wrap">
                    <div class="input-group lgr-search-group align-items-stretch flex-nowrap">
                        <input type="text" id="amchSearch" class="form-control lgr-search-input" placeholder="Search here...">
                        <button class="btn btn-light border lgr-clear-search flex-shrink-0" type="button" id="amchClearSearch" aria-label="Clear"><i class="fa fa-times"></i></button>
                        <button class="btn btn-primary flex-shrink-0" type="button" id="amchSearchBtn"><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
            </div>
            <div class="report-show-row mt-2 mb-2">
                <span class="show-entry-wrap"><label class="me-2 mb-0">Show</label><select id="amchShowEntries" class="form-select form-select-sm d-inline-block w-auto"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="9999">All</option></select><label class="ms-2 mb-0">Entries</label></span>
            </div>

            <ul class="nav nav-tabs lgr-tabs mb-0 amch-status-tabs" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active amch-tab" data-amch-tab="all" id="amchTabAll">All <small>(<?= (int) $amchTabCounts['all'] ?>)</small></button>
                </li>
                <?php foreach ($cronStatusTabs as $tabLabel): ?>
                <li class="nav-item">
                    <button type="button" class="nav-link amch-tab" data-amch-tab="<?= htmlspecialchars($tabLabel, ENT_QUOTES, 'UTF-8') ?>" id="amchTab<?= preg_replace('/\W+/', '', $tabLabel) ?>"><?= htmlspecialchars($tabLabel) ?> <small>(<?= (int) ($amchTabCounts[$tabLabel] ?? 0) ?>)</small></button>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="table-responsive lgr-table-wrap">
                <table class="table table-striped table-hover table-bordered mb-0 align-middle lgr-table">
                    <thead class="table-dark">
                        <tr>
                            <th>Status</th>
                            <th>Start At</th>
                            <th class="lgr-th-sort">End At <i class="fa fa-sort-desc ms-1" aria-hidden="true"></i></th>
                            <th>Sent Emails Count</th>
                            <th class="text-center" style="width:72px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="amchTbody">
                        <?php foreach ($cronRows as $R):
                            $rowStatus = trim((string) ($R['status'] ?? ''));
                            $searchBlob = strtolower(trim(implode(' ', [
                                $rowStatus,
                                $fmtDt($R['started_at'] ?? null),
                                $fmtDt($R['ended_at'] ?? null),
                                (string) ($R['sent_emails_count'] ?? ''),
                            ])));
                            ?>
                        <tr class="amch-row" data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>" data-cron-status="<?= htmlspecialchars($rowStatus, ENT_QUOTES, 'UTF-8') ?>">
                            <td><span class="badge bg-<?= htmlspecialchars($amchStatusBadgeClass($rowStatus), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($rowStatus !== '' ? $rowStatus : 'N/A') ?></span></td>
                            <td><?= htmlspecialchars($fmtDt($R['started_at'] ?? null)) ?></td>
                            <td><?= htmlspecialchars($fmtDt($R['ended_at'] ?? null)) ?></td>
                            <td><?= htmlspecialchars((string) ($R['sent_emails_count'] ?? '0')) ?></td>
                            <td class="text-center">
                                <form method="POST" action="<?= BASE_URL ?>/admin/reports/match-making/auto-match-email/cron-history/delete" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= (int) ($R['id'] ?? 0) ?>">
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0 lgr-trash" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="lgr-footer-bar d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center gap-2 mt-2 pt-2 px-2 pb-3">
                <div class="small text-muted flex-shrink-0" id="amchInfo">Showing 0 to 0 of 0 entries</div>
                <nav class="amch-pag-wrap flex-shrink-0" aria-label="Pagination">
                    <ul class="pagination pagination-sm mb-0 amch-pagination" id="amchPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<style>.amch-status-tabs .nav-link.amch-tab{border:0;background:transparent;cursor:pointer}.amch-status-tabs .nav-link.amch-tab.active{font-weight:600}</style>
<script>
(function(){
    const searchInput = document.getElementById('amchSearch');
    const rows = () => Array.from(document.querySelectorAll('.amch-row'));
    const showSel = document.getElementById('amchShowEntries');
    const infoEl = document.getElementById('amchInfo');
    const pagEl = document.getElementById('amchPagination');
    let currentPage = 1;
    let activeStatusTab = 'all';

    function filtered() {
        const q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        return rows().filter(function(r) {
            if (activeStatusTab !== 'all') {
                const st = (r.getAttribute('data-cron-status') || '').trim();
                if (st !== activeStatusTab) return false;
            }
            return !q || (r.getAttribute('data-search') || '').indexOf(q) !== -1;
        });
    }

    document.querySelectorAll('.amch-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            activeStatusTab = (btn.getAttribute('data-amch-tab') || 'all');
            document.querySelectorAll('.amch-tab').forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            currentPage = 1;
            render();
        });
    });

    function appendPageLink(text, targetPage, isActive, isDisabled) {
        const li = document.createElement('li');
        li.className = 'page-item';
        if (isActive) li.classList.add('active');
        if (isDisabled) li.classList.add('disabled');
        const isStatic = isActive || isDisabled;
        const el = document.createElement(isStatic ? 'span' : 'a');
        el.className = 'page-link';
        el.textContent = text;
        if (!isStatic) {
            el.href = '#';
            el.addEventListener('click', function(e) {
                e.preventDefault();
                currentPage = targetPage;
                render();
            });
        }
        li.appendChild(el);
        pagEl.appendChild(li);
    }

    function render() {
        const all = filtered();
        const total = all.length;
        let per = parseInt(showSel && showSel.value ? showSel.value : '10', 10);
        if (isNaN(per) || per < 1) per = 10;
        let pages = 0;
        if (total === 0) {
            pages = 0;
            currentPage = 1;
        } else if (per >= 9999) {
            pages = 1;
        } else {
            pages = Math.max(1, Math.ceil(total / per));
        }
        if (currentPage > pages && pages > 0) currentPage = pages;
        if (currentPage < 1) currentPage = 1;
        const start = total === 0 ? 0 : (per >= 9999 ? 1 : (currentPage - 1) * per + 1);
        const end = total === 0 ? 0 : (per >= 9999 ? total : Math.min(currentPage * per, total));

        rows().forEach(function(r) { r.style.display = 'none'; });
        if (per >= 9999) {
            all.forEach(function(r) { r.style.display = ''; });
        } else {
            all.slice((currentPage - 1) * per, currentPage * per).forEach(function(r) {
                r.style.display = '';
            });
        }

        infoEl.textContent = total === 0
            ? 'Showing 0 to 0 of 0 entries'
            : ('Showing ' + start + ' to ' + end + ' of ' + total + ' entries');

        pagEl.innerHTML = '';
        if (pages <= 0) return;

        appendPageLink('Previous', currentPage - 1, false, currentPage === 1);

        for (let p = 1; p <= pages; p++) {
            const active = p === currentPage;
            appendPageLink(String(p), p, active, false);
        }

        appendPageLink('Next', currentPage + 1, false, currentPage === pages);
        appendPageLink('Last', pages, false, currentPage === pages);
    }

    searchInput && searchInput.addEventListener('keyup', function() { currentPage = 1; render(); });
    document.getElementById('amchSearchBtn') && document.getElementById('amchSearchBtn').addEventListener('click', function() { currentPage = 1; render(); });
    document.getElementById('amchClearSearch') && document.getElementById('amchClearSearch').addEventListener('click', function() {
        searchInput.value = '';
        currentPage = 1;
        render();
    });
    showSel && showSel.addEventListener('change', function() { currentPage = 1; render(); });

    render();
})();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
