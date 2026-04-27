<?php
declare(strict_types=1);
/** @var array $leads */
/** @var array $byModule */
?>
<div class="admin-page">
    <header class="admin-page__head">
        <div>
            <h1>Lead signups</h1>
            <p class="muted" style="margin:6px 0 0;"><?= count($leads) ?> total. Captured from /aircraft/{slug} and /coming-soon/{slug} forms.</p>
        </div>
        <div class="admin-page__actions">
            <a href="/admin" class="btn btn-sm btn-ghost">← Dashboard</a>
        </div>
    </header>

    <section class="admin-grid">
        <div class="admin-panel">
            <h2 class="admin-panel__title">All signups (newest first)</h2>
            <?php if (empty($leads)): ?>
                <p class="muted">No signups yet.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr><th>Email</th><th>Module</th><th>IP</th><th>When</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $l): ?>
                            <tr>
                                <td><a href="mailto:<?= htmlspecialchars((string) $l['email']) ?>"><?= htmlspecialchars((string) $l['email']) ?></a></td>
                                <td class="mono"><?= htmlspecialchars((string) ($l['requested_module_slug'] ?? '?')) ?></td>
                                <td class="muted mono"><?= htmlspecialchars((string) ($l['ip'] ?? '—')) ?></td>
                                <td class="muted"><?= htmlspecialchars(date('M j Y H:i', strtotime((string) $l['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="admin-panel">
            <h2 class="admin-panel__title">By module</h2>
            <?php if (empty($byModule)): ?>
                <p class="muted">No signups yet.</p>
            <?php else: ?>
                <ul class="admin-list">
                    <?php foreach ($byModule as $m): ?>
                        <li>
                            <span class="admin-list__primary mono"><?= htmlspecialchars((string) ($m['slug'] ?? '?')) ?></span>
                            <span class="admin-list__meta mono"><?= (int) $m['n'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</div>
