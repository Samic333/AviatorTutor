<?php
declare(strict_types=1);
/**
 * Admin AI Job detail page.
 *
 * @var array        $job
 * @var ?array       $lesson
 * @var array        $slides
 * @var array        $flashcards
 * @var array        $quiz_questions
 * @var ?array       $target_system
 * @var string       $csrf_token
 */

$status = (string) $job['status'];
$colors = [
    'queued'=>'#9ca3af','running'=>'#3b82f6','review'=>'#f59e0b',
    'published'=>'#10b981','failed'=>'#ef4444','cancelled'=>'#6b7280'
];
$statusColor = $colors[$status] ?? '#9ca3af';

$canPublish = in_array($status, ['review', 'failed'], true) && $lesson;
$canDiscard = in_array($status, ['review', 'failed'], true);
$isLive     = $status === 'published' && $lesson;
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Job #<?= (int) $job['id'] ?></h1>
    <p class="adm-page-header__sub">
      <?= htmlspecialchars($job['source_label'] ?: ($job['original_filename'] ?? '(pasted excerpt)')) ?>
      &mdash; <?= htmlspecialchars($job['mode']) ?>, <?= htmlspecialchars($job['analysis_depth']) ?>
    </p>
  </div>
  <div style="display:flex; gap:10px;">
    <a href="/admin/ai-jobs" class="adm-btn adm-btn--ghost">All jobs</a>
    <a href="/admin/import" class="adm-btn adm-btn--ghost">Back to import</a>
  </div>
</div>

<!-- Status panel -->
<div class="adm-panel" style="max-width:1100px;">
  <div class="adm-panel__body">
    <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; flex-wrap:wrap;">
      <span style="font-size:12px; text-transform:uppercase; letter-spacing:0.6px; padding:4px 10px; border-radius:999px; background:<?= $statusColor ?>22; color:<?= $statusColor ?>; font-weight:700;">
        <?= htmlspecialchars($status) ?>
      </span>
      <span style="font-size:14px;">
        <?= (int)$job['progress_pct'] ?>% &mdash; <?= htmlspecialchars((string)$job['progress_message']) ?>
      </span>
      <?php if ($canPublish || $canDiscard): ?>
        <div style="margin-left:auto; display:flex; gap:8px;">
          <?php if ($canPublish): ?>
            <form method="POST" action="/admin/ai-jobs/<?= (int)$job['id'] ?>/publish" style="display:inline; margin:0;"
                  onsubmit="return confirm('Publish this lesson + slides + flashcards + quiz to learners?');">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <button type="submit" class="adm-btn adm-btn--primary" style="background:#10b981; border-color:#10b981;">
                ✓ Publish all drafts
              </button>
            </form>
          <?php endif; ?>
          <?php if ($canDiscard): ?>
            <form method="POST" action="/admin/ai-jobs/<?= (int)$job['id'] ?>/discard" style="display:inline; margin:0;"
                  onsubmit="return confirm('Discard this job and delete all draft rows? This cannot be undone.');">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <button type="submit" class="adm-btn adm-btn--ghost" style="color:#ef4444; border-color:#ef4444;">
                Discard drafts
              </button>
            </form>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($status === 'queued' || $status === 'running'): ?>
      <p style="margin:0; color:#9ca3af; font-size:13px;">
        This page auto-refreshes every 4 seconds while the job is in flight.
      </p>
      <meta http-equiv="refresh" content="4">
    <?php endif; ?>

    <?php if ($isLive): ?>
      <div style="margin-top:12px; padding:12px; background:#10b98122; border:1px solid #10b981; border-radius:6px; font-size:13px;">
        ✓ <strong>Live.</strong> Learners can now see this lesson.
        <?php if ($target_system): ?>
          <a href="/study/<?= (int)$target_system['id'] ?>" style="color:#10b981; margin-left:8px;">View on the learner site →</a>
        <?php endif; ?>
      </div>
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
<div class="adm-panel" style="margin-top:16px; max-width:1100px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Drafts produced</h2>
    <span style="margin-left:auto; font-size:13px; color:#9ca3af;">
      Lesson #<?= (int)$lesson['id'] ?> &middot; <?= count($slides) ?> slides &middot; <?= count($flashcards) ?> flashcards &middot; <?= count($quiz_questions) ?> quiz items
    </span>
  </div>
  <div class="adm-panel__body">
    <p style="margin:0 0 12px; font-size:13px; color:#9ca3af;">
      <?php if ($isLive): ?>
        These rows are now <strong style="color:#10b981;">live</strong> for learners.
      <?php else: ?>
        These rows are tagged <code>status='draft'</code> and are NOT yet visible to learners. Review the slides below, then click <strong>Publish all drafts</strong>.
      <?php endif; ?>
    </p>

    <h3 style="font-size:14px; margin:16px 0 8px;">Lesson</h3>
    <div style="border:1px solid #2a2a2a; border-radius:6px; padding:12px; background:#0b0b0d;">
      <strong><?= htmlspecialchars($lesson['title']) ?></strong>
      <p style="margin:6px 0 0; font-size:13px; color:#cbd5e1;"><?= htmlspecialchars((string)$lesson['summary']) ?></p>
    </div>

    <h3 style="font-size:14px; margin:16px 0 8px;">Slides (<?= count($slides) ?>)</h3>
    <div style="display:flex; flex-direction:column; gap:10px;">
      <?php foreach ($slides as $i => $s):
        $q = is_string($s['question'])
            ? json_decode($s['question'], true)
            : (is_array($s['question']) ? $s['question'] : null);
      ?>
        <details style="border:1px solid #2a2a2a; border-radius:6px; background:#0b0b0d;">
          <summary style="padding:10px 14px; cursor:pointer; font-size:13px;">
            <span style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:#9ca3af; margin-right:10px;">
              [<?= htmlspecialchars($s['slide_type']) ?>]
            </span>
            <strong><?= htmlspecialchars($s['title']) ?></strong>
            <?php if (($s['status'] ?? '') === 'draft'): ?>
              <span style="margin-left:8px; padding:1px 8px; font-size:10px; background:#f59e0b22; color:#f59e0b; border-radius:999px;">DRAFT</span>
            <?php endif; ?>
          </summary>
          <div style="padding:0 14px 14px; font-size:13px; line-height:1.55; color:#cbd5e1;">
            <?php if (!empty($s['body'])): ?>
              <p style="white-space:pre-wrap;"><?= htmlspecialchars((string)$s['body']) ?></p>
            <?php endif; ?>
            <?php if (!empty($s['key_point'])): ?>
              <p><strong>Memory hook:</strong> <?= htmlspecialchars((string)$s['key_point']) ?></p>
            <?php endif; ?>
            <?php if (!empty($s['ops_relevance'])): ?>
              <p><strong>Ops relevance:</strong> <?= htmlspecialchars((string)$s['ops_relevance']) ?></p>
            <?php endif; ?>
            <?php if (is_array($q)): ?>
              <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #2a2a2a;">
                <strong><?= htmlspecialchars((string)($q['prompt'] ?? '')) ?></strong>
                <ol type="A" style="margin:6px 0 0; padding-left:24px;">
                  <?php foreach ((array)($q['options'] ?? []) as $oi => $opt): ?>
                    <li style="<?= ((int)($q['correct_index'] ?? -1) === (int)$oi) ? 'color:#10b981;' : '' ?>">
                      <?= htmlspecialchars((string)$opt) ?>
                      <?php if ((int)($q['correct_index'] ?? -1) === (int)$oi): ?>&nbsp;✓<?php endif; ?>
                    </li>
                  <?php endforeach; ?>
                </ol>
                <?php if (!empty($q['explanation'])): ?>
                  <p style="margin:6px 0 0; font-size:12px; color:#9ca3af;">
                    <em><?= htmlspecialchars((string)$q['explanation']) ?></em>
                  </p>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($s['source_quote'])): ?>
              <p style="margin-top:8px; font-size:11px; color:#6b7280;">
                <strong>Grounded quote:</strong> "<?= htmlspecialchars((string)$s['source_quote']) ?>"
              </p>
            <?php endif; ?>
          </div>
        </details>
      <?php endforeach; ?>
    </div>

    <h3 style="font-size:14px; margin:24px 0 8px;">Flashcards (<?= count($flashcards) ?>)</h3>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead><tr style="text-align:left;">
        <th style="padding:6px 10px; border-bottom:1px solid #2a2a2a;">Front</th>
        <th style="padding:6px 10px; border-bottom:1px solid #2a2a2a;">Back</th>
        <th style="padding:6px 10px; border-bottom:1px solid #2a2a2a; width:80px;">Difficulty</th>
      </tr></thead>
      <tbody>
        <?php foreach ($flashcards as $f): ?>
          <tr style="border-bottom:1px solid #1f1f23;">
            <td style="padding:6px 10px;"><?= htmlspecialchars((string)$f['front']) ?></td>
            <td style="padding:6px 10px;"><?= htmlspecialchars((string)$f['back']) ?></td>
            <td style="padding:6px 10px;"><?= htmlspecialchars((string)$f['difficulty']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h3 style="font-size:14px; margin:24px 0 8px;">Quiz items (<?= count($quiz_questions) ?>)</h3>
    <ol style="padding-left:22px; margin:0; font-size:13px; line-height:1.6;">
      <?php foreach ($quiz_questions as $q):
        $opts = is_string($q['options']) ? json_decode($q['options'], true) : (array) $q['options'];
        $corr = is_string($q['correct_answer']) ? json_decode($q['correct_answer'], true) : (array) $q['correct_answer'];
        $correctIdx = (int) ($corr['index'] ?? -1);
      ?>
        <li style="margin-bottom:8px;">
          <strong><?= htmlspecialchars((string)$q['question_text']) ?></strong>
          <ol type="A" style="margin:4px 0 0; padding-left:22px; color:#cbd5e1;">
            <?php foreach ((array) $opts as $oi => $opt): ?>
              <li style="<?= $correctIdx === (int)$oi ? 'color:#10b981;' : '' ?>">
                <?= htmlspecialchars((string)$opt) ?>
                <?php if ($correctIdx === (int)$oi): ?>&nbsp;✓<?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ol>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($job['raw_response'])): ?>
<details style="margin-top:16px; max-width:1100px;">
  <summary style="cursor:pointer; padding:8px 0; font-size:13px; color:#9ca3af;">Raw model response (debugging)</summary>
  <pre style="background:#0b0b0d; padding:12px; border-radius:6px; font-size:11px; max-height:420px; overflow:auto; white-space:pre-wrap; word-break:break-word; margin-top:8px;"><?= htmlspecialchars((string)$job['raw_response']) ?></pre>
</details>
<?php endif; ?>
