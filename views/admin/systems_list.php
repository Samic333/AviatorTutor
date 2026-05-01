<?php
declare(strict_types=1);
/**
 * Admin Systems list — manage the target-system catalog.
 *
 * @var string $csrf_token
 * @var array  $systems
 * @var string $flash_ok
 * @var string $flash_error
 */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Aircraft Systems</h1>
    <p class="adm-page-header__sub">Manage the catalog of target systems used by /admin/import and the learner-side study tree.</p>
  </div>
  <div>
    <a href="/admin/systems/new" class="adm-btn adm-btn--primary">+ New system</a>
  </div>
</div>

<?php if (!empty($flash_ok)): ?>
  <div class="adm-panel" style="max-width:1100px; border:1px solid #10b981; background:#10b98111;">
    <div class="adm-panel__body" style="color:#10b981;">✓ <?= htmlspecialchars($flash_ok) ?></div>
  </div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
  <div class="adm-panel" style="max-width:1100px; border:1px solid #ef4444; background:#ef444411;">
    <div class="adm-panel__body" style="color:#ef4444;">✗ <?= htmlspecialchars($flash_error) ?></div>
  </div>
<?php endif; ?>

<div class="adm-panel" style="max-width:1100px;">
  <div class="adm-panel__body" style="padding:0;">
    <?php if (empty($systems)): ?>
      <div style="padding:32px; text-align:center;">
        <p style="margin:0 0 12px; color:#9ca3af;">No systems yet.</p>
        <a href="/admin/systems/new" class="adm-btn adm-btn--primary">+ Create your first system</a>
      </div>
    <?php else: ?>
      <table class="adm-table" style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left; padding:10px 16px;">#</th>
            <th style="text-align:left; padding:10px 16px;">System</th>
            <th style="text-align:left; padding:10px 16px;">ATA</th>
            <th style="text-align:left; padding:10px 16px;">Lessons</th>
            <th style="text-align:left; padding:10px 16px;">Flashcards</th>
            <th style="text-align:left; padding:10px 16px;">Unlock after</th>
            <th style="text-align:left; padding:10px 16px;">Status</th>
            <th style="text-align:left; padding:10px 16px;">Sort</th>
            <th style="text-align:right; padding:10px 16px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($systems as $s): ?>
          <tr style="border-top:1px solid #2a2a2a;">
            <td style="padding:10px 16px;"><?= (int)$s['id'] ?></td>
            <td style="padding:10px 16px;">
              <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:<?= htmlspecialchars($s['color_hex'] ?? '#34d399') ?>; margin-right:8px;"></span>
              <strong><?= htmlspecialchars($s['name']) ?></strong>
            </td>
            <td style="padding:10px 16px;"><code><?= htmlspecialchars($s['ata_code']) ?></code></td>
            <td style="padding:10px 16px;"><?= (int) ($s['lesson_count'] ?? 0) ?></td>
            <td style="padding:10px 16px;"><?= (int) ($s['flashcard_count'] ?? 0) ?></td>
            <td style="padding:10px 16px; font-size:12px; color:#9ca3af;">
              <?= htmlspecialchars((string) ($s['unlock_after_name'] ?? '—')) ?>
            </td>
            <td style="padding:10px 16px;">
              <?php if ((int) $s['is_published'] === 1): ?>
                <span style="color:#10b981;">published</span>
              <?php else: ?>
                <span style="color:#9ca3af;">draft</span>
              <?php endif; ?>
            </td>
            <td style="padding:10px 16px;"><?= (int) $s['sort_order'] ?></td>
            <td style="padding:10px 16px; text-align:right; white-space:nowrap;">
              <a href="/admin/systems/<?= (int)$s['id'] ?>/edit" class="adm-btn adm-btn--ghost" style="padding:4px 10px; font-size:12px;">Edit</a>
              <form method="POST" action="/admin/systems/<?= (int)$s['id'] ?>/delete" style="display:inline; margin:0;"
                    onsubmit="return confirm('Delete system \'<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>\' and ALL its lessons, slides, flashcards, and quizzes? This cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <button type="submit" class="adm-btn adm-btn--ghost" style="padding:4px 10px; font-size:12px; color:#ef4444; border-color:#ef4444;">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
