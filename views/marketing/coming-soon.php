<?php declare(strict_types=1); ?>
<section class="section">
    <div class="container container-tight">
        <span class="section__chip" style="background:rgba(251,191,36,0.14);color:var(--warn);border:1px solid rgba(251,191,36,0.4);">Coming soon</span>
        <h1 style="font-size:clamp(1.8rem,3vw,2.4rem);"><?= htmlspecialchars($moduleLabel, ENT_QUOTES, 'UTF-8') ?></h1>
        <p>This module is in production. Drop your email and we'll let you know the moment it goes live — no marketing emails, just one notification when the content lands.</p>

        <?php if (!empty($flashOk)): ?>
            <div class="flash flash--success"><?= htmlspecialchars($flashOk, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
            <div class="flash flash--error"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="/coming-soon/<?= htmlspecialchars($moduleSlug, ENT_QUOTES, 'UTF-8') ?>/notify" class="card" style="margin-top:24px;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group">
                <label class="form-label" for="email">Notify me at</label>
                <input class="form-input" type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Notify me when this goes live</button>
        </form>

        <p style="margin-top:24px;font-size:.92rem;color:var(--text-muted);text-align:center;">
            Or <a href="/">explore the modules already live</a>.
        </p>
    </div>
</section>
