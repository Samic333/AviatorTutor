<?php
declare(strict_types=1);
/** @var bool $isAuthenticated */
$isAuthenticated = $isAuthenticated ?? false;
$tiers = require __DIR__ . '/../../config/pricing.php';
?>
<style>
.pricing-hero { padding:64px 0 24px; text-align:center; background:linear-gradient(180deg, rgba(56,189,248,0.06) 0%, transparent 100%); }
.pricing-hero h1 { margin:0 0 12px; font-family:'DM Sans',Inter,sans-serif; font-size:42px; font-weight:700; color:#F1F5F9; letter-spacing:-0.02em; }
.pricing-hero p { margin:0 auto; max-width:640px; color:#94A3B8; font-size:16px; line-height:1.6; }
.pricing-tier-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px; max-width:1200px; margin:32px auto 0; }
.pricing-tier {
  display:flex; flex-direction:column; gap:14px;
  padding:24px; background:rgba(255,255,255,0.03);
  border:1px solid rgba(255,255,255,0.08); border-radius:14px;
}
.pricing-tier--highlight {
  border-color:rgba(56,189,248,0.45);
  box-shadow:0 0 0 1px rgba(56,189,248,0.32), 0 16px 40px -20px rgba(56,189,248,0.4);
  background:linear-gradient(180deg, rgba(56,189,248,0.06), rgba(255,255,255,0.03));
}
.pricing-tier__badge {
  display:inline-block; align-self:flex-start;
  padding:4px 10px; border-radius:999px;
  background:rgba(148,163,184,0.12); color:#CBD5E1;
  font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;
}
.pricing-tier--highlight .pricing-tier__badge { background:rgba(56,189,248,0.18); color:#38BDF8; }
.pricing-tier__name { margin:0; font-family:'DM Sans',Inter,sans-serif; font-size:18px; font-weight:700; color:#F1F5F9; }
.pricing-tier__blurb { margin:0; color:#94A3B8; font-size:13.5px; line-height:1.55; flex:1; }
.pricing-tier__price { display:flex; align-items:baseline; gap:6px; flex-wrap:wrap; padding-top:6px; }
.pricing-tier__price-amount { font-family:'DM Sans',Inter,sans-serif; font-size:28px; font-weight:700; color:#F1F5F9; line-height:1; }
.pricing-tier__price-suffix { font-size:13px; color:#94A3B8; }
.pricing-tier__features { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px; font-size:13px; color:#CBD5E1; }
.pricing-tier__features li { display:flex; gap:8px; align-items:flex-start; }
.pricing-tier__features li::before { content:""; flex-shrink:0; width:14px; height:14px; margin-top:2px; border-radius:50%; background:rgba(56,189,248,0.18); display:inline-flex; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px rgba(56,189,248,0.45); }
.pricing-tier__cta { margin-top:auto; }
.pricing-bottom { margin-top:48px; padding:32px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:14px; display:grid; grid-template-columns:1fr 1fr; gap:24px; }
.pricing-bottom h3 { margin:0 0 8px; font-family:'DM Sans',Inter,sans-serif; font-size:15px; font-weight:700; color:#F1F5F9; }
.pricing-bottom p { margin:0 0 14px; color:#94A3B8; font-size:13.5px; line-height:1.6; }
@media (max-width: 720px) {
  .pricing-hero h1 { font-size:30px; }
  .pricing-bottom { grid-template-columns:1fr; }
}
</style>

<section class="pricing-hero">
  <div class="container">
    <h1>Plans for every aviation learner.</h1>
    <p>From a free preview through to multi-seat instructor and organisation licences. We're locking in launch pricing now &mdash; join the waitlist for the tier that fits you and we'll email you the day it goes live. Activation codes from training partners work today.</p>
  </div>
</section>

<div class="container" style="padding:32px 16px 64px;">
  <div class="pricing-tier-grid">
    <?php foreach ($tiers as $tier): ?>
      <div class="pricing-tier<?= !empty($tier['highlight']) ? ' pricing-tier--highlight' : '' ?>">
        <span class="pricing-tier__badge"><?= htmlspecialchars((string) $tier['badge']) ?></span>
        <h2 class="pricing-tier__name"><?= htmlspecialchars((string) $tier['name']) ?></h2>
        <p class="pricing-tier__blurb"><?= $tier['blurb'] /* allowed: tier copy is trusted config */ ?></p>
        <div class="pricing-tier__price">
          <span class="pricing-tier__price-amount"><?= htmlspecialchars((string) $tier['price_label']) ?></span>
          <?php if (!empty($tier['price_suffix'])): ?>
            <span class="pricing-tier__price-suffix"><?= $tier['price_suffix'] ?></span>
          <?php endif; ?>
        </div>
        <?php if (!empty($tier['features']) && is_array($tier['features'])): ?>
          <ul class="pricing-tier__features">
            <?php foreach ($tier['features'] as $f): ?>
              <li><?= $f ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <a href="<?= htmlspecialchars((string) $tier['cta_url']) ?>" class="btn <?= !empty($tier['highlight']) ? 'btn-primary' : 'btn-ghost' ?> pricing-tier__cta"><?= htmlspecialchars((string) $tier['cta_label']) ?></a>
      </div>
    <?php endforeach; ?>
  </div>

  <section class="pricing-bottom">
    <div>
      <h3>Have an activation code?</h3>
      <p>Partners, training organisations, and early customers receive activation codes that unlock full access. Redeem yours to bypass the waitlist.</p>
      <a href="/redeem" class="btn" style="background:rgba(56,189,248,0.12);color:#38BDF8;border:1px solid rgba(56,189,248,0.3);">Redeem code</a>
    </div>
    <div>
      <h3>Schools, airlines, training centres</h3>
      <p>Need multi-seat access for cadets, line crews, or recurrent training? Get in touch &mdash; we set up volume bundles with shared activation codes and a dedicated point of contact.</p>
      <a href="/contact?tier=instructor" class="btn" style="background:rgba(242,201,76,0.12);color:#F2C94C;border:1px solid rgba(242,201,76,0.3);">Talk to us</a>
    </div>
  </section>
</div>
