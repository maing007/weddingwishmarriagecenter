<?php
$title = "Our Blogs";
require __DIR__ . '/../partials/header.php';
?>
<style><>
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
</style>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Our Blogs</h1>

    <div class="row g-4">
        <?php foreach($blogs as $b): ?>
            <div class="col-sm-6 col-md-4">
                <div class="card h-100 shadow-sm">
                    <?php if(!empty($b['image'])): ?>
                        <img src="<?= htmlspecialchars(public_url_for_path('uploads/blogs/' . (string) $b['image']), ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($b['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($b['title']) ?></h5>
                        <p class="card-text"><?= substr(strip_tags($b['content']), 0, 120) ?>...</p>
                        <a href="<?= BASE_URL ?>/blog/id/?id=<?= $b['id'] ?>" class="btn btn-primary mt-auto">Read More</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
