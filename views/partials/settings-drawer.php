<?php
declare(strict_types=1);
/**
 * Phase 3 — settings drawer. Right-side slide-out (bottom sheet on mobile)
 * exposing every reading preference. Persists per-account via
 * /api/settings/update; mirrored to localStorage for FOUC-free first paint.
 *
 * Vars in scope:
 *   $userSettings  array  current reading prefs (defaults if not loaded)
 *   $csrf_token    string CSRF token for the API call
 */

$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$us = is_array($userSettings ?? null) ? $userSettings : \App\Services\UserSettings::DEFAULTS;
$csrf = (string) ($csrf_token ?? \App\Core\CSRF::generate());
?>
<aside class="settings-drawer" id="settings-drawer" aria-label="Reading settings" hidden>
  <div class="settings-drawer-backdrop" data-settings-close></div>
  <div class="settings-drawer-panel" role="dialog" aria-labelledby="settings-drawer-title">
    <header class="settings-drawer-head">
      <h2 id="settings-drawer-title" style="margin:0;font-size:16px;font-weight:700;">Reading settings</h2>
      <button type="button" class="sc-iconbtn" data-settings-close aria-label="Close settings">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </header>

    <form id="settings-form" data-csrf="<?= $h($csrf) ?>" autocomplete="off">

      <fieldset class="sd-group">
        <legend>Theme</legend>
        <div class="sd-grid sd-grid-3">
          <?php foreach (['dark'=>'Dark','light'=>'Light','sepia'=>'Sepia','high-contrast'=>'High contrast','blue-light'=>'Blue-light filter','solarized'=>'Solarized'] as $key => $label): ?>
            <label class="sd-tile">
              <input type="radio" name="theme" value="<?= $h($key) ?>" <?= $us['theme'] === $key ? 'checked' : '' ?>>
              <span><?= $h($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset class="sd-group">
        <legend>Font size</legend>
        <div class="sd-grid sd-grid-5">
          <?php foreach (['xs'=>'XS','s'=>'S','m'=>'M','l'=>'L','xl'=>'XL'] as $key => $label): ?>
            <label class="sd-tile">
              <input type="radio" name="font_size" value="<?= $h($key) ?>" <?= $us['font_size'] === $key ? 'checked' : '' ?>>
              <span><?= $h($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset class="sd-group">
        <legend>Font family</legend>
        <div class="sd-grid sd-grid-3">
          <?php foreach (['system'=>'System','serif'=>'Serif','dyslexic'=>'OpenDyslexic'] as $key => $label): ?>
            <label class="sd-tile">
              <input type="radio" name="font_family" value="<?= $h($key) ?>" <?= $us['font_family'] === $key ? 'checked' : '' ?>>
              <span><?= $h($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset class="sd-group">
        <legend>Line spacing</legend>
        <div class="sd-grid sd-grid-3">
          <?php foreach (['tight'=>'Tight','normal'=>'Normal','loose'=>'Loose'] as $key => $label): ?>
            <label class="sd-tile">
              <input type="radio" name="line_spacing" value="<?= $h($key) ?>" <?= $us['line_spacing'] === $key ? 'checked' : '' ?>>
              <span><?= $h($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset class="sd-group">
        <legend>Reading width</legend>
        <div class="sd-grid sd-grid-3">
          <?php foreach (['narrow'=>'Narrow','medium'=>'Medium','wide'=>'Wide'] as $key => $label): ?>
            <label class="sd-tile">
              <input type="radio" name="reading_width" value="<?= $h($key) ?>" <?= $us['reading_width'] === $key ? 'checked' : '' ?>>
              <span><?= $h($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset class="sd-group">
        <legend>Motion & audio</legend>
        <label class="sd-toggle">
          <input type="checkbox" name="reduced_motion" value="1" <?= !empty($us['reduced_motion']) ? 'checked' : '' ?>>
          <span>Reduce motion (disable auto-hide chrome)</span>
        </label>
        <div class="sd-grid sd-grid-2" style="margin-top:10px;">
          <?php foreach (['us'=>'American','uk'=>'British'] as $key => $label): ?>
            <label class="sd-tile">
              <input type="radio" name="audio_accent" value="<?= $h($key) ?>" <?= $us['audio_accent'] === $key ? 'checked' : '' ?>>
              <span><?= $h($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <p class="sd-status" id="settings-status" aria-live="polite"></p>
    </form>
  </div>
</aside>
