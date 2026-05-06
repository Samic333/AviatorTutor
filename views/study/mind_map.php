<?php
declare(strict_types=1);
/** @var array $system */
/** @var array $tree */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$treeJson = json_encode($tree, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
$sysId = (int) ($system['id'] ?? 0);
?>
<style>
  .mm-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
  .mm-head h1 { margin: 0; font-size: 22px; font-weight: 700; }
  .mm-help { color: var(--thm-fg-muted, #94A3B8); font-size: 12.5px; }

  /* Phase 12 — split layout: canvas on the left, detail panel on the right.
     Collapses to stacked + bottom sheet on mobile. */
  .mm-stage {
      display: grid;
      grid-template-columns: 1fr 360px;
      gap: 14px;
      align-items: stretch;
  }
  @media (max-width: 900px) {
      .mm-stage { grid-template-columns: 1fr; }
  }

  .mm-canvas {
      width: 100%;
      min-height: 60vh;
      border-radius: 14px;
      background: var(--thm-card, rgba(255,255,255,0.03));
      border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
      overflow: hidden;
      position: relative;
  }
  .mm-canvas svg { display: block; touch-action: none; user-select: none; cursor: grab; }
  .mm-canvas svg.mm-dragging { cursor: grabbing; }
  .mm-canvas .mm-toolbar {
      position: absolute; top: 10px; right: 10px;
      display: flex; gap: 6px; z-index: 2;
  }
  .mm-canvas .mm-toolbar button {
      width: 32px; height: 32px; border-radius: 8px;
      background: rgba(15,23,42,0.7); border: 1px solid var(--thm-border, rgba(255,255,255,0.10));
      color: var(--thm-fg, #F1F5F9); font-size: 14px; cursor: pointer;
  }
  .mm-canvas .mm-toolbar button:hover { background: rgba(15,23,42,0.9); }

  .mm-panel {
      border-radius: 14px;
      background: var(--thm-card, rgba(255,255,255,0.03));
      border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
      padding: 18px;
      min-height: 60vh;
      display: flex; flex-direction: column;
  }
  .mm-panel__placeholder {
      margin: auto; text-align: center;
      color: var(--thm-fg-muted, #94A3B8);
      font-size: 13.5px; line-height: 1.55;
  }
  .mm-panel__kind {
      display: inline-flex; padding: 3px 10px; border-radius: 999px;
      font-size: 11px; font-weight: 700; letter-spacing: 0.06em;
      text-transform: uppercase; margin-bottom: 12px;
  }
  .mm-panel__title { font-size: 18px; font-weight: 700; margin: 0 0 8px; }
  .mm-panel__detail {
      font-size: 14px; line-height: 1.55;
      color: var(--thm-fg, #F1F5F9);
      white-space: pre-wrap;
  }
  .mm-panel__links { margin-top: 14px; display: flex; gap: 8px; flex-wrap: wrap; }
  .mm-panel__link {
      padding: 8px 14px; border-radius: 999px;
      background: rgba(56,189,248,0.16); color: #38BDF8;
      text-decoration: none; font-size: 13px; font-weight: 600;
  }
  .mm-panel__link.mm-panel__link--secondary {
      background: transparent; border: 1px solid var(--thm-border, rgba(255,255,255,0.16));
      color: var(--thm-fg, #F1F5F9);
  }

  @media (max-width: 900px) {
      .mm-panel {
          position: fixed; bottom: 0; left: 0; right: 0;
          z-index: 60; max-height: 50vh;
          border-radius: 18px 18px 0 0;
          box-shadow: 0 -10px 24px rgba(0,0,0,0.45);
          transform: translateY(110%);
          transition: transform 0.18s ease;
      }
      .mm-panel.mm-panel--open { transform: translateY(0); }
      .mm-panel__close-mobile {
          position: absolute; top: 8px; right: 12px;
          background: transparent; border: 0; color: var(--thm-fg-muted, #94A3B8);
          font-size: 22px; cursor: pointer;
      }
  }

  .mm-empty {
      padding: 32px; text-align: center;
      color: var(--thm-fg-muted, #94A3B8);
      border: 1px dashed var(--thm-border, rgba(255,255,255,0.12));
      border-radius: 14px;
  }

  .mm-node rect { transition: stroke-width 0.12s; }
  .mm-node.is-active rect { stroke-width: 2.5; }
</style>

<header class="mm-head">
  <h1>Mind map — <?= $h((string)$system['name']) ?></h1>
  <span class="mm-help">Click a node for detail · Drag to pan · Wheel/pinch to zoom</span>
</header>

<?php if (empty($tree['children'])): ?>
  <p class="mm-empty">No published lessons in this system yet.</p>
<?php else: ?>
  <div class="mm-stage">
    <div class="mm-canvas">
      <div class="mm-toolbar">
        <button type="button" id="mm-zoom-in"  aria-label="Zoom in">+</button>
        <button type="button" id="mm-zoom-out" aria-label="Zoom out">−</button>
        <button type="button" id="mm-fit"      aria-label="Fit to screen" title="Fit to screen">⤢</button>
      </div>
      <div id="mind-map"
           data-tree='<?= $treeJson ?>'
           data-system-id="<?= $sysId ?>"></div>
    </div>

    <aside class="mm-panel" id="mm-panel" aria-live="polite">
      <button type="button" class="mm-panel__close-mobile" id="mm-panel-close" aria-label="Close panel">×</button>
      <div class="mm-panel__placeholder" id="mm-panel-placeholder">
        Tap any node to see its details, jump to the source slide, or open related flashcards / quiz / mnemonics.
      </div>
      <div class="mm-panel__body" id="mm-panel-body" hidden>
        <span class="mm-panel__kind" id="mm-panel-kind"></span>
        <h2 class="mm-panel__title" id="mm-panel-title"></h2>
        <div class="mm-panel__detail" id="mm-panel-detail"></div>
        <div class="mm-panel__links" id="mm-panel-links"></div>
      </div>
    </aside>
  </div>
<?php endif; ?>

<script src="/assets/js/mind-map.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/mind-map.js') ?: '0' ?>" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.MindMap && typeof window.MindMap.init === 'function') {
      window.MindMap.init('#mind-map');
    }
  });
</script>
