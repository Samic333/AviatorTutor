<?php
declare(strict_types=1);
/** @var string $content */
/** @var ?array $currentUser */
/** @var ?string $title */
/** @var ?string $currentPath */

$user       = $currentUser ?? [];
$path       = $currentPath ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$pageTitle  = ($title ?? 'Dashboard') . ' — AviatorTutor';
$isAdmin    = isset($user['role']) && $user['role'] === 'admin';
$pilotName  = htmlspecialchars((string)($user['name'] ?? 'Pilot'));
$pilotInit  = strtoupper(mb_substr((string)($user['name'] ?? 'P'), 0, 1));
$avatarSrc  = !empty($user['avatar']) ? '/assets/uploads/avatars/' . htmlspecialchars((string)$user['avatar']) : null;

// Check premium status from session (subscription or purchase)
$isPremium = !empty($user['is_premium']) || $isAdmin;

// 4th element = true means "coming soon" (disabled, badge shown)
$features = [];
try {
    $appCfg = require __DIR__ . '/../../config/app.php';
    $features = is_array($appCfg['features'] ?? null) ? $appCfg['features'] : [];
} catch (\Throwable $e) { /* config unavailable — fall back to defaults */ }

$navMain = [
    ['/dashboard',  'Dashboard',   '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',  false],
    ['/aircraft',   'My Aircraft', '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>',  false],
    ['/systems',    'Study',       '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',  false],
    ['/flashcards', 'Flashcards',  '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 4v16"/>',  false],
    ['/quiz',       'Quizzes',     '<polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',  false],
    ['/progress',   'Progress',    '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',  false],
];

// Feature-flagged items. Each entry has [href, label, svgPath, featureKey].
// Hidden entirely when the flag is false; rendered as live nav (no "Soon"
// badge) when the flag is true. Toggle in config/app.php → 'features'.
$navOptional = [
    ['/planner', 'Planner', '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>', 'planner'],
    ['/notes',   'Notes',   '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/>', 'notes'],
    ['/search',  'Search',  '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>', 'search'],
];
foreach ($navOptional as [$href, $label, $svgPath, $featureKey]) {
    if (!empty($features[$featureKey])) {
        $navMain[] = [$href, $label, $svgPath, false];
    }
}

$navAccount = [
    ['/profile',  'Profile',  '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
    ['/account',  'Account',  '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
];

$isActive = function(string $href) use ($path): bool {
    if ($href === '/dashboard') return $path === '/dashboard';
    return str_starts_with($path, $href);
};
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="theme-color" content="#0F172A">
  <meta name="robots" content="noindex">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="/assets/css/pilot.css">
  <?php include __DIR__ . '/../partials/head-favicons.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
</head>
<body class="plt-body">
<div class="plt-overlay" id="plt-overlay"></div>
<div class="plt-shell">

  <!-- Sidebar -->
  <aside class="plt-sidebar" id="plt-sidebar">
    <!-- Brand -->
    <div class="plt-sidebar__brand">
      <div class="plt-sidebar__brand-icon">
        <svg width="20" height="20" viewBox="0 0 64 64" fill="none" aria-hidden="true">
          <path d="M11 51 L32 11 L53 51" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
          <line x1="22" y1="40" x2="42" y2="40" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
          <circle cx="32" cy="11" r="4" fill="#38BDF8"/>
        </svg>
      </div>
      <span class="plt-sidebar__brand-name">Aviator<span style="color:var(--plt-sky,#38BDF8);">Tutor</span></span>
    </div>

    <!-- Pilot Identity -->
    <div class="plt-sidebar__pilot">
      <div class="plt-sidebar__pilot-avatar">
        <?php if ($avatarSrc): ?>
          <img src="<?= $avatarSrc ?>" alt="<?= $pilotName ?>">
        <?php else: ?>
          <?= $pilotInit ?>
        <?php endif; ?>
      </div>
      <div class="plt-sidebar__pilot-info">
        <div class="plt-sidebar__pilot-name"><?= $pilotName ?></div>
        <?php if ($isPremium): ?>
          <div class="plt-badge-pro">
            <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            PRO
          </div>
        <?php else: ?>
          <div class="plt-badge-free">Free Plan</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Main Nav -->
    <nav class="plt-sidebar__nav" aria-label="Main navigation">
      <span class="plt-nav-section">Study</span>
      <?php foreach ($navMain as [$href, $label, $svgPath, $isSoon]): ?>
        <?php if ($isSoon): ?>
          <span class="plt-nav-link plt-nav-link--soon" aria-disabled="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <?= $svgPath ?>
            </svg>
            <?= htmlspecialchars($label) ?>
            <span class="plt-badge-soon">Soon</span>
          </span>
        <?php else: ?>
          <a href="<?= htmlspecialchars($href) ?>" class="plt-nav-link<?= $isActive($href) ? ' active' : '' ?>" aria-current="<?= $isActive($href) ? 'page' : 'false' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <?= $svgPath ?>
            </svg>
            <?= htmlspecialchars($label) ?>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php if ($isAdmin): ?>
        <span class="plt-nav-section">Admin</span>
        <a href="/admin" class="plt-nav-link<?= str_starts_with($path, '/admin') ? ' active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>
          </svg>
          Admin Panel
        </a>
      <?php endif; ?>
    </nav>

    <!-- Account nav -->
    <div class="plt-sidebar__footer">
      <?php foreach ($navAccount as [$href, $label, $svgPath]): ?>
        <a href="<?= htmlspecialchars($href) ?>" class="plt-nav-link<?= $isActive($href) ? ' active' : '' ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <?= $svgPath ?>
          </svg>
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
      <a href="/logout" class="plt-nav-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Sign out
      </a>
    </div>
  </aside>

  <!-- Main -->
  <div class="plt-main">
    <!-- Mobile topbar -->
    <header class="plt-topbar">
      <button class="plt-topbar__hamburger" id="plt-hamburger" aria-label="Toggle sidebar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
      <span class="plt-topbar__title"><?= htmlspecialchars($title ?? 'Dashboard') ?></span>
    </header>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['flash_ok'])): ?>
      <div class="plt-flash plt-flash--ok"><?= htmlspecialchars($_SESSION['flash_ok'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash_ok']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="plt-flash plt-flash--error"><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_notice'])): ?>
      <div class="plt-flash plt-flash--info"><?= htmlspecialchars($_SESSION['flash_notice'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash_notice']); ?>
    <?php endif; ?>

    <!-- Content -->
    <main class="plt-content">
      <?= $content ?? '' ?>
    </main>
  </div>
</div>

<script>
(function() {
  // Mobile sidebar toggle
  var sidebar  = document.getElementById('plt-sidebar');
  var overlay  = document.getElementById('plt-overlay');
  var hamburger = document.getElementById('plt-hamburger');

  function openSidebar() {
    sidebar && sidebar.classList.add('open');
    overlay && overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar && sidebar.classList.remove('open');
    overlay && overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  hamburger && hamburger.addEventListener('click', function() {
    sidebar && sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay && overlay.addEventListener('click', closeSidebar);

  // Progress rings
  document.querySelectorAll('[data-ring]').forEach(function(el) {
    var pct    = Math.min(100, Math.max(0, parseInt(el.getAttribute('data-ring'), 10)));
    var radius = 30;
    var circ   = 2 * Math.PI * radius;
    var fill   = el.querySelector('.plt-ring__fill');
    if (fill) {
      fill.setAttribute('stroke-dasharray', (pct / 100 * circ) + ' ' + circ);
    }
  });
})();
</script>
</body>
</html>
