<?php
declare(strict_types=1);
/** @var array $cards */
/** @var array $systems */
/** @var string $csrf_token */
?>
<div class="adm-page-header" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
  <div>
    <h1 class="adm-page-header__title">Flashcards</h1>
    <p class="adm-page-header__sub"><?= count($cards) ?> card<?= count($cards) === 1 ? '' : 's' ?> (newest 100)</p>
  </div>
  <a href="/admin/flashcards/new" class="adm-btn adm-btn--primary">+ New flashcard</a>
</div>

<div class="adm-panel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th style="width:60px;">#</th>
          <th>System</th>
          <th>Front</th>
          <th>Difficulty</th>
          <th style="width:160px;text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($cards)): ?>
          <tr><td colspan="5" class="adm-muted" style="text-align:center;padding:32px;">No flashcards yet. <a href="/admin/flashcards/new" style="color:var(--plt-sky,#38BDF8);">Create the first one →</a></td></tr>
        <?php else: ?>
          <?php foreach ($cards as $c): ?>
            <tr>
              <td class="adm-muted adm-mono"><?= (int)$c['id'] ?></td>
              <td>
                <?php if (!empty($c['system_name'])): ?>
                  <span style="display:inline-block;padding:2px 8px;background:rgba(56,189,248,0.12);color:#38BDF8;border-radius:4px;font-size:12px;">
                    <?= htmlspecialchars((string)$c['system_name']) ?>
                  </span>
                <?php else: ?>
                  <span class="adm-muted">—</span>
                <?php endif; ?>
              </td>
              <td style="max-width:480px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars((string)$c['front']) ?>">
                <?= htmlspecialchars(mb_substr((string)$c['front'], 0, 80)) ?><?= mb_strlen((string)$c['front']) > 80 ? '…' : '' ?>
              </td>
              <td>
                <span class="adm-badge adm-badge--<?= $c['difficulty'] === 'easy' ? 'success' : ($c['difficulty'] === 'hard' ? 'danger' : 'warn') ?>">
                  <?= htmlspecialchars(strtoupper((string)$c['difficulty'])) ?>
                </span>
              </td>
              <td style="text-align:right;white-space:nowrap;">
                <a href="/admin/flashcards/<?= (int)$c['id'] ?>/edit" class="adm-btn adm-btn--ghost">Edit</a>
                <form method="post" action="/admin/flashcards/<?= (int)$c['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this flashcard?');">
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
</div>
