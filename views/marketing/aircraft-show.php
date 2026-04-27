<?php
declare(strict_types=1);
/** @var array $aircraft */
/** @var array $panels */
/** @var bool  $isLogged */
/** @var bool  $isActiveSub */
/** @var string $csrf_token */
$status = (string) $aircraft['status'];
$isLive = $status === 'live' || $status === 'beta';
$badgeClass = match ($status) {
    'live'        => 'tile-badge tile-badge--live',
    'beta'        => 'tile-badge tile-badge--beta',
    'coming_soon' => 'tile-badge tile-badge--soon',
    default       => 'tile-badge',
};
$cockpitImg = (string) ($aircraft['cockpit_image_path'] ?? '');
?>
<section class="hero">
    <div class="container">
        <span class="<?= $badgeClass ?>" style="display:inline-block;margin-bottom:18px;">
            <?= \App\Services\AircraftService::statusLabel($status) ?>
        </span>
        <h1><?= htmlspecialchars((string) $aircraft['name']) ?></h1>
        <p class="lead" style="max-width:60ch;"><?= htmlspecialchars((string) ($aircraft['tagline'] ?? '')) ?></p>

        <div class="hero__cta">
            <?php if ($isLive): ?>
                <?php if ($isLogged && $isActiveSub): ?>
                    <form method="post" action="/aircraft/<?= htmlspecialchars($aircraft['slug']) ?>/study" style="display:inline-block;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <button type="submit" class="btn btn-primary btn-lg">Start studying <?= htmlspecialchars($aircraft['short_name']) ?> →</button>
                    </form>
                    <a href="/dashboard" class="btn btn-lg">My dashboard</a>
                <?php elseif ($isLogged): ?>
                    <a href="/redeem" class="btn btn-primary btn-lg">Redeem code to start →</a>
                <?php else: ?>
                    <a href="/register" class="btn btn-primary btn-lg">Create account →</a>
                    <a href="/pricing" class="btn btn-lg">View pricing</a>
                <?php endif; ?>
            <?php else: ?>
                <span class="muted" style="display:inline-flex;align-items:center;gap:8px;">
                    Module in production · <strong><?= (int) ($aircraft['waitlist_count'] ?? 0) ?></strong> pilot<?= ((int) ($aircraft['waitlist_count'] ?? 0)) === 1 ? '' : 's' ?> waitlisted
                </span>
            <?php endif; ?>
        </div>

        <?php if (!empty($flashOk)): ?>
            <div class="flash flash--success" style="margin-top:24px;max-width:520px;"><?= htmlspecialchars($flashOk) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
            <div class="flash flash--error" style="margin-top:24px;max-width:520px;"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>
    </div>
</section>

<?php if ($isLive && $cockpitImg !== ''): ?>
    <section class="section section--alt" style="padding-top:0;">
        <div class="container">
            <div class="cockpit-stage">
                <img class="cockpit-stage__poster" src="<?= htmlspecialchars($cockpitImg) ?>" alt="<?= htmlspecialchars($aircraft['name']) ?> cockpit">
                <?php if (!empty($panels)): ?>
                    <div class="cockpit-hotspots">
                    <?php foreach ($panels as $p): ?>
                        <a class="cockpit-hotspot"
                           href="<?= !empty($p['system_slug']) ? '/systems/' . htmlspecialchars((string) $p['system_id']) : '#' ?>"
                           style="left:<?= htmlspecialchars((string) $p['pos_x']) ?>%;top:<?= htmlspecialchars((string) $p['pos_y']) ?>%;width:<?= htmlspecialchars((string) $p['width']) ?>%;height:<?= htmlspecialchars((string) $p['height']) ?>%;"
                           title="<?= htmlspecialchars((string) $p['label']) ?>">
                            <span class="cockpit-hotspot__label"><?= htmlspecialchars((string) $p['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <p class="muted text-center" style="margin-top:18px;font-size:.85rem;">Hover the cockpit panels to identify them — click to jump straight to that study system.</p>
        </div>
    </section>
<?php endif; ?>

<section class="section">
    <div class="container container-tight">
        <h2>About this module</h2>
        <p><?= nl2br(htmlspecialchars((string) ($aircraft['description'] ?? ''))) ?></p>
        <div class="grid grid--auto" style="margin-top:32px;">
            <div class="card">
                <h3>Manufacturer</h3>
                <p><?= htmlspecialchars((string) $aircraft['manufacturer']) ?></p>
            </div>
            <div class="card">
                <h3>Category</h3>
                <p><?= htmlspecialchars(ucfirst(str_replace('_',' ', (string) $aircraft['category']))) ?></p>
            </div>
            <div class="card">
                <h3>Status</h3>
                <p><?= \App\Services\AircraftService::statusLabel($status) ?><?php if (!$isLive): ?> · <?= (int) ($aircraft['waitlist_count'] ?? 0) ?> waitlisted<?php endif; ?></p>
            </div>
        </div>
    </div>
</section>

<?php if (!$isLive): ?>
    <section class="section section--alt">
        <div class="container container-tight">
            <h2 class="text-center">Notify me when <?= htmlspecialchars($aircraft['short_name']) ?> goes live</h2>
            <p class="text-center muted" style="max-width:48ch;margin:0 auto 28px;">One short email. No spam. Unsubscribe whenever.</p>
            <form method="post" action="/aircraft/<?= htmlspecialchars($aircraft['slug']) ?>/notify" style="max-width:480px;margin:0 auto;display:flex;gap:10px;flex-wrap:wrap;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input class="form-input" type="email" name="email" required placeholder="you@example.com" style="flex:1;min-width:220px;">
                <button type="submit" class="btn btn-primary">Notify me</button>
            </form>
        </div>
    </section>
<?php endif; ?>
