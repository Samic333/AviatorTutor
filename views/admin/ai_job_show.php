<?php
declare(strict_types=1);
/**
 * Admin AI Job detail page.
 *
 * @var array        $job
 * @var ?array       $lesson    The draft lesson row, if any.
 * @var array        $slides    Lesson slides linked to this job.
 * @var array        $flashcards
 * @var array        $quiz_questions
 * @var ?array       $target_system
 */

$status = (string) $job['status'];
$colors = [
    'queued'=>'#9ca3af','running'=>'#3b82f6','review'=>'#f59e0b',
    'published'=>'#10b981','failed'=>'#ef4444','cancelled'=>'#6b7280'
];
$statusColor = $colors[$status] ?? '#9ca3af';
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Job #<?= (int) $job['id'] ?></h1>
    <p class="adm-page-header__sub">
      <?= htmlspecialchars($job['source_label'] ?: ($job['original_filename'] ?? '(pasted excerpt)')) ?>
      &mdash; <?= htmlspecialchars($job['mode']) ?>, <?= htmlspecialchars($job['analysis_depth']) ?>
    </p>
  </div>
  <div>
    <a href="/admin/ai-jobs" class="adm-btn adm-btn--ghost">All jobs</a>
    <a href="/admin/import" class="adm-btn adm-btn--ghost">Back to import</a>
  </div>
</div>

<!-- Status panel -->
<div class="adm-panel" style="max-width:1024px;">
  <div class="adm-panel__body">
    <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px;">
      <span style="font-size:12px; text-transform:uppercase; letter-spacing:0.6px; padding:4px 10px; border-radius:999px; background:<?= $statusColor ?>22; color:<?= $statusColor ?>; font-weight:700;">
        <?= htmlspecialchars($status) ?>
      </span>
      <span style="font-size:14px;">
        <?= (int)$job['progress_pct'] ?>% &mdash; <?= htmlspecialchars((string)$job['progress_message']) ?>
      </span>
    </div>

    <?php if ($status === 'queued' || $status === 'running'): ?>
      <p style="margin:0; color:#9ca3af; font-size:13px;">
        This page auto-refreshes every 4 seconds while the job is in flight.
      </p>
      <meta http-equiv="refresh" content="4">
    <?php endif; ?>

    <?php if (!empty($job['error'])): ?>
      <div style="margin-top:12px; padding:12px; background:#7f1d1d33; border:1px solid #7f1d1d; border-radius:6px; font-size:13px;">
        <strong>Error:</strong> <?= htmlspecialchars((string)$job['error']) ?>
      </div>
    <?php endif; ?>

    <ul style="margin:16px 0 0; padding:0; list-style:none; font-size:13px; line-height:1.7;">
      <li><strong>Target system:</strong>
        <?php if ($target_system): ?>
          <?= htmlspecialchars(($target_system['ata_code'] ?? '') . ' — ' . $target_system['name']) ?>
        <?php else: ?>
          <em>(none)</em>
        <?php endif; ?>
      </li>
      <li><strong>Tokens:</strong>
        in <?= (int)$job['prompt_tokens'] ?>,
        out <?= (int)$job['completion_tokens'] ?>
        — round-trip <?= (int)$job['request_ms'] ?> ms
      </li>
      <li><strong>Created:</strong> <?= htmlspecialchars($job['created_at']) ?></li>
      <?php if (!empty($job['finished_at'])): ?>
        <li><strong>Finished:</strong> <?= htmlspecialchars($job['finished_at']) ?></li>
      <?php endif; ?>
    </ul>
  </div>
</div>

<?php if ($lesson): ?>
<!-- Drafts panel -->
<div class="adm-panel" style="margin-top:16px; max-width:1024px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Drafts produced</h2>
    <span style="margin-left:auto; font-size:13px; color:#9ca3af;">
      Lesson #<?= (int)$lesson['id'] ?> &middot; <?= count($slides) ?> slides &middot; <?= count($flashcards) ?> flashcards &middot; <?= count($quiz_questions) ?> quiz items
    </span>
  </div>
  <div class="adm-panel__body">
    <p style="margin:0 0 12px; font-size:13px; color:#9ca3af;">
      All draft rows below are tagged <code>status='draft'</code> and are NOT visible to learners. Phase 4 will add a one-click Publish action that flips them live.
    </p>
    <h3 style="font-size:14px; margin:16px 0 8px;">Lesson</h3>
    <div style="border:1px solid #2a2a2a; border-radius:6px; padding:12px; background:#0b0b0d;">
      <strong><?= htmlspecialchars($lesson['title']) ?></strong>
      <p style="margin:6px 0 0; font-size:13px; color:#cbd5e1;"><?= htmlspecialchars((string)$lesson['summary']) ?></p>
    </div>

    <h3 style="font-size:14px; margin:16px 0 8px;">Slides (first 5 shown)</h3>
    <ol style="padding-left:18px; margin:0; font-size:13px;">
    <?php foreach (array_slice($slides, 0, 5) as $s): ?>
      <li style="margin-bottom:6px;">
        <strong>[<?= htmlspecialchars($s['slide_type']) ?>]</strong> <?= htmlspecialchars($s['title']) ?>
      </li>
    <?php endforeach; ?>
    </ol>

    <?php if (count($slides) > 5): ?>
      <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">…and <?= count($slides) - 5 ?> more.</p>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($job['raw_response'])): ?>
<div class="adm-panel" style="margin-top:16px; max-width:1024px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Raw model response</h2>
  </div>
  <div class="adm-panel__body">
    <pre style="background:#0b0b0d; padding:12px; border-radius:6px; font-size:11px; max-height:420px; overflow:auto; white-space:pre-wrap; word-break:break-word;"><?= htmlspecialchars((string)$job['raw_response']) ?></pre>
  </div>
</div>
<?php endif; ?>
