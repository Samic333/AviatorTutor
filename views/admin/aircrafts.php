<?php
declare(strict_types=1);
/** @var array $aircrafts */
/** @var string $csrf_token */
?>
<div class="admin-page">
    <header class="admin-page__head">
        <div>
            <h1>Aircraft catalog</h1>
            <p class="muted" style="margin:6px 0 0;">Set status (live / beta / coming_soon / archived) and ordering. Cockpit images are managed via the cockpit upload tool.</p>
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
                <tr><th>Name</th><th>Slug</th><th>Status</th><th>Sort</th><th class="num">Waitlist</th><th>Cockpit</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($aircrafts as $a): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars((string) $a['name']) ?></strong> <span class="muted">(<?= htmlspecialchars((string) $a['short_name']) ?>)</span></td>
                        <td class="mono"><?= htmlspecialchars((string) $a['slug']) ?></td>
                        <td>
                            <form method="post" action="/admin/aircrafts/update" style="display:inline-flex;gap:6px;align-items:center;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                <select name="status" class="form-input form-input--inline">
                                    <option value="live"        <?= $a['status'] === 'live' ? 'selected' : '' ?>>live</option>
                                    <option value="beta"        <?= $a['status'] === 'beta' ? 'selected' : '' ?>>beta</option>
                                    <option value="coming_soon" <?= $a['status'] === 'coming_soon' ? 'selected' : '' ?>>coming_soon</option>
                                    <option value="archived"    <?= $a['status'] === 'archived' ? 'selected' : '' ?>>archived</option>
                                </select>
                                <input type="number" name="sort_order" class="form-input form-input--inline" value="<?= (int) $a['sort_order'] ?>" style="width:64px;">
                                <button type="submit" class="btn btn-sm">Save</button>
                            </form>
                        </td>
                        <td class="muted mono"><?= (int) $a['sort_order'] ?></td>
                        <td class="num mono"><?= (int) $a['waitlist_count'] ?></td>
                        <td class="muted">
                            <?php if (!empty($a['cockpit_image_path'])): ?>
                                <span class="badge badge--success">SET</span>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><a href="/aircraft/<?= htmlspecialchars((string) $a['slug']) ?>" class="btn btn-sm btn-ghost">View →</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
