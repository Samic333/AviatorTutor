<?php
declare(strict_types=1);
/** @var array $leads */
/** @var array $byModule */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Lead Signups</h1>
    <p class="adm-page-header__sub"><?= count($leads) ?> total — captured from coming-soon waitlist forms.</p>
  </div>
</div>

<div class="adm-grid-2">
  <div class="adm-panel">
    <div class="adm-panel__header">
      <h2 class="adm-panel__title">All Signups (newest first)</h2>
    </div>
    <?php if (empty($leads)): ?>
      <div class="adm-empty">No signups yet.</div>
    <?php else: ?>
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr><th>Email</th><th>Module</th><th>IP</th><th>When</th></tr>
          </thead>
          <tbody>
            <?php foreach ($leads as $l): ?>
              <tr>
                <td><a href="mailto:<?= htmlspecialchars((string)$l['email']) ?>"><?= htmlspecialchars((string)$l['email']) ?></a></td>
                <td class="adm-mono"><?= htmlspecialchars((string)($l['requested_module_slug'] ?? '?')) ?></td>
                <td class="adm-muted adm-mono"><?= htmlspecialchars((string)($l['ip'] ?? '—')) ?></td>
                <td class="adm-muted"><?= htmlspecialchars(date('M j Y H:i', strtotime((string)$l['created_at']))) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <div class="adm-panel">
    <div class="adm-panel__header">
      <h2 class="adm-panel__title">By Module</h2>
    </div>
    <div class="adm-panel__body">
      <?php if (empty($byModule)): ?>
        <p class="adm-muted">No signups yet.</p>
      <?php else: ?>
        <ul class="adm-list">
          <?php foreach ($byModule as $m): ?>
            <li>
              <span class="adm-list__primary adm-mono"><?= htmlspecialchars((string)($m['slug'] ?? '?')) ?></span>
              <span class="adm-list__meta adm-mono"><?= (int)$m['n'] ?> signup<?= (int)$m['n'] === 1 ? '' : 's' ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
