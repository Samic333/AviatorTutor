<?php
declare(strict_types=1);
/** @var array $aircraftPacks */
/** @var string $csrf_token */
?>
<section style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:48px 16px;background:linear-gradient(135deg, #0F172A 0%, #1E293B 100%);">
  <div style="max-width:920px;width:100%;">
    <div style="text-align:center;margin-bottom:32px;">
      <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 14px;background:rgba(56,189,248,0.12);color:#38BDF8;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;margin-bottom:16px;">
        Step 1 of 2
      </div>
      <h1 style="margin:0 0 8px;font-family:'DM Sans',Inter,sans-serif;font-size:32px;font-weight:700;color:#F1F5F9;">Which aircraft do you fly?</h1>
      <p style="margin:0;color:#94A3B8;font-size:15px;max-width:560px;margin-left:auto;margin-right:auto;line-height:1.55;">Pick the type you're studying. You can buy access later — this just tailors what we show on your dashboard.</p>
    </div>

    <form method="post" action="/onboarding/aircraft">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));gap:14px;margin-bottom:28px;">
        <?php foreach ($aircraftPacks as $p): ?>
          <?php
            $slug      = (string) $p['slug'];
            $name      = (string) ($p['name'] ?? $slug);
            $blurb     = (string) ($p['short_blurb'] ?? '');
            $price     = (float)  ($p['price_usd']   ?? 29);
            $coming    = !empty($p['is_coming_soon']);
            $color     = (string) ($p['color_hex']   ?? '#38BDF8');
          ?>
          <label style="position:relative;display:block;padding:20px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;cursor:pointer;transition:all .15s ease;">
            <input type="radio" name="subject_slug" value="<?= htmlspecialchars($slug) ?>"
                   style="position:absolute;top:16px;right:16px;accent-color:<?= htmlspecialchars($color) ?>;"
                   <?= $coming ? '' : 'required' ?>>
            <div style="width:38px;height:38px;border-radius:10px;background:<?= htmlspecialchars($color) ?>22;color:<?= htmlspecialchars($color) ?>;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            </div>
            <h3 style="margin:0 0 4px;font-family:'DM Sans',Inter,sans-serif;font-size:15px;font-weight:700;color:#F1F5F9;"><?= htmlspecialchars($name) ?></h3>
            <?php if ($blurb !== ''): ?>
              <p style="margin:0 0 10px;font-size:12.5px;color:#94A3B8;line-height:1.5;"><?= htmlspecialchars($blurb) ?></p>
            <?php endif; ?>
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <span style="font-size:14px;font-weight:600;color:#F1F5F9;">$<?= number_format($price, 0) ?></span>
              <?php if ($coming): ?>
                <span style="font-size:10px;font-weight:700;color:#C9A84C;background:rgba(201,168,76,0.12);padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.04em;">Coming soon</span>
              <?php else: ?>
                <span style="font-size:10px;font-weight:700;color:#22C55E;background:rgba(34,197,94,0.12);padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.04em;">Live</span>
              <?php endif; ?>
            </div>
          </label>
        <?php endforeach; ?>
      </div>

      <div style="display:flex;gap:10px;justify-content:space-between;align-items:center;">
        <a href="/dashboard" style="color:#94A3B8;font-size:13px;text-decoration:none;">Skip for now →</a>
        <button type="submit" class="btn btn-primary btn-lg" style="min-width:200px;">Continue</button>
      </div>
    </form>
  </div>
</section>
