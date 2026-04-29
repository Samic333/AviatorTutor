<?php
declare(strict_types=1);
/** @var string $content_for_layout */
$title       = $title       ?? 'AviatorTutor — Premium aviation learning platform';
$description = $description ?? 'Premium aviation learning platform built by pilots, cabin crew, SMS trainers, and instructors. Aircraft systems, weather, SOPs, QRH, CRM, SMS, cabin safety, and airline interview prep.';
$canonical   = $canonical   ?? 'https://aviatortutor.com' . ($_SERVER['REQUEST_URI'] ?? '/');
$ogImage     = $ogImage     ?? 'https://aviatortutor.com/assets/og/aviatortutor-1200x630.png';
$path        = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isLogged    = !empty($_SESSION['auth_user']);
$isActiveSub = false;
$isAdmin     = false;
if ($isLogged) {
    $isAdmin = ($_SESSION['auth_user']['role'] ?? '') === 'admin';
    try {
        $isActiveSub = $isAdmin || \App\Services\SubscriptionService::hasActive((int) $_SESSION['auth_user']['id']);
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="/assets/css/marketing.css">
    <link rel="stylesheet" href="/assets/css/app-theme.css">
    <meta name="theme-color" content="#0b1322">

    <!-- Favicons -->
    <?php include __DIR__ . '/../partials/head-favicons.php'; ?>

    <?php if (!empty($jsonLd)): ?>
        <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <?php endif; ?>
</head>
<body>
    <input type="checkbox" id="navToggle" class="nav-toggle" hidden>
    <header class="site-header">
        <div class="container site-header__inner">
            <a href="/" class="brand" aria-label="AviatorTutor home">
                <span class="brand__logo">
                    <svg width="22" height="22" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                        <path d="M11 51 L32 11 L53 51" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="22" y1="40" x2="42" y2="40" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                        <circle cx="32" cy="11" r="4" fill="#38BDF8"/>
                    </svg>
                </span>
                <span class="brand__wordmark">Aviator<span class="brand__wordmark-accent">Tutor</span></span>
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
                    <?php if ($isAdmin): ?>
                        <a href="/admin" class="active" style="color:var(--warn);">Admin</a>
                    <?php endif; ?>
                    <a href="/logout">Sign out</a>
                    <?php if (!$isActiveSub && !$isAdmin): ?>
                        <a href="/redeem" class="btn btn-primary btn-sm">Redeem code</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/login" class="btn btn-ghost btn-sm">Sign in</a>
                    <a href="/register" class="btn btn-primary btn-sm">Register</a>
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
                            <svg width="20" height="20" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                <path d="M11 51 L32 11 L53 51" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="22" y1="40" x2="42" y2="40" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                                <circle cx="32" cy="11" r="4" fill="#38BDF8"/>
                            </svg>
                        </span>
                        <span class="brand__wordmark">Aviator<span class="brand__wordmark-accent">Tutor</span></span>
                    </a>
                    <p style="margin-top:12px;color:var(--text-soft);max-width:32ch;font-size:.85rem;">
                        Premium aviation learning platform — built by pilots, cabin crew, SMS trainers, and instructors for the entire aviation community.
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
                <div>© <?= date('Y') ?> AviatorTutor.com — premium aviation learning, built by aviation professionals.</div>
                <div>v0.2 · self-study platform</div>
            </div>
        </div>
    </footer>
</body>
</html>
