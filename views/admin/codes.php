<?php
declare(strict_types=1);
/** @var array $codes */
/** @var array $summary */
/** @var string $statusFilter */
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Activation Codes</h1>
    <p class="adm-page-header__sub">
      <?= (int)$summary['unused'] ?> unused &middot;
      <?= (int)$summary['redeemed'] ?> redeemed &middot;
      <?= (int)$summary['revoked'] ?> revoked &middot;
      <?= (int)$summary['total'] ?> total
    </p>
  </div>
</div>

<!-- Generate -->
<div class="adm-panel" style="margin-bottom:20px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Generate Codes</h2>
  </div>
  <div class="adm-panel__body">
    <form method="post" action="/admin/codes/generate" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <div class="adm-form-group" style="margin:0;min-width:80px;">
        <label class="adm-label">Count</label>
        <input class="adm-input" type="number" name="count" value="5" min="1" max="100" style="width:90px;">
      </div>
      <div class="adm-form-group" style="margin:0;min-width:100px;">
        <label class="adm-label">Days valid</label>
        <input class="adm-input" type="number" name="days" value="30" min="1" max="3650" style="width:100px;">
      </div>
      <div class="adm-form-group" style="margin:0;">
        <label class="adm-label">Plan</label>
        <input class="adm-input" type="text" name="plan" value="monthly" style="width:120px;">
      </div>
      <button type="submit" class="adm-btn adm-btn--primary">Generate</button>
    </form>
  </div>
</div>

<!-- Codes list -->
<div class="adm-panel">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Codes</h2>
    <div style="display:flex;gap:6px;">
      <?php foreach (['all' => 'All', 'unused' => 'Unused', 'redeemed' => 'Redeemed', 'revoked' => 'Revoked'] as $val => $label): ?>
        <a href="/admin/codes<?= $val !== 'all' ? '?status='.$val : '' ?>"
           class="adm-btn adm-btn--ghost adm-btn--sm <?= $statusFilter === $val ? 'adm-badge--gold' : '' ?>"
           style="<?= $statusFilter === $val ? 'color:var(--adm-gold);border-color:var(--adm-gold-dim);' : '' ?>">
          <?= $label ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>Code</th>
          <th>Status</th>
          <th>Plan / Days</th>
          <th>Redeemed by</th>
          <th>Created</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($codes as $c):
          $status = (string)$c['status']; ?>
          <tr>
            <td class="adm-mono"><?= htmlspecialchars((string)$c['code']) ?></td>
            <td>
              <span class="adm-badge adm-badge--<?= $status === 'unused' ? 'success' : ($status === 'redeemed' ? 'gold' : 'warn') ?>">
                <?= htmlspecialchars(strtoupper($status)) ?>
              </span>
            </td>
            <td><?= htmlspecialchars((string)$c['plan']) ?> &middot; <?= (int)$c['days'] ?>d</td>
            <td>
              <?php if (!empty($c['redeemed_email'])): ?>
                <?= htmlspecialchars((string)$c['redeemed_name']) ?>
                <span class="adm-muted">(<?= htmlspecialchars((string)$c['redeemed_email']) ?>)</span>
              <?php else: ?>
                <span class="adm-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="adm-muted"><?= htmlspecialchars(date('M j Y', strtotime((string)$c['created_at']))) ?></td>
            <td>
              <?php if ($status === 'unused'): ?>
                <form method="post" action="/admin/codes/revoke" style="display:inline;">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                  <input type="hidden" name="code" value="<?= htmlspecialchars((string)$c['code']) ?>">
                  <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm" data-confirm="Revoke this code?">Revoke</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($codes)): ?>
          <tr><td colspan="6" class="adm-muted" style="text-align:center;padding:32px;">No codes match this filter.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
