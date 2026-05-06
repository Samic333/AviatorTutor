<?php
/**
 * Interactive slide-based lesson player
 *
 * Vars in scope:
 *   $system   array  id, name, ata_code, description, color, icon
 *   $lesson   array  id, system_id, title, slug, summary
 *   $slides   array  rows from lesson_slides (ordered by sort_order)
 *   $progress array  [slide_id => ['answered_correct'=>0|1, 'attempts'=>N]]
 */

$systemId = (int)$system['id'];
$lessonId = (int)$lesson['id'];

// Decode the per-slide question JSON for display.
// SECURITY: never echo `correct_index` or the explanation into the initial
// page render. Both are returned by /api/lessons/{id}/slide-answer only after
// the learner submits — and the explanation only after the gate is settled
// (correct OR retry budget exhausted).
$slideTypeLabels = [
    'intro'       => 'Introduction',
    'concept'     => 'Concept',
    'system'      => 'System',
    'normal_op'   => 'Normal Op',
    'abnormal'    => 'Abnormal / Failure',
    'operational' => 'Operational',
    'qrh'         => 'QRH / Memory Item',
    'scenario'    => 'Scenario',
    'revision'    => 'Revision',
    'quiz'        => 'Quiz',
];

$slideTypeIcons = [
    'intro'       => 'play-circle',
    'concept'     => 'lightbulb',
    'system'      => 'cpu',
    'normal_op'   => 'check-circle',
    'abnormal'    => 'alert-triangle',
    'operational' => 'plane',
    'qrh'         => 'book-open',
    'scenario'    => 'message-circle-question',
    'revision'    => 'refresh-cw',
    'quiz'        => 'award',
];

$totalSlides    = count($slides);
$gateCount      = 0;
$gateCorrectFTU = 0; // first-try unique correct gates
foreach ($slides as $s) {
    if (!empty($s['question'])) {
        $gateCount++;
        $sid = (int)$s['id'];
        $p = $progress[$sid] ?? null;
        if ($p && (int)$p['answered_correct'] === 1 && (int)$p['attempts'] <= 1) {
            $gateCorrectFTU++;
        }
    }
}
$systemColor = $system['color'] ?? '#34d399';
?>
<link rel="stylesheet" href="/assets/css/app.css">

<?php
// COORD: Phase 2 — when study_chrome_v2 is on the new study layout owns
// the breadcrumb, progress bar, mode switcher, and settings entry. The
// existing in-template <header class="slide-player-header"> is suppressed
// below so we don't double-stack chrome. The slide content rendering itself
// (slide cards, gates, qrh panels) is untouched on purpose so the parallel
// slide-design session can keep iterating without merge pain.
$useV2Chrome = !empty($studyChromeV2);
?>
<div class="slide-player" data-lesson-id="<?= $lessonId ?>" data-system-id="<?= $systemId ?>" data-total-slides="<?= $totalSlides ?>">

  <?php if (!$useV2Chrome): ?>
  <!-- Legacy stacked header (suppressed under study_chrome_v2). -->
  <header class="slide-player-header" style="border-left: 4px solid <?= htmlspecialchars($systemColor) ?>;">
    <div class="slide-player-back">
      <a href="/study/<?= $systemId ?>" class="slide-back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        <span><?= htmlspecialchars($system['name']) ?></span>
      </a>
      <span class="slide-back-ata"><?= htmlspecialchars($system['ata_code']) ?></span>
    </div>
    <div class="slide-player-title">
      <h1><?= htmlspecialchars($lesson['title']) ?></h1>
      <?php if (!empty($lesson['summary'])): ?>
        <p class="slide-player-summary"><?= htmlspecialchars($lesson['summary']) ?></p>
      <?php endif; ?>
    </div>

    <div class="slide-progress">
      <div class="slide-progress-bar"><div class="slide-progress-fill" id="slide-progress-fill" style="width:0%; background:<?= htmlspecialchars($systemColor) ?>;"></div></div>
      <div class="slide-progress-text">
        Slide <span id="slide-progress-current">1</span> / <span id="slide-progress-total"><?= max(1,$totalSlides) ?></span>
      </div>
    </div>

    <div class="slide-player-controls">
      <?php $activeDiff = $difficulty ?? 'intermediate'; ?>
      <select
        class="slide-ctrl-btn slide-difficulty"
        id="slide-difficulty"
        title="Lesson difficulty &mdash; filters which slides you see"
        aria-label="Difficulty"
        onchange="window.location.search='?difficulty='+this.value;">
        <option value="beginner"     <?= $activeDiff === 'beginner'     ? 'selected' : '' ?>>Beginner</option>
        <option value="intermediate" <?= $activeDiff === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
        <option value="advanced"     <?= $activeDiff === 'advanced'     ? 'selected' : '' ?>>Advanced</option>
      </select>
      <button type="button" class="slide-ctrl-btn" id="slide-smaller-text" aria-pressed="false" title="Smaller text">A&minus;</button>
      <button type="button" class="slide-ctrl-btn" id="slide-larger-text" aria-pressed="false" title="Larger text">A+</button>
      <button type="button" class="slide-ctrl-btn" id="slide-contrast" aria-pressed="false" title="Light / dark mode">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2 a10 10 0 0 0 0 20"/></svg>
      </button>
      <button type="button" class="slide-ctrl-btn" id="slide-focus-mode" aria-pressed="false" title="Focus mode (hide sidebar)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3"/><path d="M21 8V5a2 2 0 0 0-2-2h-3"/><path d="M3 16v3a2 2 0 0 0 2 2h3"/><path d="M16 21h3a2 2 0 0 0 2-2v-3"/></svg>
      </button>
      <select class="slide-ctrl-btn slide-jump" id="slide-jump" title="Jump to slide" aria-label="Jump to slide">
        <?php foreach ($slides as $idx => $s): ?>
          <option value="<?= $idx ?>"><?= ($idx+1) ?>. <?= htmlspecialchars($s['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </header>
  <?php else: ?>
  <!-- COORD: V2 chrome owns the topbar. We still need #slide-progress-current
       and #slide-progress-total so lesson_slides.js can update the progress
       text — kept as hidden DOM nodes for backward compat. The visible
       progress bar fill (#slide-progress-fill) lives in the layout's topbar. -->
  <span id="slide-progress-current" hidden>1</span>
  <span id="slide-progress-total"   hidden><?= max(1, $totalSlides) ?></span>
  <?php endif; ?>

  <!-- Slide stack -->
  <div class="slide-stack" id="slide-stack">

    <?php if ($totalSlides === 0): ?>
      <div class="slide-card slide-empty is-active">
        <h2>Slides Coming Soon</h2>
        <p>Interactive slides for this lesson are still being built. In the meantime, you can read the full lesson on the system page.</p>
        <a class="slide-btn slide-btn-primary" href="/study/<?= $systemId ?>">Back to system</a>
      </div>
    <?php else: ?>

      <?php foreach ($slides as $idx => $slide):
        $sid       = (int)$slide['id'];
        $stype     = $slide['slide_type'] ?: 'concept';
        $sLabel    = $slideTypeLabels[$stype] ?? ucfirst($stype);
        $sIcon     = $slideTypeIcons[$stype] ?? 'circle';
        $question  = null;
        if (!empty($slide['question'])) {
          $decoded  = is_string($slide['question']) ? json_decode($slide['question'], true) : $slide['question'];
          if (is_array($decoded) && !empty($decoded['options']) && isset($decoded['correct_index'])) {
            $question = $decoded;
          }
        }
        $hasGate         = $question !== null;
        $alreadyCorrect  = isset($progress[$sid]) && (int)$progress[$sid]['answered_correct'] === 1;
      ?>
      <article
        class="slide-card<?= $idx === 0 ? ' is-active' : '' ?><?= $hasGate ? ' has-gate' : '' ?><?= $alreadyCorrect ? ' gate-already-passed' : '' ?>"
        data-slide-id="<?= $sid ?>"
        data-slide-index="<?= $idx ?>"
        data-has-gate="<?= $hasGate ? '1' : '0' ?>">

        <div class="slide-pill slide-pill-<?= htmlspecialchars($stype) ?>">
          <i data-lucide="<?= htmlspecialchars($sIcon) ?>"></i>
          <span><?= htmlspecialchars($sLabel) ?></span>
          <span class="slide-pill-num"><?= ($idx+1) ?> / <?= $totalSlides ?></span>
        </div>

        <h2 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h2>

        <?php
          $mediaType = $slide['media_type'] ?? 'none';
          $mediaUrl  = trim((string)($slide['media_url'] ?? ''));
          $mediaAlt  = htmlspecialchars($slide['media_alt'] ?? $slide['title']);
        ?>
        <?php if ($mediaType !== 'none'): ?>
          <div class="slide-media slide-media-<?= htmlspecialchars($mediaType) ?>">
            <?php if ($mediaType === 'image' || $mediaType === 'diagram'): ?>
              <?php if ($mediaUrl): ?>
                <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="<?= $mediaAlt ?>"
                     loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>" decoding="async"
                     onerror="this.parentNode.classList.add('media-missing'); this.remove();">
              <?php endif; ?>
              <div class="slide-media-placeholder">
                <i data-lucide="image"></i>
                <span><?= htmlspecialchars($mediaType === 'diagram' ? 'Diagram' : 'Image') ?> — <?= $mediaAlt ?></span>
                <small>Visual content coming soon</small>
              </div>
            <?php elseif ($mediaType === 'video'): ?>
              <?php if ($mediaUrl): ?>
                <video controls preload="metadata" src="<?= htmlspecialchars($mediaUrl) ?>"></video>
              <?php else: ?>
                <div class="slide-media-placeholder">
                  <i data-lucide="play-circle"></i>
                  <span>Video — <?= $mediaAlt ?></span>
                  <small>Video coming soon</small>
                </div>
              <?php endif; ?>
            <?php elseif ($mediaType === 'animation'): ?>
              <div class="slide-media-placeholder slide-media-animation">
                <i data-lucide="zap"></i>
                <span>Animation — <?= $mediaAlt ?></span>
                <small><?= $mediaUrl ? 'Animation will play here' : 'Animation coming soon' ?></small>
              </div>
            <?php elseif ($mediaType === 'model3d'): ?>
              <div class="slide-media-placeholder slide-media-3d">
                <i data-lucide="box"></i>
                <span>3D Model — <?= $mediaAlt ?></span>
                <small>Interactive 3D viewer coming soon</small>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($slide['body'])): ?>
          <div class="slide-body"><?= nl2br(htmlspecialchars($slide['body'])) ?></div>
        <?php endif; ?>

        <?php if (!empty($slide['key_point'])): ?>
          <div class="slide-key-point">
            <div class="slide-info-head"><i data-lucide="brain"></i> Memory Hook</div>
            <p><?= nl2br(htmlspecialchars($slide['key_point'])) ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($slide['ops_relevance'])): ?>
          <div class="slide-ops">
            <div class="slide-info-head"><i data-lucide="plane"></i> Operational Relevance</div>
            <p><?= nl2br(htmlspecialchars($slide['ops_relevance'])) ?></p>
          </div>
        <?php endif; ?>

        <?php
          // QRH panel — render structured QRH excerpts on qrh-type slides
          // (or any slide that has explicit slide-scoped links).
          $qrhRows = [];
          if (!empty($qrhLinks['bySlide'][$sid])) {
              $qrhRows = $qrhLinks['bySlide'][$sid];
          } elseif ($stype === 'qrh' && !empty($qrhLinks['lessonWide'])) {
              $qrhRows = $qrhLinks['lessonWide'];
          }
        ?>
        <?php if (!empty($qrhRows)): ?>
          <div class="slide-qrh-panel" aria-label="QRH cross-reference">
            <div class="slide-info-head"><i data-lucide="book-open"></i> Linked QRH sections</div>
            <?php foreach ($qrhRows as $q): ?>
              <article class="slide-qrh-item<?= $q['memory_item'] ? ' is-memory-item' : '' ?>">
                <header class="slide-qrh-item-head">
                  <h4 class="slide-qrh-title"><?= htmlspecialchars((string) $q['qrh_section_title']) ?></h4>
                  <?php if (!empty($q['memory_item'])): ?>
                    <span class="slide-qrh-pill slide-qrh-pill-memory"><i data-lucide="alert-octagon"></i> Memory item</span>
                  <?php endif; ?>
                </header>
                <p class="slide-qrh-excerpt"><?= nl2br(htmlspecialchars((string) $q['qrh_excerpt'])) ?></p>
                <dl class="slide-qrh-meta">
                  <?php if (!empty($q['recognition_cue'])): ?>
                    <div><dt>Recognition cue</dt><dd><?= htmlspecialchars((string) $q['recognition_cue']) ?></dd></div>
                  <?php endif; ?>
                  <?php if (!empty($q['ops_meaning'])): ?>
                    <div><dt>Operational meaning</dt><dd><?= htmlspecialchars((string) $q['ops_meaning']) ?></dd></div>
                  <?php endif; ?>
                  <?php if (!empty($q['memory_trigger'])): ?>
                    <div><dt>Memory trigger</dt><dd class="slide-qrh-trigger"><?= htmlspecialchars((string) $q['memory_trigger']) ?></dd></div>
                  <?php endif; ?>
                </dl>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($hasGate): ?>
          <fieldset class="slide-gate" data-gate-state="<?= $alreadyCorrect ? 'correct' : 'unanswered' ?>">
            <legend class="slide-gate-prompt">
              <i data-lucide="help-circle"></i>
              <?= htmlspecialchars($question['prompt'] ?? 'Check your understanding:') ?>
            </legend>
            <div class="slide-gate-options">
              <?php foreach ((array)$question['options'] as $optIdx => $optText): ?>
                <label class="slide-gate-option">
                  <input type="radio" name="gate-<?= $sid ?>" value="<?= (int)$optIdx ?>" <?= $alreadyCorrect ? 'disabled' : '' ?>>
                  <span class="slide-gate-letter"><?= chr(65 + $optIdx) ?></span>
                  <span class="slide-gate-text"><?= htmlspecialchars($optText) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="slide-gate-actions">
              <button type="button" class="slide-btn slide-btn-primary slide-gate-submit" <?= $alreadyCorrect ? 'disabled' : '' ?>>
                <?= $alreadyCorrect ? 'Already answered' : 'Submit Answer' ?>
              </button>
            </div>
            <div class="slide-gate-feedback" hidden>
              <div class="slide-gate-feedback-inner"></div>
              <!-- Explanation is filled in by the slide-answer API response,
                   only after the learner has either answered correctly or
                   used up their retries. We never render it server-side. -->
              <div class="slide-gate-explanation" hidden></div>
            </div>
          </fieldset>
        <?php endif; ?>

      </article>
      <?php endforeach; ?>

      <!-- Final completion card -->
      <article class="slide-card slide-complete" data-slide-index="<?= $totalSlides ?>">
        <div class="slide-pill slide-pill-quiz">
          <i data-lucide="award"></i>
          <span>Lesson Complete</span>
        </div>
        <h2 class="slide-title">You finished the lesson</h2>

        <div class="slide-complete-stats">
          <div class="slide-stat">
            <div class="slide-stat-num" id="slide-final-score-num"><?= $gateCorrectFTU ?></div>
            <div class="slide-stat-lbl">First-try correct</div>
          </div>
          <div class="slide-stat">
            <div class="slide-stat-num"><?= $gateCount ?></div>
            <div class="slide-stat-lbl">Question gates</div>
          </div>
          <div class="slide-stat">
            <div class="slide-stat-num"><?= $totalSlides ?></div>
            <div class="slide-stat-lbl">Slides reviewed</div>
          </div>
        </div>

        <p class="slide-complete-msg">
          Great work, Captain. Mark this lesson complete to update your progress, then jump back to the system overview to continue your study.
        </p>

        <div class="slide-complete-actions">
          <button type="button" class="slide-btn slide-btn-primary" id="slide-mark-complete-btn">
            <i data-lucide="check-circle-2"></i> Mark Lesson Complete
          </button>
          <a href="/study/<?= $systemId ?>/revision" class="slide-btn slide-btn-ghost">
            <i data-lucide="refresh-cw"></i> Quick Revision
          </a>
          <a href="/study/<?= $systemId ?>" class="slide-btn slide-btn-ghost">
            Back to <?= htmlspecialchars($system['name']) ?>
          </a>
        </div>
      </article>

    <?php endif; ?>
  </div>

  <!-- Footer nav -->
  <nav class="slide-nav" aria-label="Slide navigation">
    <button type="button" class="slide-btn slide-btn-ghost" id="slide-prev-btn" disabled>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      Previous
    </button>
    <div class="slide-nav-hint" id="slide-nav-hint"></div>
    <button type="button" class="slide-btn slide-btn-primary" id="slide-next-btn">
      Next
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </button>
  </nav>

</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="/assets/js/lesson_slides.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/lesson_slides.js') ?: '0' ?>"></script>
<script>
  if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons();
  }
  if (window.LessonSlides && typeof window.LessonSlides.init === 'function') {
    window.LessonSlides.init({
      lessonId: <?= $lessonId ?>,
      systemId: <?= $systemId ?>,
      totalSlides: <?= $totalSlides ?>,
      slideAnswerUrl: '/api/lessons/<?= $lessonId ?>/slide-answer',
      lessonCompleteUrl: '/api/lessons/<?= $lessonId ?>/complete',
      systemUrl: '/study/<?= $systemId ?>'
    });
  }
</script>

<?php
$sessionType     = 'detail';
$sessionSystemId = $systemId;
include __DIR__ . '/../partials/study-session-heartbeat.php';
?>
