<?php
$title = "Paid Profiles";
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<style>
	.sales-main {
		margin-left: var(--sidebar-width);
		transition: margin-left .28s ease;
		min-height: 100vh;
		background: #efefef;
	}
	body.admin-sidebar-collapsed .sales-main { margin-left: var(--sidebar-collapsed-width); }
	.sales-wrap { padding: 14px; }
	.sales-title { font-size: 13px; font-weight: 700; color: #565656; margin-bottom: 10px; }
	.sales-panel { background: #f8f8f8; border: 1px solid #d9d9d9; border-radius: 3px; padding: 12px; }
	.sales-tabs { display: flex; gap: 8px; flex-wrap: wrap; border-bottom: 1px solid #ddd; padding-bottom: 8px; margin-bottom: 10px; }
	.sales-tab { background: #e8e8e8; border: 1px solid #d8d8d8; color: #444; text-decoration: none; border-radius: 3px 3px 0 0; padding: 8px 12px; font-size: 12px; font-weight: 700; }
	.sales-tab small { display: block; font-size: 10px; color: #666; font-weight: 600; }
	.sales-tab.active { background: #53c5eb; color: #fff; border-color: #49bbe0; }
	.sales-tab.active small { color: #fff; }
	.table-sm td, .table-sm th { font-size: 12px; }
	.pagination-row { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; gap: 10px; flex-wrap: wrap; }
	@media (max-width: 991.98px) {
		.sales-main { margin-left: 0 !important; }
		body.admin-sidebar-collapsed .sales-main { margin-left: 0 !important; }
	}
</style>

<div class="sales-main">
	<div class="admin-topbar">
		<button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
		<div class="admin-profile" id="adminProfileTrigger">
			<div class="admin-profile-box">
				<span><?= htmlspecialchars($this->displayadminname()) ?></span>
				<i class="fa fa-user"></i>
			</div>
			<div class="admin-dropdown" id="adminDropdown">
				<a href="<?= BASE_URL ?>/admin/change-password"><i class="fa fa-key"></i> Change Password</a>
				<a href="<?= BASE_URL ?>/admin/logout"><i class="fa fa-sign-out"></i> Logout</a>
			</div>
		</div>
	</div>
	<div class="sales-wrap">
		<div class="sales-title">Members Sales Report</div>
		<div class="sales-panel">
			<form method="get" action="<?= BASE_URL ?>/admin/sales-report" class="row g-2 align-items-center mb-3">
				<input type="hidden" name="tab" value="<?= htmlspecialchars($tab ?? 'reg_receivable') ?>">
				<div class="col-lg-5">
					<div class="input-group">
						<input type="search" name="search_filed" class="form-control" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search here..">
						<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>
				</div>
				<div class="col-lg-2">
					<select name="limit_per_page" class="form-select" onchange="this.form.submit()">
						<?php foreach ([1,2,3,5,10,25,50,100] as $n): ?>
							<option value="<?= $n ?>" <?= ((int)($limit ?? 10) === $n) ? 'selected' : '' ?>>Show <?= $n ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</form>

			<div class="sales-tabs">
				<a class="sales-tab <?= ($tab ?? '') === 'reg_receivable' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/sales-report?tab=reg_receivable&search_filed=<?= urlencode($search ?? '') ?>&limit_per_page=<?= (int)($limit ?? 10) ?>">Registration fee receivable <small>(<?= (int)($tabCounts['reg_receivable'] ?? 0) ?>)</small></a>
				<a class="sales-tab <?= ($tab ?? '') === 'reg_received' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/sales-report?tab=reg_received&search_filed=<?= urlencode($search ?? '') ?>&limit_per_page=<?= (int)($limit ?? 10) ?>">Registration fee received <small>(<?= (int)($tabCounts['reg_received'] ?? 0) ?>)</small></a>
				<a class="sales-tab <?= ($tab ?? '') === 'rishta_receivable' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/sales-report?tab=rishta_receivable&search_filed=<?= urlencode($search ?? '') ?>&limit_per_page=<?= (int)($limit ?? 10) ?>">Rishta fee receivable <small>(<?= (int)($tabCounts['rishta_receivable'] ?? 0) ?>)</small></a>
				<a class="sales-tab <?= ($tab ?? '') === 'rishta_received' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/sales-report?tab=rishta_received&search_filed=<?= urlencode($search ?? '') ?>&limit_per_page=<?= (int)($limit ?? 10) ?>">Rishta fee received <small>(<?= (int)($tabCounts['rishta_received'] ?? 0) ?>)</small></a>
			</div>

			<?php if (empty($rows)): ?>
				<div class="alert alert-warning mb-0">No record found.</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-sm">
						<thead>
							<tr>
								<th>ID</th>
								<th>Matri ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>Mobile</th>
								<th>City</th>
								<th>Registration Fee</th>
								<th>Rishta Fee</th>
								<th>User Status</th>
								<th>Featured Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($rows as $r): ?>
								<tr>
									<td><?= (int)$r['id'] ?></td>
									<td><?= htmlspecialchars($r['matri_id'] ?? '-') ?></td>
									<td><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['second_name'] ?? ''))) ?></td>
									<td><?= htmlspecialchars($r['email'] ?? '-') ?></td>
									<td><?= htmlspecialchars($r['mobile_number'] ?? '-') ?></td>
									<td><?= htmlspecialchars($r['city'] ?? '-') ?></td>
									<td><?= number_format((float)($r['registration_fee'] ?? 0), 2) ?></td>
									<td><?= number_format((float)($r['final_fee'] ?? 0), 2) ?></td>
									<td><?= htmlspecialchars($r['user_status'] ?? '-') ?></td>
									<td><?= htmlspecialchars($r['featured_status'] ?? '-') ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>

			<div class="pagination-row">
				<div>Page <?= (int)($page ?? 1) ?> of <?= (int)($totalPages ?? 1) ?> | Total <?= (int)($totalRows ?? 0) ?> record(s)</div>
				<div class="d-flex gap-2">
					<?php
						$prev = max(1, (int)($page ?? 1) - 1);
						$next = min((int)($totalPages ?? 1), (int)($page ?? 1) + 1);
						$q = urlencode($search ?? '');
						$l = (int)($limit ?? 10);
						$t = urlencode($tab ?? 'reg_receivable');
					?>
					<a class="btn btn-sm btn-outline-secondary <?= (int)($page ?? 1) <= 1 ? 'disabled' : '' ?>" href="<?= BASE_URL ?>/admin/sales-report?tab=<?= $t ?>&search_filed=<?= $q ?>&limit_per_page=<?= $l ?>&page=<?= $prev ?>">Prev</a>
					<a class="btn btn-sm btn-outline-secondary <?= (int)($page ?? 1) >= (int)($totalPages ?? 1) ? 'disabled' : '' ?>" href="<?= BASE_URL ?>/admin/sales-report?tab=<?= $t ?>&search_filed=<?= $q ?>&limit_per_page=<?= $l ?>&page=<?= $next ?>">Next</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>