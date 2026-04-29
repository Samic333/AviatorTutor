<?php declare(strict_types=1); ?>

<!-- 1. HERO -->
<section class="hero">
    <div class="container">
        <span class="hero__chip">Built by aviation professionals · for the aviation community</span>
        <h1>Aircraft systems, weather, SOPs, QRH, CRM, SMS &mdash; one premium aviation learning platform.</h1>
        <p class="lead">
            AviatorTutor is a premium aviation learning platform built by working pilots, cabin crew, SMS trainers, instructors, and operational aviation experts &mdash; for pilots, cabin crew, instructors, safety teams, aviation students, and operations professionals. Practical, interactive, scenario-based study designed for memory retention and real-world application.
        </p>
        <div class="hero__cta">
            <a class="btn btn-primary btn-lg" href="/register">Start studying free</a>
            <a class="btn btn-ghost btn-lg" href="/pricing">View plans</a>
        </div>
        <div class="hero__metabar">
            <span><strong class="num">Aircraft</strong> systems</span>
            <span><strong class="num">QRH &amp; SOP</strong> mastery</span>
            <span><strong class="num">CRM, SMS</strong> &amp; cabin safety</span>
            <span><strong class="num">Mobile</strong> &amp; tablet ready</span>
        </div>
    </div>
</section>

<!-- 2. VALUE PROPS -->
<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">Why AviatorTutor</span>
            <h2>Built for serious aviation learners</h2>
            <p>Every feature is here because aviation professionals actually use it during line-check prep, type-rating revision, recurrent training, or job interviews.</p>
        </div>
        <div class="grid grid--3">
            <div class="card">
                <div class="card__icon">A</div>
                <h3>Aircraft-specific modules</h3>
                <p>Chapter-by-chapter coverage of every aircraft system, sourced from real training manuals.</p>
            </div>
            <div class="card">
                <div class="card__icon">I</div>
                <h3>Airline interview prep</h3>
                <p>Tech, HR, and sim-profile prep for the world's biggest carriers — examiner-style questions with model answers.</p>
            </div>
            <div class="card">
                <div class="card__icon">S</div>
                <h3>Aviation subject packs</h3>
                <p>Weather, performance &amp; W&amp;B, CRM, SMS, DGR, navigation, comms, ops control — ICAO-aligned content for the whole industry.</p>
            </div>
            <div class="card">
                <div class="card__icon">F</div>
                <h3>Spaced-repetition flashcards</h3>
                <p>SM-2 scheduling. Numbers, components, indications — surfaced when you're about to forget them.</p>
            </div>
            <div class="card">
                <div class="card__icon">P</div>
                <h3>Self-paced learning</h3>
                <p>No fixed schedule, no live classes. You move at your own speed — your progress is saved.</p>
            </div>
            <div class="card">
                <div class="card__icon">M</div>
                <h3>Mobile-friendly</h3>
                <p>Phone, tablet, or laptop — the entire platform reflows cleanly. Study during a layover or commute.</p>
            </div>
        </div>
    </div>
</section>

<!-- 3. AIRCRAFT MODULES (data-driven from aircrafts table) -->
<section class="section section--alt">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">Aircraft library</span>
            <h2>Aircraft systems libraries</h2>
            <p>Type-specific systems study with interactive slide-based lessons, flashcards, quizzes, and progress tracking. New aircraft libraries are added regularly &mdash; pick yours and we'll email you the day it goes live.</p>
        </div>
        <div class="grid grid--auto">
            <?php foreach (($aircraftList ?? []) as $a): ?>
                <?php include __DIR__ . '/../partials/aircraft-tile.php'; ?>
            <?php endforeach; ?>
        </div>
        <p class="text-center" style="margin-top:32px;">
            <a href="/aircraft" class="btn">See full aircraft catalog →</a>
        </p>
    </div>
</section>

<!-- 3b. SKILL MODULES (non-aircraft topics) -->
<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">Beyond the aircraft</span>
            <h2>Aviation subject packs &amp; airline interview prep</h2>
            <p>Cross-cutting subjects every aviation professional needs — and dedicated prep for the world's biggest airline interviews.</p>
        </div>
        <div class="grid grid--auto">
            <a href="/pricing#airline-interview" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">20 AIRLINES</span>
                <h3 style="margin-top:8px;">Airline Interview Prep</h3>
                <p>Tech, HR, and sim-profile prep for Emirates, Qatar, Ethiopian, BA, Lufthansa, Cathay, Delta, Singapore Airlines and more.</p>
            </a>
            <a href="/pricing#aviation-subject" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">10 SUBJECTS</span>
                <h3 style="margin-top:8px;">Weather &amp; Performance</h3>
                <p>METAR/TAF, fronts, hazards, V-speeds, runway analysis, weight &amp; balance — ICAO-aligned.</p>
            </a>
            <a href="/pricing#aviation-subject" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">FOR ALL CREW</span>
                <h3 style="margin-top:8px;">CRM &amp; Human Factors</h3>
                <p>Crew Resource Management essentials, threat-and-error management, fatigue, decision-making — for pilots and cabin crew.</p>
            </a>
            <a href="/pricing#aviation-subject" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">SAFETY</span>
                <h3 style="margin-top:8px;">SMS &amp; Dangerous Goods</h3>
                <p>ICAO Annex 19 SMS pillars and IATA DGR awareness — built for safety teams, trainers, and crew.</p>
            </a>
            <a href="/pricing#aviation-subject" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">REGULATION</span>
                <h3 style="margin-top:8px;">Navigation, Comms &amp; Air Law</h3>
                <p>RNAV/RNP/PBN, ICAO phraseology, ICAO Annexes &amp; FAR/JAR rules — the regulation layer pilots and dispatchers actually need.</p>
            </a>
        </div>
        <p class="text-center" style="margin-top:32px;">
            <a href="/pricing" class="btn">See full subject &amp; airline catalog →</a>
        </p>
    </div>
</section>

<!-- 4. HOW IT WORKS -->
<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">How it works</span>
            <h2>From sign-up to your first lesson in two minutes</h2>
        </div>
        <div class="grid grid--4 steps">
            <div class="step">
                <h3>Create your account</h3>
                <p>Email and password. Confirm your email and you're in.</p>
            </div>
            <div class="step">
                <h3>Pick what to study</h3>
                <p>Aircraft systems, an aviation subject (weather, CRM, SMS, QRH, SOPs), or airline interview prep. Start free; upgrade when you're ready.</p>
            </div>
            <div class="step">
                <h3>Start studying</h3>
                <p>Slide-based lessons with question gates, flashcards, quizzes, scenarios. Progress saved automatically.</p>
            </div>
            <div class="step">
                <h3>Practice &amp; track</h3>
                <p>Spaced repetition surfaces what you're about to forget. Quiz scores show your weak areas.</p>
            </div>
        </div>
    </div>
</section>

<!-- 5. PRICING -->
<section id="pricing" class="section section--alt">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">Plans</span>
            <h2>Plans for every aviation learner</h2>
            <p>From free preview to multi-seat instructor licences. Pricing tiers below give you a sense of what's coming &mdash; full launch pricing locks in soon. Activation codes from partners and training organisations work today.</p>
        </div>
        <?php $pricingTiers = require __DIR__ . '/../../config/pricing.php'; ?>
        <div class="grid grid--4" style="max-width:1200px;margin:0 auto;align-items:stretch;gap:18px;">
            <?php foreach ($pricingTiers as $tier): ?>
            <div class="card" style="display:flex;flex-direction:column;gap:12px;<?= !empty($tier['highlight']) ? 'border-color:var(--accent,#38BDF8);box-shadow:0 0 0 1px rgba(56,189,248,0.32);' : '' ?>">
                <span class="section__chip" style="align-self:flex-start;<?= !empty($tier['highlight']) ? 'background:rgba(56,189,248,0.18);color:var(--accent,#38BDF8);' : '' ?>"><?= htmlspecialchars($tier['badge']) ?></span>
                <h3 style="margin:0;font-size:1.18rem;"><?= htmlspecialchars($tier['name']) ?></h3>
                <p style="margin:0;color:var(--text-muted);font-size:.92rem;line-height:1.5;flex:1;"><?= htmlspecialchars($tier['blurb']) ?></p>
                <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:wrap;">
                    <span style="font-family:'DM Sans',Inter,sans-serif;font-size:1.7rem;font-weight:700;color:var(--text);"><?= htmlspecialchars($tier['price_label']) ?></span>
                    <?php if (!empty($tier['price_suffix'])): ?>
                        <span style="color:var(--text-soft);font-size:.85rem;"><?= htmlspecialchars($tier['price_suffix']) ?></span>
                    <?php endif; ?>
                </div>
                <a href="<?= htmlspecialchars($tier['cta_url']) ?>" class="btn <?= !empty($tier['highlight']) ? 'btn-primary' : 'btn-ghost' ?>" style="margin-top:auto;"><?= htmlspecialchars($tier['cta_label']) ?></a>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-center" style="margin-top:32px;color:var(--text-soft);font-size:.9rem;">
            Have an activation code from a partner or training organisation? <a href="/redeem">Redeem here</a> &mdash; full access until your tier launches.
        </p>
    </div>
</section>

<!-- 6. PLATFORM PREVIEW -->
<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">Platform preview</span>
            <h2>What you'll see inside</h2>
            <p>Real screens from the study app — dashboard, lessons, flashcards, quiz.</p>
        </div>
        <div class="grid grid--auto" style="gap:18px;">
            <div class="card">
                <h3>Dashboard</h3>
                <p>Your in-progress packs, due flashcards, recent quiz scores, and weak areas — at a glance.</p>
            </div>
            <div class="card">
                <h3>Lessons</h3>
                <p>Subject-by-subject content with key facts, must-know items, and exam traps highlighted.</p>
            </div>
            <div class="card">
                <h3>Flashcards</h3>
                <p>Spaced-repetition: cards surface exactly when you're about to forget them.</p>
            </div>
            <div class="card">
                <h3>Quizzes</h3>
                <p>Timed and untimed. MCQ, true/false. Immediate feedback with explanations.</p>
            </div>
        </div>
    </div>
</section>

<!-- 7. SEO PROSE -->
<section class="section section--alt">
    <div class="container container-tight">
        <h2 style="text-align:center;">A focused online aviation study platform</h2>
        <p>AviatorTutor is built for aviation professionals: line pilots and type-rating candidates, cabin crew working through recurrent training, dispatchers, ground and safety teams, instructors, SMS trainers, and aviation interview candidates putting the final polish on their preparation. The platform replaces the fragmented worksheet-and-PDF workflow with a single, structured, scenario-based study environment.</p>
        <p>We cover aircraft systems across multiple types, ICAO-aligned aviation subjects (weather and meteorology, performance and weight &amp; balance, CRM and human factors, SMS, dangerous goods, air law, navigation and PBN, communications and phraseology, operations control), QRH and memory-item mastery, SOPs, cabin safety and emergency procedures, and airline interview prep for 20+ major carriers. Every module is delivered as interactive slide-based lessons with question gates, spaced-repetition flashcards, and timed quizzes.</p>
        <p>For the team behind it: AviatorTutor is built by a network of working aviation professionals &mdash; pilots, cabin crew, safety/SMS trainers, instructors, and operational aviation experts &mdash; who got tired of fragmented study material. Aviation training online should be focused, practical, scenario-driven, and respect your time. That is what AviatorTutor is built for.</p>
    </div>
</section>

<!-- 8. FAQ (preview, full FAQ at /faq) -->
<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">FAQ</span>
            <h2>Common questions</h2>
        </div>
        <div class="faq">
            <?php foreach (\App\Controllers\MarketingController::faqList() as $f): ?>
                <details>
                    <summary><?= htmlspecialchars($f['q'], ENT_QUOTES, 'UTF-8') ?></summary>
                    <p><?= htmlspecialchars($f['a'], ENT_QUOTES, 'UTF-8') ?></p>
                </details>
            <?php endforeach; ?>
        </div>
        <p class="text-center mt-4"><a href="/faq" class="btn btn-ghost">See all questions</a></p>
    </div>
</section>

<!-- 9. CTA -->
<section class="section">
    <div class="container container-tight">
        <div class="card card--accent" style="text-align:center;padding:48px 32px;">
            <h2>Ready to study seriously?</h2>
            <p style="max-width:52ch;margin:0 auto 24px;">Create a free account and start tonight. Slide-based interactive lessons, question gates, flashcards, quizzes, QRH and memory-item drills &mdash; built by working aviation professionals for the entire aviation community.</p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <a href="/register" class="btn btn-primary btn-lg">Create free account</a>
                <a href="/pricing" class="btn btn-ghost btn-lg">View plans</a>
            </div>
        </div>
    </div>
</section>
