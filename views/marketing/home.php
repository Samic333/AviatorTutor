<?php declare(strict_types=1); ?>

<!-- 1. HERO -->
<section class="hero">
    <div class="container">
        <span class="hero__chip">Built by aviation professionals · for the aviation community</span>
        <h1>Master aircraft systems, aviation interviews, and pilot knowledge — anytime.</h1>
        <p class="lead">
            AviatorTutor is a premium self-study platform built by pilots, cabin crew, SMS trainers, instructors, and operational aviation experts — for the entire aviation community. Aircraft systems, airline interview prep, and aviation subject packs in one place.
        </p>
        <div class="hero__cta">
            <a class="btn btn-primary btn-lg" href="/register">Start learning</a>
            <a class="btn btn-ghost btn-lg" href="/pricing">View pricing</a>
        </div>
        <div class="hero__metabar">
            <span><strong class="num">38+</strong> study packs</span>
            <span><strong class="num">22</strong> Q400 systems live</span>
            <span><strong class="num">SM-2</strong> spaced-repetition flashcards</span>
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
            <p>Type-specific systems study with flashcards, quizzes, and progress tracking. Q400 is fully live; B737, A320, B787, ATR-72, A350 and more are in production — pick yours and we'll email you the day it lands.</p>
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
                <h3>Pick a study pack</h3>
                <p>Aircraft pack, airline interview pack, or aviation subject pack — one-time payment, lifetime access.</p>
            </div>
            <div class="step">
                <h3>Start studying</h3>
                <p>Lessons, flashcards, quizzes, scenarios. Your progress is saved automatically.</p>
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
            <span class="section__chip">Pricing</span>
            <h2>Pay for what you study</h2>
            <p>Per-pack one-time payment. Lifetime access. No subscription, no recurring fees.</p>
        </div>
        <div class="grid grid--3" style="max-width:1100px;margin:0 auto;align-items:stretch;">
            <div class="card" style="display:flex;flex-direction:column;gap:14px;">
                <span class="section__chip" style="align-self:flex-start;">Aircraft Packs</span>
                <h3 style="margin:0;font-size:1.2rem;">Aircraft systems study</h3>
                <p style="margin:0;color:var(--text-muted);">Type-specific systems study with flashcards, quizzes, and progress tracking. 8 aircraft published — Q400 live now.</p>
                <div style="display:flex;align-items:baseline;gap:6px;">
                    <span style="font-family:'DM Sans',Inter,sans-serif;font-size:2rem;font-weight:700;color:var(--text);">$29</span>
                    <span style="color:var(--text-soft);font-size:.9rem;">/ pack · lifetime</span>
                </div>
                <a href="/pricing" class="btn btn-primary" style="margin-top:auto;">Browse aircraft packs</a>
            </div>
            <div class="card" style="display:flex;flex-direction:column;gap:14px;">
                <span class="section__chip" style="align-self:flex-start;">Airline Interview</span>
                <h3 style="margin:0;font-size:1.2rem;">Airline interview prep</h3>
                <p style="margin:0;color:var(--text-muted);">Tech, HR, and sim-profile prep for Emirates, Qatar, Ethiopian, BA, Lufthansa, and 15 more carriers.</p>
                <div style="display:flex;align-items:baseline;gap:6px;">
                    <span style="font-family:'DM Sans',Inter,sans-serif;font-size:2rem;font-weight:700;color:var(--text);">$19</span>
                    <span style="color:var(--text-soft);font-size:.9rem;">/ pack · lifetime</span>
                </div>
                <a href="/pricing" class="btn btn-primary" style="margin-top:auto;">Browse interview packs</a>
            </div>
            <div class="card" style="display:flex;flex-direction:column;gap:14px;">
                <span class="section__chip" style="align-self:flex-start;">Aviation Subjects</span>
                <h3 style="margin:0;font-size:1.2rem;">Aviation subject packs</h3>
                <p style="margin:0;color:var(--text-muted);">Weather, performance, CRM, SMS, DGR, navigation, comms, ops control, human factors, air law.</p>
                <div style="display:flex;align-items:baseline;gap:6px;">
                    <span style="font-family:'DM Sans',Inter,sans-serif;font-size:2rem;font-weight:700;color:var(--text);">$14</span>
                    <span style="color:var(--text-soft);font-size:.9rem;">/ pack · lifetime</span>
                </div>
                <a href="/pricing" class="btn btn-primary" style="margin-top:auto;">Browse subject packs</a>
            </div>
        </div>
        <p class="text-center" style="margin-top:32px;color:var(--text-soft);font-size:.9rem;">
            Have an activation code from a partner? <a href="/redeem">Redeem here</a> — works for any pack.
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
        <p>AviatorTutor is built for aviation professionals: line pilots and type-rating candidates, cabin crew working through recurrent training, dispatchers, ground and safety teams, instructors, and aviation interview candidates putting the final polish on their preparation. The platform replaces the fragmented worksheet-and-PDF workflow with a single, structured study environment.</p>
        <p>We cover aircraft systems for multiple types (Q400 live; B737, B787, A320, ATR-72, A350 and more in production), ICAO-aligned aviation subjects (weather and meteorology, performance and weight &amp; balance, CRM and human factors, SMS, dangerous goods, air law, navigation and PBN, communications and phraseology, operations control), and airline interview prep for 20+ major carriers. Every pack ships with structured lessons, interactive content, spaced-repetition flashcards, and timed quizzes.</p>
        <p>For the team behind it: AviatorTutor is built by a network of working aviation professionals — pilots, cabin crew, safety/SMS trainers, instructors, and operational aviation experts — who got tired of fragmented study material. Aviation training online should be focused, practical, and respect your time. That is what AviatorTutor is built for.</p>
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
            <p style="max-width:48ch;margin:0 auto 24px;">Pick a pack and start tonight. One-time payment, lifetime access. Mobile, tablet, desktop — built by working aviation professionals.</p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <a href="/register" class="btn btn-primary btn-lg">Create your account</a>
                <a href="/pricing" class="btn btn-ghost btn-lg">Browse packs</a>
            </div>
        </div>
    </div>
</section>
