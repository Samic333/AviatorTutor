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
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
          </svg>
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
