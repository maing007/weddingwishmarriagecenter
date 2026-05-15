<?php
if (!isset($title)) {
    $title = $blog['title'] ?? 'Blog Detail';
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Blog Image -->
            <?php if(!empty($blog['image'])): ?>
                <img src="<?= htmlspecialchars(public_url_for_path('uploads/blogs/' . (string) $blog['image']), ENT_QUOTES, 'UTF-8') ?>" 
                     class="img-fluid rounded mb-4 shadow-sm" 
                     alt="<?= htmlspecialchars($blog['title']) ?>">
            <?php endif; ?>

            <!-- Blog Title -->
            <h1 class="mb-3"><?= htmlspecialchars($blog['title']) ?></h1>

            <!-- Blog Metadata -->
            <?php if(!empty($blog['created_at'])): ?>
                <p class="text-muted mb-4">
                    Published on <?= date("F j, Y", strtotime($blog['created_at'])) ?>
                </p>
            <?php endif; ?>

            <!-- Blog Content -->
            <div class="blog-content">
                <?= nl2br($blog['content']) ?>
            </div>

            <!-- Back Button -->
            <a href="<?= BASE_URL ?>/blogs" class="btn btn-secondary mt-4">← Back to Blogs</a>
        </div>
    </div>
</div>

<?php 
// require __DIR__ . '/../partials/footer.php';
 ?>
