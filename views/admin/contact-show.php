<?php
declare(strict_types=1);
/** @var array $msg */
/** @var array $replies */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */

$status   = (string) ($msg['status'] ?? 'new');
$subject  = (string) ($msg['subject'] ?? 'General enquiry');
$replies  = $replies ?? [];
?>

<?php if ($flashOk): ?><div class="adm-flash adm-flash--ok"><?= htmlspecialchars($flashOk) ?></div><?php endif; ?>
<?php if ($flashError): ?><div class="adm-flash adm-flash--error"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title"><?= htmlspecialchars($subject) ?></h1>
    <p class="adm-page-header__sub">
      From <strong><?= htmlspecialchars((string) $msg['name']) ?></strong>
      &middot; received <?= htmlspecialchars(date('M j Y · g:ia', strtotime((string) $msg['created_at']))) ?>
      <?php if (!empty($msg['user_id'])): ?>
        &middot; sent while signed in as <strong><?= htmlspecialchars((string) ($msg['user_email'] ?? '')) ?></strong>
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
      <div style="margin-bottom:16px;display:flex;gap:14px;flex-wrap:wrap;align-items:center;font-size:13px;color:var(--adm-text-muted);">
        <span><strong style="color:var(--adm-text);">From:</strong> <?= htmlspecialchars((string) $msg['name']) ?> &lt;<span class="adm-mono" style="color:var(--adm-text);"><?= htmlspecialchars((string) $msg['email']) ?></span>&gt;</span>
        <?php if (!empty($msg['ip'])): ?><span><strong style="color:var(--adm-text);">IP:</strong> <span class="adm-mono"><?= htmlspecialchars((string) $msg['ip']) ?></span></span><?php endif; ?>
        <button type="button"
                onclick="navigator.clipboard.writeText('<?= htmlspecialchars((string) $msg['email'], ENT_QUOTES) ?>');this.textContent='Copied';setTimeout(()=>{this.textContent='Copy email';},1500);"
                class="adm-btn adm-btn--ghost adm-btn--sm" style="margin-left:auto;">Copy email</button>
      </div>
      <div style="white-space:pre-wrap;font-family:Inter, sans-serif;font-size:14px;line-height:1.65;color:var(--adm-text);background:rgba(255,255,255,0.02);padding:18px;border-radius:var(--adm-radius);border:1px solid var(--adm-border);">
        <?= htmlspecialchars((string) $msg['message']) ?>
      </div>

      <!-- Reply thread (Phase 5) -->
      <?php if (!empty($replies)): ?>
        <div style="margin-top:24px;">
          <h3 style="margin:0 0 12px;font-size:13px;text-transform:uppercase;letter-spacing:0.06em;color:var(--adm-text-muted);">Reply thread &mdash; <?= count($replies) ?> message<?= count($replies) === 1 ? '' : 's' ?></h3>
          <?php foreach ($replies as $r): ?>
            <article style="background:rgba(56,189,248,0.05);border:1px solid rgba(56,189,248,0.18);border-left:3px solid #38BDF8;border-radius:var(--adm-radius);padding:14px 16px;margin-bottom:12px;">
              <header style="display:flex;justify-content:space-between;font-size:12px;color:var(--adm-text-muted);margin-bottom:8px;flex-wrap:wrap;gap:8px;">
                <span><strong style="color:var(--adm-text);"><?= htmlspecialchars((string) $r['admin_name']) ?></strong> &lt;<?= htmlspecialchars((string) $r['admin_email']) ?>&gt;</span>
                <span><?= htmlspecialchars(date('M j Y · g:ia', strtotime((string) $r['sent_at']))) ?>
                <?php if (($r['mail_status'] ?? '') === 'sent'): ?>
                  &middot; <span style="color:#22C55E;font-weight:600;">delivered</span>
                <?php elseif (($r['mail_status'] ?? '') === 'failed'): ?>
                  &middot; <span style="color:#FCA5A5;font-weight:600;" title="<?= htmlspecialchars((string) ($r['error'] ?? '')) ?>">mail() failed</span>
                <?php else: ?>
                  &middot; <span style="color:var(--adm-text-muted);"><?= htmlspecialchars((string) $r['mail_status']) ?></span>
                <?php endif; ?>
                </span>
              </header>
              <div style="white-space:pre-wrap;font-size:13.5px;line-height:1.6;color:var(--adm-text);">
                <?= htmlspecialchars((string) $r['body']) ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Reply composer -->
      <form method="post" action="/admin/contacts/<?= (int) $msg['id'] ?>/reply" style="margin-top:24px;padding-top:20px;border-top:1px solid var(--adm-border);">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <label class="adm-label" for="reply_body" style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
          <span>Reply to <?= htmlspecialchars((string) $msg['name']) ?></span>
          <span style="font-size:11px;color:var(--adm-text-muted);">Sent via SMTP &mdash; logged to mail.log</span>
        </label>
        <textarea class="adm-input" id="reply_body" name="body" rows="6"
                  placeholder="Hi <?= htmlspecialchars((string) $msg['name'], ENT_QUOTES) ?>, thanks for reaching out..." minlength="10" required></textarea>
        <div style="display:flex;justify-content:flex-end;margin-top:10px;">
          <button type="submit" class="adm-btn adm-btn--primary">Send reply</button>
        </div>
      </form>
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
