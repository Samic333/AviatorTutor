<?php
/**
 * ATA24 Electrical Power — Full CBT Chapter Content
 * Included by detail.php when system ATA code is ATA24
 */
?>

<style>
/* ── Chapter-based CBT Layout ─────────────────────────── */
.cbt-wrapper { display:flex; gap:0; min-height:80vh; }
.cbt-nav {
  width:220px; flex-shrink:0;
  background:#0d1b2e; border-right:1px solid #1e3a5f;
  position:sticky; top:0; height:100vh; overflow-y:auto;
  padding:16px 0;
}
.cbt-nav-title { font-size:10px; font-weight:700; color:#4a6080; letter-spacing:1.5px; padding:0 16px 8px; text-transform:uppercase; }
.cbt-nav-item {
  display:flex; align-items:center; gap:10px;
  padding:10px 16px; cursor:pointer;
  border-left:3px solid transparent;
  font-size:13px; color:#64748b;
  transition:all .2s; text-decoration:none;
}
.cbt-nav-item:hover { background:#131f33; color:#cbd5e1; }
.cbt-nav-item.active { border-left-color:#f59e0b; background:#131f33; color:#f59e0b; }
.cbt-nav-item .nav-num {
  width:22px; height:22px; border-radius:50%;
  background:#1e3a5f; display:flex; align-items:center; justify-content:center;
  font-size:11px; font-weight:700; color:#60a5fa; flex-shrink:0;
}
.cbt-nav-item.active .nav-num { background:#f59e0b22; color:#f59e0b; }
.cbt-nav-item.done .nav-num { background:#22c55e22; color:#22c55e; }
.cbt-nav-item.done { color:#22c55e44; }
.cbt-nav-progress { padding:16px; border-top:1px solid #1e3a5f; margin-top:8px; }
.nav-prog-bar { height:4px; background:#1e3a5f; border-radius:2px; margin-top:6px; }
.nav-prog-fill { height:100%; background:#f59e0b; border-radius:2px; transition:width .4s; }

.cbt-content { flex:1; padding:0 40px 60px; max-width:860px; }

/* ── Chapter Sections ── */
.chapter-section {
  padding:48px 0 40px; border-bottom:1px solid #1e3a5f;
  animation:fadeInUp .4s ease both;
}
.chapter-section:last-child { border-bottom:none; }
.chapter-badge {
  display:inline-flex; align-items:center; gap:8px;
  background:#1e3a5f; border:1px solid #3b82f633;
  border-radius:20px; padding:4px 14px;
  font-size:11px; font-weight:700; color:#60a5fa;
  letter-spacing:.8px; text-transform:uppercase; margin-bottom:14px;
}
.chapter-badge .badge-num { color:#f59e0b; }
.chapter-title {
  font-size:26px; font-weight:800; color:#f1f5f9;
  margin:0 0 6px; line-height:1.2;
}
.chapter-subtitle { font-size:14px; color:#64748b; margin:0 0 24px; }
.chapter-objective {
  background:#0f2040; border:1px solid #1d4ed855; border-radius:10px;
  padding:14px 18px; margin-bottom:28px;
  display:flex; gap:12px; align-items:flex-start;
}
.chapter-objective .obj-icon { font-size:18px; flex-shrink:0; margin-top:1px; }
.chapter-objective p { margin:0; font-size:13.5px; color:#93c5fd; line-height:1.6; }
.chapter-objective strong { color:#bfdbfe; }

/* ── Content Blocks ── */
.cbt-body { font-size:15px; color:#cbd5e1; line-height:1.85; margin-bottom:24px; }
.cbt-body p { margin:0 0 16px; }
.cbt-body strong { color:#f1f5f9; }
.cbt-body em { color:#f59e0b; font-style:normal; font-weight:600; }

.analogy-box {
  background:linear-gradient(135deg,#1a2d4a,#0f1f38);
  border:1px solid #f59e0b44; border-left:4px solid #f59e0b;
  border-radius:12px; padding:20px 24px; margin:24px 0;
}
.analogy-box .analogy-label { font-size:10px; font-weight:700; color:#f59e0b; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:8px; }
.analogy-box p { margin:0; font-size:14.5px; color:#e2e8f0; line-height:1.7; }

.info-card {
  border-radius:12px; padding:20px 24px; margin:20px 0;
}
.info-card.blue { background:#0f2040; border:1px solid #3b82f633; }
.info-card.amber { background:#1a1500; border:1px solid #f59e0b44; }
.info-card.red { background:#1a0808; border:1px solid #ef444444; }
.info-card.green { background:#071a0f; border:1px solid #22c55e44; }
.info-card.purple { background:#150d2a; border:1px solid #8b5cf644; }
.info-card .card-head {
  font-size:11px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase;
  margin-bottom:12px; display:flex; align-items:center; gap:8px;
}
.info-card.blue .card-head { color:#60a5fa; }
.info-card.amber .card-head { color:#f59e0b; }
.info-card.red .card-head { color:#ef4444; }
.info-card.green .card-head { color:#22c55e; }
.info-card.purple .card-head { color:#a78bfa; }

/* ── Component Cards ── */
.component-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin:24px 0; }
.comp-card {
  background:#0d1b2e; border:1px solid #1e3a5f; border-radius:12px;
  padding:20px; cursor:pointer; transition:all .2s;
}
.comp-card:hover { border-color:#3b82f6; transform:translateY(-2px); }
.comp-card .comp-icon { font-size:28px; margin-bottom:10px; }
.comp-card .comp-name { font-size:15px; font-weight:700; color:#f1f5f9; margin:0 0 4px; }
.comp-card .comp-role { font-size:12px; color:#64748b; margin:0 0 12px; }
.comp-card .comp-spec { font-size:12px; color:#f59e0b; font-weight:600; }
.comp-card .comp-body { font-size:13px; color:#94a3b8; line-height:1.6; margin-top:10px; display:none; border-top:1px solid #1e3a5f; padding-top:10px; }
.comp-card.expanded .comp-body { display:block; }
.comp-card.expanded { border-color:#f59e0b; }

/* ── Spec Table ── */
.spec-table { width:100%; border-collapse:collapse; margin:20px 0; border-radius:10px; overflow:hidden; }
.spec-table th { background:#131f33; color:#60a5fa; font-size:11px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; padding:10px 16px; text-align:left; }
.spec-table td { padding:11px 16px; font-size:13.5px; border-bottom:1px solid #1e3a5f; }
.spec-table tr:last-child td { border-bottom:none; }
.spec-table tr:nth-child(even) td { background:#0a1628; }
.spec-table .td-key { color:#94a3b8; width:45%; }
.spec-table .td-val { color:#f1f5f9; font-weight:600; }
.spec-table .td-badge { }
.spec-badge {
  display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700;
}
.spec-badge.must { background:#f59e0b22; color:#f59e0b; border:1px solid #f59e0b44; }
.spec-badge.critical { background:#ef444422; color:#ef4444; border:1px solid #ef444444; }
.spec-badge.normal { background:#22c55e22; color:#22c55e; border:1px solid #22c55e44; }

/* ── Flow Steps ── */
.flow-steps { display:flex; flex-direction:column; gap:0; margin:24px 0; }
.flow-step {
  display:flex; gap:16px; padding:16px 0;
  border-left:2px solid #1e3a5f; padding-left:20px; margin-left:10px;
  position:relative;
}
.flow-step::before {
  content:attr(data-step);
  position:absolute; left:-14px; top:16px;
  width:26px; height:26px; border-radius:50%;
  background:#0f1f38; border:2px solid #3b82f6;
  display:flex; align-items:center; justify-content:center;
  font-size:11px; font-weight:800; color:#60a5fa;
  display:grid; place-items:center;
}
.flow-step:last-child { border-left-color:transparent; }
.flow-step-content { flex:1; padding-bottom:8px; }
.flow-step-title { font-size:15px; font-weight:700; color:#f1f5f9; margin:0 0 6px; }
.flow-step-desc { font-size:13.5px; color:#94a3b8; line-height:1.65; margin:0; }
.flow-step-state {
  display:inline-flex; align-items:center; gap:6px;
  font-size:11px; font-weight:700; padding:2px 10px; border-radius:20px; margin-top:6px;
}
.state-normal { background:#22c55e22; color:#22c55e; }
.state-warn { background:#f59e0b22; color:#f59e0b; }
.state-emer { background:#ef444422; color:#ef4444; }

/* ── Failure Scenarios ── */
.failure-grid { display:flex; flex-direction:column; gap:14px; margin:24px 0; }
.failure-card {
  background:#0d1b2e; border:1px solid #1e3a5f; border-radius:12px; overflow:hidden;
}
.failure-header {
  display:flex; align-items:center; gap:12px; padding:14px 18px;
  cursor:pointer; transition:background .2s;
}
.failure-header:hover { background:#131f33; }
.failure-severity {
  padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; flex-shrink:0;
}
.sev-high { background:#ef444422; color:#ef4444; border:1px solid #ef444433; }
.sev-med { background:#f59e0b22; color:#f59e0b; border:1px solid #f59e0b33; }
.failure-name { font-size:14px; font-weight:700; color:#f1f5f9; flex:1; }
.failure-eicas { font-size:11px; color:#60a5fa; font-family:monospace; background:#0f1f38; padding:2px 8px; border-radius:6px; }
.failure-body { display:none; padding:0 18px 18px; }
.failure-body.open { display:block; }
.failure-row { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-top:12px; }
.failure-col { background:#0a1628; border-radius:8px; padding:12px; }
.failure-col-label { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#4a6080; margin-bottom:6px; }
.failure-col-text { font-size:13px; color:#cbd5e1; line-height:1.6; }

/* ── Inline Quiz ── */
.inline-quiz {
  background:linear-gradient(135deg,#150d2a,#0d1225);
  border:1px solid #8b5cf644; border-radius:14px;
  padding:24px; margin:32px 0;
}
.quiz-label { font-size:10px; font-weight:700; color:#8b5cf6; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:10px; }
.quiz-question { font-size:16px; color:#f1f5f9; font-weight:600; margin:0 0 16px; line-height:1.5; }
.quiz-options { display:flex; flex-direction:column; gap:8px; }
.quiz-opt {
  padding:12px 16px; border-radius:8px; cursor:pointer;
  border:1px solid #2d1f6e; background:#1a0f3d;
  font-size:13.5px; color:#c4b5fd; transition:all .2s;
}
.quiz-opt:hover { border-color:#8b5cf6; background:#231553; }
.quiz-opt.correct { background:#071a0f; border-color:#22c55e; color:#4ade80; }
.quiz-opt.incorrect { background:#1a0808; border-color:#ef4444; color:#fca5a5; }
.quiz-opt.disabled { cursor:default; }
.quiz-explanation { margin-top:14px; padding:12px 16px; background:#0a1628; border-radius:8px; font-size:13px; color:#93c5fd; line-height:1.6; display:none; }
.quiz-explanation.show { display:block; }

/* ── Full Quiz Section ── */
.full-quiz { padding:48px 0; }
.quiz-progress-bar { height:6px; background:#1e3a5f; border-radius:3px; margin:16px 0 32px; }
.quiz-progress-fill { height:100%; background:linear-gradient(90deg,#f59e0b,#fbbf24); border-radius:3px; transition:width .4s; }
.quiz-card {
  background:#0d1b2e; border:1px solid #1e3a5f; border-radius:16px;
  padding:32px; margin-bottom:16px;
}
.quiz-q-num { font-size:11px; color:#4a6080; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px; }
.quiz-q-text { font-size:17px; color:#f1f5f9; font-weight:600; line-height:1.5; margin:0 0 20px; }
.quiz-result {
  display:none; margin-top:12px; padding:14px 18px; border-radius:10px; font-size:13.5px; line-height:1.6;
}
.quiz-result.correct-result { background:#071a0f; border:1px solid #22c55e44; color:#4ade80; display:block; }
.quiz-result.wrong-result { background:#1a0808; border:1px solid #ef444444; color:#fca5a5; display:block; }
.quiz-next-btn {
  margin-top:20px; padding:10px 28px; background:#f59e0b; color:#000;
  border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;
  display:none;
}
.quiz-next-btn.show { display:inline-block; }
.quiz-score-card {
  background:linear-gradient(135deg,#0a1628,#131f33);
  border:2px solid #f59e0b44; border-radius:20px; padding:40px;
  text-align:center; display:none;
}
.quiz-score-card.show { display:block; }
.score-number { font-size:64px; font-weight:900; color:#f59e0b; line-height:1; }
.score-label { font-size:16px; color:#94a3b8; margin:8px 0 20px; }
.score-message { font-size:15px; color:#cbd5e1; line-height:1.6; }

/* ── Read Time / Chapter Done ── */
.chapter-meta { display:flex; align-items:center; gap:16px; margin-bottom:24px; }
.meta-pill { font-size:12px; color:#4a6080; display:flex; align-items:center; gap:5px; }
.mark-read-btn {
  margin-top:28px; padding:10px 24px;
  background:#22c55e1a; border:1px solid #22c55e44; color:#22c55e;
  border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; transition:all .2s;
}
.mark-read-btn:hover { background:#22c55e2a; }
.mark-read-btn.read { background:#22c55e; color:#000; cursor:default; }
</style>

<div class="cbt-wrapper">

  <!-- ── LEFT NAV ── -->
  <nav class="cbt-nav">
    <div class="cbt-nav-title">Chapters</div>
    <a class="cbt-nav-item active" href="#ch1" onclick="setActiveNav(this)">
      <span class="nav-num">1</span> The Big Picture
    </a>
    <a class="cbt-nav-item" href="#ch2" onclick="setActiveNav(this)">
      <span class="nav-num">2</span> AC Generation
    </a>
    <a class="cbt-nav-item" href="#ch3" onclick="setActiveNav(this)">
      <span class="nav-num">3</span> The TRU System
    </a>
    <a class="cbt-nav-item" href="#ch4" onclick="setActiveNav(this)">
      <span class="nav-num">4</span> Battery System
    </a>
    <a class="cbt-nav-item" href="#ch5" onclick="setActiveNav(this)">
      <span class="nav-num">5</span> Distribution Buses
    </a>
    <a class="cbt-nav-item" href="#ch6" onclick="setActiveNav(this)">
      <span class="nav-num">6</span> Normal Operation
    </a>
    <a class="cbt-nav-item" href="#ch7" onclick="setActiveNav(this)">
      <span class="nav-num">7</span> Abnormal Procedures
    </a>
    <a class="cbt-nav-item" href="#ch8" onclick="setActiveNav(this)">
      <span class="nav-num">8</span> Limits &amp; Numbers
    </a>
    <a class="cbt-nav-item" href="#ch9" onclick="setActiveNav(this)">
      <span class="nav-num">9</span> Knowledge Check
    </a>
    <div class="cbt-nav-progress">
      <div class="meta-pill">Progress</div>
      <div class="nav-prog-bar"><div class="nav-prog-fill" id="navProgress" style="width:0%"></div></div>
    </div>
  </nav>

  <!-- ── MAIN CONTENT ── -->
  <div class="cbt-content">

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 1 — THE BIG PICTURE
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch1">
      <div class="chapter-badge"><span class="badge-num">01</span> Introduction</div>
      <h2 class="chapter-title">The Big Picture</h2>
      <p class="chapter-subtitle">Why the electrical system exists and what you need to walk away knowing</p>
      <div class="chapter-meta">
        <span class="meta-pill">⏱ 8 min read</span>
        <span class="meta-pill">📋 ATA Chapter 24</span>
      </div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> After this chapter you will understand the <strong>purpose and priority chain</strong> of the Q400 electrical system — who makes power, who converts it, and who uses it as backup.</p>
      </div>

      <div class="analogy-box">
        <div class="analogy-label">💡 Start Here — The Analogy</div>
        <p>Think of your home. The <strong>power company</strong> generates electricity and sends it down wires to your <strong>distribution board</strong> (the breaker box). From there, circuits branch out to power your lights, fridge, and TV. If the power company fails, your <strong>generator</strong> in the garage kicks in.
        <br><br>
        The Q400 electrical system works <em>exactly</em> the same way — just at 35,000 feet. The engines are your power company, the buses are your breaker box, and the batteries are your backup generator. Once you see it this way, the whole system clicks.</p>
      </div>

      <div class="cbt-body">
        <p>The <strong>Q400 Electrical Power and Generation Distribution System (EPGDS)</strong> has one job: keep reliable electricity flowing to every aircraft system that needs it — at all times, even if a source fails.</p>
        <p>The system has <em>two completely independent generation sources</em> (one per engine) feeding a network of distribution buses. Each source is capable of powering the entire aircraft independently. Add two batteries as a last-resort emergency supply, and you have a system with multiple layers of redundancy.</p>
      </div>

      <div class="info-card amber">
        <div class="card-head">⚡ The Power Priority Chain — Memorize This</div>
        <div class="flow-steps">
          <div class="flow-step" data-step="1">
            <div class="flow-step-content">
              <p class="flow-step-title">Engine-Driven Alternators (Primary)</p>
              <p class="flow-step-desc">Two alternators — one on each engine. They produce <strong>115V AC</strong> and are the aircraft's main power supply during all normal flight operations. As long as one engine is running, you have primary power.</p>
              <span class="flow-step-state state-normal">✓ Normal operation</span>
            </div>
          </div>
          <div class="flow-step" data-step="2">
            <div class="flow-step-content">
              <p class="flow-step-title">TRUs — Transformer Rectifier Units (Conversion)</p>
              <p class="flow-step-desc">The AC from the alternators feeds into two TRUs. Each TRU converts <strong>115V AC → 28V DC</strong>. Almost every flight-critical instrument and avionics box runs on 28V DC.</p>
              <span class="flow-step-state state-normal">✓ Powered when alternator is online</span>
            </div>
          </div>
          <div class="flow-step" data-step="3">
            <div class="flow-step-content">
              <p class="flow-step-title">Batteries (Emergency Backup)</p>
              <p class="flow-step-desc">Two NiCad batteries provide <strong>28V DC</strong> directly. They power the Hot Battery Bus at all times and power the Emergency Bus when everything else has failed. Battery-only endurance is approximately <em>30 minutes</em>.</p>
              <span class="flow-step-state state-emer">⚠ Emergency use only during flight</span>
            </div>
          </div>
          <div class="flow-step" data-step="4">
            <div class="flow-step-content">
              <p class="flow-step-title">External Power (Ground Only)</p>
              <p class="flow-step-desc">A Ground Power Unit (GPU) connects via the external power receptacle. Used for maintenance and pre-flight to power the aircraft without running engines. AC GPU supplies <strong>115V AC / 400Hz</strong>; DC GPU supplies <strong>28V DC</strong>.</p>
              <span class="flow-step-state state-warn">⛽ Ground use only</span>
            </div>
          </div>
        </div>
      </div>

      <div class="info-card blue">
        <div class="card-head">📌 5 Numbers You Must Know Before Moving On</div>
        <table class="spec-table">
          <thead><tr><th>Parameter</th><th>Value</th><th>Importance</th></tr></thead>
          <tbody>
            <tr><td class="td-key">AC bus voltage</td><td class="td-val">115V AC, 3-phase</td><td><span class="spec-badge must">MUST KNOW</span></td></tr>
            <tr><td class="td-key">DC bus voltage</td><td class="td-val">28V DC (27.5–29V normal)</td><td><span class="spec-badge must">MUST KNOW</span></td></tr>
            <tr><td class="td-key">Number of alternators</td><td class="td-val">2 (one per engine)</td><td><span class="spec-badge must">MUST KNOW</span></td></tr>
            <tr><td class="td-key">Number of TRUs</td><td class="td-val">2 (one per AC bus)</td><td><span class="spec-badge must">MUST KNOW</span></td></tr>
            <tr><td class="td-key">Number of batteries</td><td class="td-val">2 NiCad batteries</td><td><span class="spec-badge must">MUST KNOW</span></td></tr>
          </tbody>
        </table>
      </div>

      <div class="inline-quiz" data-quiz="q1">
        <div class="quiz-label">🧠 Quick Check — Chapter 1</div>
        <p class="quiz-question">If both engine-driven alternators fail in flight, what provides electrical power to essential instruments?</p>
        <div class="quiz-options">
          <div class="quiz-opt" onclick="checkQuiz(this,'q1',false)">The TRUs switch to battery mode and continue supplying 115V AC</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q1',true)">The two NiCad batteries supply 28V DC to the essential and emergency buses</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q1',false)">External power automatically connects via the APU</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q1',false)">The No.3 hydraulic system powers an emergency generator</div>
        </div>
        <div class="quiz-explanation" id="q1-exp">
          <strong>Correct.</strong> When both alternators fail, the TRUs lose their AC input and stop producing DC. The two NiCad batteries take over, supplying 28V DC directly to the DC Essential Bus and Emergency Bus. There is no APU on the standard Q400. Battery-only endurance is approximately 30 minutes — this is why a dual generator failure demands immediate landing.
        </div>
      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 1)">✓ Mark Chapter 1 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 2 — AC GENERATION
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch2">
      <div class="chapter-badge"><span class="badge-num">02</span> AC Power Generation</div>
      <h2 class="chapter-title">The Alternators — Your Primary Power Source</h2>
      <p class="chapter-subtitle">Where the electricity is born and why the Q400 is different from a jet</p>
      <div class="chapter-meta"><span class="meta-pill">⏱ 12 min read</span></div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> Understand how the Q400's AC generators work, why they produce <strong>variable frequency</strong> (unlike jet aircraft), and what happens when one fails.</p>
      </div>

      <div class="cbt-body">
        <p>Each Q400 engine drives a <strong>brushless alternator</strong> through the propeller reduction gearbox. When the engine spins, the alternator spins — and out comes <strong>115V AC, 3-phase</strong> electrical power.</p>
        <p>Here is the key fact that surprises many candidates:</p>
      </div>

      <div class="analogy-box">
        <div class="analogy-label">⚡ Why Variable Frequency? — Critical Concept</div>
        <p>On jet aircraft (A320, B737, etc.), the generators always spin at the same speed via a <strong>Constant Speed Drive (CSD)</strong>, producing exactly <strong>400 Hz</strong> regardless of engine RPM. The Q400 is a turboprop — it has <strong>no CSD</strong>. The alternator is directly coupled to the propeller reduction gearbox, so its speed (and therefore the AC frequency) varies with engine RPM.<br><br>
        This means Q400 AC power is <em>variable frequency</em> — and that is completely normal. The aircraft's AC-powered systems are specifically designed to accept variable frequency. Don't let this throw you in the oral exam — it's a feature, not a fault.</p>
      </div>

      <div class="info-card blue">
        <div class="card-head">🔧 Generator Technical Specifications</div>
        <table class="spec-table">
          <thead><tr><th>Parameter</th><th>Value</th><th>Note</th></tr></thead>
          <tbody>
            <tr><td class="td-key">Type</td><td class="td-val">Brushless AC alternator</td><td>No brushes = less maintenance</td></tr>
            <tr><td class="td-key">Voltage output</td><td class="td-val">115V AC, 3-phase</td><td>Industry standard aircraft AC</td></tr>
            <tr><td class="td-key">Frequency</td><td class="td-val">Variable (engine RPM dependent)</td><td>Not 400Hz like jets</td></tr>
            <tr><td class="td-key">Drive source</td><td class="td-val">Propeller reduction gearbox</td><td>One per engine</td></tr>
            <tr><td class="td-key">Quantity</td><td class="td-val">2 (Generator 1 + Generator 2)</td><td>Either can power entire aircraft</td></tr>
            <tr><td class="td-key">Control</td><td class="td-val">Generator Control Unit (GCU)</td><td>Manages voltage regulation &amp; protection</td></tr>
          </tbody>
        </table>
      </div>

      <div class="cbt-body">
        <p>Each generator is controlled by a <strong>Generator Control Unit (GCU)</strong>. The GCU does three things: regulates the output voltage to 115V, protects the bus from fault conditions (overvoltage, undervoltage, overcurrent), and controls the generator line contactor (the "switch" that connects the generator to its AC bus).</p>
        <p>Under normal conditions, <strong>Gen 1 powers AC Bus 1</strong> and <strong>Gen 2 powers AC Bus 2</strong>. A <em>Bus Tie Contactor (BTC)</em> can connect both buses together — this is what allows one generator to power the entire aircraft if the other fails.</p>
      </div>

      <div class="component-grid">
        <div class="comp-card" onclick="toggleComp(this)">
          <div class="comp-icon">⚡</div>
          <p class="comp-name">Generator 1</p>
          <p class="comp-role">Driven by Engine 1 / P3 Gearbox</p>
          <p class="comp-spec">115V AC → AC Bus 1</p>
          <div class="comp-body">Normally powers AC Bus 1 exclusively. If Gen 2 fails, the Bus Tie Contactor closes automatically and Gen 1 picks up the full aircraft AC load. The GCU continuously monitors output and trips the line contactor in case of fault.</div>
        </div>
        <div class="comp-card" onclick="toggleComp(this)">
          <div class="comp-icon">⚡</div>
          <p class="comp-name">Generator 2</p>
          <p class="comp-role">Driven by Engine 2 / P4 Gearbox</p>
          <p class="comp-spec">115V AC → AC Bus 2</p>
          <div class="comp-body">Normally powers AC Bus 2 exclusively. If Gen 1 fails, Gen 2 similarly takes over both buses via the Bus Tie. Each generator is fully rated to supply the entire aircraft, so single-generator operation is normal and safe — just reduce unnecessary loads.</div>
        </div>
        <div class="comp-card" onclick="toggleComp(this)">
          <div class="comp-icon">🔌</div>
          <p class="comp-name">Generator Control Unit (GCU)</p>
          <p class="comp-role">One per generator</p>
          <p class="comp-spec">Voltage regulation + protection</p>
          <div class="comp-body">The GCU is the "brain" behind each generator. It regulates output voltage, monitors for faults (overvoltage, undervoltage, feeder fault), and controls the Generator Line Contactor (GLC). If a fault is detected, the GCU opens the GLC instantly to protect the bus.</div>
        </div>
        <div class="comp-card" onclick="toggleComp(this)">
          <div class="comp-icon">🔗</div>
          <p class="comp-name">Bus Tie Contactor (BTC)</p>
          <p class="comp-role">Connects AC Bus 1 to AC Bus 2</p>
          <p class="comp-spec">Auto-closes on generator failure</p>
          <div class="comp-body">Under normal operation, the BTC is open — both AC buses are isolated. If one generator fails, the BTC closes automatically within milliseconds, connecting both buses so the remaining generator can supply both. This happens without pilot input. The pilot sees the EICAS caution but the electrical system self-recovers.</div>
        </div>
      </div>

      <div class="info-card red">
        <div class="card-head">⚠️ Exam Trap — Generator vs Alternator</div>
        <p style="font-size:14px;color:#fca5a5;line-height:1.7;margin:0;">
          Many candidates use "generator" and "alternator" interchangeably — and in casual conversation that's fine. But technically: an <strong>alternator</strong> produces AC power inherently (no commutator needed). The Q400 uses alternators. In the FCOM and exam questions you'll see "generator" used loosely to mean the alternator assembly + GCU combination. Don't get confused — they mean the same physical unit.
        </p>
      </div>

      <div class="info-card green">
        <div class="card-head">✅ Single Generator Failure — What Happens Automatically</div>
        <div class="flow-steps">
          <div class="flow-step" data-step="1"><div class="flow-step-content"><p class="flow-step-title">Generator trips</p><p class="flow-step-desc">GCU detects fault, opens Generator Line Contactor. That AC bus loses power momentarily.</p></div></div>
          <div class="flow-step" data-step="2"><div class="flow-step-content"><p class="flow-step-title">Bus Tie Contactor closes (auto)</p><p class="flow-step-desc">Within milliseconds, the BTC closes automatically, connecting both AC buses to the remaining generator.</p></div></div>
          <div class="flow-step" data-step="3"><div class="flow-step-content"><p class="flow-step-title">EICAS caution displayed</p><p class="flow-step-desc">Crew sees GEN 1 (or GEN 2) FAIL caution. The remaining generator is now supplying full aircraft AC load.</p></div></div>
          <div class="flow-step" data-step="4"><div class="flow-step-content"><p class="flow-step-title">Pilot action</p><p class="flow-step-desc">Identify, verify, reduce non-essential electrical loads. Land at nearest suitable aerodrome — not immediately, but without delay. Single-generator operation is safe but not indefinite.</p></div></div>
        </div>
      </div>

      <div class="inline-quiz" data-quiz="q2">
        <div class="quiz-label">🧠 Quick Check — Chapter 2</div>
        <p class="quiz-question">Why does the Q400 produce variable frequency AC power, while a Boeing 737 produces constant 400 Hz?</p>
        <div class="quiz-options">
          <div class="quiz-opt" onclick="checkQuiz(this,'q2',false)">The Q400 uses older technology — newer jets have upgraded to fixed-frequency generators</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q2',true)">The Q400 alternators are directly coupled to the propeller gearbox with no CSD, so frequency varies with engine RPM</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q2',false)">Variable frequency is more fuel efficient at all engine RPMs</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q2',false)">The Q400 GCU automatically adjusts frequency based on electrical demand</div>
        </div>
        <div class="quiz-explanation" id="q2-exp">
          <strong>Correct.</strong> Jet aircraft use a Constant Speed Drive (CSD) or Integrated Drive Generator (IDG) to maintain the generator at a fixed RPM regardless of engine speed — ensuring constant 400Hz output. The Q400 turboprop has no CSD; its alternator is mechanically coupled to the propeller reduction gearbox, so as engine RPM changes (e.g., during power changes), the AC frequency changes with it. The aircraft's loads are designed to tolerate this.
        </div>
      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 2)">✓ Mark Chapter 2 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 3 — TRU SYSTEM
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch3">
      <div class="chapter-badge"><span class="badge-num">03</span> DC Conversion</div>
      <h2 class="chapter-title">The TRU — Converting AC to DC</h2>
      <p class="chapter-subtitle">The bridge between your generators and your flight instruments</p>
      <div class="chapter-meta"><span class="meta-pill">⏱ 10 min read</span></div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> Understand exactly what a TRU does, why we need DC power, and what critical systems it feeds.</p>
      </div>

      <div class="analogy-box">
        <div class="analogy-label">💡 The Analogy — Your Laptop Charger</div>
        <p>Your laptop charger does exactly what a TRU does. Your wall socket outputs AC power (240V/50Hz in most countries). Your laptop needs DC power (typically 19V DC). The charger takes AC in, transforms it to a lower voltage, then rectifies it to DC. The TRU is that charger — but for the aircraft, converting <strong>115V AC → 28V DC</strong> to power avionics, instruments, and controls.</p>
      </div>

      <div class="cbt-body">
        <p>The name breaks down simply: <strong>T</strong>ransformer <strong>R</strong>ectifier <strong>U</strong>nit. The transformer steps down the voltage; the rectifier converts AC waveform to DC. The output is smooth, steady <strong>28V DC</strong>.</p>
        <p>The Q400 has <strong>two TRUs</strong>. TRU 1 is powered by AC Bus 1 (Gen 1). TRU 2 is powered by AC Bus 2 (Gen 2). Each TRU supplies its own DC bus. This mirrors the AC redundancy — lose one generator, you still have one TRU and one DC bus fully powered. Lose both generators, both TRUs go offline and the batteries take over.</p>
      </div>

      <div class="info-card blue">
        <div class="card-head">🔧 TRU Specifications</div>
        <table class="spec-table">
          <thead><tr><th>Parameter</th><th>Value</th></tr></thead>
          <tbody>
            <tr><td class="td-key">Input</td><td class="td-val">115V AC, 3-phase (from AC bus)</td></tr>
            <tr><td class="td-key">Output</td><td class="td-val">28V DC (regulated)</td></tr>
            <tr><td class="td-key">Quantity</td><td class="td-val">2 (TRU 1 and TRU 2)</td></tr>
            <tr><td class="td-key">TRU 1 powered by</td><td class="td-val">AC Bus 1 (Gen 1)</td></tr>
            <tr><td class="td-key">TRU 2 powered by</td><td class="td-val">AC Bus 2 (Gen 2)</td></tr>
            <tr><td class="td-key">Output — TRU 1</td><td class="td-val">DC Bus 1</td></tr>
            <tr><td class="td-key">Output — TRU 2</td><td class="td-val">DC Bus 2</td></tr>
          </tbody>
        </table>
      </div>

      <div class="info-card amber">
        <div class="card-head">⭐ What Runs on DC Power (28V DC)?</div>
        <p style="font-size:14px;color:#fde68a;line-height:1.7;margin:0 0 10px;">Almost everything the pilot directly relies on uses DC. This is why DC is the "lifeblood" — even in a dual-generator failure, the batteries maintain DC power to preserve essential flight capability:</p>
        <ul style="margin:0;padding-left:20px;color:#fde68a;font-size:14px;line-height:1.9;">
          <li>Flight instruments (attitude, altimeter, airspeed)</li>
          <li>Navigation systems (FMS, VOR, ILS)</li>
          <li>Autopilot and flight director</li>
          <li>Engine controls and FADEC</li>
          <li>EICAS display and warning systems</li>
          <li>Propeller pitch control</li>
          <li>Landing gear indication</li>
          <li>Communication radios (VHF)</li>
        </ul>
      </div>

      <div class="inline-quiz" data-quiz="q3">
        <div class="quiz-label">🧠 Quick Check — Chapter 3</div>
        <p class="quiz-question">If TRU 1 fails but Generator 1 is still operating normally, what is the most likely consequence?</p>
        <div class="quiz-options">
          <div class="quiz-opt" onclick="checkQuiz(this,'q3',false)">The aircraft loses all DC power immediately</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q3',true)">DC Bus 1 loses power but DC Bus 2 (from TRU 2) remains operational; essential systems are maintained</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q3',false)">Generator 1 automatically shuts down in sympathy with the TRU</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q3',false)">The batteries activate and take over all DC loads</div>
        </div>
        <div class="quiz-explanation" id="q3-exp">
          <strong>Correct.</strong> TRU 1 and TRU 2 are independent. A TRU 1 failure only affects DC Bus 1. DC Bus 2, powered by TRU 2 (which is powered by Gen 2/AC Bus 2), remains fully operational. The DC Essential Bus will shed some loads but critical flight systems remain powered. The batteries do not automatically activate — they only take over if both TRUs lose their AC input simultaneously.
        </div>
      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 3)">✓ Mark Chapter 3 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 4 — BATTERY SYSTEM
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch4">
      <div class="chapter-badge"><span class="badge-num">04</span> Emergency Power</div>
      <h2 class="chapter-title">The Battery System — Your Last Resort</h2>
      <p class="chapter-subtitle">What NiCad means, what the Hot Battery Bus is, and how long you have</p>
      <div class="chapter-meta"><span class="meta-pill">⏱ 12 min read</span></div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> Understand the Q400 battery chemistry, their three critical roles, the Hot Battery Bus concept, and battery endurance in an emergency.</p>
      </div>

      <div class="cbt-body">
        <p>The Q400 has two <strong>Nickel-Cadmium (NiCad) batteries</strong>. You'll see NiCad written as "Ni-Cd" on data plates. These aren't your AA household batteries — they are high-performance aviation batteries designed to deliver large amounts of current instantly.</p>
      </div>

      <div class="analogy-box">
        <div class="analogy-label">💡 Why NiCad and Not Lead-Acid?</div>
        <p>Your car uses a lead-acid battery. It's cheap and it works — but under cold temperature or heavy load, voltage drops significantly. At -40°C on final approach with everything demanding power, a lead-acid battery might let you down at the worst moment.<br><br>
        <strong>NiCad batteries maintain voltage under heavy load and extreme temperatures.</strong> They also have a longer service life and can withstand deep discharge cycles without damage. For aviation, this reliability is worth the extra cost.</p>
      </div>

      <div class="info-card blue">
        <div class="card-head">🔋 Battery Specifications</div>
        <table class="spec-table">
          <thead><tr><th>Parameter</th><th>Value</th></tr></thead>
          <tbody>
            <tr><td class="td-key">Type</td><td class="td-val">Nickel-Cadmium (NiCad / Ni-Cd)</td></tr>
            <tr><td class="td-key">Nominal voltage</td><td class="td-val">24V DC each (nominal)</td></tr>
            <tr><td class="td-key">Operating voltage</td><td class="td-val">~28V DC when fully charged</td></tr>
            <tr><td class="td-key">Quantity</td><td class="td-val">2 batteries</td></tr>
            <tr><td class="td-key">Emergency endurance</td><td class="td-val">~30 minutes (full load)</td></tr>
            <tr><td class="td-key">Location</td><td class="td-val">Equipment bay / battery compartment</td></tr>
            <tr><td class="td-key">Charging</td><td class="td-val">By TRUs when generators online</td></tr>
          </tbody>
        </table>
      </div>

      <div class="cbt-body">
        <p>The batteries serve three distinct roles — and candidates often only know one. All three will come up in your oral exam:</p>
      </div>

      <div class="info-card amber">
        <div class="card-head">⭐ The Three Roles of the Batteries</div>
        <div class="flow-steps">
          <div class="flow-step" data-step="1">
            <div class="flow-step-content">
              <p class="flow-step-title">Role 1 — Engine Starting</p>
              <p class="flow-step-desc">The batteries provide the initial electrical power for engine starting — energizing the starter motor and fuel controls before the engine's own generator comes online. Once the engine reaches sufficient RPM, the alternator takes over and the battery resumes charging.</p>
              <span class="flow-step-state state-normal">Used every start</span>
            </div>
          </div>
          <div class="flow-step" data-step="2">
            <div class="flow-step-content">
              <p class="flow-step-title">Role 2 — Hot Battery Bus (Always Live)</p>
              <p class="flow-step-desc">The <em>Hot Battery Bus</em> is permanently and directly connected to Battery 1. It powers items that must <strong>always</strong> have power regardless of any switch position: the fire detection and suppression system, certain cockpit lighting, and the EICAS memory function. Even with the battery master switch OFF, the Hot Battery Bus remains powered from the battery.</p>
              <span class="flow-step-state state-warn">Always powered — cannot be turned off in normal operation</span>
            </div>
          </div>
          <div class="flow-step" data-step="3">
            <div class="flow-step-content">
              <p class="flow-step-title">Role 3 — Emergency Backup Power</p>
              <p class="flow-step-desc">If both generators fail, the TRUs lose their AC supply and go offline. The batteries automatically take over the DC Essential Bus and Emergency Bus, providing power to essential flight instruments, navigation, communications, and critical controls. Duration: approximately <em>30 minutes</em> with full emergency load — less if you have extra loads on.</p>
              <span class="flow-step-state state-emer">⚠ Land immediately on battery-only power</span>
            </div>
          </div>
        </div>
      </div>

      <div class="info-card red">
        <div class="card-head">🚨 Exam Trap — What is the Hot Battery Bus?</div>
        <p style="font-size:14px;color:#fca5a5;line-height:1.7;margin:0;">
          This is one of the most commonly failed oral exam questions. Candidates know what a bus is but forget about the Hot Battery Bus specifically.<br><br>
          <strong>Hot Battery Bus = a bus that is DIRECTLY connected to the battery, with NO isolation switch in between.</strong> It cannot be de-energized by the pilot in normal operation. This ensures fire detection always works, even if a pilot accidentally turns off all electrical power (or in a crash where circuit breakers trip). Think of it as the "always-on" emergency circuit.
        </p>
      </div>

      <div class="inline-quiz" data-quiz="q4">
        <div class="quiz-label">🧠 Quick Check — Chapter 4</div>
        <p class="quiz-question">What is the Hot Battery Bus, and why can it not be de-energized in normal operation?</p>
        <div class="quiz-options">
          <div class="quiz-opt" onclick="checkQuiz(this,'q4',false)">A bus that runs hotter than others due to higher current — it has thermal protection to prevent shutdown</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q4',true)">A bus directly connected to the battery with no isolation switch, ensuring fire detection and critical items always have power</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q4',false)">A standby bus that activates automatically when the main bus fails</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q4',false)">A bus powered by both batteries simultaneously for double redundancy</div>
        </div>
        <div class="quiz-explanation" id="q4-exp">
          <strong>Correct.</strong> The Hot Battery Bus is "hot" because it is permanently energized — it has a direct hard-wired connection to Battery 1 with no master switch or contactor in the path. This guarantees that fire detection systems, certain warning lights, and EICAS memory always have power regardless of any switch position. It's a fundamental safety design: even if the crew turns off all electrical power during an emergency, the Hot Battery Bus keeps fire protection alive.
        </div>
      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 4)">✓ Mark Chapter 4 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 5 — DISTRIBUTION BUSES
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch5">
      <div class="chapter-badge"><span class="badge-num">05</span> Power Distribution</div>
      <h2 class="chapter-title">The Bus System — Power's Highway Network</h2>
      <p class="chapter-subtitle">How electricity gets from generators to every corner of the aircraft</p>
      <div class="chapter-meta"><span class="meta-pill">⏱ 14 min read</span></div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> Map out the Q400's bus hierarchy — AC buses, DC buses, Essential buses, and Emergency bus — and understand which systems remain powered at each failure level.</p>
      </div>

      <div class="analogy-box">
        <div class="analogy-label">💡 The Analogy — Airport Terminal Gates</div>
        <p>Think of an airport. The main terminal serves all passengers (like the Main AC Bus). From there, you have concourses A, B, C (AC Bus 1, AC Bus 2, DC Bus 1, DC Bus 2). Deep inside is a VIP lounge that always stays open even when the main terminal is evacuated (the Hot Battery Bus). And there's an emergency exit corridor that's never blocked (the Emergency Bus). Each "concourse" powers different aircraft systems — just like airport gates serve different airlines.</p>
      </div>

      <div class="cbt-body">
        <p>A <strong>bus</strong> (short for busbar) is simply a common electrical distribution point — a thick copper bar or rail to which multiple circuits connect. Think of it as a power outlet strip: many devices plug into it, and it's powered by one source. If the source fails, all connected devices lose power simultaneously.</p>
        <p>The Q400 has multiple buses arranged in a <em>hierarchy</em> — from the most abundant (Main buses, powered during normal ops) to the most essential (Emergency bus, powered even on battery alone).</p>
      </div>

      <div class="info-card blue">
        <div class="card-head">🗺️ The Complete Bus Hierarchy</div>
        <table class="spec-table">
          <thead><tr><th>Bus Name</th><th>Power Source</th><th>Fails When</th></tr></thead>
          <tbody>
            <tr>
              <td class="td-key"><strong style="color:#f59e0b">AC Bus 1</strong></td>
              <td class="td-val">Generator 1 (primary) or Gen 2 via BTC</td>
              <td>Both generators fail</td>
            </tr>
            <tr>
              <td class="td-key"><strong style="color:#f59e0b">AC Bus 2</strong></td>
              <td class="td-val">Generator 2 (primary) or Gen 1 via BTC</td>
              <td>Both generators fail</td>
            </tr>
            <tr>
              <td class="td-key"><strong style="color:#60a5fa">DC Bus 1</strong></td>
              <td class="td-val">TRU 1 (from AC Bus 1)</td>
              <td>TRU 1 fails OR both gens fail</td>
            </tr>
            <tr>
              <td class="td-key"><strong style="color:#60a5fa">DC Bus 2</strong></td>
              <td class="td-val">TRU 2 (from AC Bus 2)</td>
              <td>TRU 2 fails OR both gens fail</td>
            </tr>
            <tr>
              <td class="td-key"><strong style="color:#22c55e">DC Essential Bus</strong></td>
              <td class="td-val">DC Bus 1, or DC Bus 2, or Batteries</td>
              <td>Only fails if all power lost</td>
            </tr>
            <tr>
              <td class="td-key"><strong style="color:#a78bfa">Hot Battery Bus</strong></td>
              <td class="td-val">Battery 1 — DIRECT, always live</td>
              <td>Never fails in normal ops</td>
            </tr>
            <tr>
              <td class="td-key"><strong style="color:#ef4444">Emergency Bus</strong></td>
              <td class="td-val">Batteries (when all else fails)</td>
              <td>Only if both batteries fail</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="cbt-body">
        <p>The key to understanding bus architecture is <strong>priority shedding</strong>. When power is limited (e.g., single generator or battery only), non-essential loads are automatically disconnected — shed — so critical systems keep their power. The hierarchy determines who gets power first.</p>
        <p>During <em>battery-only emergency operation</em>, only the DC Essential Bus and Emergency Bus are powered. All AC power is lost (both TRUs are offline). Essential flight instruments, navigation, communications, and flight controls remain powered. Galley power, cabin lighting, and entertainment systems — gone.</p>
      </div>

      <div class="info-card red">
        <div class="card-head">🚨 What Goes Offline in a Complete Generator Failure</div>
        <ul style="margin:0;padding-left:20px;color:#fca5a5;font-size:14px;line-height:1.9;">
          <li>All AC power — galley, some avionics, anti-icing heaters, AC-powered instruments</li>
          <li>DC Bus 1 and DC Bus 2 (TRUs both offline)</li>
          <li>Non-essential DC loads (auto-shed)</li>
          <li>Cabin pressurization control (degrades — manual control required)</li>
        </ul>
        <p style="margin:10px 0 0;font-size:14px;color:#fca5a5;"><strong>What stays ON:</strong> DC Essential Bus (essential instruments, radios, engine controls), Hot Battery Bus (fire detection), Emergency Bus (standby instruments). You can still fly, navigate, and communicate — but land immediately.</p>
      </div>

      <div class="inline-quiz" data-quiz="q5">
        <div class="quiz-label">🧠 Quick Check — Chapter 5</div>
        <p class="quiz-question">Generator 2 fails. The Bus Tie Contactor closes automatically. Which buses are now powered by Generator 1?</p>
        <div class="quiz-options">
          <div class="quiz-opt" onclick="checkQuiz(this,'q5',false)">Only AC Bus 1 — AC Bus 2 remains unpowered until the pilot manually closes the bus tie</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q5',true)">Both AC Bus 1 AND AC Bus 2 — Generator 1 now supplies the entire AC network via the closed Bus Tie Contactor</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q5',false)">AC Bus 1 only — AC Bus 2 is isolated to prevent overloading Generator 1</div>
          <div class="quiz-opt" onclick="checkQuiz(this,'q5',false)">The batteries automatically take over AC Bus 2 while Gen 1 maintains AC Bus 1</div>
        </div>
        <div class="quiz-explanation" id="q5-exp">
          <strong>Correct.</strong> The Bus Tie Contactor (BTC) is designed to close automatically within milliseconds of a generator failure. Once closed, it electrically connects AC Bus 1 and AC Bus 2, allowing Generator 1 to supply both. This is seamless from the crew's perspective — systems don't lose power, only the EICAS caution indicates the failure. This automatic cross-feeding is exactly why each generator is rated for the full aircraft electrical load.
        </div>
      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 5)">✓ Mark Chapter 5 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 6 — NORMAL OPERATION
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch6">
      <div class="chapter-badge"><span class="badge-num">06</span> Normal Operation</div>
      <h2 class="chapter-title">Following the Electrons — A Full Flight</h2>
      <p class="chapter-subtitle">Trace electricity from cockpit power-up to shutdown, step by step</p>
      <div class="chapter-meta"><span class="meta-pill">⏱ 10 min read</span></div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> Walk through a complete flight sequence and understand exactly which power sources are active at each phase.</p>
      </div>

      <div class="flow-steps">
        <div class="flow-step" data-step="1">
          <div class="flow-step-content">
            <p class="flow-step-title">Pre-Flight / Cockpit Setup (Engines Off)</p>
            <p class="flow-step-desc">Battery Master ON → both batteries energize the DC Essential Bus, Hot Battery Bus, and Emergency Bus. EICAS comes alive. If a GPU is connected, External Power switch ON → GPU supplies the AC buses → TRUs come online → more DC systems powered. Batteries transition to charging mode or standby. Engineers can now run checks on all systems without draining batteries.</p>
            <span class="flow-step-state state-warn">Power source: Batteries and/or GPU</span>
          </div>
        </div>
        <div class="flow-step" data-step="2">
          <div class="flow-step-content">
            <p class="flow-step-title">Engine 1 Start</p>
            <p class="flow-step-desc">Battery and/or GPU power the starter. Engine accelerates through light-off and up to idle RPM. At a set RPM threshold, Generator 1 comes online automatically — its GCU closes the Generator Line Contactor. Gen 1 takes over AC Bus 1. TRU 1 energizes, powering DC Bus 1. The GPU (if connected) can now be disconnected as Gen 1 handles the load.</p>
            <span class="flow-step-state state-normal">Gen 1 + Battery online</span>
          </div>
        </div>
        <div class="flow-step" data-step="3">
          <div class="flow-step-content">
            <p class="flow-step-title">Engine 2 Start</p>
            <p class="flow-step-desc">Same process as Engine 1. Generator 2 comes online, takes over AC Bus 2, TRU 2 energizes, DC Bus 2 powered. Bus Tie Contactor opens (back to normal split-bus configuration). Both batteries now in charging mode — they've been slightly depleted during starting and the TRUs are now restoring them to full charge.</p>
            <span class="flow-step-state state-normal">Both Gens online — full power available</span>
          </div>
        </div>
        <div class="flow-step" data-step="4">
          <div class="flow-step-content">
            <p class="flow-step-title">Normal Flight (Cruise)</p>
            <p class="flow-step-desc">Gen 1 → AC Bus 1 → TRU 1 → DC Bus 1. Gen 2 → AC Bus 2 → TRU 2 → DC Bus 2. Both batteries fully charged, sitting in standby. All aircraft systems fully powered. This is the designed normal state — two completely independent and parallel electrical systems running in total silence.</p>
            <span class="flow-step-state state-normal">✓ Fully normal — dual independent power</span>
          </div>
        </div>
        <div class="flow-step" data-step="5">
          <div class="flow-step-content">
            <p class="flow-step-title">Shutdown</p>
            <p class="flow-step-desc">Generators trip as engines shut down (GCUs open the contactors automatically). DC Essential Bus and Hot Battery Bus remain powered by batteries for a short period to allow crew actions and EICAS logging. Battery Master OFF → most power removed. Hot Battery Bus remains live until battery is physically isolated.</p>
            <span class="flow-step-state state-warn">Power source: Batteries (briefly)</span>
          </div>
        </div>
      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 6)">✓ Mark Chapter 6 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 7 — ABNORMAL PROCEDURES
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch7">
      <div class="chapter-badge"><span class="badge-num">07</span> Abnormal Procedures</div>
      <h2 class="chapter-title">When Things Go Wrong</h2>
      <p class="chapter-subtitle">Every electrical failure scenario the examiner will probe — and exactly how to handle each one</p>
      <div class="chapter-meta"><span class="meta-pill">⏱ 15 min read</span></div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> For each abnormal scenario: recognize the EICAS indication, understand what has happened automatically, and know the required pilot action.</p>
      </div>

      <div class="failure-grid">

        <div class="failure-card">
          <div class="failure-header" onclick="toggleFailure(this)">
            <span class="failure-severity sev-med">CAUTION</span>
            <span class="failure-name">Single Generator Failure (GEN 1 or GEN 2 FAIL)</span>
            <span class="failure-eicas">GEN 1 FAIL / GEN 2 FAIL</span>
            <span style="color:#4a6080;font-size:18px;margin-left:auto">▼</span>
          </div>
          <div class="failure-body">
            <div class="failure-row">
              <div class="failure-col">
                <div class="failure-col-label">What happened</div>
                <div class="failure-col-text">One generator has tripped or failed. Its GCU opened the Generator Line Contactor. That AC bus briefly lost power.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Automatic response</div>
                <div class="failure-col-text">Bus Tie Contactor closes automatically. The remaining generator now supplies both AC buses and both TRUs. Full DC power maintained.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Pilot action</div>
                <div class="failure-col-text">1. Identify failed generator. 2. Reduce non-essential electrical loads. 3. Check remaining generator load. 4. Land at nearest suitable aerodrome — not immediately, but without delay.</div>
              </div>
            </div>
            <div class="info-card green" style="margin-top:12px">
              <div class="card-head">✅ Key Point</div>
              <p style="margin:0;font-size:13px;color:#86efac;">Single generator failure is manageable. The aircraft can fly indefinitely on one generator. The concern is that you now have zero redundancy — if the remaining generator also fails, you're on batteries. That's why you land without delay.</p>
            </div>
          </div>
        </div>

        <div class="failure-card">
          <div class="failure-header" onclick="toggleFailure(this)">
            <span class="failure-severity sev-high">WARNING</span>
            <span class="failure-name">Both Generators Fail (Dual Generator Failure)</span>
            <span class="failure-eicas">GEN 1 FAIL + GEN 2 FAIL</span>
            <span style="color:#4a6080;font-size:18px;margin-left:auto">▼</span>
          </div>
          <div class="failure-body">
            <div class="failure-row">
              <div class="failure-col">
                <div class="failure-col-label">What happened</div>
                <div class="failure-col-text">Both generators have failed. Both AC buses are dead. Both TRUs have lost input and are offline. All AC power is lost.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Automatic response</div>
                <div class="failure-col-text">Batteries automatically supply the DC Essential Bus and Emergency Bus. Hot Battery Bus remains live. Emergency equipment comes online from battery power.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Pilot action</div>
                <div class="failure-col-text">IMMEDIATE ACTION. 1. Declare emergency. 2. Shed ALL non-essential loads. 3. Squawk 7700. 4. LAND IMMEDIATELY. Battery endurance ~30 min — every minute of delay reduces your margin.</div>
              </div>
            </div>
            <div class="info-card red" style="margin-top:12px">
              <div class="card-head">🚨 Critical — No Delay Permitted</div>
              <p style="margin:0;font-size:13px;color:#fca5a5;">30 minutes sounds like a lot. But account for: approach and landing time, possible missed approach, troubleshooting time, and the fact that load shedding is rarely perfect. Treat dual generator failure as a <em>fly immediately to the nearest suitable airport</em> emergency — not "let's continue to destination."</p>
            </div>
          </div>
        </div>

        <div class="failure-card">
          <div class="failure-header" onclick="toggleFailure(this)">
            <span class="failure-severity sev-med">CAUTION</span>
            <span class="failure-name">TRU Failure (TRU 1 or TRU 2 FAIL)</span>
            <span class="failure-eicas">TRU 1 FAIL / TRU 2 FAIL</span>
            <span style="color:#4a6080;font-size:18px;margin-left:auto">▼</span>
          </div>
          <div class="failure-body">
            <div class="failure-row">
              <div class="failure-col">
                <div class="failure-col-label">What happened</div>
                <div class="failure-col-text">One TRU has failed internally. Its AC input may still be fine (generator still running) but DC output has ceased.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Automatic response</div>
                <div class="failure-col-text">DC Essential Bus transfers to remaining TRU. That TRU now supplies both DC Bus 2 and the DC Essential Bus. Some non-essential DC loads may be automatically shed.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Pilot action</div>
                <div class="failure-col-text">1. Identify failed TRU. 2. Check essential systems are powered. 3. Reduce DC load where possible. 4. Land at nearest suitable aerodrome.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="failure-card">
          <div class="failure-header" onclick="toggleFailure(this)">
            <span class="failure-severity sev-med">CAUTION</span>
            <span class="failure-name">Battery Fault / Low Charge</span>
            <span class="failure-eicas">BAT FAULT / BAT LOW</span>
            <span style="color:#4a6080;font-size:18px;margin-left:auto">▼</span>
          </div>
          <div class="failure-body">
            <div class="failure-row">
              <div class="failure-col">
                <div class="failure-col-label">What happened</div>
                <div class="failure-col-text">A battery has a fault (overtemperature, low voltage, charging fault) or is not charging correctly. If generators are online, this is non-critical in the short term.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Automatic response</div>
                <div class="failure-col-text">EICAS caution displayed. Systems continue on generator power. The battery contactor may open to isolate a faulty battery.</div>
              </div>
              <div class="failure-col">
                <div class="failure-col-label">Pilot action</div>
                <div class="failure-col-text">1. Identify which battery. 2. If one battery only — continue with awareness. 3. If both batteries affected — treat as significant. Land when practical. Your emergency backup is now severely reduced.</div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 7)">✓ Mark Chapter 7 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 8 — LIMITS & NUMBERS
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section" id="ch8">
      <div class="chapter-badge"><span class="badge-num">08</span> Limitations</div>
      <h2 class="chapter-title">The Numbers — Everything Your Examiner Will Ask</h2>
      <p class="chapter-subtitle">All voltages, limits, and key specifications on one page</p>
      <div class="chapter-meta"><span class="meta-pill">⏱ 8 min read</span></div>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><strong>Learning Objective:</strong> Have every key number committed to memory — volt values, quantities, bus names, and endurance figures.</p>
      </div>

      <div class="info-card amber">
        <div class="card-head">⭐ Master Reference Table — Print This in Your Head</div>
        <table class="spec-table">
          <thead><tr><th>Item</th><th>Value / Limit</th><th>Why It Matters</th></tr></thead>
          <tbody>
            <tr><td class="td-key">AC bus voltage (normal)</td><td class="td-val">115V AC, 3-phase</td><td>Standard aircraft AC — examiner will ask</td></tr>
            <tr><td class="td-key">AC frequency</td><td class="td-val">Variable (engine RPM dependent)</td><td>Different from jets — key Q400 fact</td></tr>
            <tr><td class="td-key">DC bus voltage (normal)</td><td class="td-val">27.5V – 29V DC</td><td>Regulated range — 28V nominal</td></tr>
            <tr><td class="td-key">Battery voltage (nominal)</td><td class="td-val">24V (28V when charging)</td><td>NiCad chemistry characteristic</td></tr>
            <tr><td class="td-key">External AC power</td><td class="td-val">115V AC / 400 Hz</td><td>GPU uses constant frequency — unlike aircraft gen</td></tr>
            <tr><td class="td-key">External DC power</td><td class="td-val">28V DC</td><td>Same as aircraft DC bus</td></tr>
            <tr><td class="td-key">Number of AC generators</td><td class="td-val">2</td><td>One per engine</td></tr>
            <tr><td class="td-key">Number of TRUs</td><td class="td-val">2</td><td>One per AC bus</td></tr>
            <tr><td class="td-key">Number of batteries</td><td class="td-val">2 NiCad</td><td>Ni-Cd chemistry</td></tr>
            <tr><td class="td-key">Battery emergency endurance</td><td class="td-val">~30 minutes</td><td>Dual generator failure — land immediately</td></tr>
            <tr><td class="td-key">AC buses</td><td class="td-val">AC Bus 1, AC Bus 2</td><td>One per generator</td></tr>
            <tr><td class="td-key">DC buses</td><td class="td-val">DC Bus 1, DC Bus 2, DC Essential</td><td>Essential is last to go offline</td></tr>
            <tr><td class="td-key">Always-live bus</td><td class="td-val">Hot Battery Bus</td><td>Cannot be de-energized — feeds fire detection</td></tr>
            <tr><td class="td-key">Last-resort bus</td><td class="td-val">Emergency Bus</td><td>Battery-only — minimum essential power</td></tr>
          </tbody>
        </table>
      </div>

      <div class="info-card purple">
        <div class="card-head">🧠 Memory Anchor — "2-2-28"</div>
        <p style="font-size:15px;color:#c4b5fd;line-height:1.7;margin:0;">
          The mnemonic <strong>2-2-28</strong> locks in the core architecture:<br>
          <strong>2</strong> alternators · <strong>2</strong> batteries · <strong>28</strong> volts DC<br><br>
          Everything else hangs off this. Two alternators → two TRUs → two DC buses → 28V. Two batteries → backup for all of it. Repeat this until it's automatic.
        </p>
      </div>

      <button class="mark-read-btn" onclick="markChapterRead(this, 8)">✓ Mark Chapter 8 Complete</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         CHAPTER 9 — KNOWLEDGE CHECK QUIZ
    ═══════════════════════════════════════════════════════ -->
    <div class="chapter-section full-quiz" id="ch9">
      <div class="chapter-badge"><span class="badge-num">09</span> Knowledge Check</div>
      <h2 class="chapter-title">Test Your Understanding</h2>
      <p class="chapter-subtitle">10 exam-style questions — see how well you know ATA24</p>

      <div class="quiz-progress-bar">
        <div class="quiz-progress-fill" id="quizProgressFill" style="width:0%"></div>
      </div>

      <div id="quizContainer"></div>
      <div class="quiz-score-card" id="quizScoreCard">
        <div class="score-number" id="scoreNum">0/10</div>
        <div class="score-label">Knowledge Check Complete</div>
        <div class="score-message" id="scoreMsg"></div>
        <button onclick="restartQuiz()" style="margin-top:20px;padding:12px 32px;background:#f59e0b;color:#000;border:none;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer;">Restart Quiz</button>
      </div>
    </div>

  </div><!-- end cbt-content -->
</div><!-- end cbt-wrapper -->

<script>
// ── Chapter Navigation ──────────────────────────────────────
function setActiveNav(el) {
  document.querySelectorAll('.cbt-nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}

// Scroll spy
const sections = document.querySelectorAll('.chapter-section');
const navItems = document.querySelectorAll('.cbt-nav-item');
window.addEventListener('scroll', () => {
  let current = '';
  sections.forEach(s => {
    if (window.scrollY >= s.offsetTop - 120) current = s.id;
  });
  navItems.forEach(n => {
    n.classList.toggle('active', n.getAttribute('href') === '#' + current);
  });
});

// ── Component Cards ─────────────────────────────────────────
function toggleComp(card) {
  const wasExpanded = card.classList.contains('expanded');
  document.querySelectorAll('.comp-card').forEach(c => c.classList.remove('expanded'));
  if (!wasExpanded) card.classList.add('expanded');
}

// ── Failure Accordions ──────────────────────────────────────
function toggleFailure(header) {
  const body = header.nextElementSibling;
  body.classList.toggle('open');
  header.querySelector('span:last-child').textContent = body.classList.contains('open') ? '▲' : '▼';
}

// ── Inline Quick Checks ─────────────────────────────────────
function checkQuiz(el, quizId, isCorrect) {
  const container = el.parentElement;
  if (container.querySelector('.correct')) return; // already answered
  container.querySelectorAll('.quiz-opt').forEach(o => {
    o.classList.add('disabled');
    o.onclick = null;
  });
  el.classList.add(isCorrect ? 'correct' : 'incorrect');
  if (!isCorrect) {
    container.querySelectorAll('.quiz-opt').forEach(o => {
      if (o !== el) { /* find correct option */ }
    });
  }
  const exp = document.getElementById(quizId + '-exp');
  if (exp) exp.classList.add('show');
}

// ── Chapter Progress ────────────────────────────────────────
let chaptersRead = new Set();
function markChapterRead(btn, num) {
  if (btn.classList.contains('read')) return;
  btn.classList.add('read');
  btn.textContent = '✓ Completed';
  chaptersRead.add(num);
  // Mark nav item as done
  const navItem = document.querySelector(`.cbt-nav-item[href="#ch${num}"]`);
  if (navItem) navItem.classList.add('done');
  // Update progress
  const pct = Math.round((chaptersRead.size / 9) * 100);
  document.getElementById('navProgress').style.width = pct + '%';
}

// ── Full Knowledge Check Quiz ────────────────────────────────
const quizQuestions = [
  {
    q: "What type of batteries does the Q400 use and why?",
    options: [
      "Lead-acid — cheap, reliable, and easily replaceable",
      "Lithium-ion — high energy density for weight saving",
      "Nickel-Cadmium (NiCad) — maintains voltage under heavy load and extreme temperatures",
      "Alkaline — long shelf life and no maintenance required"
    ],
    correct: 2,
    explanation: "NiCad (Nickel-Cadmium) batteries are used because they maintain stable voltage under high current demand and across a wide temperature range (-40°C to +70°C). They also withstand deep discharge cycles without damage — critical for repeated engine start cycles. Lead-acid batteries suffer significant voltage drop under the heavy loads typical of aircraft use."
  },
  {
    q: "The Q400 generators produce variable frequency AC power. What does this mean for aircraft systems?",
    options: [
      "It means pilots must manually adjust frequency during power changes — adding workload",
      "It is a malfunction — properly operating generators should always produce 400 Hz",
      "AC-powered equipment on the Q400 is specifically designed to accept variable frequency, so this is normal and by design",
      "The variable frequency is smoothed to 400 Hz by the GCU before reaching the buses"
    ],
    correct: 2,
    explanation: "Variable frequency is normal and expected on the Q400. Because the alternators are directly coupled to the propeller reduction gearbox (no Constant Speed Drive), the frequency varies with engine RPM. The aircraft's AC systems are designed to accept this. It is a design characteristic, not a fault. The GCU regulates voltage (115V) but does not control frequency."
  },
  {
    q: "What is the Bus Tie Contactor (BTC) and when does it close?",
    options: [
      "A manual switch the crew closes after a generator failure to cross-connect both AC buses",
      "A contactor that automatically closes to connect AC Bus 1 and AC Bus 2 when one generator fails",
      "A component that connects the AC buses to the batteries during emergency operation",
      "A protection relay that opens when bus voltage exceeds limits"
    ],
    correct: 1,
    explanation: "The Bus Tie Contactor (BTC) is an automatic relay that connects AC Bus 1 and AC Bus 2. Under normal dual-generator operation, the BTC is open — both buses are isolated and independent. When one generator fails, the BTC closes automatically (no crew input required) within milliseconds, allowing the remaining generator to supply both buses and their associated TRUs and DC buses."
  },
  {
    q: "Both generators fail. You are at FL250 with 45 minutes of fuel remaining. What do you do?",
    options: [
      "Continue to destination — 45 minutes of fuel means you'll make it before batteries run out",
      "Attempt to restart generators — do not declare emergency until restart attempts are exhausted",
      "Declare emergency immediately, shed non-essential loads, squawk 7700, divert to nearest suitable airport — batteries give approximately 30 minutes",
      "Reduce altitude to FL100 to reduce electrical load and extend battery life"
    ],
    correct: 2,
    explanation: "Dual generator failure demands immediate action. Battery endurance is approximately 30 minutes with proper load shedding — and that 30 minutes includes: identifying the failure, declaring emergency, getting vectors, descending, and landing. With 45 min of fuel you might reach destination, but you may not have electrical power when you get there. Always divert to nearest suitable. Squawk 7700, declare emergency, and move."
  },
  {
    q: "Which bus on the Q400 remains powered even when the Battery Master switch is OFF?",
    options: [
      "DC Essential Bus — it has its own power source",
      "Emergency Bus — it connects directly to both batteries",
      "Hot Battery Bus — it is directly connected to Battery 1 with no isolating switch",
      "AC Bus 1 — it retains a charge briefly after shutdown"
    ],
    correct: 2,
    explanation: "The Hot Battery Bus is directly and permanently connected to Battery 1, with no master switch or contactor in between. It is intentionally non-switchable to ensure fire detection, fire suppression, and certain critical systems always have power — including in crash scenarios where circuit breakers might trip. This is a fundamental safety design of the aircraft."
  },
  {
    q: "What does a TRU (Transformer Rectifier Unit) do?",
    options: [
      "Converts 28V DC battery power to 115V AC for AC systems when generators are offline",
      "Converts 115V AC generator output to 28V DC for the DC buses and instruments",
      "Regulates generator voltage to maintain exactly 115V AC on the buses",
      "Acts as a voltage limiter, preventing bus overvoltage when both generators operate together"
    ],
    correct: 1,
    explanation: "A TRU takes the 115V AC from the AC bus (generator output), uses a transformer to step down the voltage, and a rectifier to convert the AC waveform to smooth DC. Output: 28V DC. This powers the DC buses, which supply flight instruments, avionics, navigation, and communication systems. The TRU does not work in reverse — it cannot convert DC back to AC."
  },
  {
    q: "If TRU 2 fails, which bus immediately loses DC power?",
    options: [
      "DC Bus 1",
      "DC Essential Bus",
      "DC Bus 2",
      "Hot Battery Bus"
    ],
    correct: 2,
    explanation: "TRU 2 is powered by AC Bus 2 (Generator 2) and its output powers DC Bus 2. If TRU 2 fails, DC Bus 2 loses power directly. The DC Essential Bus transfers automatically to TRU 1 if TRU 2 was the primary supplier. DC Bus 1 (from TRU 1) is unaffected. The Hot Battery Bus is always connected to Battery 1 and is never affected by TRU failures."
  },
  {
    q: "A Generator 1 failure EICAS caution appears. The crew takes no action. What is the risk?",
    options: [
      "No risk — single generator operation is certified for the entire flight envelope indefinitely",
      "The remaining generator will automatically shut down after 30 minutes to prevent overheating",
      "You now have zero generator redundancy — if Generator 2 also fails, you immediately go to battery-only emergency power",
      "DC Bus 1 will be unpowered, causing loss of essential flight instruments"
    ],
    correct: 2,
    explanation: "A single generator failure is manageable and safe in the short term. Generator 2 can handle the full aircraft load (BTC has closed, both buses supplied by Gen 2). However, you now have NO redundancy. If Gen 2 also fails, you immediately enter dual generator failure with battery-only power (~30 min). This is why the procedure mandates landing without delay — preserving that redundancy margin."
  },
  {
    q: "What is the approximate emergency battery endurance after a complete dual generator failure with proper load shedding?",
    options: [
      "10 minutes — batteries are only designed for engine starting, not sustained operation",
      "60 minutes — NiCad batteries have significant capacity for sustained flight",
      "Approximately 30 minutes — sufficient for emergency descent and approach to nearest airport",
      "2 hours — NiCad technology stores enough energy for extended battery-only flight"
    ],
    correct: 2,
    explanation: "Approximately 30 minutes with proper load shedding. This is the critical number every Q400 pilot must know. It is enough for an emergency descent and approach to the nearest airport if action is taken immediately. It is NOT enough to reach a distant alternate, attempt multiple approaches, or delay decision-making. After a dual generator failure, declare emergency and land at the nearest suitable airport — not the most convenient one."
  },
  {
    q: "External power (GPU) connected to the Q400 provides 115V AC at 400 Hz. Why does the GPU use 400 Hz but the aircraft generators do not?",
    options: [
      "The GPU must match jet aircraft specifications and the Q400 adapts the frequency internally",
      "Aircraft generators produce 400 Hz at full power — 400 Hz is only guaranteed at cruise RPM",
      "The GPU has its own engine running at constant speed, maintaining 400 Hz exactly. Aircraft generators vary with propeller RPM — so frequency varies",
      "The GPU specification is incorrect — GPUs also produce variable frequency power"
    ],
    correct: 2,
    explanation: "A GPU (Ground Power Unit) has its own diesel engine driving a generator at a constant, regulated speed — specifically to produce stable 400 Hz AC power. The Q400's own alternators produce variable frequency because they are mechanically coupled to the propeller gearbox with no speed control. This is why GPU power is constant frequency but aircraft generator power varies. Both are 115V AC — the difference is only in frequency."
  }
];

let currentQ = 0;
let score = 0;
let answered = [];

function renderQuestion(idx) {
  const q = quizQuestions[idx];
  const container = document.getElementById('quizContainer');
  const pct = Math.round((idx / quizQuestions.length) * 100);
  document.getElementById('quizProgressFill').style.width = pct + '%';

  container.innerHTML = `
    <div class="quiz-card">
      <div class="quiz-q-num">Question ${idx + 1} of ${quizQuestions.length}</div>
      <p class="quiz-q-text">${q.q}</p>
      <div class="quiz-options">
        ${q.options.map((opt, i) => `
          <div class="quiz-opt" onclick="answerQuestion(${i}, ${q.correct}, '${escStr(q.explanation)}')" id="fopt${i}">
            ${opt}
          </div>`).join('')}
      </div>
      <div class="quiz-result" id="quizResult"></div>
      <button class="quiz-next-btn" id="nextBtn" onclick="nextQuestion()">
        ${idx < quizQuestions.length - 1 ? 'Next Question →' : 'See My Score'}
      </button>
    </div>`;
}

function escStr(s) { return s.replace(/'/g,"&#39;").replace(/"/g,'&quot;'); }

function answerQuestion(chosen, correct, explanation) {
  if (document.getElementById('nextBtn').classList.contains('show')) return;
  const opts = document.querySelectorAll('.quiz-opt');
  opts.forEach((o, i) => {
    o.onclick = null;
    if (i === correct) o.classList.add('correct');
    else if (i === chosen) o.classList.add('incorrect');
  });
  const result = document.getElementById('quizResult');
  const isRight = chosen === correct;
  if (isRight) score++;
  result.className = 'quiz-result ' + (isRight ? 'correct-result' : 'wrong-result');
  result.innerHTML = (isRight ? '✓ Correct! ' : '✗ Incorrect. ') + explanation;
  document.getElementById('nextBtn').classList.add('show');
}

function nextQuestion() {
  currentQ++;
  if (currentQ >= quizQuestions.length) {
    showScore();
  } else {
    renderQuestion(currentQ);
  }
  document.getElementById('quizProgressFill').style.width = Math.round((currentQ / quizQuestions.length) * 100) + '%';
}

function showScore() {
  document.getElementById('quizContainer').style.display = 'none';
  document.getElementById('quizProgressFill').style.width = '100%';
  const card = document.getElementById('quizScoreCard');
  card.classList.add('show');
  document.getElementById('scoreNum').textContent = score + '/' + quizQuestions.length;
  const pct = score / quizQuestions.length;
  let msg = '';
  if (pct >= 0.9) msg = '🏆 Excellent! You are ready for the oral exam on ATA24. Your understanding of the Q400 electrical system is thorough. Move on to the next system.';
  else if (pct >= 0.7) msg = '✈️ Good work. You have a solid foundation. Review the chapters covering your incorrect answers, then re-test. Focus especially on abnormal procedures.';
  else if (pct >= 0.5) msg = '📖 You need more study time on this system. Re-read Chapters 5-7 (buses and abnormal procedures), then attempt the quiz again.';
  else msg = '🔁 Start again from Chapter 1. Work through each chapter, use the quick checks, and come back to the full quiz when you feel confident.';
  document.getElementById('scoreMsg').textContent = msg;
  markChapterRead(document.querySelector('#ch9 .mark-read-btn') || document.createElement('button'), 9);
}

function restartQuiz() {
  currentQ = 0; score = 0;
  document.getElementById('quizContainer').style.display = 'block';
  document.getElementById('quizScoreCard').classList.remove('show');
  renderQuestion(0);
}

// Init quiz on page load
document.addEventListener('DOMContentLoaded', () => renderQuestion(0));
// Also run immediately in case DOMContentLoaded already fired
if (document.readyState !== 'loading') renderQuestion(0);
</script>
