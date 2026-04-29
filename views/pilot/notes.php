<?php
declare(strict_types=1);
/** @var array $bySystem */
/** @var int $totalNotes */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */
?>

<?php if ($flashOk): ?>
  <div class="plt-flash plt-flash--ok" style="margin:0 0 20px;"><?= htmlspecialchars($flashOk) ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
  <div class="plt-flash plt-flash--error" style="margin:0 0 20px;"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<div class="plt-page-header">
  <div>
    <h1 class="plt-page-header__title">My Notes</h1>
    <p class="plt-page-header__sub">
      <?php if ($totalNotes === 0): ?>
        No notes yet — open a system and use the Notes panel to start capturing what matters.
      <?php else: ?>
        <?= $totalNotes ?> note<?= $totalNotes === 1 ? '' : 's' ?> across <?= count($bySystem) ?> system<?= count($bySystem) === 1 ? '' : 's' ?>.
      <?php endif; ?>
    </p>
  </div>
</div>

<?php if (empty($bySystem)): ?>
  <div class="plt-glass-card" style="padding:48px;text-align:center;max-width:520px;margin:0 auto;">
    <div style="width:64px;height:64px;border-radius:14px;background:rgba(56,189,248,0.12);color:var(--plt-sky);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/>
      </svg>
    </div>
    <h2 style="margin:0 0 10px;font-family:var(--plt-font-head);font-size:18px;font-weight:700;color:var(--plt-text);">No notes yet</h2>
    <p style="margin:0 0 24px;color:var(--plt-text-muted);font-size:14px;line-height:1.55;">Open any aircraft system, scroll to the Notes panel on the right, and write what you want to remember. Everything saved there shows up on this page.</p>
    <a href="/systems" class="plt-btn plt-btn--primary">Browse systems</a>
  </div>
<?php else: ?>
  <div style="display:flex;flex-direction:column;gap:20px;">
    <?php foreach ($bySystem as $group): ?>
      <section class="plt-glass-card" style="padding:24px;">
        <div class="plt-section-header">
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:32px;height:32px;border-radius:8px;background:<?= htmlspecialchars((string)$group['system_color']) ?>22;color:<?= htmlspecialchars((string)$group['system_color']) ?>;display:flex;align-items:center;justify-content:center;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <h2 class="plt-section-header__title"><?= htmlspecialchars((string)$group['system_name']) ?></h2>
            <span class="plt-mono" style="font-size:11px;color:var(--plt-text-muted);">(<?= count($group['notes']) ?>)</span>
          </div>
          <?php if (!empty($group['system_id'])): ?>
            <a href="/systems/<?= (int)$group['system_id'] ?>" class="plt-section-header__link">Open system →</a>
          <?php endif; ?>
        </div>

        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php foreach ($group['notes'] as $n): ?>
            <article style="padding:14px;background:rgba(255,255,255,0.025);border:1px solid var(--plt-glass-border);border-radius:10px;">
              <?php if (!empty($n['lesson_title'])): ?>
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:var(--plt-sky);margin-bottom:6px;">
                  <?= htmlspecialchars((string)$n['lesson_title']) ?>
                </div>
              <?php endif; ?>
              <div style="white-space:pre-wrap;font-size:13.5px;color:var(--plt-text);line-height:1.6;"><?= htmlspecialchars((string)$n['content']) ?></div>
              <div style="margin-top:10px;display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--plt-text-muted);">
                <span>Updated <?= htmlspecialchars(date('M j, Y g:ia', strtotime((string)$n['updated_at']))) ?></span>
                <form method="post" action="/api/notes/delete" style="margin:0;" onsubmit="return confirm('Delete this note?');">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                  <input type="hidden" name="note_id" value="<?= (int)$n['id'] ?>">
                  <button type="submit" style="background:transparent;border:0;color:var(--plt-danger);font-size:11px;cursor:pointer;padding:0;">Delete</button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
