<?php
$title = "Contact Messages";
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<style>
    body {
        background: #f5f7fb;
    }

    .page-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
    }

    .bulk-bar {
        background: #fff;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .table thead {
        background: #00a8c6;
        color: #fff;
    }

    .btn-delete {
        background: #e74c3c;
        color: #fff;
    }

    .btn-export {
        background: #27ae60;
        color: #fff;
    }

    .badge-id {
        background: #eee;
        padding: 5px 10px;
        border-radius: 6px;
    }

    .spacer {
        margin-top: 97px;
    }
</style>

<div class="container spacer mt-4">

    <h4 class="mb-3">📩 Contact Messages</h4>

    <!-- SUCCESS / ERROR -->
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- BULK ACTION -->
    <form method="POST" action="<?= BASE_URL ?>/admin/contact/bulk-action">

        <div class="bulk-bar d-flex justify-content-between align-items-center">

            <div>
                <input type="checkbox" id="selectAll">
                <label><strong>Select All</strong></label>
            </div>

            <div class="d-flex">
                <select name="action" class="form-control me-2">
                    <option value="delete">Delete</option>
                    <option value="export">Export CSV</option>
                </select>

                <button type="submit" class="btn btn-primary btn-sm me-2">Apply</button>

                <a href="<?= BASE_URL ?>/admin/contact/export-all"
                    class="btn btn-export btn-sm">
                    Export All
                </a>
            </div>

        </div>

        <!-- TABLE -->
        <div class="page-card">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($Contact_Messages)): ?>
                        <?php foreach ($Contact_Messages as $c): ?>

                            <tr>

                                <td>
                                    <input type="checkbox" name="ids[]" value="<?= $c['id'] ?>">
                                </td>

                                <td>
                                    <span class="badge-id">#<?= htmlspecialchars($c['id']) ?></span>
                                </td>

                                <td><?= htmlspecialchars($c['name']) ?></td>

                                <td><?= htmlspecialchars($c['phone']) ?></td>

                                <td>
                                    <strong><?= htmlspecialchars($c['subject']) ?></strong>
                                </td>

                                <td style="max-width:250px;">
                                    <?= htmlspecialchars(substr($c['description'], 0, 60)) ?>...
                                </td>

                                <td>
                                    <a href="<?= BASE_URL ?>/admin/contact/delete/<?= $c['id'] ?>"
                                        onclick="return confirm('Delete this message?')"
                                        class="btn btn-delete btn-sm">
                                        Delete
                                    </a>
                                </td>

                            </tr>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No messages found
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </form>

</div>

<!-- JS -->
<script>
    document.getElementById('selectAll').addEventListener('click', function() {
        let checkboxes = document.querySelectorAll('input[name="ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>