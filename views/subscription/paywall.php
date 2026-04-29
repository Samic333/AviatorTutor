<?php declare(strict_types=1); /** @var array $user; @var ?array $latestSubscription */ ?>
<section class="section">
    <div class="container container-tight">
        <span class="section__chip" style="background:rgba(251,191,36,0.14);color:var(--warn);border:1px solid rgba(251,191,36,0.4);">Subscription required</span>
        <h1 style="font-size:clamp(1.8rem,3vw,2.4rem);">Welcome <?= htmlspecialchars(explode(' ', (string)($user['name'] ?? 'pilot'))[0], ENT_QUOTES, 'UTF-8') ?> — let's get you studying.</h1>

        <?php if ($latestSubscription): ?>
            <p>Your last subscription expired on <strong class="num"><?= htmlspecialchars(substr((string)$latestSubscription['expires_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></strong>. Redeem a new code to continue from where you left off.</p>
        <?php else: ?>
            <p>You're signed in, but the full study library is unlocked via activation code or by joining a paid tier when it launches. Free Preview content is available now.</p>
        <?php endif; ?>

        <div class="card card--accent" style="margin-top:24px;">
            <h2 style="margin-bottom:12px;">Two ways to unlock</h2>

            <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
                <div>
                    <h3 style="font-size:1rem;margin-bottom:6px;">1. I have an activation code</h3>
                    <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:14px;">Codes from training partners and organisations unlock full access immediately.</p>
                    <a href="/redeem" class="btn btn-primary btn-block">Redeem code →</a>
                </div>
                <div>
                    <h3 style="font-size:1rem;margin-bottom:6px;">2. Join the tier waitlist</h3>
                    <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:14px;">Student, Professional, and Instructor tiers launch soon &mdash; we'll email you the day they go live.</p>
                    <a href="/pricing" class="btn btn-block">View plans →</a>
                </div>
            </div>
        </div>

        <p style="margin-top:32px;text-align:center;color:var(--text-soft);font-size:.85rem;">
            Need help? <a href="/contact">Contact us</a> &nbsp;·&nbsp;
            <a href="/account">Account</a>
        </p>
    </div>
</section>
