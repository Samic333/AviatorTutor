<?php
declare(strict_types=1);
/** @var array $airline */
/** @var array $aviation */
/** @var string $aircraftSlug */
/** @var string $csrf_token */

$tile = function(array $p): string {
    $slug   = (string) $p['slug'];
    $name   = (string) ($p['name'] ?? $slug);
    $blurb  = (string) ($p['short_blurb'] ?? '');
    $color  = (string) ($p['color_hex']   ?? '#38BDF8');
    return '<label style="position:relative;display:block;padding:14px 14px 14px 42px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:10px;cursor:pointer;">
      <input type="checkbox" name="subjects[]" value="' . htmlspecialchars($slug) . '"
             style="position:absolute;top:16px;left:14px;accent-color:' . htmlspecialchars($color) . ';">
      <div style="font-family:DM Sans,Inter,sans-serif;font-size:13.5px;font-weight:600;color:#F1F5F9;margin-bottom:2px;">' . htmlspecialchars($name) . '</div>
      <div style="font-size:11.5px;color:#94A3B8;line-height:1.5;">' . htmlspecialchars($blurb) . '</div>
    </label>';
};
?>
<section style="min-height:100vh;padding:48px 16px;background:linear-gradient(135deg, #0F172A 0%, #1E293B 100%);">
  <div style="max-width:1080px;margin:0 auto;">
    <div style="text-align:center;margin-bottom:32px;">
      <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 14px;background:rgba(56,189,248,0.12);color:#38BDF8;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;margin-bottom:16px;">
        Step 2 of 2
      </div>
      <h1 style="margin:0 0 8px;font-family:'DM Sans',Inter,sans-serif;font-size:32px;font-weight:700;color:#F1F5F9;">What else are you studying?</h1>
      <p style="margin:0;color:#94A3B8;font-size:15px;max-width:600px;margin-left:auto;margin-right:auto;line-height:1.55;">Pick any airline interview prep packs and aviation subject packs you're interested in. We'll suggest these on your pricing page.</p>
    </div>

    <form method="post" action="/onboarding/subjects">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <?php if (!empty($airline)): ?>
        <h2 style="margin:0 0 14px;font-family:'DM Sans',Inter,sans-serif;font-size:16px;font-weight:700;color:#F1F5F9;">
          <span style="color:#38BDF8;">✈</span>&nbsp; Airline interview prep <span style="color:#94A3B8;font-weight:500;">($19 each)</span>
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;margin-bottom:32px;">
          <?php foreach ($airline as $p) echo $tile($p); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($aviation)): ?>
        <h2 style="margin:0 0 14px;font-family:'DM Sans',Inter,sans-serif;font-size:16px;font-weight:700;color:#F1F5F9;">
          <span style="color:#C9A84C;">★</span>&nbsp; Aviation subject packs <span style="color:#94A3B8;font-weight:500;">($14 each)</span>
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;margin-bottom:32px;">
          <?php foreach ($aviation as $p) echo $tile($p); ?>
        </div>
      <?php endif; ?>

      <?php if (empty($airline) && empty($aviation)): ?>
        <div style="padding:32px;background:rgba(201,168,76,0.06);border:1px solid rgba(201,168,76,0.2);border-radius:12px;text-align:center;color:#C9A84C;margin-bottom:24px;">
          Subject catalog not yet seeded. Run the Phase&nbsp;4 database migration to populate subjects.
        </div>
      <?php endif; ?>

      <div style="display:flex;gap:10px;justify-content:space-between;align-items:center;border-top:1px solid rgba(255,255,255,0.08);padding-top:24px;">
        <a href="/onboarding/aircraft" style="color:#94A3B8;font-size:13px;text-decoration:none;">← Back</a>
        <div style="display:flex;gap:10px;">
          <a href="/dashboard" class="btn" style="background:rgba(255,255,255,0.06);color:#E2E8F0;border:1px solid rgba(255,255,255,0.1);">Skip</a>
          <button type="submit" class="btn btn-primary btn-lg" style="min-width:200px;">Continue to pricing</button>
        </div>
      </div>
    </form>
  </div>
</section>
