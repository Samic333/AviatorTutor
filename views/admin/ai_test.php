<?php
declare(strict_types=1);
/**
 * Admin AI Smoke Test page.
 *
 * @var string      $csrf_token
 * @var bool        $configured
 * @var string      $model_chunk
 * @var string      $model_outline
 * @var string|null $pdftotext_path
 * @var array|null  $result        Set after a submit; null on initial load.
 * @var bool        $submitted
 * @var string      $source_label
 * @var string      $pasted_text
 */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">AI Smoke Test</h1>
    <p class="adm-page-header__sub">Phase 2 — prove end-to-end Claude integration on one excerpt. No DB writes.</p>
  </div>
</div>

<div class="adm-panel" style="max-width: 880px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Configuration</h2>
  </div>
  <div class="adm-panel__body">
    <ul style="margin:0; padding:0; list-style:none; font-size:13px; line-height:1.7;">
      <li>
        <strong>Anthropic API key:</strong>
        <?php if ($configured): ?>
          <span style="color:var(--adm-success, #10b981);">configured</span>
        <?php else: ?>
          <span style="color:var(--adm-danger, #ef4444);">missing</span> —
          set <code>'anthropic_api_key' =&gt; '...'</code> in <code>config/app.local.php</code>
        <?php endif; ?>
      </li>
      <li><strong>Chunk model (smoke test uses this):</strong> <code><?= htmlspecialchars($model_chunk) ?></code></li>
      <li><strong>Outline model (Phase 3+):</strong> <code><?= htmlspecialchars($model_outline) ?></code></li>
      <li>
        <strong>pdftotext on host:</strong>
        <?php if ($pdftotext_path): ?>
          <span style="color:var(--adm-success, #10b981);">found at <code><?= htmlspecialchars($pdftotext_path) ?></code></span>
        <?php else: ?>
          <span style="color:var(--adm-danger, #ef4444);">not found</span> —
          paste text below instead, or install poppler-utils.
        <?php endif; ?>
      </li>
    </ul>
  </div>
</div>

<div class="adm-panel" style="margin-top: 16px; max-width: 880px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Generate one sample slide</h2>
  </div>
  <div class="adm-panel__body">
    <form method="POST" enctype="multipart/form-data" action="/admin/ai-test/run">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div class="adm-form-group">
        <label class="adm-label">Source label (optional)</label>
        <input type="text" name="source_label" class="adm-input"
               value="<?= htmlspecialchars($source_label) ?>"
               placeholder="e.g. Q400-Hydraulic_Power.pdf, p4-6">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">PDF file (optional, max 50 MB)</label>
        <input type="file" name="pdf_file" class="adm-input" accept=".pdf">
        <small style="display:block; margin-top:6px; color:var(--adm-muted, #9ca3af);">
          If you upload a PDF and <code>pdftotext</code> is available, we'll extract up to ~120 KB of text from it. If not, paste below.
        </small>
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Or paste an excerpt directly</label>
        <textarea name="pasted_text" class="adm-input" rows="10"
                  placeholder="Paste a section of a Q400 system manual here…"
                  style="font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 13px;"><?= htmlspecialchars($pasted_text) ?></textarea>
      </div>

      <button type="submit" class="adm-btn adm-btn--primary" <?= $configured ? '' : 'disabled' ?>>
        Generate sample slide
      </button>
      <?php if (!$configured): ?>
        <small style="display:block; margin-top:8px; color:var(--adm-danger, #ef4444);">
          Add the Anthropic API key to <code>config/app.local.php</code> on the server before submitting.
        </small>
      <?php endif; ?>
    </form>
  </div>
</div>

<?php if ($submitted && is_array($result)): ?>
<div class="adm-panel" style="margin-top: 16px; max-width: 880px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Result</h2>
  </div>
  <div class="adm-panel__body">
    <?php if (($result['ok'] ?? false) === true): ?>
      <p style="color: var(--adm-success, #10b981); margin-top:0;">✓ Claude responded successfully.</p>
      <ul style="margin:0 0 16px; padding:0; list-style:none; font-size:13px; line-height:1.7;">
        <li><strong>Model:</strong> <code><?= htmlspecialchars((string) ($result['api']['model'] ?? '')) ?></code></li>
        <li><strong>Stop reason:</strong> <code><?= htmlspecialchars((string) ($result['api']['stop_reason'] ?? '')) ?></code></li>
        <li><strong>Round-trip:</strong> <?= (int) ($result['api']['request_ms'] ?? 0) ?> ms</li>
        <li>
          <strong>Tokens:</strong>
          input <?= (int) ($result['api']['usage']['input_tokens']  ?? 0) ?>,
          output <?= (int) ($result['api']['usage']['output_tokens'] ?? 0) ?>
        </li>
        <li><strong>Input chars:</strong> <?= (int) ($result['input_chars'] ?? 0) ?></li>
        <li>
          <strong>JSON parse:</strong>
          <?php if ($result['parsed_json_ok'] ?? false): ?>
            <span style="color:var(--adm-success, #10b981);">parsed cleanly</span>
          <?php else: ?>
            <span style="color:var(--adm-danger, #ef4444);">failed to parse — see raw text below</span>
          <?php endif; ?>
        </li>
      </ul>

      <?php if ($result['parsed_json_ok'] ?? false):
        $j = $result['parsed_json'];
        $q = $j['question'] ?? null;
      ?>
        <h3 style="margin: 16px 0 8px; font-size: 15px;">Sample slide preview</h3>
        <div style="border:1px solid var(--adm-border, #2a2a2a); border-radius: 8px; padding: 16px; background: var(--adm-bg-elev, #18181b);">
          <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--adm-muted, #9ca3af); margin-bottom: 6px;">
            <?= htmlspecialchars((string) ($j['slide_type'] ?? 'concept')) ?>
          </div>
          <h4 style="margin: 0 0 10px; font-size: 18px;">
            <?= htmlspecialchars((string) ($j['title'] ?? '(no title)')) ?>
          </h4>
          <div style="font-size: 14px; line-height: 1.55; white-space: pre-wrap; margin-bottom: 12px;">
            <?= htmlspecialchars((string) ($j['body'] ?? '')) ?>
          </div>
          <?php if (!empty($j['key_point'])): ?>
            <div style="margin-top: 8px; font-size: 13px;">
              <strong>Memory hook:</strong> <?= htmlspecialchars((string) $j['key_point']) ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($j['ops_relevance'])): ?>
            <div style="margin-top: 4px; font-size: 13px;">
              <strong>Ops relevance:</strong> <?= htmlspecialchars((string) $j['ops_relevance']) ?>
            </div>
          <?php endif; ?>

          <?php if (is_array($q)): ?>
            <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--adm-border, #2a2a2a);">
              <div style="font-size: 13px; font-weight: 600; margin-bottom: 8px;">
                <?= htmlspecialchars((string) ($q['prompt'] ?? '')) ?>
              </div>
              <ol type="A" style="font-size: 13px; padding-left: 22px; margin: 0;">
                <?php foreach ((array) ($q['options'] ?? []) as $oi => $optText): ?>
                  <li style="margin: 4px 0; <?= ((int)($q['correct_index'] ?? -1) === (int)$oi) ? 'color: var(--adm-success, #10b981);' : '' ?>">
                    <?= htmlspecialchars((string) $optText) ?>
                    <?php if ((int)($q['correct_index'] ?? -1) === (int)$oi): ?>
                      &nbsp;✓ <em>correct</em>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ol>
              <?php if (!empty($q['explanation'])): ?>
                <div style="margin-top: 8px; font-size: 12px; color: var(--adm-muted, #9ca3af);">
                  <strong>Explanation:</strong> <?= htmlspecialchars((string) $q['explanation']) ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <h3 style="margin: 16px 0 8px; font-size: 15px;">Raw model output</h3>
      <pre style="background: #0b0b0d; color: #e5e7eb; padding: 12px; border-radius: 6px; font-size: 12px; overflow:auto; max-height: 360px; white-space: pre-wrap; word-break: break-word;"><?= htmlspecialchars((string) ($result['api']['text'] ?? '')) ?></pre>

    <?php else:
      $err     = (string) ($result['error']        ?? ($result['api']['error']        ?? 'unknown'));
      $detail  = (string) ($result['error_detail'] ?? ($result['api']['error_detail'] ?? ''));
      $http    = (int)    ($result['api']['http_code'] ?? 0);
    ?>
      <p style="color: var(--adm-danger, #ef4444); margin-top:0;">✗ Generation failed.</p>
      <ul style="margin:0; padding:0; list-style:none; font-size:13px; line-height:1.7;">
        <li><strong>Error:</strong> <code><?= htmlspecialchars($err) ?></code></li>
        <?php if ($detail !== ''): ?>
          <li><strong>Detail:</strong> <?= htmlspecialchars($detail) ?></li>
        <?php endif; ?>
        <?php if ($http): ?>
          <li><strong>HTTP:</strong> <?= (int) $http ?></li>
        <?php endif; ?>
      </ul>
      <?php if (!empty($result['extract_info']) && ($result['extract_info']['ok'] ?? true) === false): ?>
        <p style="margin-top:12px; font-size:12px; color: var(--adm-muted, #9ca3af);">
          PDF extract: <code><?= htmlspecialchars((string) $result['extract_info']['error']) ?></code>
          — <?= htmlspecialchars((string) ($result['extract_info']['error_detail'] ?? '')) ?>
        </p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
