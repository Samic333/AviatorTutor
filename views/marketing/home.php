<?php declare(strict_types=1); ?>

<!-- 1. HERO -->
<section class="hero">
    <div class="container">
        <span class="hero__chip">Q400 module — live · more aircraft coming soon</span>
        <h1>Master aircraft systems, aviation interviews, and pilot knowledge — anytime.</h1>
        <p class="lead">
            AviatorTutor gives pilots and aviation learners structured self-study access to aircraft systems, interview questions, quizzes, and aviation knowledge — for one simple monthly subscription.
        </p>
        <div class="hero__cta">
            <a class="btn btn-primary btn-lg" href="/register">Start studying</a>
            <a class="btn btn-ghost btn-lg" href="/pricing">View pricing</a>
        </div>
        <div class="hero__metabar">
            <span><strong class="num">$10</strong> per month, all-in</span>
            <span><strong class="num">22</strong> Q400 systems mapped</span>
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
            <p>Every feature is here because pilots actually use it during line-check prep, type-rating revision, or job interviews.</p>
        </div>
        <div class="grid grid--3">
            <div class="card">
                <div class="card__icon">A</div>
                <h3>Aircraft-specific modules</h3>
                <p>Chapter-by-chapter coverage of every aircraft system, sourced from real training manuals.</p>
            </div>
            <div class="card">
                <div class="card__icon">I</div>
                <h3>Pilot interview prep</h3>
                <p>Examiner-style questions with short answers, long answers, follow-ups, and the common mistakes pilots make.</p>
            </div>
            <div class="card">
                <div class="card__icon">Q</div>
                <h3>Aviation question banks</h3>
                <p>MCQ, true/false, fill-in, scenario. Immediate feedback with explanations and weak-area tagging.</p>
            </div>
            <div class="card">
                <div class="card__icon">F</div>
                <h3>Spaced-repetition flashcards</h3>
                <p>SM-2 scheduling. Numbers, components, indications — surfaced when you're about to forget them.</p>
            </div>
            <div class="card">
                <div class="card__icon">P</div>
                <h3>Self-paced learning</h3>
                <p>No instructor, no schedule, no live classes. You move at your own speed — your progress is saved.</p>
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
            <h2>Every aircraft we cover.</h2>
            <p>Q400 is live now. <?= count(array_filter($aircraftList ?? [], fn($a) => $a['status'] === 'coming_soon')) ?> more aircraft modules are in production — pick yours and we'll email you the day it lands.</p>
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
            <span class="section__chip">Skill modules</span>
            <h2>Beyond the aircraft.</h2>
            <p>Cross-cutting topics every pilot revisits — from interview prep to CRM and general aviation fundamentals.</p>
        </div>
        <div class="grid grid--auto">
            <a href="/coming-soon/pilot-interview" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">COMING SOON</span>
                <h3 style="margin-top:8px;">Pilot Interview Questions</h3>
                <p>Technical, HR, and scenario questions with model answers.</p>
            </a>
            <a href="/coming-soon/cabin-crew" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">COMING SOON</span>
                <h3 style="margin-top:8px;">Cabin Crew Safety</h3>
                <p>Recurrent training fundamentals for flight attendants and pursers.</p>
            </a>
            <a href="/coming-soon/emergency" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">COMING SOON</span>
                <h3 style="margin-top:8px;">Emergency Procedures</h3>
                <p>Memory items, abnormal/emergency checklists, decision-making drills.</p>
            </a>
            <a href="/coming-soon/crm" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">COMING SOON</span>
                <h3 style="margin-top:8px;">CRM / Human Factors</h3>
                <p>Crew Resource Management essentials, threat-and-error management, communication.</p>
            </a>
            <a href="/coming-soon/general-aviation" class="card" style="text-decoration:none;color:inherit;">
                <span class="tile-badge tile-badge--soon">COMING SOON</span>
                <h3 style="margin-top:8px;">General Aviation Knowledge</h3>
                <p>Air law, meteorology, navigation, principles of flight, performance.</p>
            </a>
        </div>
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
                <p>Email and password — no credit card needed to register.</p>
            </div>
            <div class="step">
                <h3>Subscribe for $10/month</h3>
                <p>Redeem an activation code to unlock the study library for 30 days.</p>
            </div>
            <div class="step">
                <h3>Choose your study area</h3>
                <p>Pick an aircraft, system, or question bank. Save what you've covered.</p>
            </div>
            <div class="step">
                <h3>Learn, quiz, and track</h3>
                <p>Lessons, flashcards, quizzes, scenarios. Your progress is saved automatically.</p>
            </div>
        </div>
    </div>
</section>

<!-- 5. PRICING -->
<section id="pricing" class="section section--alt">
    <div class="container">
        <div class="section__head">
            <span class="section__chip">Pricing</span>
            <h2>One simple subscription</h2>
            <p>No tiers, no upsells, no hidden costs.</p>
        </div>
        <div class="pricing-card">
            <div style="font-size:.78rem;color:var(--text-soft);text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Monthly</div>
            <div class="pricing-card__price">$10<small>/month</small></div>
            <p class="muted" style="font-size:.92rem;margin:0;">Full access to every module currently live.</p>
            <ul class="pricing-card__list">
                <li>All Q400 aircraft systems</li>
                <li>Aviation question bank &amp; quizzes</li>
                <li>Spaced-repetition flashcards (SM-2)</li>
                <li>Progress tracking &amp; weak-area surfacing</li>
                <li>Interactive system diagrams</li>
                <li>Mobile, tablet &amp; desktop ready</li>
                <li>Cancel anytime — billing ends with the period</li>
            </ul>
            <a href="/register" class="btn btn-primary btn-lg btn-block">Start studying now</a>
            <p style="margin-top:14px;font-size:.82rem;color:var(--text-soft);">
                Already have an account? <a href="/login">Sign in</a>.
            </p>
        </div>
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
                <p>Your in-progress systems, due flashcards, recent quiz scores, and weak areas — at a glance.</p>
            </div>
            <div class="card">
                <h3>Lessons</h3>
                <p>System-by-system content with key facts, must-know items, and exam traps highlighted.</p>
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
        <p>AviatorTutor is built for pilots transitioning to a new aircraft type, student pilots preparing for theory exams, cabin crew working through recurrent training, and aviation interview candidates putting the final polish on their preparation. The platform replaces the fragmented worksheet-and-PDF workflow with a single, structured study environment.</p>
        <p>Our Q400 systems coverage is chapter-aligned to the aircraft's training manuals — electrical, hydraulic, fuel, powerplant, propeller, flight controls, landing gear, air conditioning &amp; pressurization, pneumatics, ice and rain protection, fire protection, autoflight, navigation, communications, indicating and recording (EFIS/EICAS), oxygen, lighting, and the airframe general chapter — backed by interactive diagrams, flashcards with spaced repetition, and timed quizzes.</p>
        <p>For interview preparation we are building a question bank covering technical, HR, and scenario items with model answers and follow-up prompts. For self-paced pilot learning the system saves your progress, suggests next steps, and surfaces the topics where your retention is weakest. Aviation training online should be focused, practical, and respect your time — that is what AviatorTutor is built for.</p>
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
            <p style="max-width:48ch;margin:0 auto 24px;">$10/month. Cancel anytime. Mobile, tablet, desktop. Built by someone who actually flies the line.</p>
            <a href="/register" class="btn btn-primary btn-lg">Create your account</a>
        </div>
    </div>
</section>
