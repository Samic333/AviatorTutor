<?php
declare(strict_types=1);
/** @var string $error */
/** @var string $csrf_token */
?>
<section class="auth-shell">
  <div class="container container-tight">
    <div class="auth-card" style="text-align:center;">
      <a href="/" class="brand auth-card__brand">
        <span class="brand__logo">
          <svg width="22" height="22" viewBox="0 0 64 64" fill="none" aria-hidden="true"><path d="M11 51 L32 11 L53 51" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/><line x1="22" y1="40" x2="42" y2="40" stroke="currentColor" stroke-width="6" stroke-linecap="round"/><circle cx="32" cy="11" r="4" fill="#38BDF8"/></svg>
        </span>
        AviatorTutor
      </a>

      <div style="width:64px;height:64px;border-radius:50%;background:rgba(239,68,68,0.12);color:#EF4444;display:flex;align-items:center;justify-content:center;margin:8px auto 18px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      </div>

      <h1 class="auth-card__title">Verification problem</h1>
      <p class="auth-card__sub" style="color:#FCA5A5;"><?= htmlspecialchars($error) ?></p>

      <hr style="border:0;border-top:1px solid rgba(255,255,255,0.08);margin:28px 0;">

      <h3 style="margin:0 0 8px;font-size:14px;color:#E2E8F0;">Request a fresh verification link</h3>
      <form method="post" action="/verify-email/resend" class="auth-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="form-group">
          <input class="form-input" type="email" name="email" required placeholder="your@email.com" autocomplete="email">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send new link</button>
      </form>

      <p class="auth-card__alt" style="margin-top:24px;">
        <a href="/login">← Back to login</a>
      </p>
    </div>
  </div>
</section>
