<?php
declare(strict_types=1);
/** @var array $quizzes */
/** @var string $csrf_token */
?>
<div class="adm-page-header" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div>
    <h1 class="adm-page-header__title">Quizzes</h1>
    <p class="adm-page-header__sub"><?= count($quizzes) ?> quiz<?= count($quizzes) === 1 ? '' : 'zes' ?> (newest 100)</p>
  </div>
  <a href="/admin/quizzes/new" class="adm-btn adm-btn--primary">+ New quiz</a>
</div>

<div class="adm-panel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th style="width:60px;">#</th>
          <th>Title</th>
          <th>System</th>
          <th>Type</th>
          <th class="num">Pass</th>
          <th class="num">Time</th>
          <th>Status</th>
          <th style="width:160px;text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($quizzes)): ?>
          <tr><td colspan="8" class="adm-muted" style="text-align:center;padding:32px;">No quizzes yet. <a href="/admin/quizzes/new" style="color:var(--plt-sky,#38BDF8);">Create the first one →</a></td></tr>
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
                  <?= htmlspecialchars(strtoupper(str_replace('_', ' ', (string)$q['quiz_type']))) ?>
                </span>
              </td>
              <td class="num adm-mono"><?= (int)$q['pass_score'] ?>%</td>
              <td class="num adm-mono">
                <?= !empty($q['time_limit_minutes']) ? (int)$q['time_limit_minutes'] . ' min' : '—' ?>
              </td>
              <td>
                <?php if ((int)$q['is_published'] === 1): ?>
                  <span class="adm-badge adm-badge--success">PUBLISHED</span>
                <?php else: ?>
                  <span class="adm-badge adm-badge--muted">DRAFT</span>
                <?php endif; ?>
              </td>
              <td style="text-align:right;white-space:nowrap;">
                <a href="/admin/quizzes/<?= (int)$q['id'] ?>/edit" class="adm-btn adm-btn--ghost">Edit</a>
                <form method="post" action="/admin/quizzes/<?= (int)$q['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this quiz?');">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" class="adm-btn adm-btn--danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <p class="adm-muted" style="font-size:12px;margin:16px 0 0;padding:12px;background:rgba(56,189,248,0.06);border-radius:6px;border-left:3px solid #38BDF8;">
    Note: quiz <strong>questions</strong> are stored in the legacy <code>quiz_questions → modules</code> pipeline and are managed via the q400-study importer. This editor manages quiz containers (title, type, timing, pass score, publish state) only.
  </p>
</div>
