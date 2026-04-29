<?php /** @var ?string $error */ /** @var string $csrf_token */ ?>
<section class="auth-shell">
    <div class="container container-tight">
        <div class="auth-card">
            <a href="/" class="brand auth-card__brand" aria-label="AviatorTutor home">
                <span class="brand__logo">
                    <svg width="22" height="22" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                        <path d="M11 51 L32 11 L53 51" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="22" y1="40" x2="42" y2="40" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                        <circle cx="32" cy="11" r="4" fill="#38BDF8"/>
                    </svg>
                </span>
                <span class="brand__wordmark">Aviator<span class="brand__wordmark-accent">Tutor</span></span>
            </a>

            <h1 class="auth-card__title">Sign in</h1>
            <p class="auth-card__sub">Welcome back. Continue your aviation studies.</p>

            <?php if (!empty($error)): ?>
                <div class="flash flash--error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login" class="auth-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input
                        class="form-input"
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="you@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Your password">
                </div>

                <div class="form-group form-row" style="display:flex;justify-content:space-between;align-items:center;">
                    <label class="form-check">
                        <input type="checkbox" name="remember_me" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="/forgot-password" style="font-size:13px;color:#38BDF8;text-decoration:none;">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
            </form>

            <p class="auth-card__alt">
                New to AviatorTutor? <a href="/register">Create an account</a>
            </p>
        </div>
    </div>
</section>
