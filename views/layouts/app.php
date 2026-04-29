<?php
declare(strict_types=1);
/** @var string $content */
/** @var ?array  $currentUser */
/** @var ?string $currentPath */
/** @var ?string $title */
/** @var ?array  $breadcrumbs */
$path     = $currentPath ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$pageTitle= ($title ?? 'Dashboard') . ' — AviatorTutor';
$isAdmin  = isset($currentUser['role']) && $currentUser['role'] === 'admin';
$nav = [
    ['/dashboard',  'Dashboard',  'home'],
    ['/systems',    'Systems',    'layers'],
    ['/flashcards', 'Flashcards', 'cards'],
    ['/quiz',       'Quizzes',    'check'],
    ['/progress',   'Progress',   'chart'],
    ['/planner',    'Planner',    'calendar'],
    ['/search',     'Search',     'search'],
];
$active = static function (string $href) use ($path): string {
    if ($href === '/dashboard') {
        return $path === '/dashboard' ? ' active' : '';
    }
    return ($path === $href || str_starts_with($path ?? '', $href . '/')) ? ' active' : '';
};
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="theme-color" content="#0b1322">
    <meta name="robots" content="noindex">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="/assets/css/marketing.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/marketing.css') ?: '0' ?>">
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/app.css') ?: '0' ?>">
    <link rel="stylesheet" href="/assets/css/app-theme.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/app-theme.css') ?: '0' ?>">
    <?php include __DIR__ . '/../partials/head-favicons.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
</head>
<body class="app-body">
    <input type="checkbox" id="navToggle" class="nav-toggle" hidden>
    <header class="site-header app-header">
        <div class="container site-header__inner">
            <a href="/dashboard" class="brand" aria-label="AviatorTutor dashboard">
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
            <nav class="app-nav">
                <?php foreach ($nav as [$href, $label, $_icon]): ?>
                    <a href="<?= $href ?>" class="<?= trim($active($href)) ?>"><?= htmlspecialchars($label) ?></a>
                <?php endforeach; ?>
                <?php if ($isAdmin): ?>
                    <a href="/admin" class="<?= trim($active('/admin')) ?>">Admin</a>
                <?php endif; ?>
                <span class="app-nav__sep" aria-hidden="true"></span>
                <?php if (!empty($currentUser)): ?>
                    <a href="/account" class="app-nav__user">
                        <span class="app-nav__avatar"><?= strtoupper(mb_substr((string) ($currentUser['name'] ?? '?'), 0, 1)) ?></span>
                        <span class="app-nav__name"><?= htmlspecialchars((string) $currentUser['name']) ?></span>
                    </a>
                    <a href="/logout" class="app-nav__logout">Sign out</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-sm">Sign in</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="app-main">
        <div class="container">
            <?php if (!empty($_SESSION['flash_ok'])): ?>
                <div class="flash flash--success" style="margin-top:24px;"><?= htmlspecialchars($_SESSION['flash_ok'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php unset($_SESSION['flash_ok']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="flash flash--error" style="margin-top:24px;"><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_notice'])): ?>
                <div class="flash flash--info" style="margin-top:24px;"><?= htmlspecialchars($_SESSION['flash_notice'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php unset($_SESSION['flash_notice']); ?>
            <?php endif; ?>

            <?php if (!empty($breadcrumbs)): ?>
                <nav aria-label="Breadcrumb" class="app-breadcrumb">
                    <?php foreach ($breadcrumbs as $i => $crumb):
                        $isLast = $i === count($breadcrumbs) - 1; ?>
                        <?php if ($isLast): ?>
                            <span class="app-breadcrumb__current"><?= htmlspecialchars($crumb['label']) ?></span>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                            <span class="app-breadcrumb__sep">/</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </main>

    <footer class="site-footer app-footer">
        <div class="container site-footer__bottom" style="border-top:none;padding-top:0;">
            <div>© <?= date('Y') ?> AviatorTutor.com</div>
            <div><a href="/contact">Contact</a> · <a href="/faq">FAQ</a> · <a href="/account">Account</a></div>
        </div>
    </footer>
</body>
</html>
