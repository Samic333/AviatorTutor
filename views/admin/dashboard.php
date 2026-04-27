<?php
declare(strict_types=1);
/** @var array $metrics */
$m = $metrics;
?>
<div class="admin-page">
    <header class="admin-page__head">
        <div>
            <h1>Platform admin</h1>
            <p class="muted" style="margin:6px 0 0;">Live metrics across users, subscriptions, content and the aircraft catalog.</p>
        </div>
        <div class="admin-page__actions">
            <a href="/admin/codes" class="btn btn-sm">Codes</a>
            <a href="/admin/users" class="btn btn-sm">Users</a>
            <a href="/admin/leads" class="btn btn-sm">Leads</a>
            <a href="/admin/aircrafts" class="btn btn-sm">Aircraft</a>
            <a href="/" class="btn btn-sm btn-ghost">View public site</a>
        </div>
    </header>

    <section class="admin-stats">
        <div class="stat-card">
            <span class="stat-card__label">Users</span>
            <span class="stat-card__value mono"><?= number_format($m['user_count']) ?></span>
            <span class="stat-card__hint"><?= (int) $m['admin_count'] ?> admin · <?= max(0, $m['user_count'] - $m['admin_count']) ?> learners</span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Active subs</span>
            <span class="stat-card__value mono"><?= number_format($m['subs_active']) ?></span>
            <span class="stat-card__hint"><?= number_format($m['subs_expired']) ?> expired</span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Codes ready</span>
            <span class="stat-card__value mono"><?= number_format($m['codes_unused']) ?></span>
            <span class="stat-card__hint"><?= (int) $m['codes_redeemed_24h'] ?> redeemed in 24h</span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Lead signups</span>
            <span class="stat-card__value mono"><?= number_format($m['leads_total']) ?></span>
            <span class="stat-card__hint">Coming-soon waitlists</span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Sessions today</span>
            <span class="stat-card__value mono"><?= number_format($m['sessions_today']) ?></span>
            <span class="stat-card__hint">Last 14d:
                <?= (int) array_sum(array_column($m['sparkline'] ?? [], 'c')) ?> total
            </span>
        </div>
        <div class="stat-card">
            <span class="stat-card__label">Content</span>
            <span class="stat-card__value mono"><?= number_format($m['system_count']) ?></span>
            <span class="stat-card__hint"><?= (int) $m['lesson_count'] ?> lessons · <?= (int) $m['flashcard_count'] ?> cards · <?= (int) $m['quiz_count'] ?> quizzes</span>
        </div>
    </section>

    <section class="admin-grid">
        <div class="admin-panel">
            <h2 class="admin-panel__title">Recent activity</h2>

            <h3 class="admin-panel__sub">Newest accounts</h3>
            <?php if (empty($m['recent_users'])): ?>
                <p class="muted">No users yet.</p>
            <?php else: ?>
                <ul class="admin-list">
                    <?php foreach ($m['recent_users'] as $u): ?>
                        <li>
                            <span class="admin-list__primary"><?= htmlspecialchars((string) $u['name']) ?></span>
                            <span class="admin-list__secondary"><?= htmlspecialchars((string) $u['email']) ?></span>
                            <span class="admin-list__meta"><?= htmlspecialchars((string) $u['role']) ?> · <?= htmlspecialchars(date('M j', strtotime((string) $u['created_at']))) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h3 class="admin-panel__sub">Recent code redeems</h3>
            <?php if (empty($m['recent_redeems'])): ?>
                <p class="muted">No redeems yet.</p>
            <?php else: ?>
                <ul class="admin-list">
                    <?php foreach ($m['recent_redeems'] as $r): ?>
                        <li>
                            <span class="admin-list__primary mono"><?= htmlspecialchars((string) $r['code']) ?></span>
                            <span class="admin-list__secondary"><?= htmlspecialchars((string) ($r['email'] ?? '?')) ?></span>
                            <span class="admin-list__meta"><?= htmlspecialchars(date('M j H:i', strtotime((string) $r['redeemed_at']))) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h3 class="admin-panel__sub">Recent leads</h3>
            <?php if (empty($m['recent_leads'])): ?>
                <p class="muted">No leads yet.</p>
            <?php else: ?>
                <ul class="admin-list">
                    <?php foreach ($m['recent_leads'] as $l): ?>
                        <li>
                            <span class="admin-list__primary"><?= htmlspecialchars((string) $l['email']) ?></span>
                            <span class="admin-list__secondary">→ <?= htmlspecialchars((string) ($l['slug'] ?? '?')) ?></span>
                            <span class="admin-list__meta"><?= htmlspecialchars(date('M j', strtotime((string) $l['created_at']))) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="admin-panel">
            <h2 class="admin-panel__title">Aircraft catalog</h2>
            <table class="admin-table">
                <thead>
                    <tr><th>Aircraft</th><th>Status</th><th class="num">Waitlist</th></tr>
                </thead>
                <tbody>
                    <?php foreach (($m['aircraft_stats'] ?? []) as $a): ?>
                        <tr>
                            <td><a href="/aircraft/<?= htmlspecialchars($a['slug']) ?>"><?= htmlspecialchars($a['short_name']) ?></a></td>
                            <td>
                                <?php $st = $a['status']; ?>
                                <span class="badge badge--<?= $st === 'live' ? 'success' : ($st === 'beta' ? 'accent' : 'warn') ?>">
                                    <?= htmlspecialchars(strtoupper(str_replace('_',' ', $st))) ?>
                                </span>
                            </td>
                            <td class="num mono"><?= (int) $a['waitlist_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!empty($m['leads_by_module'])): ?>
                <h3 class="admin-panel__sub">Top waitlist modules</h3>
                <ul class="admin-list">
                    <?php foreach ($m['leads_by_module'] as $row): ?>
                        <li>
                            <span class="admin-list__primary"><?= htmlspecialchars((string) ($row['slug'] ?? '?')) ?></span>
                            <span class="admin-list__meta mono"><?= (int) $row['n'] ?> signup<?= ((int) $row['n']) === 1 ? '' : 's' ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</div>
