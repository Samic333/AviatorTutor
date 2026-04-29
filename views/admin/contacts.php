<?php
declare(strict_types=1);
/** @var array $messages */
/** @var string $statusFilter */
/** @var array $statusCounts */
/** @var int $unreadCount */
/** @var bool $tableExists */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */

$statusBadge = function (string $s): string {
    $map = [
        'new'      => ['#38BDF8', 'rgba(56,189,248,0.14)', 'New'],
        'read'     => ['#94A3B8', 'rgba(148,163,184,0.14)', 'Read'],
        'replied'  => ['#22C55E', 'rgba(34,197,94,0.14)',   'Replied'],
        'archived' => ['#64748B', 'rgba(100,116,139,0.14)', 'Archived'],
    ];
    [$fg, $bg, $label] = $map[$s] ?? $map['read'];
    return '<span style="display:inline-block;padding:2px 9px;border-radius:6px;font-size:11px;font-weight:600;color:' . $fg . ';background:' . $bg . ';">' . $label . '</span>';
};
?>

<?php if ($flashOk): ?><div class="adm-flash adm-flash--ok"><?= htmlspecialchars($flashOk) ?></div><?php endif; ?>
<?php if ($flashError): ?><div class="adm-flash adm-flash--error"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Inquiries</h1>
    <p class="adm-page-header__sub">
      <?php if ($unreadCount > 0): ?>
        <strong style="color:var(--adm-gold);"><?= $unreadCount ?> new</strong> message<?= $unreadCount === 1 ? '' : 's' ?> · contact-form messages from the public site.
      <?php else: ?>
        Contact-form messages from the public site.
      <?php endif; ?>
    </p>
  </div>
</div>

<?php if (!$tableExists): ?>
  <div class="adm-panel" style="max-width:640px;">
    <div class="adm-panel__body" style="padding:32px;text-align:center;">
      <p class="adm-muted" style="margin:0 0 16px;">The <code>contact_messages</code> table doesn't exist yet. Run the Phase&nbsp;1 migration to enable the admin inbox.</p>
      <div style="padding:12px;background:var(--adm-gold-soft);border:1px solid var(--adm-gold-dim);border-radius:var(--adm-radius);text-align:left;">
        <code style="font-size:12px;color:var(--adm-text);">database/migrations/2026_04_29_contact_messages.sql</code>
      </div>
    </div>
  </div>
<?php else: ?>

  <!-- Status filter chips -->
  <div style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;">
    <?php
      $chip = function (string $key, string $label, int $count) use ($statusFilter): string {
          $active = $statusFilter === $key;
          $bg = $active ? 'var(--adm-gold)' : 'transparent';
          $color = $active ? '#0A0A0A' : 'var(--adm-text-muted)';
          $border = $active ? 'var(--adm-gold)' : 'var(--adm-border)';
          return '<a href="/admin/contacts?status=' . $key . '" style="padding:6px 14px;font-size:12.5px;font-weight:600;border-radius:999px;border:1px solid ' . $border . ';color:' . $color . ';background:' . $bg . ';text-decoration:none;">' . htmlspecialchars($label) . ' <span style="opacity:.7;">' . $count . '</span></a>';
      };
      echo $chip('all', 'All', array_sum($statusCounts));
      echo $chip('new', 'New', (int) $statusCounts['new']);
      echo $chip('read', 'Read', (int) $statusCounts['read']);
      echo $chip('replied', 'Replied', (int) $statusCounts['replied']);
      echo $chip('archived', 'Archived', (int) $statusCounts['archived']);
    ?>
  </div>

  <div class="adm-panel">
    <div class="adm-panel__body" style="padding:0;">
      <?php if (empty($messages)): ?>
        <div style="padding:40px;text-align:center;color:var(--adm-text-muted);font-size:14px;">No messages in this view.</div>
      <?php else: ?>
        <div class="adm-table-wrap">
          <table class="adm-table">
            <thead>
              <tr>
                <th>Status</th>
                <th>From</th>
                <th>Subject &amp; preview</th>
                <th>When</th>
                <th style="width:80px;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($messages as $m): ?>
                <tr<?= $m['status'] === 'new' ? ' style="background:rgba(201,168,76,0.04);"' : '' ?>>
                  <td><?= $statusBadge((string) $m['status']) ?></td>
                  <td>
                    <strong style="color:var(--adm-text);"><?= htmlspecialchars((string) $m['name']) ?></strong>
                    <div class="adm-muted adm-mono" style="font-size:11px;"><?= htmlspecialchars((string) $m['email']) ?></div>
                  </td>
                  <td style="max-width:480px;">
                    <strong style="color:var(--adm-text);font-size:13.5px;display:block;margin-bottom:2px;"><?= htmlspecialchars((string) ($m['subject'] ?? 'General enquiry')) ?></strong>
                    <span style="display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;color:var(--adm-text-muted);font-size:12.5px;line-height:1.4;overflow:hidden;text-overflow:ellipsis;">
                      <?= htmlspecialchars((string) $m['message']) ?>
                    </span>
                  </td>
                  <td class="adm-muted adm-mono" style="font-size:11.5px;white-space:nowrap;">
                    <?= htmlspecialchars(date('M j · g:ia', strtotime((string) $m['created_at']))) ?>
                  </td>
                  <td><a href="/admin/contacts/<?= (int) $m['id'] ?>" class="adm-btn adm-btn--ghost adm-btn--sm">View</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

<?php endif; ?>
