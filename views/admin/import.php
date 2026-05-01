<?php
declare(strict_types=1);
/**
 * Admin Import / AI Generation form.
 *
 * @var string      $csrf_token
 * @var array       $systems         List of system rows for the target dropdown
 * @var array       $ai_config       ['default_provider'=>..., 'providers'=>[name=>{configured,label,model_chunk,...}, ...]]
 * @var array       $extract_status  ['smalot'=>bool,'pdftotext'=>?string,'any'=>bool]
 * @var array       $recent_jobs     Last 10 ai_generation_jobs rows
 */

$providers = $ai_config['providers'] ?? [];
$defaultProvider = (string) ($ai_config['default_provider'] ?? 'anthropic');
$anyConfigured = false;
foreach ($providers as $p) { if ($p['configured']) { $anyConfigured = true; break; } }
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Import &amp; AI Generation</h1>
    <p class="adm-page-header__sub">Upload a Q400 PDF (or paste text) — pick how much help you want and which AI to use.</p>
  </div>
</div>

<!-- Configuration banner -->
<div class="adm-panel" style="max-width:920px;">
  <div class="adm-panel__body" style="display:flex; gap:24px; flex-wrap:wrap; align-items:center;">
    <div style="display:flex; gap:12px; align-items:center;">
      <strong>AI providers:</strong>
      <?php foreach ($providers as $name => $p): ?>
        <span style="font-size:12px; padding:3px 10px; border-radius:999px; background:<?= $p['configured'] ? '#10b98122' : '#9ca3af22' ?>; color:<?= $p['configured'] ? '#10b981' : '#9ca3af' ?>;">
          <?= htmlspecialchars($p['label']) ?><?= $p['configured'] ? '' : ' (off)' ?>
        </span>
      <?php endforeach; ?>
    </div>
    <div>
      <strong>PDF extract:</strong>
      <?php if ($extract_status['smalot']): ?>
        <span style="color:#10b981;">Smalot ready</span>
      <?php elseif ($extract_status['pdftotext']): ?>
        <span style="color:#10b981;">pdftotext</span>
      <?php else: ?>
        <span style="color:#ef4444;">no backend</span>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="adm-panel" style="margin-top:16px; max-width:880px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">New generation job</h2>
  </div>
  <div class="adm-panel__body">
    <form method="POST" enctype="multipart/form-data" action="/admin/import/process">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div class="adm-form-group">
        <label class="adm-label">Source PDF (optional, max 50 MB)</label>
        <input type="file" name="pdf_file" class="adm-input" accept=".pdf">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Or paste an excerpt directly</label>
        <textarea name="pasted_text" class="adm-input" rows="8"
                  placeholder="Paste a section of a Q400 manual…"
                  style="font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 13px;"></textarea>
        <small style="display:block; margin-top:6px; color:#9ca3af;">
          One of PDF or pasted text is required.
        </small>
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Source label (optional)</label>
        <input type="text" name="source_label" class="adm-input"
               placeholder="e.g. Q400-Hydraulic_Power.pdf">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">AI provider</label>
        <select name="provider" class="adm-select">
          <?php foreach ($providers as $name => $p): ?>
            <option value="<?= htmlspecialchars($name) ?>"
                    <?= $name === $defaultProvider ? 'selected' : '' ?>
                    <?= !$p['configured'] ? 'disabled' : '' ?>>
              <?= htmlspecialchars($p['label']) ?>
              — <?= htmlspecialchars($p['model_chunk']) ?>
              <?= !$p['configured'] ? ' (key missing)' : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small style="display:block; margin-top:6px; color:#9ca3af;">
          Manual mode ignores this — no AI is called.
        </small>
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Target system</label>
        <select name="target_system_id" class="adm-select" required>
          <option value="">— select a system —</option>
          <?php foreach ($systems as $sys): ?>
            <option value="<?= (int)$sys['id'] ?>">
              <?= htmlspecialchars($sys['ata_code'] ?? '') ?> — <?= htmlspecialchars($sys['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <fieldset class="adm-form-group" style="border:1px solid #2a2a2a; padding:12px; border-radius:8px;">
        <legend style="padding:0 8px; font-size:13px; font-weight:600;">Generation mode</legend>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:8px 0;">
          <input type="radio" name="mode" value="manual">
          <div>
            <strong>Manual</strong>
            <small style="display:block; color:#9ca3af;">Don't call Claude — just enqueue the job for me to fill in by hand.</small>
          </div>
        </label>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:8px 0;">
          <input type="radio" name="mode" value="assisted">
          <div>
            <strong>AI-assisted</strong>
            <small style="display:block; color:#9ca3af;">Claude drafts the lesson; I review and edit each slide before publish.</small>
          </div>
        </label>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:8px 0;">
          <input type="radio" name="mode" value="full" checked>
          <div>
            <strong>AI-only (recommended)</strong>
            <small style="display:block; color:#9ca3af;">Claude produces a full lesson + flashcards + quiz. I review the bundle and click Publish.</small>
          </div>
        </label>
      </fieldset>

      <fieldset class="adm-form-group" style="border:1px solid #2a2a2a; padding:12px; border-radius:8px;">
        <legend style="padding:0 8px; font-size:13px; font-weight:600;">Analysis depth</legend>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:8px 0;">
          <input type="radio" name="analysis_depth" value="standard" checked>
          <div>
            <strong>Standard</strong>
            <small style="display:block; color:#9ca3af;">8-12 slides, 10 flashcards, 6 quiz items. Use this for most systems.</small>
          </div>
        </label>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:8px 0;">
          <input type="radio" name="analysis_depth" value="detail">
          <div>
            <strong>Detail</strong>
            <small style="display:block; color:#9ca3af;">14-20 slides, 18 flashcards, 10 quiz items, mandatory scenario + abnormal slides. Use for the Q400 Caution Draft and similar reference-heavy PDFs.</small>
          </div>
        </label>
      </fieldset>

      <button type="submit" class="adm-btn adm-btn--primary">Enqueue generation job</button>
    </form>
  </div>
</div>

<div class="adm-panel" style="margin-top:16px; max-width:880px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Recent jobs</h2>
    <a href="/admin/ai-jobs" class="adm-btn adm-btn--ghost" style="margin-left:auto;">View all</a>
  </div>
  <div class="adm-panel__body" style="padding:0;">
    <?php if (empty($recent_jobs)): ?>
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
          </tr>
        </thead>
        <tbody>
        <?php foreach ($recent_jobs as $j): ?>
          <tr style="border-top:1px solid #2a2a2a;">
            <td style="padding:10px 16px;">
              <a href="/admin/ai-jobs/<?= (int)$j['id'] ?>"><?= (int)$j['id'] ?></a>
            </td>
            <td style="padding:10px 16px;">
              <?= htmlspecialchars($j['source_label'] ?: ($j['original_filename'] ?? '(pasted)')) ?>
            </td>
            <td style="padding:10px 16px;"><?= htmlspecialchars($j['mode']) ?></td>
            <td style="padding:10px 16px;"><?= htmlspecialchars($j['analysis_depth']) ?></td>
            <td style="padding:10px 16px; font-size:12px;">
              <?= htmlspecialchars((string) ($j['provider'] ?? 'anthropic')) ?>
              <?php if (!empty($j['model'])): ?>
                <br><span style="color:#9ca3af;"><?= htmlspecialchars((string) $j['model']) ?></span>
              <?php endif; ?>
            </td>
            <td style="padding:10px 16px;">
              <?php
                $color = ['queued'=>'#9ca3af','running'=>'#3b82f6','review'=>'#f59e0b','published'=>'#10b981','failed'=>'#ef4444','cancelled'=>'#6b7280'];
                $c = $color[$j['status']] ?? '#9ca3af';
              ?>
              <span style="color:<?= $c ?>; font-weight:600;"><?= htmlspecialchars($j['status']) ?></span>
            </td>
            <td style="padding:10px 16px;">
              <?= (int)$j['progress_pct'] ?>% — <?= htmlspecialchars((string)$j['progress_message']) ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
