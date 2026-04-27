<?php declare(strict_types=1); /** @var array $faqs */ ?>
<section class="section">
    <div class="container container-tight">
        <div class="section__head">
            <span class="section__chip">FAQ</span>
            <h1 style="font-size:clamp(1.8rem,3vw,2.4rem);">Frequently asked questions</h1>
            <p>Can't find what you need? <a href="/contact">Get in touch</a>.</p>
        </div>
        <div class="faq">
            <?php foreach ($faqs as $f): ?>
                <details>
                    <summary><?= htmlspecialchars($f['q'], ENT_QUOTES, 'UTF-8') ?></summary>
                    <p><?= htmlspecialchars($f['a'], ENT_QUOTES, 'UTF-8') ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
