<?php
declare(strict_types=1);
/** @var array $cards */
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Flashcards</h1>
    <p class="adm-page-header__sub"><?= count($cards) ?> cards (newest 100)</p>
  </div>
</div>

<div class="adm-panel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>#</th>
          <th>System</th>
          <th>Question</th>
          <th>Difficulty</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($cards)): ?>
          <tr><td colspan="4" class="adm-muted" style="text-align:center;padding:32px;">No flashcards yet.</td></tr>
        <?php else: ?>
          <?php foreach ($cards as $c): ?>
            <tr>
              <td class="adm-muted adm-mono"><?= (int)$c['id'] ?></td>
              <td>
                <span style="display:inline-block;padding:2px 8px;background:rgba(56,189,248,0.12);color:#38BDF8;border-radius:4px;font-size:12px;">
                  <?= htmlspecialchars((string)($c['system_name'] ?? '—')) ?>
                </span>
              </td>
              <td style="max-width:480px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars((string)$c['question']) ?>">
                <?= htmlspecialchars(mb_substr((string)$c['question'], 0, 80)) ?><?= mb_strlen((string)$c['question']) > 80 ? '…' : '' ?>
              </td>
              <td>
                <span class="adm-badge adm-badge--<?= $c['difficulty'] === 'easy' ? 'success' : ($c['difficulty'] === 'hard' ? 'danger' : 'warn') ?>">
                  <?= htmlspecialchars(strtoupper((string)$c['difficulty'])) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
