<?php /** @var ?string $error */ /** @var ?string $success */ /** @var string $csrf_token */ ?>
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

            <h1 class="auth-card__title">Create your free account</h1>
            <p class="auth-card__sub">Aircraft systems, weather, SOPs, QRH, CRM, SMS &mdash; built by aviation professionals for the entire aviation community.</p>

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
