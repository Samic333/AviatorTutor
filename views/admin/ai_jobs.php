<?php
declare(strict_types=1);
/**
 * Admin AI Jobs index.
 *
 * @var array $jobs
 */

$colors = [
    'queued'=>'#9ca3af','running'=>'#3b82f6','review'=>'#f59e0b',
    'published'=>'#10b981','failed'=>'#ef4444','cancelled'=>'#6b7280'
];
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">AI Generation Jobs</h1>
    <p class="adm-page-header__sub">All AI lesson-generation jobs, newest first.</p>
  </div>
  <div>
    <a href="/admin/import" class="adm-btn adm-btn--primary">+ New job</a>
  </div>
</div>

<div class="adm-panel" style="max-width:1100px;">
  <div class="adm-panel__body" style="padding:0;">
    <?php if (empty($jobs)): ?>
      <p style="padding:16px; color:#9ca3af; margin:0;">No jobs yet.</p>
    <?php else: ?>
      <table class="adm-table" style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left; padding:10px 16px;">#</th>
            <th style="text-align:left; padding:10px 16px;">Source</th>
            <th style="text-align:left; padding:10px 16px;">Mode</th>
            <th style="text-align:left; padding:10px 16px;">Depth</th>
            <th style="text-align:left; padding:10px 16px;">Provider</th>
            <th style="text-align:left; padding:10px 16px;">Status</th>
            <th style="text-align:left; padding:10px 16px;">Progress</th>
            <th style="text-align:left; padding:10px 16px;">Tokens</th>
            <th style="text-align:left; padding:10px 16px;">Created</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($jobs as $j): $c = $colors[$j['status']] ?? '#9ca3af'; ?>
          <tr style="border-top:1px solid #2a2a2a;">
            <td style="padding:10px 16px;"><a href="/admin/ai-jobs/<?= (int)$j['id'] ?>"><?= (int)$j['id'] ?></a></td>
            <td style="padding:10px 16px; max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
              <?= htmlspecialchars($j['source_label'] ?: ($j['original_filename'] ?? '(pasted)')) ?>
            </td>
            <td style="padding:10px 16px;"><?= htmlspecialchars($j['mode']) ?></td>
            <td style="padding:10px 16px;"><?= htmlspecialchars($j['analysis_depth']) ?></td>
            <td style="padding:10px 16px; font-size:12px;">
              <?= htmlspecialchars((string) ($j['provider'] ?? '—')) ?>
              <?php if (!empty($j['model'])): ?>
                <br><span style="color:#9ca3af;"><?= htmlspecialchars((string) $j['model']) ?></span>
              <?php endif; ?>
            </td>
            <td style="padding:10px 16px;"><span style="color:<?= $c ?>; font-weight:600;"><?= htmlspecialchars($j['status']) ?></span></td>
            <td style="padding:10px 16px;"><?= (int)$j['progress_pct'] ?>%</td>
            <td style="padding:10px 16px;">
              <?= (int)$j['prompt_tokens'] ?>/<?= (int)$j['completion_tokens'] ?>
            </td>
            <td style="padding:10px 16px;"><?= htmlspecialchars($j['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
