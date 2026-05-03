<?php
declare(strict_types=1);
/** @var array $system */
/** @var array $tree */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$treeJson = json_encode($tree, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
?>
<style>
  .mm-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
  .mm-head h1 { margin: 0; font-size: 22px; font-weight: 700; }
  .mm-help { color: var(--thm-fg-muted, #94A3B8); font-size: 12.5px; }
  .mm-canvas {
      width: 100%;
      min-height: 60vh;
      border-radius: 14px;
      background: var(--thm-card, rgba(255,255,255,0.03));
      border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
      overflow: hidden;
  }
  .mm-canvas svg { display: block; touch-action: none; user-select: none; }
  .mm-empty {
      padding: 32px; text-align: center;
      color: var(--thm-fg-muted, #94A3B8);
      border: 1px dashed var(--thm-border, rgba(255,255,255,0.12));
      border-radius: 14px;
  }
</style>

<header class="mm-head">
  <h1>Mind map — <?= $h((string)$system['name']) ?></h1>
  <span class="mm-help">Click a node to collapse · Drag to pan · Wheel/pinch to zoom</span>
</header>

<?php if (empty($tree['children'])): ?>
  <p class="mm-empty">No published lessons in this system yet.</p>
<?php else: ?>
  <div class="mm-canvas">
    <div id="mind-map" data-tree='<?= $treeJson ?>'></div>
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
