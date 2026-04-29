<?php
declare(strict_types=1);
/** @var array|null $card */
/** @var array $systems */
/** @var string $csrf_token */

$isEdit  = is_array($card);
$action  = $isEdit ? '/admin/flashcards/' . (int)$card['id'] . '/update' : '/admin/flashcards/create';
$front   = $isEdit ? (string)$card['front'] : '';
$back    = $isEdit ? (string)$card['back']  : '';
$hint    = $isEdit ? (string)($card['hint'] ?? '') : '';
$sysId   = $isEdit ? (int)$card['system_id'] : 0;
$diff    = $isEdit ? (string)$card['difficulty'] : 'medium';
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title"><?= $isEdit ? 'Edit Flashcard #' . (int)$card['id'] : 'New Flashcard' ?></h1>
    <p class="adm-page-header__sub">
      <a href="/admin/flashcards" style="color:var(--plt-sky,#38BDF8);">← Back to flashcards</a>
    </p>
  </div>
</div>

<div class="adm-panel" style="max-width:760px;">
  <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
      <div style="flex:1;min-width:240px;">
        <label class="adm-label">System</label>
        <select name="system_id" class="adm-input" required>
          <option value="">— pick a system —</option>
          <?php foreach ($systems as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= $sysId === (int)$s['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['ata_code'] . ' · ' . $s['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="min-width:180px;">
        <label class="adm-label">Difficulty</label>
        <select name="difficulty" class="adm-input">
          <option value="easy"   <?= $diff === 'easy'   ? 'selected' : '' ?>>Easy</option>
          <option value="medium" <?= $diff === 'medium' ? 'selected' : '' ?>>Medium</option>
          <option value="hard"   <?= $diff === 'hard'   ? 'selected' : '' ?>>Hard</option>
        </select>
      </div>
    </div>

    <div style="margin-bottom:16px;">
      <label class="adm-label">Front (question / prompt)</label>
      <textarea name="front" rows="3" class="adm-input" maxlength="2000" required placeholder="What is the maximum operating altitude of the Q400?"><?= htmlspecialchars($front) ?></textarea>
    </div>

    <div style="margin-bottom:16px;">
      <label class="adm-label">Back (answer)</label>
      <textarea name="back" rows="4" class="adm-input" maxlength="2000" required placeholder="FL250 (25,000 ft) operationally; certified ceiling FL270."><?= htmlspecialchars($back) ?></textarea>
    </div>

    <div style="margin-bottom:24px;">
      <label class="adm-label">Hint <span class="adm-muted" style="font-weight:normal;">(optional)</span></label>
      <input type="text" name="hint" class="adm-input" maxlength="500" value="<?= htmlspecialchars($hint) ?>" placeholder="Think pressurisation differential limits.">
    </div>

    <div style="display:flex;gap:12px;align-items:center;">
      <button type="submit" class="adm-btn adm-btn--primary"><?= $isEdit ? 'Save changes' : 'Create flashcard' ?></button>
      <a href="/admin/flashcards" class="adm-btn adm-btn--ghost">Cancel</a>
    </div>
  </form>
</div>
