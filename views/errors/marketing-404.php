<?php /** @var ?string $message */ ?>
<section class="hero">
    <div class="container container-tight" style="text-align:center;padding:80px 24px;">
        <span class="hero__chip">404</span>
        <h1>Page not found</h1>
        <p class="lead"><?= htmlspecialchars($message ?? 'The page you were looking for doesn\'t exist or has moved.') ?></p>
        <div class="hero__cta" style="justify-content:center;">
            <a href="/" class="btn btn-primary btn-lg">Back to home</a>
            <a href="/aircraft" class="btn btn-lg">Browse aircraft</a>
        </div>
    </div>
</section>
