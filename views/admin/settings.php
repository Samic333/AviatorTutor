<?php
declare(strict_types=1);
/**
 * Admin Settings page — runtime config for AI providers and platform.
 *
 * @var string $csrf_token
 * @var array  $ai_config       ['default_provider'=>..., 'providers'=>[...]]
 * @var array  $masked_keys     [setting_key => masked-tail-or-empty]
 * @var string $flash_ok
 * @var string $flash_error
 */

$providers = $ai_config['providers'] ?? [];
$defaultProvider = (string) ($ai_config['default_provider'] ?? 'anthropic');
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Settings</h1>
    <p class="adm-page-header__sub">AI provider keys, default provider, and model overrides — all editable here, no SSH needed.</p>
  </div>
</div>

<?php if (!empty($flash_ok)): ?>
  <div class="adm-panel" style="max-width:880px; border:1px solid #10b981; background:#10b98111;">
    <div class="adm-panel__body" style="color:#10b981;">✓ <?= htmlspecialchars($flash_ok) ?></div>
  </div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
  <div class="adm-panel" style="max-width:880px; border:1px solid #ef4444; background:#ef444411;">
    <div class="adm-panel__body" style="color:#ef4444;">✗ <?= htmlspecialchars($flash_error) ?></div>
  </div>
<?php endif; ?>

<form method="POST" action="/admin/settings/update">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

  <!-- Default provider -->
  <div class="adm-panel" style="max-width:880px;">
    <div class="adm-panel__header">
      <h2 class="adm-panel__title">Default AI provider</h2>
    </div>
    <div class="adm-panel__body">
      <p style="margin:0 0 12px; font-size:13px; color:#9ca3af;">
        Used when admin doesn't pick one explicitly on /admin/import or /admin/ai-test. Falls back to the first configured provider if the chosen one has no key.
      </p>
      <div class="adm-form-group">
        <select name="ai_default_provider" class="adm-select">
          <?php foreach ($providers as $name => $p): ?>
            <option value="<?= htmlspecialchars($name) ?>"
                    <?= $name === $defaultProvider ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['label']) ?>
              <?= $p['configured'] ? '— configured' : '— (key missing)' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <!-- Per-provider API keys + model overrides -->
  <?php
  $providerForms = [
    ['anthropic', 'Anthropic Claude', 'sk-ant-…',
      'https://console.anthropic.com/settings/keys',
      'claude-sonnet-4-5', 'claude-opus-4-5'],
    ['openai',    'OpenAI GPT', 'sk-…',
      'https://platform.openai.com/api-keys',
      'gpt-4.1-mini', 'gpt-4.1'],
    ['gemini',    'Google Gemini', 'AIza…',
      'https://aistudio.google.com/app/apikey',
      'gemini-2.5-flash', 'gemini-2.5-pro'],
  ];
  ?>
  <?php foreach ($providerForms as [$key, $label, $placeholder, $consoleUrl, $defChunk, $defOutline]):
    $apiKeySettingName = $key . '_api_key';
    $maskedTail = $masked_keys[$apiKeySettingName] ?? '';
    $isConfigured = $providers[$key]['configured'] ?? false;
    $modelChunkCurrent  = $providers[$key]['model_chunk']   ?? $defChunk;
    $modelOutlineCurrent = $providers[$key]['model_outline'] ?? $defOutline;
  ?>
  <div class="adm-panel" style="margin-top:16px; max-width:880px;">
    <div class="adm-panel__header">
      <h2 class="adm-panel__title"><?= htmlspecialchars($label) ?></h2>
      <span style="margin-left:auto; font-size:12px; padding:3px 10px; border-radius:999px;
                   background:<?= $isConfigured ? '#10b98122' : '#9ca3af22' ?>;
                   color:<?= $isConfigured ? '#10b981' : '#9ca3af' ?>;">
        <?= $isConfigured ? 'configured' : 'not set' ?>
      </span>
    </div>
    <div class="adm-panel__body">

      <div class="adm-form-group">
        <label class="adm-label">API key</label>
        <input type="password" name="<?= htmlspecialchars($apiKeySettingName) ?>"
               class="adm-input" autocomplete="new-password"
               placeholder="<?= $isConfigured && $maskedTail
                              ? 'Currently saved: ' . htmlspecialchars($maskedTail) . ' — paste a new key to replace, or leave blank to keep'
                              : ($isConfigured
                                  ? 'A key is loaded from config/app.local.php — paste here to override in DB'
                                  : 'Paste your ' . htmlspecialchars($placeholder) . ' key') ?>">
        <small style="display:block; margin-top:6px; color:#9ca3af;">
          Get a key: <a href="<?= htmlspecialchars($consoleUrl) ?>" target="_blank" rel="noopener" style="color:#3b82f6;"><?= htmlspecialchars($consoleUrl) ?></a><br>
          The key is stored in the <code>app_settings</code> table. Leave the field blank to keep the existing key. Tick the Clear box and Save to wipe it.
        </small>
        <?php if ($maskedTail !== ''): ?>
          <label style="display:block; margin-top:8px; font-size:12px; color:#ef4444;">
            <input type="checkbox" name="clear_keys[]" value="<?= htmlspecialchars($apiKeySettingName) ?>">
            Clear this saved key on save
          </label>
        <?php endif; ?>
      </div>

      <details style="margin-top:6px;">
        <summary style="cursor:pointer; font-size:13px; color:#9ca3af;">Advanced — model overrides</summary>
        <div style="margin-top:12px; padding-left:12px; border-left:2px solid #2a2a2a;">
          <div class="adm-form-group">
            <label class="adm-label">Chunk model (used for per-call generation)</label>
            <input type="text" name="<?= htmlspecialchars($key) ?>_model_chunk"
                   class="adm-input" value="<?= htmlspecialchars($modelChunkCurrent) ?>">
            <small style="display:block; margin-top:6px; color:#9ca3af;">Default: <code><?= htmlspecialchars($defChunk) ?></code></small>
          </div>
          <div class="adm-form-group">
            <label class="adm-label">Outline model (Phase 3+ chunked-PDF outline pass)</label>
            <input type="text" name="<?= htmlspecialchars($key) ?>_model_outline"
                   class="adm-input" value="<?= htmlspecialchars($modelOutlineCurrent) ?>">
            <small style="display:block; margin-top:6px; color:#9ca3af;">Default: <code><?= htmlspecialchars($defOutline) ?></code></small>
          </div>
        </div>
      </details>
    </div>
  </div>
  <?php endforeach; ?>

  <div class="adm-panel" style="margin-top:16px; max-width:880px;">
    <div class="adm-panel__body" style="display:flex; gap:12px; align-items:center;">
      <button type="submit" class="adm-btn adm-btn--primary">Save settings</button>
      <a href="/admin/ai-test" class="adm-btn adm-btn--ghost">Test a provider →</a>
    </div>
  </div>
</form>

<div class="adm-panel" style="margin-top:24px; max-width:880px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title" style="font-size:14px;">How key resolution works</h2>
  </div>
  <div class="adm-panel__body">
    <ol style="margin:0; padding-left:18px; font-size:13px; line-height:1.7; color:#cbd5e1;">
      <li>If a key is saved here in the dashboard, it wins.</li>
      <li>Otherwise, fall back to <code>config/app.local.php</code> on the server.</li>
      <li>Otherwise, the provider is treated as unconfigured.</li>
    </ol>
    <p style="margin-top:12px; font-size:12px; color:#9ca3af;">
      Stored values never leave the server. The masked tail (<code>••••XXXX</code>) above is the only preview shown after save — paste a fresh key to replace, or use the Clear box to wipe.
    </p>
  </div>
</div>
