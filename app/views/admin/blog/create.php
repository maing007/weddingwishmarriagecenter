<?php
$title = "Add New Blog";
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/sidebar.php';
?>

<style>
.admin-content {
    background: #f5f5f5;
    min-height: 100vh;
    padding: 25px;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #444;
    margin-bottom: 25px;
}

.blog-card {
    background: #fff;
    padding: 30px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    max-width: 900px;
}

.form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 8px;
}

.form-control,
.form-select {
    border-radius: 0;
    border: 1px solid #ddd;
    min-height: 48px;
    box-shadow: none;
}

textarea.form-control {
    min-height: 180px;
    resize: vertical;
}

.form-control:focus,
.form-select:focus {
    border-color: #2196f3;
    box-shadow: none;
}

.btn-publish {
    background: #2ecc71;
    border: none;
    color: #fff;
    padding: 10px 24px;
    font-weight: 500;
}

.btn-publish:hover {
    background: #27ae60;
    color: #fff;
}

.btn-cancel {
    background: #95a5a6;
    border: none;
    color: #fff;
    padding: 10px 24px;
    font-weight: 500;
}

.btn-cancel:hover {
    background: #7f8c8d;
    color: #fff;
}

.media-card {
    background: #fff;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    max-width: 900px;
    margin-bottom: 16px;
}
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    gap: 14px;
}
.media-item {
    border: 1px solid #e4e4e4;
    border-radius: 6px;
    overflow: hidden;
    background: #fafafa;
}
.media-preview {
    width: 100%;
    height: 120px;
    object-fit: cover;
    background: #f0f0f0;
    display: block;
}
.media-meta {
    padding: 10px;
    font-size: 12px;
    color: #666;
}
.media-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.btn-delete-media {
    width: 100%;
    margin-top: 8px;
    background: #e74c3c;
    color: #fff;
    border: 0;
    padding: 8px 10px;
}
.btn-delete-media:hover {
    background: #c0392b;
    color: #fff;
}

@media (max-width: 768px) {
    .blog-card {
        padding: 20px;
    }
}
</style>

<div class="admin-content container">

    <div class="page-title">Add New Blog</div>

    <div class="media-card">
        <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 12px;">
            <h5 style="margin:0; font-weight:600;">Media Management</h5>
            <span class="badge bg-primary">
                Total Uploaded Files: <?= (int)count($mediaFiles ?? []) ?>
            </span>
        </div>

        <?php if (empty($mediaFiles)): ?>
            <p class="text-muted" style="margin:0;">No uploaded media found in blog uploads yet.</p>
        <?php else: ?>
            <div class="media-grid">
                <?php foreach ($mediaFiles as $m): ?>
                    <div class="media-item">
                        <img src="<?= htmlspecialchars($m['url'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="Profile preview"
                             class="media-preview">
                        <div class="media-meta">
                            <div class="media-name" title="<?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div><?= number_format(((int)$m['size']) / 1024, 1) ?> KB</div>
                            <form method="POST" onsubmit="return confirm('Delete this media file?');">
                                <input type="hidden" name="media_delete" value="1">
                                <input type="hidden" name="file_name" value="<?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn-delete-media">Delete Media</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="blog-card">

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control"
                       placeholder="Enter blog title"
                       required>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea name="content"
                          id="content"
                          class="form-control"
                          rows="6"
                          placeholder="Enter blog content"
                          required></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Upload Image</label>
                <input type="file"
                       name="image"
                       id="image"
                       class="form-control">
            </div>

            <div class="mb-4">
                <label for="status" class="form-label">Status</label>
                <select name="status"
                        id="status"
                        class="form-select">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>

            <button type="submit" class="btn btn-publish">
                Publish
            </button>

            <a href="<?= BASE_URL ?>/admin/blogs"
               class="btn btn-cancel ms-2">
               Cancel
            </a>

        </form>

    </div>

</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>