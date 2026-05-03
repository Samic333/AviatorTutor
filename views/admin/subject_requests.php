<?php
declare(strict_types=1);
/** @var array $requests */
/** @var array $byStatus */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');

$pillStyle = static function (string $status): string {
    return match ($status) {
        'pending'   => 'background:rgba(245,158,11,0.18);color:#FBBF24;',
        'quoted'    => 'background:rgba(56,189,248,0.18);color:#7DD3FC;',
        'paid'      => 'background:rgba(16,185,129,0.18);color:#A7F3D0;',
        'declined'  => 'background:rgba(239,68,68,0.18);color:#FCA5A5;',
        'cancelled' => 'background:rgba(148,163,184,0.18);color:#CBD5E1;',
        default     => 'background:rgba(148,163,184,0.18);color:#CBD5E1;',
    };
};
?>
<style>
.sr-summary { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
.sr-summary-pill { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.sr-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.sr-table th, .sr-table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); vertical-align: top; }
.sr-table th { font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8; font-weight: 600; }
.sr-pill { display: inline-block; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; }
.sr-actions { display: flex; flex-direction: column; gap: 6px; align-items: stretch; min-width: 220px; }
.sr-actions form { display: flex; gap: 6px; }
.sr-actions input[type="text"], .sr-actions input[type="number"], .sr-actions textarea {
    flex: 1; padding: 6px 10px; border-radius: 6px; font-size: 12.5px;
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.10); color: #F1F5F9;
}
.sr-actions textarea { resize: vertical; min-height: 32px; }
.sr-actions .btn { padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; border: 0; min-width: 70px; }
.sr-btn-quote   { background: #38BDF8; color: #0F172A; }
.sr-btn-grant   { background: #10B981; color: #0F172A; }
.sr-btn-decline { background: rgba(239,68,68,0.2); color: #FCA5A5; }
.sr-btn-reopen  { background: rgba(148,163,184,0.18); color: #CBD5E1; }
.sr-empty { padding: 28px; text-align: center; color: #94A3B8; border: 1px dashed rgba(255,255,255,0.12); border-radius: 12px; }
</style>

<h1 style="margin:0 0 12px;">Subject requests</h1>

<?php if ($flashOk): ?><div class="plt-flash plt-flash--ok" style="margin-bottom:12px;"><?= $h($flashOk) ?></div><?php endif; ?>
<?php if ($flashError): ?><div class="plt-flash plt-flash--error" style="margin-bottom:12px;"><?= $h($flashError) ?></div><?php endif; ?>

<div class="sr-summary">
  <?php foreach ($byStatus as $status => $n): if ($n === 0) continue; ?>
    <span class="sr-summary-pill" style="<?= $pillStyle($status) ?>">
      <?= $h(ucfirst($status)) ?>: <?= (int)$n ?>
    </span>
  <?php endforeach; ?>
</div>

<?php if (empty($requests)): ?>
  <p class="sr-empty">No subject requests yet. They show up when a learner taps "Request access" on My Subjects.</p>
<?php else: ?>
<table class="sr-table">
  <thead>
    <tr>
      <th>Created</th>
      <th>Pilot</th>
      <th>Subject</th>
      <th>Notes</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($requests as $r):
      $status   = (string) ($r['status'] ?? 'pending');
      $isCustom = empty($r['subject_slug']);
    ?>
      <tr>
        <td><?= $h((string)($r['created_at'] ?? '')) ?></td>
        <td>
          <strong><?= $h((string)($r['user_name'] ?? '—')) ?></strong><br>
          <span style="color:#94A3B8;font-size:12px;"><?= $h((string)($r['user_email'] ?? '')) ?></span>
        </td>
        <td>
          <strong><?= $h((string)$r['requested_subject']) ?></strong><br>
          <?php if (!$isCustom): ?>
            <code style="font-size:11.5px;color:#94A3B8;"><?= $h((string)$r['subject_slug']) ?></code>
          <?php else: ?>
            <span style="color:#FBBF24;font-size:11.5px;">Custom — create catalog entry first</span>
          <?php endif; ?>
        </td>
        <td style="max-width:240px;color:#94A3B8;font-size:12.5px;line-height:1.5;">
          <?php if (!empty($r['notes'])): ?>
            <div><strong style="color:#E2E8F0;">From pilot:</strong> <?= nl2br($h((string)$r['notes'])) ?></div>
          <?php endif; ?>
          <?php if (!empty($r['admin_notes'])): ?>
            <div style="margin-top:4px;"><strong style="color:#E2E8F0;">Admin:</strong> <?= nl2br($h((string)$r['admin_notes'])) ?></div>
          <?php endif; ?>
          <?php if (!empty($r['quoted_amount_usd'])): ?>
            <div style="margin-top:4px;color:#7DD3FC;">Quoted: $<?= number_format((float)$r['quoted_amount_usd'], 2) ?></div>
          <?php endif; ?>
        </td>
        <td><span class="sr-pill" style="<?= $pillStyle($status) ?>"><?= $h(ucfirst($status)) ?></span></td>
        <td class="sr-actions">

          <?php if (in_array($status, ['pending','quoted'], true)): ?>
            <form method="post" action="/admin/subject-requests/update">
              <input type="hidden" name="csrf_token" value="<?= $h($csrf_token) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="quote">
              <input type="number" name="quoted_amount_usd" step="0.01" placeholder="USD" value="<?= $h((string)($r['quoted_amount_usd'] ?? '')) ?>" style="max-width:90px;">
              <button class="btn sr-btn-quote" type="submit">Save quote</button>
            </form>
          <?php endif; ?>

          <?php if (in_array($status, ['pending','quoted'], true) && !$isCustom): ?>
            <form method="post" action="/admin/subject-requests/update" onsubmit="return confirm('Grant <?= $h((string)$r['requested_subject']) ?> to <?= $h((string)($r['user_email'] ?? '')) ?>?');">
              <input type="hidden" name="csrf_token" value="<?= $h($csrf_token) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="grant">
              <button class="btn sr-btn-grant" type="submit" style="width:100%;">Grant access</button>
            </form>
          <?php endif; ?>

          <?php if (in_array($status, ['pending','quoted'], true)): ?>
            <form method="post" action="/admin/subject-requests/update">
              <input type="hidden" name="csrf_token" value="<?= $h($csrf_token) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="decline">
              <button class="btn sr-btn-decline" type="submit" style="width:100%;">Decline</button>
            </form>
          <?php elseif (in_array($status, ['declined','cancelled'], true)): ?>
            <form method="post" action="/admin/subject-requests/update">
              <input type="hidden" name="csrf_token" value="<?= $h($csrf_token) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="reopen">
              <button class="btn sr-btn-reopen" type="submit" style="width:100%;">Reopen</button>
            </form>
          <?php endif; ?>

        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
