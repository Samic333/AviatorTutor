<?php
declare(strict_types=1);
/** @var string $error */
?>
<section class="auth-shell">
  <div class="container container-tight">
    <div class="auth-card" style="text-align:center;">
      <a href="/" class="brand auth-card__brand">
        <span class="brand__logo">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
          </svg>
        </span>
        AviatorTutor
      </a>

      <div style="width:64px;height:64px;border-radius:50%;background:rgba(239,68,68,0.12);color:#EF4444;display:flex;align-items:center;justify-content:center;margin:8px auto 18px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      </div>

      <h1 class="auth-card__title">Reset link unavailable</h1>
      <p class="auth-card__sub" style="color:#FCA5A5;"><?= htmlspecialchars($error) ?></p>

      <a href="/forgot-password" class="btn btn-primary btn-block" style="margin-top:24px;">Request a new reset link</a>
      <p class="auth-card__alt"><a href="/login">← Back to login</a></p>
    </div>
  </div>
</section>
