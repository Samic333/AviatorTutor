<?php
declare(strict_types=1);
/** @var array $modeUsage */
/** @var array $themeAdopt */
/** @var array $fontAdopt */
/** @var array $dropOff */
/** @var array $eventTotals */
/** @var array $completionBySystem */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
?>
<style>
.an-grid { display: grid; gap: 18px; grid-template-columns: 1fr; }
@media (min-width: 900px) { .an-grid { grid-template-columns: 1fr 1fr; } }
.an-card { padding: 18px; border-radius: 12px;
           background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); }
.an-card h2 { margin: 0 0 10px; font-size: 14px; font-weight: 700;
              letter-spacing: 0.04em; text-transform: uppercase; color: #94A3B8; }
.an-bar { display: grid; grid-template-columns: 140px 1fr 50px; gap: 10px; align-items: center; padding: 6px 0; font-size: 13px; }
.an-bar > .label { color: #E2E8F0; }
.an-bar > .track { height: 8px; border-radius: 999px; background: rgba(255,255,255,0.06); overflow: hidden; }
.an-bar > .track > span { display: block; height: 100%; background: linear-gradient(135deg,#38BDF8,#0EA5E9); }
.an-bar > .num   { text-align: right; color: #94A3B8; font-variant-numeric: tabular-nums; }
.an-empty { padding: 24px; text-align: center; color: #94A3B8; font-size: 13px;
            border: 1px dashed rgba(255,255,255,0.12); border-radius: 12px; }
table.an-table { width: 100%; border-collapse: collapse; font-size: 13px; }
table.an-table th, table.an-table td { padding: 8px 10px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); }
table.an-table th { color: #94A3B8; font-weight: 600; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.05em; }
</style>

<h1 style="margin:0 0 16px;">Analytics — last 30 days</h1>

<?php
$render_bars = static function (array $rows, string $labelKey, string $emptyMsg, $h) {
    if (empty($rows)) { echo '<p class="an-empty">' . $h($emptyMsg) . '</p>'; return; }
    $max = 0;
    foreach ($rows as $r) $max = max($max, (int) $r['n']);
    foreach ($rows as $r) {
        $label = (string) ($r[$labelKey] ?? '—');
        $n     = (int) $r['n'];
        $pct   = $max > 0 ? (int) round(100 * $n / $max) : 0;
        echo '<div class="an-bar"><span class="label">' . $h($label) . '</span><span class="track"><span style="width:' . $pct . '%;"></span></span><span class="num">' . $n . '</span></div>';
    }
};
?>

<div class="an-grid">
  <section class="an-card">
    <h2>Study mode opens</h2>
    <?php $render_bars($modeUsage, 'mode', 'No mode_open events yet — the topbar mode switcher fires this on click.', $h); ?>
  </section>

  <section class="an-card">
    <h2>Theme adoption</h2>
    <?php $render_bars($themeAdopt, 'theme', 'No theme changes yet.', $h); ?>
  </section>

  <section class="an-card">
    <h2>Font size adoption</h2>
    <?php $render_bars($fontAdopt, 'font_size', 'No font-size changes yet.', $h); ?>
  </section>

  <section class="an-card">
    <h2>Top events</h2>
    <?php $render_bars($eventTotals, 'event', 'No events logged yet.', $h); ?>
  </section>
</div>

<section class="an-card" style="margin-top:18px;">
  <h2>Slide completion by system</h2>
  <?php if (empty($completionBySystem)): ?>
    <p class="an-empty">No slide progress yet. Populates from user_slide_progress as learners answer slide gates.</p>
  <?php else: ?>
    <?php
      $maxDone = 0;
      foreach ($completionBySystem as $r) $maxDone = max($maxDone, (int) $r['done_slides']);
      foreach ($completionBySystem as $r):
        $total = (int) $r['total_slides'];
        $done  = (int) $r['done_slides'];
        $pct   = $total > 0 ? (int) round(100 * $done / $total) : 0;
        $color = $h((string) ($r['color_hex'] ?? '#38BDF8'));
        $bar   = $maxDone > 0 ? (int) round(100 * $done / $maxDone) : 0;
    ?>
      <div class="an-bar">
        <span class="label"><?= $h((string) $r['name']) ?></span>
        <span class="track"><span style="width: <?= $bar ?>%; background: <?= $color ?>;"></span></span>
        <span class="num"><?= $pct ?>%</span>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<section class="an-card" style="margin-top:18px;">
  <h2>Slide drop-offs (top 20)</h2>
  <?php if (empty($dropOff)): ?>
    <p class="an-empty">No slide_dropoff events yet — fires from the slide player when a learner closes the tab mid-deck.</p>
  <?php else: ?>
    <table class="an-table">
      <thead><tr><th>Lesson</th><th>Slide #</th><th>Count</th></tr></thead>
      <tbody>
        <?php foreach ($dropOff as $d): ?>
          <tr>
            <td><?= $h((string)($d['lesson_id'] ?? '—')) ?></td>
            <td><?= $h((string)($d['slide_index'] ?? '—')) ?></td>
            <td><?= (int)($d['n'] ?? 0) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
