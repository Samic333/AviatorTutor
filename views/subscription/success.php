<?php
declare(strict_types=1);
/** @var ?array $subject */
/** @var ?array $session */
$paid = is_array($session) && (($session['payment_status'] ?? '') === 'paid');
?>
<section style="min-height:80vh;padding:48px 16px;display:flex;align-items:center;">
  <div style="max-width:520px;margin:0 auto;text-align:center;">

    <div style="width:80px;height:80px;border-radius:50%;background:rgba(34,197,94,0.12);color:#22C55E;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"/>
      </svg>
    </div>

    <h1 style="margin:0 0 8px;font-family:'DM Sans',Inter,sans-serif;font-size:28px;font-weight:700;color:#F1F5F9;">Payment received</h1>

    <?php if ($subject): ?>
      <p style="margin:0 0 24px;color:#CBD5E1;font-size:15px;line-height:1.6;">
        You now have lifetime access to <strong style="color:#F1F5F9;"><?= htmlspecialchars((string)$subject['name']) ?></strong>.
      </p>
    <?php else: ?>
      <p style="margin:0 0 24px;color:#CBD5E1;font-size:15px;line-height:1.6;">Thank you. Your purchase will appear on your dashboard within a moment.</p>
    <?php endif; ?>

    <?php if (!$paid): ?>
      <div style="padding:14px;margin-bottom:20px;background:rgba(56,189,248,0.06);border:1px solid rgba(56,189,248,0.2);border-radius:10px;color:#CBD5E1;font-size:13px;line-height:1.5;">
        Stripe is still confirming the payment. If access doesn't appear within a minute, refresh your dashboard or
        <a href="/contact" style="color:#38BDF8;">contact support</a>.
      </div>
    <?php endif; ?>

    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
      <a href="/dashboard" class="btn btn-primary btn-lg">Go to dashboard →</a>
      <a href="/pricing" class="btn" style="background:rgba(255,255,255,0.06);color:#E2E8F0;border:1px solid rgba(255,255,255,0.1);">Browse more packs</a>
    </div>
  </div>
</section>
