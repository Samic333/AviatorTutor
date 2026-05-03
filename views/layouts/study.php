<?php
declare(strict_types=1);
/**
 * Phase 2 study layout — single thin topbar + slide-out lesson drawer.
 *
 * Replaces the old pilot.php sidebar + dual-bar chrome for study modes when
 * the study_chrome_v2 feature flag is on. Page content (slides, detail,
 * revision, future mind-map etc.) is rendered into $content_for_layout.
 *
 * Vars expected from the controller via $data:
 *   $title                        page title
 *   $studyBreadcrumb              array of [['label' => '...', 'href' => '...'], ...]
 *   $studyModes                   array of [['key' => 'slides', 'label' => 'Slides',
 *                                            'href' => '/...', 'active' => bool,
 *                                            'disabled' => bool, 'icon' => 'lucide-name'], ...]
 *   $studySystemColor             hex string used as the progress accent
 *   $studyTotalSlides             ?int (drives the slide picker grid)
 *   $studyCurrentSlideIndex       ?int
 *   plus everything the lesson-drawer.php partial reads.
 */

/** @var string $content_for_layout */
/** @var ?array $currentUser */

$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');

$pageTitle = ($title ?? 'Study') . ' — AviatorTutor';
$user      = $currentUser ?? \App\Core\Auth::user() ?? [];

$breadcrumb = is_array($studyBreadcrumb ?? null) ? $studyBreadcrumb : [];
$modes      = is_array($studyModes ?? null) ? $studyModes : [];
$accent     = (string) ($studySystemColor ?? '#38BDF8');
$totalSlide = (int) ($studyTotalSlides ?? 0);
$curSlide   = (int) ($studyCurrentSlideIndex ?? 0);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= $h($pageTitle) ?></title>
  <meta name="theme-color" content="#0F172A">
  <meta name="robots" content="noindex">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="/assets/css/pilot.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/pilot.css') ?: '0' ?>">
  <link rel="stylesheet" href="/assets/css/themes.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/themes.css') ?: '0' ?>">
  <link rel="stylesheet" href="/assets/css/study-chrome.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/study-chrome.css') ?: '0' ?>">
  <link rel="stylesheet" href="/assets/css/settings-drawer.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/settings-drawer.css') ?: '0' ?>">
  <link rel="stylesheet" href="/assets/css/polish.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/polish.css') ?: '0' ?>">
  <!-- Apply saved theme/font before the page paints to avoid FOUC. -->
  <script src="/assets/js/settings-drawer.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/settings-drawer.js') ?: '0' ?>"></script>
  <!-- Toast notifications + global window.onerror surfacer. -->
  <script src="/assets/js/toast.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/toast.js') ?: '0' ?>" defer></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
      }
    });
  </script>
  <?php include __DIR__ . '/../partials/head-favicons.php'; ?>
  <style>
    /* Per-page accent for the progress bar fill — set inline so themes can
       still override via CSS variables in Phase 3. */
    .study-progress > .slide-progress-fill,
    .study-progress > .study-progress-fill { background: <?= $h($accent) ?>; }
  </style>
</head>
<body class="has-study-chrome">

  <header class="study-topbar" role="banner">
    <!-- Top reveal hover zone for desktop mouse — when chrome is hidden,
         a small upward move still surfaces the bar. -->
    <div class="study-topbar-reveal" aria-hidden="true"></div>

    <div class="study-progress" id="study-progress" role="progressbar" aria-valuemin="0" aria-valuemax="<?= max(1, $totalSlide) ?>" aria-valuenow="<?= max(1, $curSlide + 1) ?>">
      <!-- Reuses the existing slide-progress-fill id so lesson_slides.js's
           progress logic keeps working without changes. -->
      <div class="slide-progress-fill" id="slide-progress-fill" style="width: 0%;"></div>
    </div>

    <div class="study-topbar-row">
      <button type="button" class="sc-iconbtn" data-sc-drawer-toggle aria-label="Open lessons">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>

      <nav class="study-breadcrumb" aria-label="Breadcrumb">
        <?php foreach ($breadcrumb as $i => $crumb):
          $isLast = ($i === count($breadcrumb) - 1);
          $label  = (string) ($crumb['label'] ?? '');
          $href   = (string) ($crumb['href']  ?? '');
        ?>
          <?php if ($i > 0): ?><span class="study-breadcrumb-sep" aria-hidden="true">›</span><?php endif; ?>
          <?php if ($isLast || $href === ''): ?>
            <span class="study-breadcrumb-current"><?= $h($label) ?></span>
          <?php else: ?>
            <a href="<?= $h($href) ?>"><?= $h($label) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>

      <?php if (!empty($modes)): ?>
        <nav class="study-modes" aria-label="Study modes">
          <?php foreach ($modes as $m):
            $key      = (string) ($m['key'] ?? '');
            $label    = (string) ($m['label'] ?? $key);
            $href     = (string) ($m['href']  ?? '#');
            $active   = !empty($m['active']);
            $disabled = !empty($m['disabled']);
            $icon     = (string) ($m['icon'] ?? 'circle');
            $cls      = $active ? 'is-active' : ($disabled ? 'is-disabled' : '');
            $title    = $disabled ? ($label . ' — coming in a future phase') : $label;
          ?>
            <?php if ($disabled): ?>
              <button type="button" class="<?= $cls ?>" disabled title="<?= $h($title) ?>" aria-disabled="true">
                <i data-lucide="<?= $h($icon) ?>" style="width:14px;height:14px;"></i>
                <span><?= $h($label) ?></span>
              </button>
            <?php else: ?>
              <a href="<?= $h($href) ?>" class="<?= $cls ?>" title="<?= $h($title) ?>">
                <i data-lucide="<?= $h($icon) ?>" style="width:14px;height:14px;"></i>
                <span><?= $h($label) ?></span>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>

      <a href="/dashboard" class="sc-iconbtn" title="Dashboard" aria-label="Back to dashboard">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </a>

      <!-- Settings cog — opens the Phase 3 settings drawer (themes, font,
           line spacing, reading width, motion, audio). -->
      <button type="button" class="sc-iconbtn" id="sc-settings-cog"
              data-settings-toggle
              title="Reading settings" aria-label="Open reading settings">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      </button>
    </div>
  </header>

  <?php
    // Drawer partial picks up the $drawer* variables passed in $data.
    $drawerVisible = !empty($drawerLessons) || !empty($drawerSlides);
    if ($drawerVisible) {
      include __DIR__ . '/../partials/lesson-drawer.php';
    }
    // Settings drawer is always available on study pages.
    if (!isset($userSettings)) {
      $userSettings = \App\Services\UserSettings::get((int)($user['id'] ?? 0));
    }
    if (!isset($csrf_token)) {
      $csrf_token = \App\Core\CSRF::generate();
    }
    include __DIR__ . '/../partials/settings-drawer.php';
  ?>

  <?php if ($totalSlide > 0): ?>
    <div class="study-slide-picker" id="study-slide-picker" aria-hidden="true">
      <div class="study-slide-picker-head">
        <span>Jump to slide</span>
        <span><?= $totalSlide ?> total</span>
      </div>
      <div class="study-slide-picker-grid">
        <?php for ($i = 0; $i < $totalSlide; $i++): ?>
          <button type="button" class="study-slide-tile <?= $i === $curSlide ? 'is-current' : '' ?>" data-slide-index="<?= $i ?>"><?= $i + 1 ?></button>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>

  <main class="study-chrome-frame" role="main">
    <div class="study-content<?= $totalSlide > 0 ? ' has-edge-zones' : '' ?>" data-swipe="<?= $totalSlide > 0 ? '1' : '0' ?>">
      <?php if ($totalSlide > 0): ?>
        <button type="button" class="study-edge-zone study-edge-zone--prev" aria-label="Previous slide" tabindex="-1"></button>
        <button type="button" class="study-edge-zone study-edge-zone--next" aria-label="Next slide" tabindex="-1"></button>
      <?php endif; ?>

      <?= $content_for_layout ?>
    </div>
  </main>

  <script src="/assets/js/study-chrome.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/study-chrome.js') ?: '0' ?>" defer></script>
</body>
</html>
