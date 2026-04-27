<?php
declare(strict_types=1);
/** @var array $users */
/** @var string $csrf_token */
?>
<div class="admin-page">
    <header class="admin-page__head">
        <div>
            <h1>Users</h1>
            <p class="muted" style="margin:6px 0 0;"><?= count($users) ?> account<?= count($users) === 1 ? '' : 's' ?></p>
        </div>
        <div class="admin-page__actions">
            <a href="/admin" class="btn btn-sm btn-ghost">← Dashboard</a>
        </div>
    </header>

    <?php if (!empty($flashOk)): ?><div class="flash flash--success"><?= htmlspecialchars($flashOk) ?></div><?php endif; ?>
    <?php if (!empty($flashError)): ?><div class="flash flash--error"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>

    <section class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Aircraft</th><th>Sub</th><th>Created</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="muted mono"><?= (int) $u['id'] ?></td>
                        <td><?= htmlspecialchars((string) $u['name']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars((string) $u['email']) ?>"><?= htmlspecialchars((string) $u['email']) ?></a></td>
                        <td>
                            <span class="badge badge--<?= $u['role'] === 'admin' ? 'accent' : 'success' ?>">
                                <?= htmlspecialchars(strtoupper((string) $u['role'])) ?>
                            </span>
                        </td>
                        <td class="muted"><?= htmlspecialchars((string) ($u['preferred_aircraft'] ?? '—')) ?></td>
                        <td>
                            <?php if ((int) $u['sub_active'] > 0): ?>
                                <span class="badge badge--success">ACTIVE</span>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="muted"><?= htmlspecialchars(date('M j Y', strtotime((string) $u['created_at']))) ?></td>
                        <td>
                            <form method="post" action="/admin/users/update" style="display:inline-flex;gap:6px;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                <select name="role" class="form-input form-input--inline">
                                    <option value="learner" <?= $u['role'] === 'learner' ? 'selected' : '' ?>>learner</option>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                </select>
                                <button type="submit" class="btn btn-sm">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
