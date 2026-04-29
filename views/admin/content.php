<?php
declare(strict_types=1);
/** @var array $systems */
/** @var array $aircrafts */
/** @var int $aircraftId */
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Content</h1>
    <p class="adm-page-header__sub">Systems, lessons, flashcards and quizzes across all aircraft and subjects.</p>
  </div>
  <div class="adm-page-header__actions">
    <form method="get" action="/admin/content" style="display:flex;gap:8px;align-items:center;">
      <select name="aircraft_id" class="adm-select" style="width:auto;" data-autosubmit>
        <option value="0">All aircraft</option>
        <?php foreach ($aircrafts as $a): ?>
          <option value="<?= (int)$a['id'] ?>" <?= (int)$aircraftId === (int)$a['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars((string)$a['short_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
</div>

<div class="adm-panel">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Systems (<?= count($systems) ?>)</h2>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>System</th>
          <th>ATA</th>
          <th>Aircraft</th>
          <th class="num">Lessons</th>
          <th class="num">Cards</th>
          <th class="num">Quizzes</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($systems)): ?>
          <tr><td colspan="7" class="adm-muted" style="text-align:center;padding:32px;">No systems found.</td></tr>
        <?php else: ?>
          <?php foreach ($systems as $s): ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars((string)$s['name']) ?></strong>
                <div class="adm-muted" style="font-size:12px;"><?= htmlspecialchars((string)$s['slug']) ?></div>
              </td>
              <td class="adm-mono adm-muted"><?= htmlspecialchars((string)($s['ata_code'] ?? '—')) ?></td>
              <td class="adm-muted"><?= htmlspecialchars((string)($s['aircraft'] ?? '—')) ?></td>
              <td class="num adm-mono"><?= (int)$s['lesson_count'] ?></td>
              <td class="num adm-mono"><?= (int)$s['card_count'] ?></td>
              <td class="num adm-mono"><?= (int)$s['quiz_count'] ?></td>
              <td>
                <span class="adm-badge adm-badge--<?= $s['is_published'] ? 'success' : 'muted' ?>">
                  <?= $s['is_published'] ? 'LIVE' : 'DRAFT' ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.querySelectorAll('[data-autosubmit]').forEach(function(el) {
  el.addEventListener('change', function() { el.closest('form').submit(); });
});
</script>
