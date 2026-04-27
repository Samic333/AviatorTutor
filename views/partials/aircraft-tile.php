<?php
/** @var array $a Aircraft row from the aircrafts table */
declare(strict_types=1);
$status = (string) ($a['status'] ?? 'coming_soon');
$badgeClass = match ($status) {
    'live'        => 'tile-badge tile-badge--live',
    'beta'        => 'tile-badge tile-badge--beta',
    'coming_soon' => 'tile-badge tile-badge--soon',
    default       => 'tile-badge',
};
$badgeText = \App\Services\AircraftService::statusLabel($status);
$href = '/aircraft/' . $a['slug'];
$tileClass = $status === 'live' ? 'card card--accent aircraft-tile' : 'card aircraft-tile';
?>
<a href="<?= htmlspecialchars($href) ?>" class="<?= $tileClass ?>" data-status="<?= htmlspecialchars($status) ?>" data-category="<?= htmlspecialchars((string) $a['category']) ?>">
    <span class="<?= $badgeClass ?>"><?= $badgeText ?></span>
    <h3 class="aircraft-tile__name"><?= htmlspecialchars((string) $a['name']) ?></h3>
    <p class="aircraft-tile__tagline"><?= htmlspecialchars((string) ($a['tagline'] ?? '')) ?></p>
    <span class="aircraft-tile__meta">
        <span><?= htmlspecialchars((string) $a['manufacturer']) ?></span>
        <span class="aircraft-tile__cat"><?= htmlspecialchars(ucfirst(str_replace('_',' ', (string) $a['category']))) ?></span>
    </span>
</a>
