<?php
declare(strict_types=1);
/** @var string $token */
/** @var string $csrf_token */
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

      <h1 class="auth-card__title">Choose a new password</h1>
      <p class="auth-card__sub">Pick something strong — 8+ characters, mix of letters, numbers, and a symbol.</p>

      <?php if ($flashError): ?>
        <div class="flash flash--error" role="alert"><?= htmlspecialchars($flashError) ?></div>
      <?php endif; ?>

      <form method="post" action="/reset-password/<?= htmlspecialchars($token) ?>" class="auth-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="form-group">
          <label class="form-label" for="password">New password</label>
          <input class="form-input" type="password" id="password" name="password" required minlength="8" autocomplete="new-password" autofocus
                 placeholder="At least 8 characters">
        </div>
        <div class="form-group">
          <label class="form-label" for="password_confirm">Confirm password</label>
          <input class="form-input" type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password"
                 placeholder="Repeat the password">
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Update password</button>
      </form>

      <p class="auth-card__alt">
        <a href="/login">← Back to login</a>
      </p>
    </div>
  </div>
</section>
