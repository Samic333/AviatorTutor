<?php
declare(strict_types=1);
/** @var string $content */
/** @var ?array $currentUser */
/** @var ?string $title */

$adminName  = htmlspecialchars((string)($currentUser['name'] ?? 'Admin'));
$adminInit  = strtoupper(mb_substr((string)($currentUser['name'] ?? 'A'), 0, 1));
$path       = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$pageTitle  = ($title ?? 'Admin') . ' — AviatorTutor Admin';

$navItems = [
    ['/admin',                 'Overview',      '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
    ['/admin/users',           'Users',         '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
    ['/admin/subscriptions',   'Subscriptions', '<path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>'],
    ['/admin/content',         'Content',       '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>'],
    ['/admin/aircrafts',       'Aircraft',      '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>'],
    ['/admin/pricing',         'Pricing',       '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
    ['/admin/codes',           'Codes',         '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
    ['/admin/leads',           'Leads',         '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>'],
    ['/admin/contacts',        'Inquiries',     '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
    ['/admin/settings',        'Settings',      '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>'],
];

// Unread inquiry count for sidebar badge
$inquiryUnread = 0;
try {
    $row = \App\Core\DB::instance()->queryOne('SELECT COUNT(*) AS c FROM contact_messages WHERE status = "new"');
    $inquiryUnread = (int) ($row['c'] ?? 0);
} catch (\Throwable $e) { /* table not migrated */ }

$isActive = function(string $href) use ($path): bool {
    if ($href === '/admin') return $path === '/admin' || $path === '/admin/dashboard';
    return str_starts_with($path, $href);
};
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="adm-body">
<div class="adm-overlay" id="adm-overlay"></div>
<div class="adm-shell">

  <!-- Sidebar -->
  <aside class="adm-sidebar" id="adm-sidebar">
    <div class="adm-sidebar__brand">
      <div class="adm-sidebar__brand-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
        </svg>
      </div>
      <div class="adm-sidebar__brand-text">
        <span class="adm-sidebar__brand-name">AviatorTutor</span>
        <span class="adm-sidebar__brand-tag">Admin Panel</span>
      </div>
    </div>

    <nav class="adm-sidebar__nav" aria-label="Admin navigation">
      <?php foreach ($navItems as [$href, $label, $svgPath]): ?>
        <a href="<?= htmlspecialchars($href) ?>" class="adm-nav-link<?= $isActive($href) ? ' active' : '' ?>" aria-current="<?= $isActive($href) ? 'page' : 'false' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <?= $svgPath ?>
          </svg>
          <?= htmlspecialchars($label) ?>
          <?php if ($href === '/admin/contacts' && $inquiryUnread > 0): ?>
            <span style="margin-left:auto;background:var(--adm-gold);color:#0A0A0A;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;line-height:1;"><?= $inquiryUnread ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="adm-sidebar__footer">
      <div class="adm-sidebar__user">
        <div class="adm-sidebar__avatar" aria-hidden="true"><?= $adminInit ?></div>
        <div class="adm-sidebar__user-info">
          <div class="adm-sidebar__user-name"><?= $adminName ?></div>
          <div class="adm-sidebar__user-role">Administrator</div>
        </div>
        <a href="/logout" class="adm-sidebar__signout" title="Sign out" aria-label="Sign out">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </a>
      </div>
    </div>
  </aside>

  <!-- Main -->
  <div class="adm-main">
    <!-- Topbar -->
    <header class="adm-topbar">
      <button class="adm-topbar__hamburger" id="adm-hamburger" aria-label="Toggle sidebar" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <div class="adm-topbar__title"><?= htmlspecialchars($title ?? 'Dashboard') ?></div>
      <div class="adm-topbar__actions">
        <a href="/" target="_blank" class="adm-btn adm-btn--ghost adm-btn--sm">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          View Site
        </a>
      </div>
    </header>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['flash_ok'])): ?>
      <div class="adm-flash adm-flash--ok"><?= htmlspecialchars($_SESSION['flash_ok'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash_ok']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="adm-flash adm-flash--error"><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_notice'])): ?>
      <div class="adm-flash adm-flash--info"><?= htmlspecialchars($_SESSION['flash_notice'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash_notice']); ?>
    <?php endif; ?>

    <!-- Page content -->
    <main class="adm-content">
      <?= $content ?? '' ?>
    </main>
  </div>
</div>

<script src="/assets/js/admin.js" defer></script>
</body>
</html>
