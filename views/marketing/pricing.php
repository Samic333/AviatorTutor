<?php
declare(strict_types=1);
/** @var array $aircraftPacks */
/** @var array $airlinePacks */
/** @var array $subjectPacks */
/** @var array $userPurchases */ // map of slug => true
/** @var array $selectedSlugs */ // map of slug => true (from onboarding)
/** @var bool $catalogAvailable */
/** @var bool $isAuthenticated */

$renderTile = function(array $p) use ($userPurchases, $selectedSlugs, $isAuthenticated): string {
    $slug   = (string) $p['slug'];
    $name   = (string) ($p['name']        ?? $slug);
    $blurb  = (string) ($p['short_blurb'] ?? '');
    $price  = (float)  ($p['price_usd']   ?? 0);
    $coming = !empty($p['is_coming_soon']);
    $color  = (string) ($p['color_hex']   ?? '#38BDF8');
    $owned  = !empty($userPurchases[$slug]);
    $picked = !empty($selectedSlugs[$slug]);

    $border = $picked ? "border-color:{$color};box-shadow:0 0 0 2px {$color}33;" : '';

    $cta = '';
    if ($owned) {
        $cta = '<a href="/dashboard" style="display:block;padding:10px;background:rgba(34,197,94,0.12);color:#22C55E;text-align:center;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">✓ You own this</a>';
    } elseif ($coming) {
        $cta = '<a href="/coming-soon/' . htmlspecialchars($slug) . '" style="display:block;padding:10px;background:rgba(201,168,76,0.12);color:#C9A84C;text-align:center;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">Join waitlist</a>';
    } elseif ($isAuthenticated) {
        $cta = '<a href="/checkout/' . htmlspecialchars($slug) . '" class="btn btn-primary" style="width:100%;text-align:center;">Buy — $' . number_format($price, 0) . '</a>';
    } else {
        $cta = '<a href="/register" class="btn btn-primary" style="width:100%;text-align:center;">Sign up — $' . number_format($price, 0) . '</a>';
    }

    return '<div style="position:relative;padding:18px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;display:flex;flex-direction:column;gap:10px;' . $border . '">'
        . ($picked ? '<span style="position:absolute;top:-10px;left:14px;background:' . $color . ';color:#0F172A;font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.04em;">Your pick</span>' : '')
        . '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">'
        . '<div style="width:36px;height:36px;border-radius:9px;background:' . $color . '22;color:' . $color . ';display:flex;align-items:center;justify-content:center;flex-shrink:0;">'
        . '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'
        . '</div>'
        . '<span style="font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.04em;'
        . ($coming ? 'color:#C9A84C;background:rgba(201,168,76,0.12);' : 'color:#22C55E;background:rgba(34,197,94,0.12);')
        . '">' . ($coming ? 'Coming soon' : 'Live') . '</span>'
        . '</div>'
        . '<h3 style="margin:0;font-family:DM Sans,Inter,sans-serif;font-size:14.5px;font-weight:700;color:#F1F5F9;">' . htmlspecialchars($name) . '</h3>'
        . ($blurb !== '' ? '<p style="margin:0;font-size:12.5px;color:#94A3B8;line-height:1.5;flex:1;">' . htmlspecialchars($blurb) . '</p>' : '')
        . '<div style="margin-top:auto;">' . $cta . '</div>'
        . '</div>';
};
?>
<style>
.pricing-section { padding: 32px 0; }
.pricing-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:14px; }
.pricing-tier-head { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
.pricing-tier-head h2 { margin:0; font-family:'DM Sans',Inter,sans-serif; font-size:22px; font-weight:700; color:#F1F5F9; }
.pricing-tier-head p { margin:4px 0 0; color:#94A3B8; font-size:13.5px; }
.pricing-tier-price { font-family:'DM Sans',Inter,sans-serif; font-size:28px; font-weight:700; color:#38BDF8; line-height:1; }
.pricing-tier-price small { font-size:13px; color:#94A3B8; font-weight:500; margin-left:6px; }
.pricing-hero { padding:64px 0 32px; text-align:center; background:linear-gradient(180deg, rgba(56,189,248,0.06) 0%, transparent 100%); }
.pricing-hero h1 { margin:0 0 12px; font-family:'DM Sans',Inter,sans-serif; font-size:42px; font-weight:700; color:#F1F5F9; letter-spacing:-0.02em; }
.pricing-hero p { margin:0 auto; max-width:580px; color:#94A3B8; font-size:16px; line-height:1.6; }
@media (max-width: 720px) { .pricing-hero h1 { font-size:30px; } .pricing-tier-head h2 { font-size:18px; } }
</style>

<section class="pricing-hero">
  <div class="container">
    <h1>Pay for what you study.</h1>
    <p>One purchase per pack — lifetime access, no recurring fees. Aircraft systems, airline interview prep, and aviation subjects.</p>
  </div>
</section>

<div class="container" style="padding:32px 16px 64px;">

  <?php if (!$catalogAvailable): ?>
    <div style="padding:32px;background:rgba(201,168,76,0.06);border:1px solid rgba(201,168,76,0.2);border-radius:12px;text-align:center;color:#C9A84C;max-width:560px;margin:32px auto;">
      <p style="margin:0 0 12px;"><strong>Catalog setup pending.</strong></p>
      <p style="margin:0;font-size:13.5px;">Run the Phase&nbsp;4 database migration to seed all 38 study packs.</p>
    </div>
    <div style="text-align:center;">
      <a href="/redeem" class="btn btn-primary btn-lg">Have an activation code? Redeem here →</a>
    </div>
  <?php else: ?>

    <!-- Aircraft packs -->
    <?php if (!empty($aircraftPacks)): ?>
      <section class="pricing-section">
        <div class="pricing-tier-head">
          <div>
            <h2>Aircraft Study Packs</h2>
            <p>Type-specific systems study with flashcards, quizzes, and progress tracking.</p>
          </div>
          <div class="pricing-tier-price">$29 <small>/ pack</small></div>
        </div>
        <div class="pricing-grid">
          <?php foreach ($aircraftPacks as $p) echo $renderTile($p); ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- Airline interview packs -->
    <?php if (!empty($airlinePacks)): ?>
      <section class="pricing-section">
        <div class="pricing-tier-head">
          <div>
            <h2>Airline Interview Prep</h2>
            <p>Tech, HR, and sim profile prep for the world's biggest carriers.</p>
          </div>
          <div class="pricing-tier-price">$19 <small>/ pack</small></div>
        </div>
        <div class="pricing-grid">
          <?php foreach ($airlinePacks as $p) echo $renderTile($p); ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- Aviation subject packs -->
    <?php if (!empty($subjectPacks)): ?>
      <section class="pricing-section">
        <div class="pricing-tier-head">
          <div>
            <h2>Aviation Subject Packs</h2>
            <p>ICAO and JAR/FAR aligned modules: weather, performance, CRM, SMS, DGR, navigation, and more.</p>
          </div>
          <div class="pricing-tier-price">$14 <small>/ pack</small></div>
        </div>
        <div class="pricing-grid">
          <?php foreach ($subjectPacks as $p) echo $renderTile($p); ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- FAQ / activation code prompt -->
    <section style="margin-top:48px;padding:32px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:14px;display:grid;grid-template-columns:1fr 1fr;gap:24px;">
      <div>
        <h3 style="margin:0 0 8px;font-family:'DM Sans',Inter,sans-serif;font-size:15px;font-weight:700;color:#F1F5F9;">Have an activation code?</h3>
        <p style="margin:0 0 14px;color:#94A3B8;font-size:13.5px;line-height:1.6;">If you bought a pack from a partner or distributor, redeem the code to unlock it on your account.</p>
        <a href="/redeem" class="btn" style="background:rgba(56,189,248,0.12);color:#38BDF8;border:1px solid rgba(56,189,248,0.3);">Redeem code</a>
      </div>
      <div>
        <h3 style="margin:0 0 8px;font-family:'DM Sans',Inter,sans-serif;font-size:15px;font-weight:700;color:#F1F5F9;">Want a custom bundle?</h3>
        <p style="margin:0 0 14px;color:#94A3B8;font-size:13.5px;line-height:1.6;">Schools and airlines can purchase volume bundles with shared activation codes. Get in touch for a quote.</p>
        <a href="/contact" class="btn" style="background:rgba(201,168,76,0.12);color:#C9A84C;border:1px solid rgba(201,168,76,0.3);">Contact us</a>
      </div>
    </section>

  <?php endif; ?>
</div>
