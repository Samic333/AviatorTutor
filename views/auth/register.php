<?php /** @var ?string $error */ /** @var ?string $success */ /** @var string $csrf_token */ ?>
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

            <h1 class="auth-card__title">Start studying</h1>
            <p class="auth-card__sub">One account, every aviation module — $10 / month after redeeming a code.</p>

            <?php if (!empty($error)): ?>
                <div class="flash flash--error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="flash flash--success" role="alert">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register" class="auth-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label class="form-label" for="name">Full name</label>
                    <input
                        class="form-input"
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                        autocomplete="name"
                        autofocus
                        placeholder="Captain First Last">
                </div>

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
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="At least 8 characters">
                    <p class="form-help">8+ characters. Use a mix of letters, numbers, and a symbol.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirm">Confirm password</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Repeat the password">
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="accept_terms" value="1" required>
                        <span>I agree to the <a href="/terms">Terms</a> and <a href="/privacy">Privacy Policy</a>.</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Create account</button>
            </form>

            <p class="auth-card__alt">
                Already have an account? <a href="/login">Sign in</a>
            </p>
        </div>
    </div>
</section>
