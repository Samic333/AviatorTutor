<?php /** @var ?array $currentSubscription */ ?>
<div style="max-width:560px;margin:64px auto;padding:0 20px;font-family:Inter,system-ui,-apple-system,sans-serif;">
    <h1 style="font-size:1.6rem;margin-bottom:8px;">Activate your subscription</h1>
    <p style="color:#94a3b8;line-height:1.55;">
        Enter your activation code to unlock the AviatorTutor study library.
        Each code grants 30&nbsp;days of access to every aircraft module currently live.
    </p>

    <?php if (!empty($flashError)): ?>
        <div style="margin:16px 0;padding:12px 14px;border:1px solid rgba(251,113,133,0.4);background:rgba(251,113,133,0.12);color:#fb7185;border-radius:8px;">
            <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($flashOk)): ?>
        <div style="margin:16px 0;padding:12px 14px;border:1px solid rgba(52,211,153,0.4);background:rgba(52,211,153,0.12);color:#34d399;border-radius:8px;">
            <?= htmlspecialchars($flashOk, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($flashNotice)): ?>
        <div style="margin:16px 0;padding:12px 14px;border:1px solid rgba(56,189,248,0.4);background:rgba(56,189,248,0.12);color:#38bdf8;border-radius:8px;">
            <?= htmlspecialchars($flashNotice, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($currentSubscription): ?>
        <div style="margin:18px 0;padding:18px;border:1px solid #1e293b;background:#0f172a;border-radius:12px;color:#e2e8f0;">
            <div style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#64748b;">Currently subscribed</div>
            <p style="margin-top:8px;">Plan: <strong><?= htmlspecialchars($currentSubscription['plan']) ?></strong>.<br>
            Active until <strong><?= htmlspecialchars($currentSubscription['expires_at']) ?></strong>.</p>
            <p style="margin-top:6px;color:#94a3b8;font-size:.9rem;">You can stack a new code now to extend your access — the new period starts when this one ends.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="/redeem" style="margin-top:18px;" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

        <label for="code" style="display:block;font-size:.92rem;font-weight:600;margin-bottom:8px;color:#e2e8f0;">Activation code</label>
        <input type="text" id="code" name="code" autocomplete="off" autocapitalize="characters" spellcheck="false"
               placeholder="XXXX-XXXX-XXXX-XXXX"
               style="width:100%;padding:12px 14px;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:1rem;letter-spacing:.06em;background:#0b1322;color:#e2e8f0;border:1px solid #334155;border-radius:10px;">
        <p style="color:#64748b;font-size:.82rem;margin-top:8px;">16 letters/digits. Hyphens are optional. Each code can only be used once.</p>

        <button type="submit"
                style="display:block;width:100%;margin-top:16px;padding:13px;font-size:1rem;font-weight:600;color:#02101f;background:#38bdf8;border:0;border-radius:10px;cursor:pointer;">
            Activate &amp; start studying
        </button>
    </form>

    <p style="text-align:center;color:#64748b;font-size:.85rem;margin-top:24px;">
        Don't have a code yet? <a href="/pricing" style="color:#38bdf8;">View pricing</a> or
        <a href="/dashboard" style="color:#94a3b8;">go to your dashboard</a>.
    </p>
</div>
