<?php
declare(strict_types=1);
/** @var array $rows */
/** @var string $csrf_token */

// Group lesson rows by system for a tidier display.
$bySystem = [];
foreach ($rows as $r) {
    $sid = (int)$r['system_id'];
    if (!isset($bySystem[$sid])) {
        $bySystem[$sid] = [
            'system_name' => $r['system_name'],
            'ata_code'    => $r['ata_code'],
            'lessons'     => [],
        ];
    }
    $bySystem[$sid]['lessons'][] = $r;
}
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Slide Lessons</h1>
    <p class="adm-page-header__sub">Pick a lesson to add, edit, reorder, or delete its interactive slides. Each slide becomes a step in the slide-based study player.</p>
  </div>
</div>

<?php if (empty($bySystem)): ?>
  <div class="adm-panel">
    <div class="adm-panel__body">
      <p class="adm-muted">No lessons yet for the selected filter. Create lessons in <a href="/admin/content">Content</a> first, then come back here to author slides.</p>
    </div>
  </div>
<?php else: ?>
  <?php foreach ($bySystem as $sid => $sys): ?>
    <div class="adm-panel" style="margin-bottom:16px;">
      <div class="adm-panel__header">
        <h2 class="adm-panel__title">
          <?= htmlspecialchars((string)$sys['system_name']) ?>
          <?php if (!empty($sys['ata_code'])): ?>
            <span class="adm-mono adm-muted" style="font-size:12px;font-weight:500;margin-left:8px;"><?= htmlspecialchars((string)$sys['ata_code']) ?></span>
          <?php endif; ?>
        </h2>
      </div>
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th>Lesson</th>
              <th class="num">Slides</th>
              <th>Status</th>
              <th style="width:1%;"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sys['lessons'] as $row):
              $count = (int)$row['slide_count'];
            ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars((string)$row['lesson_title']) ?></strong>
                  <div class="adm-muted" style="font-size:12px;"><?= htmlspecialchars((string)$row['lesson_slug']) ?></div>
                </td>
                <td class="num adm-mono"><?= $count ?></td>
                <td>
                  <?php if ($count === 0): ?>
                    <span class="adm-badge adm-badge--muted">EMPTY</span>
                  <?php elseif ($count < 4): ?>
                    <span class="adm-badge adm-badge--warning">SPARSE</span>
                  <?php else: ?>
                    <span class="adm-badge adm-badge--success">READY</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="/admin/slides/lesson/<?= (int)$row['lesson_id'] ?>" class="adm-btn adm-btn--primary adm-btn--sm">
                    <?= $count > 0 ? 'Edit slides' : 'Add slides' ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

