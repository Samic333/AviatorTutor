<?php
declare(strict_types=1);
/** @var array<string,array<int,array>> $groups */
$catLabels = [
    'regional'   => 'Regional',
    'narrowbody' => 'Narrowbody',
    'widebody'   => 'Widebody',
    'ga'         => 'General Aviation',
    'training'   => 'Training',
];
$order = ['regional','narrowbody','widebody','ga','training'];
?>
<section class="hero" style="padding-bottom:24px;">
    <div class="container">
        <span class="hero__chip">AviatorTutor library</span>
        <h1>Every aircraft we cover.</h1>
        <p class="lead">Q400 is live now. The rest of the fleet is in production — pick yours and we'll email you the day it lands.</p>
    </div>
</section>

<section class="section section--alt">
    <div class="container">
        <?php foreach ($order as $cat): if (empty($groups[$cat])) continue; ?>
            <div class="aircraft-cat">
                <h2 class="aircraft-cat__title"><?= htmlspecialchars($catLabels[$cat]) ?></h2>
                <div class="grid grid--auto" style="margin-top:18px;">
                    <?php foreach ($groups[$cat] as $a): ?>
                        <?php include __DIR__ . '/../partials/aircraft-tile.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="container container-tight" style="text-align:center;">
        <h2>Don't see your aircraft?</h2>
        <p>Tell us what you fly and we'll prioritise it. <a href="/contact">Send a quick note</a>.</p>
    </div>
</section>
