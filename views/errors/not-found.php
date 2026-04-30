<?php
declare(strict_types=1);
/** @var string $heading */
/** @var string $body */
/** @var string $backUrl */
/** @var string $backLabel */
?>
<div class="plt-empty-error" style="
    max-width: 560px;
    margin: 80px auto 40px;
    padding: 48px 32px;
    text-align: center;
    background: var(--plt-glass, rgba(255,255,255,0.04));
    border: 1px solid var(--plt-glass-border, rgba(255,255,255,0.10));
    border-radius: 16px;
">
    <div style="
        width: 64px; height: 64px;
        margin: 0 auto 20px;
        display: grid; place-items: center;
        background: var(--plt-sky-soft, rgba(56,189,248,0.12));
        color: var(--plt-sky, #38BDF8);
        border-radius: 16px;
        font-size: 28px; font-weight: 700;
    ">404</div>
    <h1 style="
        margin: 0 0 12px;
        font-family: 'DM Sans', Inter, system-ui, sans-serif;
        font-size: 24px; font-weight: 700;
        color: var(--plt-text, #F1F5F9);
        letter-spacing: -0.02em;
    "><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h1>
    <p style="
        margin: 0 0 24px;
        font-size: 15px; line-height: 1.55;
        color: var(--plt-text-muted, #64748B);
    "><?= htmlspecialchars($body, ENT_QUOTES, 'UTF-8') ?></p>
    <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" style="
        display: inline-flex;
        align-items: center; gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, var(--plt-sky, #38BDF8), var(--plt-sky-deep, #0EA5E9));
        color: #02101F;
        text-decoration: none;
        font-weight: 700;
        border-radius: 10px;
    ">&larr; <?= htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8') ?></a>
</div>
