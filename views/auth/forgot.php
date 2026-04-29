<?php
declare(strict_types=1);
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */
?>
<section class="auth-shell">
  <div class="container container-tight">
    <div class="auth-card">
      <a href="/" class="brand auth-card__brand">
        <span class="brand__logo">
          <svg width="22" height="22" viewBox="0 0 64 64" fill="none" aria-hidden="true"><path d="M11 51 L32 11 L53 51" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/><line x1="22" y1="40" x2="42" y2="40" stroke="currentColor" stroke-width="6" stroke-linecap="round"/><circle cx="32" cy="11" r="4" fill="#38BDF8"/></svg>
        </span>
        AviatorTutor
      </a>

      <h1 class="auth-card__title">Reset your password</h1>
      <p class="auth-card__sub">Enter your email and we'll send you a reset link.</p>

      <?php if ($flashOk): ?>
        <div class="flash flash--success" role="status"><?= htmlspecialchars($flashOk) ?></div>
      <?php endif; ?>
      <?php if ($flashError): ?>
        <div class="flash flash--error" role="alert"><?= htmlspecialchars($flashError) ?></div>
      <?php endif; ?>

      <form method="post" action="/forgot-password" class="auth-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input class="form-input" type="email" id="email" name="email" required autocomplete="email" autofocus
                 placeholder="you@example.com">
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Send reset link</button>
      </form>

      <p class="auth-card__alt">
        Remembered it? <a href="/login">Back to login</a>
      </p>
    </div>
  </div>
</section>
