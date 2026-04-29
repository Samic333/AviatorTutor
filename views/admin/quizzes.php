<?php
declare(strict_types=1);
/** @var array $quizzes */
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Quizzes</h1>
    <p class="adm-page-header__sub"><?= count($quizzes) ?> quiz<?= count($quizzes) === 1 ? '' : 'zes' ?> (newest 100)</p>
  </div>
</div>

<div class="adm-panel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>System</th>
          <th>Type</th>
          <th class="num">Questions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($quizzes)): ?>
          <tr><td colspan="5" class="adm-muted" style="text-align:center;padding:32px;">No quizzes yet.</td></tr>
        <?php else: ?>
          <?php foreach ($quizzes as $q): ?>
            <tr>
              <td class="adm-muted adm-mono"><?= (int)$q['id'] ?></td>
              <td style="max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                <?= htmlspecialchars((string)$q['title']) ?>
              </td>
              <td class="adm-muted"><?= htmlspecialchars((string)($q['system_name'] ?? '—')) ?></td>
              <td>
                <span class="adm-badge adm-badge--muted">
                  <?= htmlspecialchars(strtoupper((string)($q['difficulty'] ?? 'N/A'))) ?>
                </span>
              </td>
              <td class="num adm-mono"><?= (int)$q['qcount'] ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
