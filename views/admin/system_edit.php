<?php
declare(strict_types=1);
/**
 * Admin System edit/new form.
 *
 * @var string      $csrf_token
 * @var array|null  $system        Existing row, or null for new
 * @var array       $all_systems   For unlock-after dropdown
 */

$isNew = $system === null;
$action = $isNew ? '/admin/systems/create' : '/admin/systems/' . (int) $system['id'] . '/update';

$name        = $system['name']        ?? '';
$ataCode     = $system['ata_code']    ?? '';
$description = $system['description'] ?? '';
$colorHex    = $system['color_hex']   ?? '#34d399';
$icon        = $system['icon']        ?? 'zap';
$sortOrder   = (int) ($system['sort_order'] ?? 0);
$isPublished = (int) ($system['is_published'] ?? 1) === 1;
$unlockAfter = (int) ($system['unlock_after_system_id'] ?? 0);
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title"><?= $isNew ? 'New system' : 'Edit ' . htmlspecialchars($name) ?></h1>
    <p class="adm-page-header__sub">A system is one ATA chapter (Hydraulic Power, Fuel, etc.) — the unit lessons, flashcards, and quizzes attach to.</p>
  </div>
  <div>
    <a href="/admin/systems" class="adm-btn adm-btn--ghost">← All systems</a>
  </div>
</div>

<form method="POST" action="<?= htmlspecialchars($action) ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <div class="adm-panel" style="max-width:760px;">
    <div class="adm-panel__body">

      <div class="adm-form-group">
        <label class="adm-label">Name</label>
        <input type="text" name="name" class="adm-input" required autofocus
               value="<?= htmlspecialchars($name) ?>"
               placeholder="e.g. Hydraulic Power">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">ATA code</label>
        <input type="text" name="ata_code" class="adm-input" required maxlength="10"
               value="<?= htmlspecialchars($ataCode) ?>"
               placeholder="e.g. ATA29">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Description (optional)</label>
        <textarea name="description" class="adm-input" rows="3"><?= htmlspecialchars($description) ?></textarea>
      </div>

      <div class="adm-form-group" style="display:flex; gap:16px;">
        <div style="flex:1;">
          <label class="adm-label">Color</label>
          <input type="color" name="color_hex" class="adm-input"
                 value="<?= htmlspecialchars($colorHex) ?>" style="height:40px;">
        </div>
        <div style="flex:1;">
          <label class="adm-label">Icon (lucide name)</label>
          <input type="text" name="icon" class="adm-input"
                 value="<?= htmlspecialchars($icon) ?>"
                 placeholder="zap, plane, fuel, gauge…">
        </div>
        <div style="flex:1;">
          <label class="adm-label">Sort order</label>
          <input type="number" name="sort_order" class="adm-input"
                 value="<?= $sortOrder ?>" min="0" step="1">
        </div>
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Unlock after (optional prerequisite)</label>
        <select name="unlock_after_system_id" class="adm-select">
          <option value="0">— always available —</option>
          <?php foreach ($all_systems as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= $unlockAfter === (int)$s['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars(trim(($s['ata_code'] ?? '') . ' — ' . $s['name'], '— ')) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small style="display:block; margin-top:6px; color:#9ca3af;">
          If set, learners must master the chosen prerequisite system before this one becomes accessible.
        </small>
      </div>

      <div class="adm-form-group">
        <label style="display:flex; gap:10px; align-items:center; font-size:14px;">
          <input type="checkbox" name="is_published" value="1" <?= $isPublished ? 'checked' : '' ?>>
          Published — visible to learners
        </label>
      </div>

    </div>
  </div>

  <div class="adm-panel" style="margin-top:16px; max-width:760px;">
    <div class="adm-panel__body" style="display:flex; gap:12px; align-items:center;">
      <button type="submit" class="adm-btn adm-btn--primary">
        <?= $isNew ? 'Create system' : 'Save changes' ?>
      </button>
      <a href="/admin/systems" class="adm-btn adm-btn--ghost">Cancel</a>
    </div>
  </div>
</form>
