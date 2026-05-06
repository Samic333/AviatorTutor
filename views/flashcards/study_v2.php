<?php
declare(strict_types=1);
/** @var array $system */
/** @var array $flashcards */
/** @var string $csrf_token */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
?>
<link rel="stylesheet" href="/assets/css/flashcards-v2.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/flashcards-v2.css') ?: '0' ?>">

<header class="fcv2-head">
  <h1>Flashcards — <?= $h((string)$system['name']) ?></h1>
  <p>Swipe right if you got it, left if you missed. Wrong cards resurface within the session.</p>
</header>

<?php if (empty($flashcards)): ?>
  <p class="fcv2-empty">No flashcards published for this system yet.</p>
<?php else: ?>
  <div class="fcv2-stats" aria-live="polite">
    <span><strong id="fcv2-correct">0</strong> correct</span>
    <span><strong id="fcv2-wrong">0</strong> wrong</span>
    <span><strong id="fcv2-remaining"><?= count($flashcards) ?></strong> left</span>
  </div>

  <div class="fcv2-deck" id="fcv2-deck">
    <?php foreach (array_reverse($flashcards) as $i => $c):
      $cat      = (string) ($c['category'] ?? 'normal');
      $why      = (string) ($c['why_it_matters'] ?? '');
      $front    = (string) ($c['front'] ?? '');
      $back     = (string) ($c['back']  ?? '');
      $sid      = (int)    ($c['source_slide_id'] ?? 0);
      $typeable = !empty($c['typeable']);
    ?>
      <article class="fcv2-card fc-<?= $h($cat) ?> <?= $typeable ? 'fcv2-typeable' : '' ?>"
               data-card-id="<?= (int)$c['id'] ?>"
               data-category="<?= $h($cat) ?>"
               data-typeable="<?= $typeable ? '1' : '0' ?>"
               style="z-index: <?= $i ?>;">
        <div class="fcv2-pill"><?= $h(ucfirst($cat)) ?></div>
        <div class="fcv2-face fcv2-front">
          <div class="fcv2-q"><?= nl2br($h($front)) ?></div>

          <?php if ($typeable): ?>
            <div class="fcv2-typed">
              <textarea class="fcv2-typed-input"
                        rows="3"
                        placeholder="Type your answer…"
                        aria-label="Type your answer"
                        data-fcv2-typed-input></textarea>
              <div class="fcv2-typed-actions">
                <button type="button" class="fcv2-flip-btn fcv2-typed-skip" data-fcv2-typed-skip>Skip &amp; flip</button>
                <button type="button" class="fcv2-flip-btn fcv2-typed-submit" data-fcv2-typed-submit>Submit answer</button>
              </div>
            </div>
          <?php else: ?>
            <button type="button" class="fcv2-flip-btn" data-fcv2-flip>Flip</button>
          <?php endif; ?>
        </div>
        <div class="fcv2-face fcv2-back" hidden>
          <?php if ($typeable): ?>
            <div class="fcv2-verdict" data-fcv2-verdict hidden>
              <span class="fcv2-verdict-badge" data-fcv2-verdict-badge></span>
              <p class="fcv2-verdict-feedback" data-fcv2-verdict-feedback></p>
            </div>
          <?php endif; ?>
          <div class="fcv2-a"><?= nl2br($h($back)) ?></div>
          <?php if ($why !== ''): ?>
            <p class="fcv2-why"><strong>Why this matters:</strong> <?= $h($why) ?></p>
          <?php endif; ?>
          <?php if ($sid > 0): ?>
            <a class="fcv2-source" href="/study/<?= (int)$system['id'] ?>/lesson/<?= (int)$sid ?>#slide-<?= $sid ?>">View source slide →</a>
          <?php endif; ?>
          <div class="fcv2-grade">
            <button type="button" class="fcv2-btn fcv2-btn-wrong" data-fcv2-grade="wrong">Got it wrong</button>
            <button type="button" class="fcv2-btn fcv2-btn-right" data-fcv2-grade="right">Got it right</button>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <p class="fcv2-help" aria-hidden="true">← swipe wrong · → swipe right · Space flips</p>
<?php endif; ?>

<script>
  window.AVFlashcardsCSRF = <?= json_encode($csrf_token) ?>;
</script>
<script src="/assets/js/flashcards-v2.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/flashcards-v2.js') ?: '0' ?>" defer></script>

<?php
$sessionType     = 'flashcard';
$sessionSystemId = (int) ($system['id'] ?? 0);
include __DIR__ . '/../partials/study-session-heartbeat.php';
?>
