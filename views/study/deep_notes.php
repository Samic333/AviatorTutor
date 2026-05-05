<?php
declare(strict_types=1);
/** @var array $system */
/** @var array $lessons */
/** @var array $sectionsByLesson */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$systemId = (int) $system['id'];
?>
<style>
  .dn-wrap { display: grid; grid-template-columns: 220px minmax(0, 1fr); gap: 24px; align-items: start; }
  @media (max-width: 900px) { .dn-wrap { grid-template-columns: 1fr; } .dn-toc { position: static; } }
  .dn-toc { position: sticky; top: 80px; max-height: calc(100vh - 100px); overflow-y: auto; padding: 12px;
            border: 1px solid var(--thm-border, rgba(255,255,255,0.08)); border-radius: 12px;
            background: var(--thm-card, rgba(255,255,255,0.03)); }
  .dn-toc h3 { margin: 0 0 8px; font-size: 11.5px; letter-spacing: 0.06em; text-transform: uppercase;
               color: var(--thm-fg-muted, #94A3B8); font-weight: 700; }
  .dn-toc a  { display: block; padding: 6px 8px; border-radius: 6px; font-size: 13px;
               color: var(--thm-fg-muted, #94A3B8); text-decoration: none; }
  .dn-toc a:hover, .dn-toc a:focus { background: rgba(255,255,255,0.05); color: var(--thm-fg, #F1F5F9); }
  .dn-toc .dn-toc-section { padding: 6px 12px; font-size: 12px; }
  .dn-search { position: sticky; top: 0; padding: 10px 0 14px; background: inherit;
               z-index: 2; }
  .dn-search input {
      width: 100%; padding: 10px 14px; border-radius: 10px; font-size: 14px;
      background: var(--thm-card, rgba(255,255,255,0.03));
      border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
      color: var(--thm-fg, #F1F5F9);
  }
  .dn-section { padding: 18px 0; border-bottom: 1px solid var(--thm-border, rgba(255,255,255,0.06)); }
  .dn-section h2 { font-size: 24px; margin: 0 0 6px; font-weight: 700; }
  .dn-section h3 { font-size: 16px; margin: 16px 0 6px; font-weight: 700; color: var(--thm-fg-muted, #94A3B8); text-transform: uppercase; letter-spacing: 0.05em; }
  .dn-section p, .dn-section .dn-body { font-size: 1em; line-height: 1.7; }
  .dn-summary { color: var(--thm-fg-muted, #94A3B8); font-size: 14px; margin: 0 0 12px; }
  .dn-empty { padding: 28px; text-align: center; color: var(--thm-fg-muted, #94A3B8); }
  body.dn-filtered .dn-section.dn-hidden { display: none; }
</style>

<div class="dn-search">
  <input type="search" id="dn-search-input" placeholder="Search this system…" autocomplete="off">
</div>

<div class="dn-wrap">
  <aside class="dn-toc" aria-label="Table of contents">
    <h3>On this page</h3>
    <?php foreach ($lessons as $i => $l): ?>
      <a href="#dn-lesson-<?= (int)$l['id'] ?>" class="dn-toc-link"><?= ($i+1) ?>. <?= $h((string)$l['title']) ?></a>
    <?php endforeach; ?>
    <?php if (empty($lessons)): ?>
      <span class="dn-toc-section">No lessons yet.</span>
    <?php endif; ?>
  </aside>

  <div class="dn-content" id="dn-content">
    <?php if (empty($lessons)): ?>
      <p class="dn-empty">No published lessons for <?= $h((string)$system['name']) ?> yet.</p>
    <?php else: foreach ($lessons as $i => $l):
      $lid      = (int) $l['id'];
      $sections = $sectionsByLesson[$lid] ?? [];
    ?>
      <article class="dn-section" id="dn-lesson-<?= $lid ?>" data-search-text="<?= $h(strtolower((string)$l['title'] . ' ' . (string)($l['summary'] ?? '') . ' ' . (string)($l['body'] ?? ''))) ?>">
        <h2><?= ($i+1) ?>. <?= $h((string)$l['title']) ?></h2>
        <?php if (!empty($l['summary'])): ?>
          <p class="dn-summary"><?= $h((string)$l['summary']) ?></p>
        <?php endif; ?>
        <?php if (!empty($l['body'])): ?>
          <div class="dn-body"><?= (string)$l['body'] ?></div>
        <?php endif; ?>
        <?php foreach ($sections as $sec): ?>
          <h3 id="dn-section-<?= (int)$sec['id'] ?>"><?= $h((string)$sec['title']) ?></h3>
          <div class="dn-body"><?= (string)$sec['body'] ?></div>
        <?php endforeach; ?>
      </article>
    <?php endforeach; endif; ?>
  </div>
</div>

<script>
(function () {
  var input = document.getElementById('dn-search-input');
  var sections = Array.prototype.slice.call(document.querySelectorAll('.dn-section'));
  if (!input || !sections.length) return;
  function applyFilter() {
    var q = input.value.trim().toLowerCase();
    document.body.classList.toggle('dn-filtered', q.length > 0);
    sections.forEach(function (s) {
      var hay = s.getAttribute('data-search-text') || '';
      s.classList.toggle('dn-hidden', q.length > 0 && hay.indexOf(q) === -1);
    });
  }
  var t = null;
  input.addEventListener('input', function () {
    clearTimeout(t);
    t = setTimeout(applyFilter, 80);
  });
})();
</script>
