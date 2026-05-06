<?php
declare(strict_types=1);
/**
 * System picker — search + aircraft + category + dropdown + grouped tile grid.
 * Designed to scale from Q400's 22 systems to the future B777 set. The host
 * page passes $pickerSystems and $pickerAircrafts; the picker handles
 * grouping, search filtering, dropdown navigation client-side.
 *
 * Vars in scope:
 *   $pickerSystems   array  rows: id, name, slug, ata_code, category, color_hex, icon
 *   $pickerActiveId  ?int   currently-selected system id (highlights tile)
 *   $pickerAircrafts ?array rows: id, slug, name, status (optional)
 */

$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$rows      = is_array($pickerSystems ?? null) ? $pickerSystems : [];
$active    = (int) ($pickerActiveId ?? 0);
$aircrafts = is_array($pickerAircrafts ?? null) ? $pickerAircrafts : [];

// Category grouping
$labels = [
    'powerplant'    => 'Powerplant',
    'electrical'    => 'Electrical',
    'hydraulics'    => 'Hydraulics & flight controls',
    'avionics'      => 'Avionics',
    'environmental' => 'Environmental & safety',
    'reference'     => 'Reference',
    'general'       => 'Other',
];
$grouped = [];
foreach ($rows as $r) {
    $cat = (string) ($r['category'] ?? 'general');
    $grouped[$cat][] = $r;
}
$ordered = [];
foreach ($labels as $cat => $_label) {
    if (!empty($grouped[$cat])) $ordered[$cat] = $grouped[$cat];
}
foreach ($grouped as $cat => $items) {
    if (!isset($ordered[$cat])) $ordered[$cat] = $items;
}
$collapseDefault = count($rows) > 12;
?>
<div class="sp-wrap" id="system-picker">

  <div class="sp-controls">
    <input type="search" id="sp-search" class="sp-search" placeholder="Search systems… (e.g. electrical, hydraulic, ATA29)" autocomplete="off" aria-label="Search systems">

    <select id="sp-aircraft" class="sp-jump" aria-label="Filter by aircraft">
      <option value="">All aircraft</option>
      <?php if (!empty($aircrafts)): ?>
        <?php foreach ($aircrafts as $a):
          $isLive = (string)($a['status'] ?? '') === 'live';
        ?>
          <option value="<?= $h((string)$a['slug']) ?>" <?= $isLive ? '' : 'data-coming-soon="1"' ?>>
            <?= $h((string)$a['name']) ?><?= $isLive ? '' : ' (coming soon)' ?>
          </option>
        <?php endforeach; ?>
      <?php else: ?>
        <option value="q400">Q400</option>
      <?php endif; ?>
    </select>

    <select id="sp-category" class="sp-jump" aria-label="Filter by category">
      <option value="">All categories</option>
      <?php foreach ($ordered as $cat => $_items): ?>
        <option value="<?= $h((string)$cat) ?>"><?= $h($labels[$cat] ?? ucfirst((string)$cat)) ?></option>
      <?php endforeach; ?>
    </select>

    <select id="sp-jump" class="sp-jump" aria-label="Jump to a system">
      <option value="">Jump to system…</option>
      <?php foreach ($rows as $r): ?>
        <option value="/study/<?= (int)$r['id'] ?>" <?= (int)$r['id'] === $active ? 'selected' : '' ?>>
          <?= $h((string)$r['name']) ?> · <?= $h((string)$r['ata_code']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <p class="sp-coming-soon" id="sp-coming-soon" hidden>
    This aircraft is coming soon — join the waitlist on the
    <a href="/aircraft">aircraft catalog</a>.
  </p>

  <?php foreach ($ordered as $cat => $items):
    $label = $labels[$cat] ?? ucfirst((string)$cat);
    $open  = !$collapseDefault;
  ?>
    <details class="sp-group" data-category="<?= $h((string)$cat) ?>" <?= $open ? 'open' : '' ?>>
      <summary>
        <span><?= $h((string)$label) ?></span>
        <span class="sp-count"><?= count($items) ?></span>
      </summary>
      <div class="sp-tiles">
        <?php foreach ($items as $r):
          $color = $h((string)($r['color_hex'] ?? '#38BDF8'));
          $name  = $h((string)$r['name']);
          $ata   = $h((string)$r['ata_code']);
          $slug  = $h((string)$r['slug']);
          $isActive = (int)$r['id'] === $active;
        ?>
          <a href="/study/<?= (int)$r['id'] ?>"
             class="sp-tile <?= $isActive ? 'is-active' : '' ?>"
             data-search-text="<?= $h(strtolower($name . ' ' . $ata . ' ' . $slug)) ?>"
             style="--sp-accent: <?= $color ?>;">
            <span class="sp-tile__ata"><?= $ata ?></span>
            <span class="sp-tile__name"><?= $name ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </details>
  <?php endforeach; ?>

  <p class="sp-empty" id="sp-empty" hidden>No systems match.</p>
</div>

<style>
.sp-wrap { display: flex; flex-direction: column; gap: 14px; }
.sp-controls {
    display: grid;
    grid-template-columns: minmax(200px, 1fr) repeat(3, minmax(140px, auto));
    gap: 10px;
    align-items: center;
}
@media (max-width: 900px) { .sp-controls { grid-template-columns: 1fr 1fr; } }
@media (max-width: 600px) { .sp-controls { grid-template-columns: 1fr; } }
.sp-search, .sp-jump {
    padding: 10px 14px; border-radius: 10px;
    background: var(--thm-card, rgba(255,255,255,0.04));
    border: 1px solid var(--thm-border, rgba(255,255,255,0.10));
    color: var(--thm-fg, #F1F5F9);
    font-size: 14px;
}
.sp-jump { min-width: 140px; cursor: pointer; }

.sp-coming-soon {
    padding: 14px 16px; border-radius: 10px;
    background: rgba(251,191,36,0.10);
    border: 1px solid rgba(251,191,36,0.30);
    color: var(--thm-fg, #F1F5F9);
    font-size: 14px;
}
.sp-coming-soon a { color: #FBBF24; text-decoration: underline; }

.sp-group { border-radius: 12px; padding: 6px 12px; background: var(--thm-card, rgba(255,255,255,0.03)); border: 1px solid var(--thm-border, rgba(255,255,255,0.08)); }
.sp-group > summary { cursor: pointer; padding: 8px 4px; list-style: none; display: flex; align-items: center; justify-content: space-between; gap: 12px; font-weight: 700; font-size: 14px; color: var(--thm-fg, #F1F5F9); }
.sp-group > summary::-webkit-details-marker { display: none; }
.sp-count { font-size: 11px; padding: 2px 8px; border-radius: 999px; background: rgba(255,255,255,0.06); color: var(--thm-fg-muted, #94A3B8); font-weight: 600; }

.sp-tiles { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; padding: 8px 0 12px; }
.sp-tile {
    display: flex; flex-direction: column; gap: 4px;
    padding: 12px 14px; border-radius: 10px;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
    border-left: 4px solid var(--sp-accent, #38BDF8);
    text-decoration: none; color: var(--thm-fg, #F1F5F9);
    transition: background 0.12s ease, border-color 0.12s ease;
    min-height: 56px;
}
.sp-tile:hover { background: rgba(255,255,255,0.06); }
.sp-tile.is-active { background: rgba(56,189,248,0.16); border-color: var(--sp-accent, #38BDF8); }
.sp-tile__ata { font-size: 11px; font-weight: 700; color: var(--sp-accent, #38BDF8); letter-spacing: 0.04em; }
.sp-tile__name { font-size: 14px; font-weight: 600; }

.sp-tile.sp-hidden { display: none; }
.sp-group.sp-empty-cat,
.sp-group.sp-cat-hidden { display: none; }
.sp-empty { text-align: center; padding: 24px; color: var(--thm-fg-muted, #94A3B8); font-size: 14px; }

body[data-theme="light"] .sp-search, body[data-theme="light"] .sp-jump { background: #fff; color: #0F172A; }
</style>

<script>
(function () {
  var input    = document.getElementById('sp-search');
  var aircraft = document.getElementById('sp-aircraft');
  var category = document.getElementById('sp-category');
  var jump     = document.getElementById('sp-jump');
  var groups   = Array.prototype.slice.call(document.querySelectorAll('.sp-group'));
  var empty    = document.getElementById('sp-empty');
  var coming   = document.getElementById('sp-coming-soon');
  if (!input || !groups.length) return;

  function applyFilter() {
    var q   = input.value.trim().toLowerCase();
    var cat = category ? category.value : '';
    var anyVisible = false;

    // Aircraft "coming soon" hides the entire grid + shows a banner.
    var acOpt = aircraft && aircraft.options[aircraft.selectedIndex];
    var acComingSoon = !!(acOpt && acOpt.dataset && acOpt.dataset.comingSoon);
    if (coming) coming.hidden = !acComingSoon;

    groups.forEach(function (group) {
      var groupCat = group.getAttribute('data-category') || '';
      var catHidden = cat && cat !== groupCat;
      group.classList.toggle('sp-cat-hidden', catHidden || acComingSoon);

      if (catHidden || acComingSoon) return;

      var tiles = Array.prototype.slice.call(group.querySelectorAll('.sp-tile'));
      var visibleHere = 0;
      tiles.forEach(function (t) {
        var hay = t.getAttribute('data-search-text') || '';
        var hidden = q.length > 0 && hay.indexOf(q) === -1;
        t.classList.toggle('sp-hidden', hidden);
        if (!hidden) visibleHere++;
      });
      group.classList.toggle('sp-empty-cat', visibleHere === 0);
      if (q.length > 0 || cat) group.open = visibleHere > 0;
      if (visibleHere > 0) anyVisible = true;
    });

    if (empty) empty.hidden = anyVisible || acComingSoon;
  }

  var t = null;
  function debounced() {
    clearTimeout(t);
    t = setTimeout(applyFilter, 60);
  }

  input.addEventListener('input', debounced);
  if (aircraft) aircraft.addEventListener('change', applyFilter);
  if (category) category.addEventListener('change', applyFilter);

  if (jump) {
    jump.addEventListener('change', function () {
      var v = jump.value;
      if (v) window.location.href = v;
    });
  }
})();
</script>
