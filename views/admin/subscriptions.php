<?php
declare(strict_types=1);
/** @var array $subscriptions */
/** @var array $summary */
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Subscriptions</h1>
    <p class="adm-page-header__sub">
      <?= (int)($summary['active'] ?? 0) ?> active &middot;
      <?= (int)($summary['expired'] ?? 0) ?> expired &middot;
      <?= (int)($summary['cancelled'] ?? 0) ?> cancelled
    </p>
  </div>
</div>

<div class="adm-panel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Plan</th>
          <th>Status</th>
          <th>Provider</th>
          <th>Expires</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($subscriptions)): ?>
          <tr><td colspan="7" class="adm-muted" style="text-align:center;padding:32px;">No subscriptions yet.</td></tr>
        <?php else: ?>
          <?php foreach ($subscriptions as $s): ?>
            <tr>
              <td class="adm-muted adm-mono"><?= (int)$s['id'] ?></td>
              <td>
                <div><?= htmlspecialchars((string)($s['name'] ?? '—')) ?></div>
                <div class="adm-muted" style="font-size:12px;"><?= htmlspecialchars((string)($s['email'] ?? '')) ?></div>
              </td>
              <td class="adm-mono"><?= htmlspecialchars((string)$s['plan']) ?></td>
              <td>
                <span class="adm-badge adm-badge--<?= $s['status'] === 'active' ? 'success' : ($s['status'] === 'cancelled' ? 'danger' : 'muted') ?>">
                  <?= htmlspecialchars(strtoupper((string)$s['status'])) ?>
                </span>
              </td>
              <td class="adm-muted adm-mono"><?= htmlspecialchars((string)($s['payment_provider'] ?? '—')) ?></td>
              <td class="adm-muted"><?= htmlspecialchars(date('M j Y', strtotime((string)$s['expires_at']))) ?></td>
              <td>
                <?php if ($s['status'] === 'active'): ?>
                  <form method="post" action="/admin/subscriptions/cancel" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="subscription_id" value="<?= (int)$s['id'] ?>">
                    <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm" data-confirm="Cancel this subscription?">Cancel</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
