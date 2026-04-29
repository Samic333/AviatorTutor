<?php /** @var ?string $error */ /** @var string $csrf_token */ ?>
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
