<?php
require __DIR__ . '/../views/partials/header.php';
?>
    <meta charset="UTF-8">
    <title>404 - Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            height: 100%;
            background: linear-gradient(135deg, #5a0f14, #8b0000, #b11226);
            color: #8b0000;
            overflow: hidden;
        }

        .bg-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.18) 1px, transparent 1px);
            background-size: 45px 45px;
            animation: moveBg 12s linear infinite;
            z-index: 0;
        }

        @keyframes moveBg {
            from { background-position: 0 0; }
            to   { background-position: 120px 120px; }
        }

        .content {
            position: relative;
            z-index: 1;
            animation: fadeIn 1.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .error-code span {
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }

        .error-code span:nth-child(2) { animation-delay: .2s; }
        .error-code span:nth-child(3) { animation-delay: .4s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-20px); }
        }

        .btn-royal {
            background: #ffd6d6;
            color: #8b0000;
            font-weight: 700;
            border-radius: 50px;
            padding: 12px 34px;
            transition: all .3s ease;
        }

        .btn-royal:hover {
            background: #fff;
            color: #8b0000;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,.35);
        }
    </style>

<body class="d-flex align-items-center justify-content-center">

<div class="bg-pattern"></div>

<div class="container text-center content">
    <h1 class="display-1 fw-bold error-code mb-3">
        <span>4</span><span>0</span><span>4</span>
    </h1>

    <h2 class="fw-semibold mb-3 text-light">Page Not Found</h2>

    <p class="lead mb-4 text-light opacity-75 mx-auto" style="max-width: 450px;">
        The page you’re looking for may have been moved, deleted, or never existed.
    </p>

    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="btn btn-royal">
        Return Home
    </a>
</div>

</body>
<?php
require __DIR__ . '/../views/partials/footer.php';
?>
