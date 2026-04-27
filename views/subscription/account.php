<?php /** @var ?array $currentSubscription */ /** @var ?array $latestSubscription */ ?>
<div style="max-width:780px;margin:48px auto;padding:0 24px;color:#e2e8f0;font-family:Inter,system-ui,sans-serif;">
    <h1 style="font-size:1.6rem;margin-bottom:8px;">My account</h1>

    <?php if (!empty($flashOk)): ?>
        <div style="margin:16px 0;padding:12px 14px;border:1px solid rgba(52,211,153,0.4);background:rgba(52,211,153,0.12);color:#34d399;border-radius:8px;">
            <?= htmlspecialchars($flashOk, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <h2 style="margin-top:32px;font-size:1.1rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Subscription</h2>

    <?php if ($currentSubscription): ?>
        <div style="margin-top:12px;padding:18px;border:1px solid #1e293b;background:#0f172a;border-radius:12px;">
            <div><strong style="color:#34d399;">Active</strong> · <?= htmlspecialchars($currentSubscription['plan']) ?> plan</div>
            <p style="margin-top:8px;color:#94a3b8;">Renews/expires on <?= htmlspecialchars($currentSubscription['expires_at']) ?></p>
            <p style="margin-top:6px;color:#64748b;font-size:.85rem;">
                Provider: <?= htmlspecialchars($currentSubscription['payment_provider']) ?>
            </p>
        </div>
    <?php elseif ($latestSubscription): ?>
        <div style="margin-top:12px;padding:18px;border:1px solid #1e293b;background:#0f172a;border-radius:12px;">
            <div style="color:#fbbf24;font-weight:600;">No active subscription</div>
            <p style="margin-top:8px;color:#94a3b8;">Your last subscription expired on <?= htmlspecialchars($latestSubscription['expires_at']) ?>.</p>
            <a href="/redeem" style="display:inline-block;margin-top:10px;padding:10px 16px;background:#38bdf8;color:#02101f;font-weight:600;border-radius:8px;text-decoration:none;">Redeem a code</a>
            <a href="/pricing" style="display:inline-block;margin-top:10px;margin-left:8px;padding:10px 16px;border:1px solid #334155;color:#e2e8f0;border-radius:8px;text-decoration:none;">View pricing</a>
        </div>
    <?php else: ?>
        <div style="margin-top:12px;padding:18px;border:1px solid #1e293b;background:#0f172a;border-radius:12px;">
            <p style="color:#94a3b8;">You don't have a subscription yet.</p>
            <a href="/redeem" style="display:inline-block;margin-top:10px;padding:10px 16px;background:#38bdf8;color:#02101f;font-weight:600;border-radius:8px;text-decoration:none;">Redeem a code</a>
            <a href="/pricing" style="display:inline-block;margin-top:10px;margin-left:8px;padding:10px 16px;border:1px solid #334155;color:#e2e8f0;border-radius:8px;text-decoration:none;">View pricing</a>
        </div>
    <?php endif; ?>
</div>
