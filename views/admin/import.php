<?php
declare(strict_types=1);
/**
 * Admin Import / AI Generation form — simplified flow.
 *
 * Two required fields: PDF + Target system. Everything else (provider,
 * generation mode, analysis depth) defaults sensibly and lives in a
 * collapsed Advanced section.
 *
 * @var string $csrf_token
 * @var array  $systems         List of system rows for the target dropdown
 * @var array  $ai_config       ['default_provider'=>..., 'providers'=>[...]]
 * @var array  $extract_status  ['smalot'=>bool,'pdftotext'=>?string,'any'=>bool]
 * @var array  $recent_jobs     Last 10 ai_generation_jobs rows
 */

$providers = $ai_config['providers'] ?? [];
$defaultProvider = (string) ($ai_config['default_provider'] ?? 'anthropic');
$anyConfigured = false;
foreach ($providers as $p) { if ($p['configured']) { $anyConfigured = true; break; } }
$activeProvider = $providers[$defaultProvider] ?? null;
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Import &amp; AI Generation</h1>
    <p class="adm-page-header__sub">Upload a Q400 PDF, pick the target system, click Generate. The AI builds a full lesson + flashcards + quiz; you review and publish.</p>
  </div>
  <div>
    <a href="/admin/systems" class="adm-btn adm-btn--ghost">+ Manage systems</a>
  </div>
</div>

<!-- Status banner -->
<div class="adm-panel" style="max-width:920px;">
  <div class="adm-panel__body" style="display:flex; gap:24px; flex-wrap:wrap; align-items:center; font-size:13px;">
    <div>
      <strong>AI:</strong>
      <?php if ($activeProvider && $activeProvider['configured']): ?>
        <span style="color:#10b981;">
          <?= htmlspecialchars($activeProvider['label']) ?> — <?= htmlspecialchars($activeProvider['model_chunk']) ?>
        </span>
        <a href="/admin/settings" style="color:#9ca3af; margin-left:8px; font-size:12px;">change</a>
      <?php else: ?>
        <span style="color:#ef4444;">no provider configured</span> —
        <a href="/admin/settings" style="color:#3b82f6;">set up an API key</a>
      <?php endif; ?>
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

<div class="adm-panel" style="margin-top:16px; max-width:920px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">New lesson</h2>
  </div>
  <div class="adm-panel__body">
    <?php if (empty($systems)): ?>
      <div style="padding:14px; background:#f59e0b22; border:1px solid #f59e0b; border-radius:6px; font-size:13px; margin-bottom:16px;">
        No target systems exist yet. <a href="/admin/systems/new" style="color:#f59e0b; font-weight:600;">Add your first system →</a>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="/admin/import/process">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div class="adm-form-group">
        <label class="adm-label">Source PDF (max 50 MB)</label>
        <input type="file" name="pdf_file" class="adm-input" accept=".pdf" required>
        <small style="display:block; margin-top:6px; color:#9ca3af;">
          Upload an aircraft-system manual chapter — e.g. <code>Q400-Hydraulic_Power.pdf</code>.
        </small>
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Target system</label>
        <select name="target_system_id" class="adm-select" required <?= empty($systems) ? 'disabled' : '' ?>>
          <option value="">— select a system —</option>
          <?php foreach ($systems as $sys): ?>
            <option value="<?= (int)$sys['id'] ?>">
              <?= htmlspecialchars(trim(($sys['ata_code'] ?? '') . ' — ' . $sys['name'], '— ')) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small style="display:block; margin-top:6px; color:#9ca3af;">
          Don't see your system? <a href="/admin/systems/new" style="color:#3b82f6;">Add it</a> first.
        </small>
      </div>

      <!-- Hidden defaults: AI-only mode + Standard depth + Default provider -->
      <input type="hidden" name="mode" value="full">
      <input type="hidden" name="analysis_depth" id="depth_input" value="standard">
      <input type="hidden" name="provider" id="provider_input" value="<?= htmlspecialchars($defaultProvider) ?>">

      <details style="margin-top:8px; margin-bottom:16px;">
        <summary style="cursor:pointer; font-size:13px; color:#9ca3af; padding:8px 0;">
          Advanced — override provider, mode, or depth
        </summary>
        <div style="margin-top:12px; padding:12px; border:1px solid #2a2a2a; border-radius:6px;">

          <div class="adm-form-group">
            <label class="adm-label">AI provider</label>
            <select class="adm-select" onchange="document.getElementById('provider_input').value = this.value">
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
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Generation mode</label>
            <select class="adm-select" onchange="document.querySelector('input[name=mode]').value = this.value">
              <option value="full" selected>AI-only — Claude builds the full lesson, you review &amp; publish</option>
              <option value="assisted">AI-assisted — Claude drafts, you edit each slide before publish</option>
              <option value="manual">Manual — no AI; I'll fill in every slide by hand</option>
            </select>
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Analysis depth</label>
            <select class="adm-select" onchange="document.getElementById('depth_input').value = this.value">
              <option value="standard" selected>Standard — 8-12 slides, 10 flashcards, 6 quiz items</option>
              <option value="detail">Detail — 14-20 slides, 18 flashcards, 10 quiz items, mandatory abnormal/scenario coverage. Use for the Q400 Caution Draft.</option>
            </select>
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Source label (optional)</label>
            <input type="text" name="source_label" class="adm-input"
                   placeholder="e.g. Q400-Hydraulic_Power.pdf, p4-6">
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Or paste text directly (PDF still preferred)</label>
            <textarea name="pasted_text" class="adm-input" rows="6"
                      style="font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 12px;"
                      placeholder="Paste a section of a Q400 manual instead of uploading a PDF…"></textarea>
          </div>
        </div>
      </details>

      <button type="submit" class="adm-btn adm-btn--primary"
              <?= ($anyConfigured && !empty($systems)) ? '' : 'disabled' ?>>
        Generate full lesson
      </button>
      <?php if (!$anyConfigured): ?>
        <small style="display:block; margin-top:8px; color:#ef4444;">
          Set an API key in <a href="/admin/settings" style="color:#3b82f6;">Settings</a> first.
        </small>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="adm-panel" style="margin-top:16px; max-width:1100px;">
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
            <td style="padding:10px 16px; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
              <?= htmlspecialchars($j['source_label'] ?: ($j['original_filename'] ?? '(pasted)')) ?>
            </td>
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
            <td style="padding:10px 16px; font-size:12px;">
              <?= (int)$j['progress_pct'] ?>% — <?= htmlspecialchars((string)$j['progress_message']) ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
