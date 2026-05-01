<?php
declare(strict_types=1);
/**
 * Admin AI Smoke Test page.
 *
 * @var string      $csrf_token
 * @var array       $ai_config           ['default_provider'=>..., 'providers'=>[name=>{configured,label,model_chunk,model_outline,...}, ...]]
 * @var string|null $pdftotext_path
 * @var array|null  $result
 * @var bool        $submitted
 * @var string      $source_label
 * @var string      $pasted_text
 * @var string      $selected_provider
 */

$providers = $ai_config['providers'] ?? [];
$anyConfigured = false;
foreach ($providers as $p) { if ($p['configured']) { $anyConfigured = true; break; } }
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">AI Smoke Test</h1>
    <p class="adm-page-header__sub">Phase 6 — multi-provider. Paste an excerpt, pick a provider, get one sample slide back.</p>
  </div>
</div>

<div class="adm-panel" style="max-width: 920px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Providers</h2>
  </div>
  <div class="adm-panel__body">
    <table style="width:100%; font-size:13px; border-collapse:collapse;">
      <thead><tr style="text-align:left;">
        <th style="padding:6px 10px; border-bottom:1px solid #2a2a2a;">Provider</th>
        <th style="padding:6px 10px; border-bottom:1px solid #2a2a2a;">Status</th>
        <th style="padding:6px 10px; border-bottom:1px solid #2a2a2a;">Default model</th>
        <th style="padding:6px 10px; border-bottom:1px solid #2a2a2a;">Outline model</th>
      </tr></thead>
      <tbody>
        <?php foreach ($providers as $name => $p): ?>
        <tr style="border-bottom:1px solid #1f1f23;">
          <td style="padding:8px 10px;">
            <strong><?= htmlspecialchars($p['label']) ?></strong>
            <?php if ($name === ($ai_config['default_provider'] ?? '')): ?>
              <span style="font-size:10px; padding:1px 6px; background:#3b82f622; color:#3b82f6; border-radius:999px; margin-left:6px;">DEFAULT</span>
            <?php endif; ?>
          </td>
          <td style="padding:8px 10px;">
            <?php if ($p['configured']): ?>
              <span style="color:#10b981;">configured</span>
            <?php else: ?>
              <span style="color:#9ca3af;">api key missing</span>
            <?php endif; ?>
          </td>
          <td style="padding:8px 10px;"><code><?= htmlspecialchars($p['model_chunk']) ?></code></td>
          <td style="padding:8px 10px;"><code><?= htmlspecialchars($p['model_outline']) ?></code></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p style="margin:14px 0 0; font-size:12px; color:#9ca3af;">
      <strong>PDF extract:</strong>
      <?php if (!empty($pdftotext_path)): ?>
        pdftotext at <code><?= htmlspecialchars($pdftotext_path) ?></code>
      <?php else: ?>
        Smalot/PdfParser via Composer (preferred). pdftotext not installed.
      <?php endif; ?>
    </p>
  </div>
</div>

<div class="adm-panel" style="margin-top: 16px; max-width: 920px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Generate one sample slide</h2>
  </div>
  <div class="adm-panel__body">
    <form method="POST" enctype="multipart/form-data" action="/admin/ai-test/run">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div class="adm-form-group">
        <label class="adm-label">Provider</label>
        <select name="provider" class="adm-select">
          <?php foreach ($providers as $name => $p): ?>
            <option value="<?= htmlspecialchars($name) ?>"
                    <?= $name === $selected_provider ? 'selected' : '' ?>
                    <?= !$p['configured'] ? 'disabled' : '' ?>>
              <?= htmlspecialchars($p['label']) ?>
              — <?= htmlspecialchars($p['model_chunk']) ?>
              <?= !$p['configured'] ? ' (key missing)' : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Source label (optional)</label>
        <input type="text" name="source_label" class="adm-input"
               value="<?= htmlspecialchars($source_label) ?>"
               placeholder="e.g. Q400-Hydraulic_Power.pdf, p4-6">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">PDF file (optional)</label>
        <input type="file" name="pdf_file" class="adm-input" accept=".pdf">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Or paste an excerpt directly</label>
        <textarea name="pasted_text" class="adm-input" rows="10"
                  placeholder="Paste a section of a Q400 system manual here…"
                  style="font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 13px;"><?= htmlspecialchars($pasted_text) ?></textarea>
      </div>

      <button type="submit" class="adm-btn adm-btn--primary" <?= $anyConfigured ? '' : 'disabled' ?>>
        Generate sample slide
      </button>
      <?php if (!$anyConfigured): ?>
        <small style="display:block; margin-top:8px; color:#ef4444;">
          Set at least one of <code>anthropic_api_key</code>, <code>openai_api_key</code>, or <code>gemini_api_key</code> in <code>config/app.local.php</code>.
        </small>
      <?php endif; ?>
    </form>
  </div>
</div>

<?php if ($submitted && is_array($result)): ?>
<div class="adm-panel" style="margin-top: 16px; max-width: 920px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Result</h2>
  </div>
  <div class="adm-panel__body">
    <?php if (($result['ok'] ?? false) === true): ?>
      <p style="color: #10b981; margin-top:0;">✓ Generation succeeded.</p>
      <ul style="margin:0 0 16px; padding:0; list-style:none; font-size:13px; line-height:1.7;">
        <li><strong>Provider:</strong> <code><?= htmlspecialchars((string) ($result['api']['provider'] ?? '')) ?></code></li>
        <li><strong>Model:</strong> <code><?= htmlspecialchars((string) ($result['api']['model'] ?? '')) ?></code></li>
        <li><strong>Stop reason:</strong> <code><?= htmlspecialchars((string) ($result['api']['stop_reason'] ?? '')) ?></code></li>
        <li><strong>Round-trip:</strong> <?= (int) ($result['api']['request_ms'] ?? 0) ?> ms</li>
        <li><strong>Tokens:</strong>
          input <?= (int) ($result['api']['usage']['input_tokens']  ?? 0) ?>,
          output <?= (int) ($result['api']['usage']['output_tokens'] ?? 0) ?>
        </li>
        <li><strong>Input chars:</strong> <?= (int) ($result['input_chars'] ?? 0) ?></li>
        <li><strong>JSON parse:</strong>
          <?php if ($result['parsed_json_ok'] ?? false): ?>
            <span style="color:#10b981;">parsed cleanly</span>
          <?php else: ?>
            <span style="color:#ef4444;">failed to parse — see raw text</span>
          <?php endif; ?>
        </li>
      </ul>

      <?php if ($result['parsed_json_ok'] ?? false):
        $j = $result['parsed_json']; $q = $j['question'] ?? null;
      ?>
        <h3 style="margin: 16px 0 8px; font-size: 15px;">Sample slide preview</h3>
        <div style="border:1px solid #2a2a2a; border-radius: 8px; padding: 16px; background:#18181b;">
          <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; color:#9ca3af; margin-bottom: 6px;">
            <?= htmlspecialchars((string) ($j['slide_type'] ?? 'concept')) ?>
          </div>
          <h4 style="margin: 0 0 10px; font-size: 18px;"><?= htmlspecialchars((string) ($j['title'] ?? '(no title)')) ?></h4>
          <div style="font-size: 14px; line-height: 1.55; white-space: pre-wrap; margin-bottom: 12px;">
            <?= htmlspecialchars((string) ($j['body'] ?? '')) ?>
          </div>
          <?php if (!empty($j['key_point'])): ?>
            <div style="margin-top:8px; font-size:13px;"><strong>Memory hook:</strong> <?= htmlspecialchars((string) $j['key_point']) ?></div>
          <?php endif; ?>
          <?php if (!empty($j['ops_relevance'])): ?>
            <div style="margin-top:4px; font-size:13px;"><strong>Ops relevance:</strong> <?= htmlspecialchars((string) $j['ops_relevance']) ?></div>
          <?php endif; ?>
          <?php if (is_array($q)): ?>
            <div style="margin-top:14px; padding-top:12px; border-top:1px dashed #2a2a2a;">
              <div style="font-size:13px; font-weight:600; margin-bottom:8px;">
                <?= htmlspecialchars((string) ($q['prompt'] ?? '')) ?>
              </div>
              <ol type="A" style="font-size:13px; padding-left:22px; margin:0;">
                <?php foreach ((array) ($q['options'] ?? []) as $oi => $optText): ?>
                  <li style="margin:4px 0; <?= ((int)($q['correct_index'] ?? -1) === (int)$oi) ? 'color:#10b981;' : '' ?>">
                    <?= htmlspecialchars((string) $optText) ?>
                    <?php if ((int)($q['correct_index'] ?? -1) === (int)$oi): ?>&nbsp;✓ <em>correct</em><?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ol>
              <?php if (!empty($q['explanation'])): ?>
                <div style="margin-top:8px; font-size:12px; color:#9ca3af;"><strong>Explanation:</strong> <?= htmlspecialchars((string) $q['explanation']) ?></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <h3 style="margin:16px 0 8px; font-size:15px;">Raw model output</h3>
      <pre style="background:#0b0b0d; color:#e5e7eb; padding:12px; border-radius:6px; font-size:12px; overflow:auto; max-height:360px; white-space:pre-wrap; word-break:break-word;"><?= htmlspecialchars((string) ($result['api']['text'] ?? '')) ?></pre>

    <?php else:
      $err     = (string) ($result['error']        ?? ($result['api']['error']        ?? 'unknown'));
      $detail  = (string) ($result['error_detail'] ?? ($result['api']['error_detail'] ?? ''));
      $http    = (int)    ($result['api']['http_code'] ?? 0);
    ?>
      <p style="color:#ef4444; margin-top:0;">✗ Generation failed.</p>
      <ul style="margin:0; padding:0; list-style:none; font-size:13px; line-height:1.7;">
        <li><strong>Error:</strong> <code><?= htmlspecialchars($err) ?></code></li>
        <?php if ($detail !== ''): ?><li><strong>Detail:</strong> <?= htmlspecialchars($detail) ?></li><?php endif; ?>
        <?php if ($http): ?><li><strong>HTTP:</strong> <?= (int) $http ?></li><?php endif; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
