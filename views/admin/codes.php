<?php
declare(strict_types=1);
/** @var array $codes */
/** @var array $summary */
/** @var string $statusFilter */
/** @var string $csrf_token */
?>
<div class="admin-page">
    <header class="admin-page__head">
        <div>
            <h1>Activation codes</h1>
            <p class="muted" style="margin:6px 0 0;">
                <?= (int) $summary['unused'] ?> unused · <?= (int) $summary['redeemed'] ?> redeemed · <?= (int) $summary['revoked'] ?> revoked · <?= (int) $summary['total'] ?> total
            </p>
        </div>
        <div class="admin-page__actions">
            <a href="/admin" class="btn btn-sm btn-ghost">← Dashboard</a>
        </div>
    </header>

    <?php if (!empty($flashOk)): ?><div class="flash flash--success"><?= htmlspecialchars($flashOk) ?></div><?php endif; ?>
    <?php if (!empty($flashError)): ?><div class="flash flash--error"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>

    <section class="admin-panel">
        <h2 class="admin-panel__title">Generate codes</h2>
        <form method="post" action="/admin/codes/generate" class="admin-form-row">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <label class="form-group">
                <span class="form-label">Count</span>
                <input class="form-input" type="number" name="count" value="5" min="1" max="100">
            </label>
            <label class="form-group">
                <span class="form-label">Days valid</span>
                <input class="form-input" type="number" name="days" value="30" min="1" max="3650">
            </label>
            <label class="form-group">
                <span class="form-label">Plan</span>
                <input class="form-input" type="text" name="plan" value="monthly">
            </label>
            <button type="submit" class="btn btn-primary">Generate</button>
        </form>
    </section>

    <section class="admin-panel">
        <h2 class="admin-panel__title">Codes
            <span class="admin-tabs">
                <a href="/admin/codes" class="<?= $statusFilter === 'all' ? 'active' : '' ?>">All</a>
                <a href="/admin/codes?status=unused" class="<?= $statusFilter === 'unused' ? 'active' : '' ?>">Unused</a>
                <a href="/admin/codes?status=redeemed" class="<?= $statusFilter === 'redeemed' ? 'active' : '' ?>">Redeemed</a>
                <a href="/admin/codes?status=revoked" class="<?= $statusFilter === 'revoked' ? 'active' : '' ?>">Revoked</a>
            </span>
        </h2>
        <table class="admin-table">
            <thead>
                <tr><th>Code</th><th>Status</th><th>Plan / days</th><th>Redeemed by</th><th>Created</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($codes as $c):
                    $status = (string) $c['status']; ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars((string) $c['code']) ?></td>
                        <td>
                            <span class="badge badge--<?= $status === 'unused' ? 'success' : ($status === 'redeemed' ? 'accent' : 'warn') ?>">
                                <?= htmlspecialchars(strtoupper($status)) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars((string) $c['plan']) ?> · <?= (int) $c['days'] ?>d</td>
                        <td>
                            <?php if (!empty($c['redeemed_email'])): ?>
                                <?= htmlspecialchars((string) $c['redeemed_name']) ?>
                                <span class="muted">(<?= htmlspecialchars((string) $c['redeemed_email']) ?>)</span>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="muted"><?= htmlspecialchars(date('M j Y', strtotime((string) $c['created_at']))) ?></td>
                        <td>
                            <?php if ($status === 'unused'): ?>
                                <form method="post" action="/admin/codes/revoke" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <input type="hidden" name="code" value="<?= htmlspecialchars((string) $c['code']) ?>">
                                    <button type="submit" class="btn btn-sm btn-ghost" onclick="return confirm('Revoke this code?');">Revoke</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($codes)): ?>
                    <tr><td colspan="6" class="muted text-center">No codes match this filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>
