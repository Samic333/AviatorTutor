<?php
declare(strict_types=1);
/** @var array|null $quiz */
/** @var array $systems */
/** @var string $csrf_token */

$isEdit  = is_array($quiz);
$action  = $isEdit ? '/admin/quizzes/' . (int)$quiz['id'] . '/update' : '/admin/quizzes/create';
$title   = $isEdit ? (string)$quiz['title'] : '';
$sysId   = $isEdit ? (int)$quiz['system_id'] : 0;
$type    = $isEdit ? (string)$quiz['quiz_type'] : 'standard';
$tlim    = $isEdit ? (int)($quiz['time_limit_minutes'] ?? 0) : 0;
$pass    = $isEdit ? (int)$quiz['pass_score'] : 70;
$pub     = $isEdit ? (int)$quiz['is_published'] : 1;
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title"><?= $isEdit ? 'Edit Quiz #' . (int)$quiz['id'] : 'New Quiz' ?></h1>
    <p class="adm-page-header__sub">
      <a href="/admin/quizzes" style="color:var(--plt-sky,#38BDF8);">← Back to quizzes</a>
    </p>
  </div>
</div>

<div class="adm-panel" style="max-width:760px;">
  <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div style="margin-bottom:16px;">
      <label class="adm-label">Title</label>
      <input type="text" name="title" class="adm-input" maxlength="255" required value="<?= htmlspecialchars($title) ?>" placeholder="Hydraulic Power — End-of-module quiz">
    </div>

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
        <label class="adm-label">Type</label>
        <select name="quiz_type" class="adm-input">
          <option value="standard"   <?= $type === 'standard'   ? 'selected' : '' ?>>Standard</option>
          <option value="exam_prep"  <?= $type === 'exam_prep'  ? 'selected' : '' ?>>Exam prep</option>
          <option value="rapid_fire" <?= $type === 'rapid_fire' ? 'selected' : '' ?>>Rapid fire</option>
        </select>
      </div>
    </div>

    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
      <div style="flex:1;min-width:160px;">
        <label class="adm-label">Time limit (minutes)</label>
        <input type="number" name="time_limit_minutes" min="0" max="240" class="adm-input" value="<?= $tlim ?>" placeholder="0 = untimed">
      </div>
      <div style="flex:1;min-width:160px;">
        <label class="adm-label">Pass score (%)</label>
        <input type="number" name="pass_score" min="0" max="100" class="adm-input" value="<?= $pass ?>">
      </div>
      <div style="min-width:180px;">
        <label class="adm-label">Status</label>
        <select name="is_published" class="adm-input">
          <option value="1" <?= $pub === 1 ? 'selected' : '' ?>>Published</option>
          <option value="0" <?= $pub === 0 ? 'selected' : '' ?>>Draft</option>
        </select>
      </div>
    </div>

    <div style="display:flex;gap:12px;align-items:center;">
      <button type="submit" class="adm-btn adm-btn--primary"><?= $isEdit ? 'Save changes' : 'Create quiz' ?></button>
      <a href="/admin/quizzes" class="adm-btn adm-btn--ghost">Cancel</a>
    </div>
  </form>
</div>
