<?php
declare(strict_types=1);
/** @var array $lesson */
/** @var array $slides */
/** @var string $csrf_token */

$lessonId = (int)$lesson['id'];
$systemId = (int)$lesson['system_id'];

$slideTypes = [
    'intro'       => 'Introduction',
    'concept'     => 'Concept',
    'system'      => 'System / Component',
    'normal_op'   => 'Normal Operation',
    'abnormal'    => 'Abnormal / Failure',
    'operational' => 'Operational Example',
    'qrh'         => 'QRH / Memory Item',
    'scenario'    => 'Scenario',
    'revision'    => 'Revision / Recap',
    'quiz'        => 'Quiz',
];
$mediaTypes = [
    'none'      => 'No media',
    'image'     => 'Image',
    'diagram'   => 'Diagram',
    'video'     => 'Video',
    'animation' => 'Animation',
    'model3d'   => '3D Model',
];

/** Render one slide form. Pass an empty array for "new". */
function render_slide_form(array $slide, int $lessonId, string $csrf_token, array $slideTypes, array $mediaTypes, int $idx, int $total): void {
    $isNew = empty($slide['id']);
    $sid   = (int)($slide['id'] ?? 0);
    $q = null;
    if (!empty($slide['question'])) {
        $decoded = is_string($slide['question']) ? json_decode($slide['question'], true) : $slide['question'];
        if (is_array($decoded)) $q = $decoded;
    }
    $qOptions = $q['options'] ?? ['', '', '', ''];
    if (count($qOptions) < 4) {
        $qOptions = array_pad($qOptions, 4, '');
    }
    $qPrompt        = $q['prompt']        ?? '';
    $qCorrectIndex  = $q['correct_index'] ?? 0;
    $qExplanation   = $q['explanation']   ?? '';
?>
  <div class="adm-panel" style="margin-bottom:16px;<?= $isNew ? 'border-left:3px solid #3b82f6;' : '' ?>" id="slide-<?= $sid ?: 'new' ?>">
    <div class="adm-panel__header" style="align-items:center;">
      <h2 class="adm-panel__title">
        <?= $isNew ? 'New slide' : 'Slide #' . ($idx + 1) ?>
        <?php if (!$isNew): ?>
          <span class="adm-mono adm-muted" style="font-size:11px;font-weight:500;margin-left:8px;">id:<?= $sid ?> · order:<?= (int)$slide['sort_order'] ?></span>
        <?php endif; ?>
      </h2>
      <?php if (!$isNew): ?>
        <div style="display:flex;gap:6px;">
          <?php if ($idx > 0): ?>
            <form method="post" action="/admin/slides/<?= $sid ?>/move" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <input type="hidden" name="direction" value="up">
              <button type="submit" class="adm-btn adm-btn--ghost adm-btn--sm" title="Move up">↑</button>
            </form>
          <?php endif; ?>
          <?php if ($idx < $total - 1): ?>
            <form method="post" action="/admin/slides/<?= $sid ?>/move" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <input type="hidden" name="direction" value="down">
              <button type="submit" class="adm-btn adm-btn--ghost adm-btn--sm" title="Move down">↓</button>
            </form>
          <?php endif; ?>
          <form method="post" action="/admin/slides/<?= $sid ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this slide? This cannot be undone.');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit" class="adm-btn adm-btn--danger adm-btn--sm">Delete</button>
          </form>
        </div>
      <?php endif; ?>
    </div>

    <form method="post" action="/admin/slides/lesson/<?= $lessonId ?>/save" class="adm-panel__body">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <input type="hidden" name="slide_id" value="<?= $sid ?>">

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
        <div class="adm-form-group">
          <label>Slide type</label>
          <select name="slide_type" class="adm-select">
            <?php foreach ($slideTypes as $k => $v): ?>
              <option value="<?= $k ?>" <?= ($slide['slide_type'] ?? 'concept') === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-form-group">
          <label>Sort order <span class="adm-muted" style="font-weight:400;">(lower = earlier)</span></label>
          <input type="number" name="sort_order" class="adm-input" value="<?= htmlspecialchars((string)($slide['sort_order'] ?? '')) ?>" placeholder="auto">
        </div>
      </div>

      <div class="adm-form-group">
        <label>Title <span style="color:#f87171;">*</span></label>
        <input type="text" name="title" class="adm-input" required maxlength="255"
               value="<?= htmlspecialchars((string)($slide['title'] ?? '')) ?>"
               placeholder="e.g. The No.3 standby system feeds the elevators">
      </div>

      <div class="adm-form-group">
        <label>Body <span class="adm-muted" style="font-weight:400;">(short explanation, 2–4 sentences ideal)</span></label>
        <textarea name="body" class="adm-textarea" rows="5"
                  placeholder="Explain the concept clearly. Use \n for paragraph breaks."><?= htmlspecialchars((string)($slide['body'] ?? '')) ?></textarea>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
        <div class="adm-form-group">
          <label>Media type</label>
          <select name="media_type" class="adm-select">
            <?php foreach ($mediaTypes as $k => $v): ?>
              <option value="<?= $k ?>" <?= ($slide['media_type'] ?? 'none') === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="adm-form-group">
          <label>Media URL <span class="adm-muted" style="font-weight:400;">(file path or embed URL)</span></label>
          <input type="text" name="media_url" class="adm-input" maxlength="500"
                 value="<?= htmlspecialchars((string)($slide['media_url'] ?? '')) ?>"
                 placeholder="/assets/uploads/hydraulics/no3_diagram.png">
        </div>
        <div class="adm-form-group">
          <label>Media alt / caption</label>
          <input type="text" name="media_alt" class="adm-input" maxlength="255"
                 value="<?= htmlspecialchars((string)($slide['media_alt'] ?? '')) ?>"
                 placeholder="e.g. No.3 hydraulic schematic">
        </div>
      </div>

      <div class="adm-form-group">
        <label>Memory hook <span class="adm-muted" style="font-weight:400;">(short, punchy — shows in amber card)</span></label>
        <textarea name="key_point" class="adm-textarea" rows="2"
                  placeholder="e.g. 3 + E = 3000.  Three mains plus Emergency, all at 3000 PSI."><?= htmlspecialchars((string)($slide['key_point'] ?? '')) ?></textarea>
      </div>

      <div class="adm-form-group">
        <label>Operational relevance <span class="adm-muted" style="font-weight:400;">(crew action / line ops impact)</span></label>
        <textarea name="ops_relevance" class="adm-textarea" rows="2"
                  placeholder="e.g. Brief the FO on degraded brakes before every approach following a hyd-2 failure."><?= htmlspecialchars((string)($slide['ops_relevance'] ?? '')) ?></textarea>
      </div>

      <fieldset style="border:1px dashed rgba(148,163,184,0.25);border-radius:8px;padding:12px;margin-top:12px;">
        <legend style="font-size:12px;font-weight:600;letter-spacing:0.4px;text-transform:uppercase;color:#60a5fa;padding:0 6px;">Optional question gate</legend>
        <p class="adm-muted" style="font-size:12px;margin-bottom:10px;">Fill all four fields below to enable a check question on this slide. Leave the prompt empty to skip the gate.</p>

        <div class="adm-form-group">
          <label>Prompt</label>
          <input type="text" name="question_prompt" class="adm-input"
                 value="<?= htmlspecialchars($qPrompt) ?>"
                 placeholder="e.g. Which system feeds the elevators after a No.1 failure?">
        </div>

        <div class="adm-form-group">
          <label>Options <span class="adm-muted" style="font-weight:400;">(2–4 — leave blank rows for fewer)</span></label>
          <?php foreach ($qOptions as $oi => $opt): ?>
            <div style="display:flex;gap:8px;align-items:center;margin-bottom:6px;">
              <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#94a3b8;min-width:74px;">
                <input type="radio" name="question_correct_index" value="<?= $oi ?>" <?= (int)$qCorrectIndex === $oi ? 'checked' : '' ?> style="accent-color:#3b82f6;">
                Correct <?= chr(65 + $oi) ?>
              </label>
              <input type="text" name="question_options[]" class="adm-input" style="flex:1;"
                     value="<?= htmlspecialchars((string)$opt) ?>"
                     placeholder="Option <?= chr(65 + $oi) ?>">
            </div>
          <?php endforeach; ?>
        </div>

        <div class="adm-form-group">
          <label>Explanation <span class="adm-muted" style="font-weight:400;">(shown after wrong answer)</span></label>
          <textarea name="question_explanation" class="adm-textarea" rows="2"
                    placeholder="e.g. No.3 standby auto-feeds the elevators when 1 or 2 fails. The hand pump is for gear and brakes only."><?= htmlspecialchars($qExplanation) ?></textarea>
        </div>
      </fieldset>

      <div style="margin-top:16px;">
        <button type="submit" class="adm-btn adm-btn--primary"><?= $isNew ? 'Add slide' : 'Save slide' ?></button>
      </div>
    </form>
  </div>
<?php
}
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">
      <?= htmlspecialchars((string)$lesson['title']) ?>
    </h1>
    <p class="adm-page-header__sub">
      <?= htmlspecialchars((string)$lesson['system_name']) ?>
      <?php if (!empty($lesson['ata_code'])): ?>
        · <span class="adm-mono"><?= htmlspecialchars((string)$lesson['ata_code']) ?></span>
      <?php endif; ?>
      · <?= count($slides) ?> slide<?= count($slides) === 1 ? '' : 's' ?>
    </p>
  </div>
  <div class="adm-page-header__actions" style="display:flex;gap:8px;">
    <a href="/admin/slides" class="adm-btn adm-btn--ghost adm-btn--sm">← All lessons</a>
    <a href="/study/<?= $systemId ?>/lesson/<?= $lessonId ?>" target="_blank" class="adm-btn adm-btn--ghost adm-btn--sm">Preview →</a>
  </div>
</div>

<?php if (empty($slides)): ?>
  <div class="adm-panel" style="margin-bottom:16px;">
    <div class="adm-panel__body">
      <p class="adm-muted">No slides yet for this lesson. Use the form below to add the first one.</p>
    </div>
  </div>
<?php else: ?>
  <?php foreach ($slides as $idx => $slide): ?>
    <?php render_slide_form($slide, $lessonId, $csrf_token, $slideTypes, $mediaTypes, $idx, count($slides)); ?>
  <?php endforeach; ?>
<?php endif; ?>

<h2 style="margin:24px 0 12px;font-size:16px;font-weight:600;color:#f8fafc;">Add a new slide</h2>
<?php render_slide_form([], $lessonId, $csrf_token, $slideTypes, $mediaTypes, count($slides), count($slides) + 1); ?>
