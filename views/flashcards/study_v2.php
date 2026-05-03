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
      $cat   = (string) ($c['category'] ?? 'normal');
      $why   = (string) ($c['why_it_matters'] ?? '');
      $front = (string) ($c['front'] ?? '');
      $back  = (string) ($c['back']  ?? '');
      $sid   = (int)    ($c['source_slide_id'] ?? 0);
    ?>
      <article class="fcv2-card fc-<?= $h($cat) ?>"
               data-card-id="<?= (int)$c['id'] ?>"
               data-category="<?= $h($cat) ?>"
               style="z-index: <?= $i ?>;">
        <div class="fcv2-pill"><?= $h(ucfirst($cat)) ?></div>
        <div class="fcv2-face fcv2-front">
          <div class="fcv2-q"><?= nl2br($h($front)) ?></div>
          <button type="button" class="fcv2-flip-btn" data-fcv2-flip>Flip</button>
        </div>
        <div class="fcv2-face fcv2-back" hidden>
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
