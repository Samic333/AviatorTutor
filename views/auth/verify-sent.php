<?php
declare(strict_types=1);
/** @var string $email */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */
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

      <div style="width:64px;height:64px;border-radius:50%;background:rgba(56,189,248,0.12);color:#38BDF8;display:flex;align-items:center;justify-content:center;margin:8px auto 18px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
        </svg>
      </div>

      <h1 class="auth-card__title">Check your email</h1>
      <p class="auth-card__sub" style="margin-bottom:8px;">
        We sent a verification link to
        <?php if ($email !== ''): ?>
          <strong style="color:#E2E8F0;"><?= htmlspecialchars($email) ?></strong>.
        <?php else: ?>
          your inbox.
        <?php endif; ?>
      </p>
      <p class="auth-card__sub">Click the link to activate your account. The link expires in 48 hours.</p>

      <?php if ($flashOk): ?>
        <div class="flash flash--success" role="status" style="margin:18px 0;"><?= htmlspecialchars($flashOk) ?></div>
      <?php endif; ?>
      <?php if ($flashError): ?>
        <div class="flash flash--error" role="alert" style="margin:18px 0;"><?= htmlspecialchars($flashError) ?></div>
      <?php endif; ?>

      <hr style="border:0;border-top:1px solid rgba(255,255,255,0.08);margin:28px 0;">

      <h3 style="margin:0 0 8px;font-size:14px;color:#E2E8F0;">Didn't get it?</h3>
      <p style="margin:0 0 16px;font-size:13px;color:#94A3B8;">Check spam, or request a fresh link below.</p>

      <form method="post" action="/verify-email/resend" class="auth-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="form-group">
          <input class="form-input" type="email" name="email" required
                 value="<?= htmlspecialchars($email) ?>"
                 placeholder="your@email.com" autocomplete="email">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Resend verification email</button>
      </form>

      <p class="auth-card__alt" style="margin-top:24px;">
        <a href="/login">← Back to login</a>
      </p>
    </div>
  </div>
</section>
