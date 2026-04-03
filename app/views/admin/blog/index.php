<?php
$title = "Admin Blogs";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
?>

<style>
.admin-content {
    background: #f5f5f5;
    min-height: 100vh;
    padding: 25px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #444;
    margin: 0;
}

.blog-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}

.table thead th {
    background: #fafafa;
    font-weight: 600;
    color: #555;
    border-bottom: 2px solid #ddd;
    padding: 14px;
}

.table td {
    vertical-align: middle;
    color: #666;
    padding: 14px;
}

.badge-published {
    background: #2ecc71;
    color: #fff;
    padding: 6px 12px;
    font-size: 13px;
}

.badge-draft {
    background: #95a5a6;
    color: #fff;
    padding: 6px 12px;
    font-size: 13px;
}

.btn-add {
    background: #2196f3;
    border: none;
    color: #fff;
    padding: 10px 20px;
    font-weight: 500;
}

.btn-add:hover {
    background: #1976d2;
    color: #fff;
}

.btn-delete {
    background: #e74c3c;
    border: none;
    color: #fff;
    padding: 6px 14px;
    font-size: 13px;
}

.btn-delete:hover {
    background: #c0392b;
    color: #fff;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .blog-card {
        padding: 15px;
    }
}
</style>

<div class="admin-content container">

    <div class="page-header">
        <h2 class="page-title">Blogs</h2>

        <a href="<?= BASE_URL ?>/admin/blog/create" class="btn btn-add">
            Add New Blog
        </a>
    </div>

    <div class="blog-card">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach($blogs as $b): ?>
                    <tr>

                        <td><?= $b['id'] ?></td>

                        <td><?= htmlspecialchars($b['title']) ?></td>

                        <td>
                            <?php if($b['status'] === 'published'): ?>
                                <span class="badge badge-published">Published</span>
                            <?php else: ?>
                                <span class="badge badge-draft">Draft</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="<?= BASE_URL ?>/admin/blog/delete/<?= $b['id'] ?>"
                               class="btn btn-delete"
                               onclick="return confirm('Are you sure you want to delete this blog?');">
                               Delete
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>