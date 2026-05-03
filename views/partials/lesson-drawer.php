<?php
declare(strict_types=1);
/**
 * Lesson drawer — slide-out panel of every lesson in the current system,
 * plus (when the slide player is mounted) a TOC of the active lesson's
 * slides.
 *
 * Vars in scope (set by the controller via $data → layout → partial):
 *   $drawerSystem            ?array  current system row
 *   $drawerLessons           ?array  sibling lessons in the system
 *   $drawerCurrentLessonId   ?int    id of the currently-open lesson
 *   $drawerSlides            ?array  active lesson's slides (optional)
 *   $drawerCurrentSlideIndex ?int    starting slide index (default 0)
 */

$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$system  = $drawerSystem ?? null;
$lessons = is_array($drawerLessons ?? null) ? $drawerLessons : [];
$slides  = is_array($drawerSlides  ?? null) ? $drawerSlides  : [];
$cur     = (int) ($drawerCurrentLessonId ?? 0);
$curSI   = (int) ($drawerCurrentSlideIndex ?? 0);
?>
<div class="study-drawer-backdrop" aria-hidden="true"></div>

<aside class="study-drawer" id="study-drawer" aria-label="Lessons in this system" hidden>
  <header class="study-drawer-head">
    <h2 class="study-drawer-title">
      <?php if ($system): ?>
        <?= $h((string)($system['name'] ?? '')) ?>
        <?php if (!empty($system['ata_code'])): ?>
          <span style="font-size:11px;color:#94A3B8;font-weight:600;margin-left:6px;"><?= $h((string)$system['ata_code']) ?></span>
        <?php endif; ?>
      <?php else: ?>
        Lessons
      <?php endif; ?>
    </h2>
    <button type="button" class="sc-iconbtn" data-sc-drawer-close aria-label="Close lessons">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
  </header>

  <div class="study-drawer-body">

    <?php if (!empty($lessons)): ?>
      <div class="study-drawer-section">Lessons</div>
      <ul class="study-drawer-list">
        <?php foreach ($lessons as $i => $l):
          $lid    = (int) ($l['id'] ?? 0);
          $title  = (string) ($l['title'] ?? '');
          $href   = $system ? ('/study/' . (int)$system['id'] . '/lesson/' . $lid) : '#';
          $isCur  = ($lid === $cur);
        ?>
          <li>
            <a class="study-drawer-link <?= $isCur ? 'is-current' : '' ?>" href="<?= $h($href) ?>">
              <span class="study-drawer-link__num"><?= $i + 1 ?>.</span>
              <span><?= $h($title) ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if (!empty($slides)): ?>
      <div class="study-drawer-section" style="margin-top:14px;">Slides in this lesson</div>
      <ul class="study-drawer-list" id="drawer-slide-list">
        <?php foreach ($slides as $idx => $s):
          $stitle = (string) ($s['title'] ?? ('Slide ' . ($idx + 1)));
          $isCur  = ($idx === $curSI);
        ?>
          <li>
            <a class="study-drawer-link <?= $isCur ? 'is-current' : '' ?>"
               href="#"
               data-drawer-slide-index="<?= $idx ?>"
               onclick="event.preventDefault(); if(window.LessonSlides && LessonSlides.goTo){LessonSlides.goTo(<?= $idx ?>);} document.body.classList.remove('drawer-open');">
              <span class="study-drawer-link__num"><?= $idx + 1 ?>.</span>
              <span><?= $h($stitle) ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if (empty($lessons) && empty($slides)): ?>
      <p style="margin:14px;color:#94A3B8;font-size:13px;">No lessons available yet.</p>
    <?php endif; ?>
  </div>
</aside>
