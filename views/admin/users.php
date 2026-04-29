<?php
declare(strict_types=1);
/** @var array $users */
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Users</h1>
    <p class="adm-page-header__sub"><?= count($users) ?> account<?= count($users) === 1 ? '' : 's' ?> registered</p>
  </div>
</div>

<div class="adm-panel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Verified</th>
          <th>Aircraft</th>
          <th>Sub</th>
          <th>Joined</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <?php $isVerified = !empty($u['email_verified_at']); ?>
          <tr>
            <td class="adm-muted adm-mono"><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars((string)$u['name']) ?></td>
            <td>
              <a href="mailto:<?= htmlspecialchars((string)$u['email']) ?>"><?= htmlspecialchars((string)$u['email']) ?></a>
            </td>
            <td>
              <span class="adm-badge adm-badge--<?= $u['role'] === 'admin' ? 'gold' : 'muted' ?>">
                <?= htmlspecialchars(strtoupper((string)$u['role'])) ?>
              </span>
            </td>
            <td>
              <?php if ($isVerified): ?>
                <span class="adm-badge adm-badge--success" title="<?= htmlspecialchars((string)$u['email_verified_at']) ?>">YES</span>
              <?php else: ?>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                  <form method="post" action="/admin/users/verify" style="display:inline;" onsubmit="return confirm('Mark this user as email-verified? They will be able to log in immediately.');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                    <button type="submit" class="adm-btn adm-btn--primary adm-btn--sm" title="Manually verify this user &mdash; bypasses the email-verification gate. Use when the user can't receive the verification email.">
                      Mark verified
                    </button>
                  </form>
                  <form method="post" action="/admin/users/resend-verify" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                    <button type="submit" class="adm-btn adm-btn--ghost adm-btn--sm" title="Send a fresh verification email to this user.">
                      Resend
                    </button>
                  </form>
                </div>
              <?php endif; ?>
            </td>
            <td class="adm-muted"><?= htmlspecialchars((string)($u['preferred_aircraft'] ?? '—')) ?></td>
            <td>
              <?php if ((int)$u['sub_active'] > 0): ?>
                <span class="adm-badge adm-badge--success">ACTIVE</span>
              <?php else: ?>
                <span class="adm-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="adm-muted"><?= htmlspecialchars(date('M j Y', strtotime((string)$u['created_at']))) ?></td>
            <td>
              <form method="post" action="/admin/users/update" style="display:inline-flex;gap:6px;align-items:center;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <select name="role" class="adm-select" style="width:auto;padding:5px 8px;font-size:12px;" data-autosubmit>
                  <option value="learner" <?= $u['role'] === 'learner' ? 'selected' : '' ?>>learner</option>
                  <option value="admin"   <?= $u['role'] === 'admin'   ? 'selected' : '' ?>>admin</option>
                </select>
                <button type="submit" class="adm-btn adm-btn--ghost adm-btn--sm">Save</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
