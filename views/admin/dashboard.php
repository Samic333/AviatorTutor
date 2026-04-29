<?php
declare(strict_types=1);
/** @var array $metrics */
$m = $metrics;
?>

<!-- Stats -->
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Platform Overview</h1>
    <p class="adm-page-header__sub">Live metrics across users, subscriptions, content and the aircraft catalog.</p>
  </div>
  <div class="adm-page-header__actions">
    <a href="/admin/codes" class="adm-btn adm-btn--ghost adm-btn--sm">Generate Codes</a>
  </div>
</div>

<div class="adm-stats">
  <div class="adm-stat">
    <div class="adm-stat__icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <span class="adm-stat__label">Total Users</span>
    <span class="adm-stat__value adm-mono"><?= number_format($m['user_count']) ?></span>
    <span class="adm-stat__hint"><?= (int)$m['admin_count'] ?> admin · <?= max(0, $m['user_count'] - $m['admin_count']) ?> learners</span>
  </div>
  <div class="adm-stat">
    <div class="adm-stat__icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    </div>
    <span class="adm-stat__label">Active Subs</span>
    <span class="adm-stat__value adm-mono"><?= number_format($m['subs_active']) ?></span>
    <span class="adm-stat__hint"><?= number_format($m['subs_expired']) ?> expired</span>
  </div>
  <div class="adm-stat">
    <div class="adm-stat__icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    </div>
    <span class="adm-stat__label">Codes Ready</span>
    <span class="adm-stat__value adm-mono"><?= number_format($m['codes_unused']) ?></span>
    <span class="adm-stat__hint"><?= (int)$m['codes_redeemed_24h'] ?> redeemed in 24h</span>
  </div>
  <div class="adm-stat">
    <div class="adm-stat__icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="adm-stat__label">Lead Signups</span>
    <span class="adm-stat__value adm-mono"><?= number_format($m['leads_total']) ?></span>
    <span class="adm-stat__hint">Waitlist registrations</span>
  </div>
  <div class="adm-stat">
    <div class="adm-stat__icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="adm-stat__label">Sessions Today</span>
    <span class="adm-stat__value adm-mono"><?= number_format($m['sessions_today']) ?></span>
    <span class="adm-stat__hint">14d total: <?= (int)array_sum(array_column($m['sparkline'] ?? [], 'c')) ?></span>
  </div>
  <div class="adm-stat">
    <div class="adm-stat__icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <span class="adm-stat__label">Content</span>
    <span class="adm-stat__value adm-mono"><?= number_format($m['system_count']) ?></span>
    <span class="adm-stat__hint"><?= (int)$m['lesson_count'] ?> lessons · <?= (int)$m['flashcard_count'] ?> cards</span>
  </div>
</div>

<div class="adm-grid-2">
  <!-- Recent Activity -->
  <div class="adm-panel">
    <div class="adm-panel__header">
      <h2 class="adm-panel__title">Recent Activity</h2>
      <a href="/admin/users" class="adm-btn adm-btn--ghost adm-btn--sm">All users</a>
    </div>
    <div class="adm-panel__body">
      <p class="adm-panel__sub">Newest accounts</p>
      <?php if (empty($m['recent_users'])): ?>
        <p class="adm-muted">No users yet.</p>
      <?php else: ?>
        <ul class="adm-list">
          <?php foreach ($m['recent_users'] as $u): ?>
            <li>
              <span class="adm-list__primary"><?= htmlspecialchars((string)$u['name']) ?></span>
              <span class="adm-list__secondary"><?= htmlspecialchars((string)$u['email']) ?></span>
              <span class="adm-list__meta"><?= htmlspecialchars(date('M j', strtotime((string)$u['created_at']))) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <p class="adm-panel__sub">Recent code redeems</p>
      <?php if (empty($m['recent_redeems'])): ?>
        <p class="adm-muted">No redeems yet.</p>
      <?php else: ?>
        <ul class="adm-list">
          <?php foreach ($m['recent_redeems'] as $r): ?>
            <li>
              <span class="adm-list__primary adm-mono"><?= htmlspecialchars((string)$r['code']) ?></span>
              <span class="adm-list__secondary"><?= htmlspecialchars((string)($r['email'] ?? '?')) ?></span>
              <span class="adm-list__meta"><?= htmlspecialchars(date('M j H:i', strtotime((string)$r['redeemed_at']))) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <p class="adm-panel__sub">Recent leads</p>
      <?php if (empty($m['recent_leads'])): ?>
        <p class="adm-muted">No leads yet.</p>
      <?php else: ?>
        <ul class="adm-list">
          <?php foreach ($m['recent_leads'] as $l): ?>
            <li>
              <span class="adm-list__primary"><?= htmlspecialchars((string)$l['email']) ?></span>
              <span class="adm-list__secondary">→ <?= htmlspecialchars((string)($l['slug'] ?? '?')) ?></span>
              <span class="adm-list__meta"><?= htmlspecialchars(date('M j', strtotime((string)$l['created_at']))) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <!-- Aircraft Catalog -->
  <div class="adm-panel">
    <div class="adm-panel__header">
      <h2 class="adm-panel__title">Aircraft Catalog</h2>
      <a href="/admin/aircrafts" class="adm-btn adm-btn--ghost adm-btn--sm">Manage</a>
    </div>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Aircraft</th>
            <th>Status</th>
            <th class="num">Waitlist</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($m['aircraft_stats'] ?? []) as $a): ?>
            <tr>
              <td><a href="/aircraft/<?= htmlspecialchars($a['slug']) ?>"><?= htmlspecialchars($a['short_name']) ?></a></td>
              <td>
                <?php $st = $a['status']; ?>
                <span class="adm-badge adm-badge--<?= $st === 'live' ? 'success' : ($st === 'beta' ? 'gold' : 'muted') ?>">
                  <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $st))) ?>
                </span>
              </td>
              <td class="num adm-mono"><?= (int)$a['waitlist_count'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if (!empty($m['leads_by_module'])): ?>
      <div class="adm-panel__body">
        <p class="adm-panel__sub">Top waitlist modules</p>
        <ul class="adm-list">
          <?php foreach ($m['leads_by_module'] as $row): ?>
            <li>
              <span class="adm-list__primary"><?= htmlspecialchars((string)($row['slug'] ?? '?')) ?></span>
              <span class="adm-list__meta adm-mono"><?= (int)$row['n'] ?> signup<?= ((int)$row['n']) === 1 ? '' : 's' ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</div>
