<?php
declare(strict_types=1);
/** @var string $content_for_layout */
$title       = $title       ?? 'AviatorTutor — Pilot self-study platform';
$description = $description ?? 'Self-study aviation platform for pilots and aviation learners. Aircraft systems, interview prep, quizzes, and a structured aviation question bank — for one simple monthly subscription.';
$canonical   = $canonical   ?? 'https://aviatortutor.com' . ($_SERVER['REQUEST_URI'] ?? '/');
$ogImage     = $ogImage     ?? 'https://aviatortutor.com/assets/og/aviatortutor-1200x630.png';
$path        = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isLogged    = !empty($_SESSION['auth_user']);
$isActiveSub = false;
if ($isLogged) {
    try {
        $isActiveSub = \App\Services\SubscriptionService::hasActive((int) $_SESSION['auth_user']['id']);
    } catch (\Throwable $e) { /* swallow if service not loadable */ }
}
$is = static fn(string $p): string => $path === $p ? ' active' : '';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="AviatorTutor">
    <meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Twitter card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="/assets/css/marketing.css">
    <link rel="stylesheet" href="/assets/css/app-theme.css">
    <meta name="theme-color" content="#0b1322">

    <?php if (!empty($jsonLd)): ?>
        <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <?php endif; ?>
</head>
<body>
    <input type="checkbox" id="navToggle" class="nav-toggle" hidden>
    <header class="site-header">
        <div class="container site-header__inner">
            <a href="/" class="brand">
                <span class="brand__logo">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                    </svg>
                </span>
                AviatorTutor
            </a>
            <label for="navToggle" class="nav-toggle-btn" aria-label="Toggle menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </label>
            <nav>
                <a href="/"<?= $is('/') ? ' class="active"' : '' ?>>Home</a>
                <a href="/pricing"<?= $is('/pricing') ? ' class="active"' : '' ?>>Pricing</a>
                <a href="/about"<?= $is('/about') ? ' class="active"' : '' ?>>About</a>
                <a href="/faq"<?= $is('/faq') ? ' class="active"' : '' ?>>FAQ</a>
                <a href="/contact"<?= $is('/contact') ? ' class="active"' : '' ?>>Contact</a>
                <?php if ($isLogged): ?>
                    <a href="/dashboard">Dashboard</a>
                    <?php if (!$isActiveSub): ?>
                        <a href="/redeem" class="btn btn-primary btn-sm">Redeem code</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/login">Sign in</a>
                    <a href="/register" class="btn btn-primary btn-sm">Start studying</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <?php if (!empty($_SESSION['flash_ok'])): ?>
            <div class="container" style="margin-top:24px;"><div class="flash flash--success"><?= htmlspecialchars($_SESSION['flash_ok'], ENT_QUOTES, 'UTF-8') ?></div></div>
            <?php unset($_SESSION['flash_ok']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="container" style="margin-top:24px;"><div class="flash flash--error"><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></div></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_notice'])): ?>
            <div class="container" style="margin-top:24px;"><div class="flash flash--info"><?= htmlspecialchars($_SESSION['flash_notice'], ENT_QUOTES, 'UTF-8') ?></div></div>
            <?php unset($_SESSION['flash_notice']); ?>
        <?php endif; ?>

        <?= $content_for_layout ?? '' ?>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div>
                <div>
                    <a href="/" class="brand" style="margin-bottom:12px;">
                        <span class="brand__logo">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                            </svg>
                        </span>
                        AviatorTutor
                    </a>
                    <p style="margin-top:12px;color:var(--text-soft);max-width:32ch;font-size:.85rem;">
                        Self-study aviation platform for pilots and aviation learners.
                    </p>
                </div>
                <div>
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                        <li><a href="/dashboard">Dashboard</a></li>
                        <li><a href="/redeem">Redeem code</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/about">About</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/faq">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="/privacy">Privacy</a></li>
                        <li><a href="/terms">Terms</a></li>
                        <li><a href="/sitemap.xml">Sitemap</a></li>
                    </ul>
                </div>
            </div>
            <div class="site-footer__bottom">
                <div>© <?= date('Y') ?> AviatorTutor.com — built for pilots who study seriously.</div>
                <div>v0.2 · self-study platform</div>
            </div>
        </div>
    </footer>
</body>
</html>
