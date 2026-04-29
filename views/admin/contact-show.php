<?php
declare(strict_types=1);
/** @var array $msg */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */

$status = (string) ($msg['status'] ?? 'new');
$mailtoSubject = rawurlencode('Re: your AviatorTutor inquiry');
$mailtoBody    = rawurlencode("Hi " . ($msg['name'] ?? '') . ",\n\nThanks for reaching out — \n\n— AviatorTutor team\n\n----\n\n" . ($msg['message'] ?? ''));
?>

<?php if ($flashOk): ?><div class="adm-flash adm-flash--ok"><?= htmlspecialchars($flashOk) ?></div><?php endif; ?>
<?php if ($flashError): ?><div class="adm-flash adm-flash--error"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Inquiry from <?= htmlspecialchars((string) $msg['name']) ?></h1>
    <p class="adm-page-header__sub">
      Received <?= htmlspecialchars(date('M j Y · g:ia', strtotime((string) $msg['created_at']))) ?>
      <?php if (!empty($msg['user_id'])): ?>
        · sent while signed in as <strong><?= htmlspecialchars((string) ($msg['user_email'] ?? '')) ?></strong>
      <?php endif; ?>
    </p>
  </div>
  <div>
    <a href="/admin/contacts" class="adm-btn adm-btn--ghost adm-btn--sm">← All inquiries</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">

  <!-- Message body -->
  <div class="adm-panel">
    <div class="adm-panel__head">
      <h2 class="adm-panel__title">Message</h2>
    </div>
    <div class="adm-panel__body">
      <div style="margin-bottom:16px;display:flex;gap:14px;flex-wrap:wrap;font-size:13px;color:var(--adm-text-muted);">
        <span><strong style="color:var(--adm-text);">From:</strong> <?= htmlspecialchars((string) $msg['name']) ?> &lt;<a href="mailto:<?= htmlspecialchars((string) $msg['email']) ?>?subject=<?= $mailtoSubject ?>&body=<?= $mailtoBody ?>" style="color:var(--adm-gold);"><?= htmlspecialchars((string) $msg['email']) ?></a>&gt;</span>
        <?php if (!empty($msg['ip'])): ?><span><strong style="color:var(--adm-text);">IP:</strong> <span class="adm-mono"><?= htmlspecialchars((string) $msg['ip']) ?></span></span><?php endif; ?>
      </div>
      <div style="white-space:pre-wrap;font-family:Inter, sans-serif;font-size:14px;line-height:1.65;color:var(--adm-text);background:rgba(255,255,255,0.02);padding:18px;border-radius:var(--adm-radius);border:1px solid var(--adm-border);">
        <?= htmlspecialchars((string) $msg['message']) ?>
      </div>
      <p style="margin:14px 0 0;font-size:11.5px;color:var(--adm-text-muted);">
        Reply by clicking the email address above — this opens your default mail client. We'll add an in-dashboard reply composer in a later phase.
      </p>
    </div>
  </div>

  <!-- Status + notes -->
  <div class="adm-panel">
    <div class="adm-panel__head">
      <h2 class="adm-panel__title">Status &amp; notes</h2>
    </div>
    <div class="adm-panel__body">
      <form method="post" action="/admin/contacts/<?= (int) $msg['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="adm-form-group">
          <label class="adm-label" for="status">Status</label>
          <select class="adm-input" name="status" id="status">
            <option value="new"      <?= $status === 'new'      ? 'selected' : '' ?>>New</option>
            <option value="read"     <?= $status === 'read'     ? 'selected' : '' ?>>Read</option>
            <option value="replied"  <?= $status === 'replied'  ? 'selected' : '' ?>>Replied</option>
            <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
          </select>
        </div>

        <div class="adm-form-group">
          <label class="adm-label" for="admin_notes">Admin notes</label>
          <textarea class="adm-input" name="admin_notes" id="admin_notes" rows="6" placeholder="Internal notes — not visible to the sender."><?= htmlspecialchars((string) ($msg['admin_notes'] ?? '')) ?></textarea>
        </div>

        <button type="submit" class="adm-btn adm-btn--primary">Save</button>
      </form>
    </div>
  </div>
</div>
