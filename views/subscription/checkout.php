<?php
declare(strict_types=1);
/** @var array $subject */
/** @var bool $stripeReady */
/** @var string $csrf_token */
?>
<section style="min-height:80vh;padding:48px 16px;">
  <div style="max-width:540px;margin:0 auto;">
    <a href="/pricing" style="display:inline-flex;align-items:center;gap:6px;color:#94A3B8;text-decoration:none;font-size:13px;margin-bottom:24px;">← Back to pricing</a>

    <div style="padding:32px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;">

      <span style="display:inline-block;padding:4px 10px;background:rgba(56,189,248,0.12);color:#38BDF8;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;margin-bottom:16px;">Checkout</span>
      <h1 style="margin:0 0 8px;font-family:'DM Sans',Inter,sans-serif;font-size:24px;font-weight:700;color:#F1F5F9;"><?= htmlspecialchars((string)$subject['name']) ?></h1>
      <?php if (!empty($subject['description']) || !empty($subject['short_blurb'])): ?>
        <p style="margin:0 0 24px;color:#94A3B8;font-size:14px;line-height:1.6;"><?= htmlspecialchars((string)($subject['description'] ?? $subject['short_blurb'])) ?></p>
      <?php endif; ?>

      <div style="display:flex;justify-content:space-between;align-items:center;padding:18px;margin-bottom:24px;background:rgba(56,189,248,0.06);border:1px solid rgba(56,189,248,0.18);border-radius:10px;">
        <div>
          <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:0.06em;font-weight:600;margin-bottom:2px;">One-time payment</div>
          <div style="font-size:13px;color:#E2E8F0;">Lifetime access</div>
        </div>
        <div style="font-family:'DM Sans',Inter,sans-serif;font-size:32px;font-weight:700;color:#F1F5F9;">$<?= number_format((float)$subject['price_usd'], 0) ?></div>
      </div>

      <ul style="list-style:none;padding:0;margin:0 0 28px;display:flex;flex-direction:column;gap:10px;">
        <li style="display:flex;gap:10px;align-items:center;color:#CBD5E1;font-size:13.5px;">
          <span style="color:#22C55E;">✓</span> Full access to all lessons, flashcards, and quizzes
        </li>
        <li style="display:flex;gap:10px;align-items:center;color:#CBD5E1;font-size:13.5px;">
          <span style="color:#22C55E;">✓</span> Progress tracking + spaced repetition
        </li>
        <li style="display:flex;gap:10px;align-items:center;color:#CBD5E1;font-size:13.5px;">
          <span style="color:#22C55E;">✓</span> Note-taking + AI Q&amp;A (coming soon)
        </li>
        <li style="display:flex;gap:10px;align-items:center;color:#CBD5E1;font-size:13.5px;">
          <span style="color:#22C55E;">✓</span> No subscription, no recurring fee
        </li>
      </ul>

      <?php if ($stripeReady): ?>
        <form method="post" action="/checkout/<?= htmlspecialchars((string)$subject['slug']) ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
          <button type="submit" class="btn btn-primary btn-lg btn-block" style="width:100%;">
            Pay $<?= number_format((float)$subject['price_usd'], 0) ?> with Stripe
          </button>
        </form>
        <p style="margin:14px 0 0;text-align:center;font-size:11.5px;color:#64748B;">
          Powered by <strong style="color:#94A3B8;">Stripe</strong>. We never see your card details.
        </p>
      <?php else: ?>
        <div style="padding:18px;background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.25);border-radius:10px;margin-bottom:16px;">
          <p style="margin:0 0 8px;color:#C9A84C;font-weight:600;font-size:14px;">Online checkout not yet active</p>
          <p style="margin:0;color:#CBD5E1;font-size:13px;line-height:1.5;">Stripe keys haven't been configured yet. You can still unlock this pack with an activation code.</p>
        </div>
        <a href="/redeem" class="btn btn-primary btn-block" style="width:100%;text-align:center;">Redeem activation code</a>
        <p style="margin:14px 0 0;text-align:center;font-size:12px;color:#94A3B8;">Or <a href="/contact" style="color:#38BDF8;">contact us</a> to buy this pack.</p>
      <?php endif; ?>

    </div>
  </div>
</section>
