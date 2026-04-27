<?php
// Build mnemonic data per system ATA code
$mnemonics = [
    'ATA24' => [
        ['acronym' => 'BATT-AC-DC', 'title' => 'Power Sources', 'phrase' => 'Batteries Always Try to Tackle — Alternators Create Dedicated Current', 'meaning' => 'Batteries (NiCad) → backup; Alternators → primary AC → rectified to 28V DC'],
        ['acronym' => 'TRU', 'title' => 'Transformer Rectifier Unit', 'phrase' => 'Transforms, Rectifies, Understands AC→DC', 'meaning' => 'Converts 115V AC to 28V DC for DC bus distribution'],
        ['acronym' => '2-2-28', 'title' => 'System Numbers', 'phrase' => 'Two alternators, Two batteries, 28 volts DC', 'meaning' => '2 engine alternators + 2 NiCad batteries = 28 VDC system'],
    ],
    'ATA29' => [
        ['acronym' => '3+E = 3000', 'title' => 'Hydraulic Systems', 'phrase' => 'Three Mains + Emergency = 3000 PSI of power', 'meaning' => 'No.1, No.2, No.3 main systems + hand-pump emergency, all at 3000 PSI'],
        ['acronym' => 'FIRE-F', 'title' => 'Hydraulic Fluid', 'phrase' => 'Fire-resistant Is Required Every Flight — MIL-H-46000', 'meaning' => 'Phosphate ester fluid (MIL-H-46000) — fire-resistant, not flammable'],
        ['acronym' => 'FLB', 'title' => 'System Functions', 'phrase' => 'Flight controls, Landing gear, Brakes', 'meaning' => 'Three primary functions powered by hydraulics: FCS, gear, brakes'],
    ],
    'ATA28' => [
        ['acronym' => '2+1=3050', 'title' => 'Fuel Capacity', 'phrase' => 'Two wing tanks + 1 aux = 3050 gallons total', 'meaning' => '2 main wing tanks (2650 gal) + optional fuselage aux tank = 3050 gal'],
        ['acronym' => 'CROSS-JET', 'title' => 'Crossfeed', 'phrase' => 'Crossfeed Redundancy Offers Safety — Just Emergency Tactics', 'meaning' => 'Crossfeed valve allows either engine to feed from either tank in emergency'],
        ['acronym' => 'DUMP', 'title' => 'Jettison System', 'phrase' => 'Dump Urgently for Maximum Performance in emergency', 'meaning' => 'Fuel jettison system allows rapid weight reduction in emergency'],
    ],
    'ATA72' => [
        ['acronym' => 'PT6-2500', 'title' => 'Engine Type', 'phrase' => 'Pratt & Whitney Turboprop 6 — 2500 shaft horsepower', 'meaning' => 'PT6A-114A produces 2500 SHP each, with automatic power limiting'],
        ['acronym' => 'EICAS-N1N2', 'title' => 'Engine Monitoring', 'phrase' => 'Engine Indication Crew Alerting System: N1, N2, ITT, FF, OT', 'meaning' => 'EICAS monitors N1, N2 RPM, ITT temperature, Fuel Flow, Oil Temp'],
        ['acronym' => 'IPS-FC', 'title' => 'Engine Protection', 'phrase' => 'Inlet Particle Separator + Fire system Covers the engine', 'meaning' => 'IPS separates debris from intake air; fire detection & suppression system'],
    ],
];

// Get system-specific diagram type
$diagramType = 'electrical';
if (!empty($system['ata_code'])) {
    $ataMap = ['ATA24'=>'electrical','ATA29'=>'hydraulic','ATA28'=>'fuel','ATA72'=>'powerplant'];
    $diagramType = $ataMap[$system['ata_code']] ?? 'electrical';
}

$systemMnemonics = $mnemonics[$system['ata_code'] ?? ''] ?? [];
?>

<div class="study-rich-container">

  <!-- ── HERO HEADER ── -->
  <div class="study-hero" style="border-left: 5px solid <?php echo htmlspecialchars($system['color'] ?? '#3b82f6'); ?>">
    <div class="hero-left">
      <div class="hero-icon-wrap" style="background: <?php echo htmlspecialchars($system['color'] ?? '#3b82f6'); ?>22; border: 2px solid <?php echo htmlspecialchars($system['color'] ?? '#3b82f6'); ?>44;">
        <i data-lucide="<?php echo htmlspecialchars($system['icon'] ?? 'zap'); ?>" style="color:<?php echo htmlspecialchars($system['color'] ?? '#3b82f6'); ?>; width:36px; height:36px;"></i>
      </div>
      <div>
        <div class="hero-ata"><?php echo htmlspecialchars($system['ata_code']); ?></div>
        <h1 class="hero-title"><?php echo htmlspecialchars($system['name']); ?></h1>
        <p class="hero-desc"><?php echo htmlspecialchars($system['description']); ?></p>
      </div>
    </div>
    <div class="hero-stats">
      <div class="hstat"><span class="hstat-val"><?php echo count($lessons); ?></span><span class="hstat-lbl">Lessons</span></div>
      <div class="hstat"><span class="hstat-val"><?php echo array_sum(array_map(fn($l)=>$l['status']==='completed'?1:0, $lessons)); ?></span><span class="hstat-lbl">Done</span></div>
      <div class="hstat"><span class="hstat-val"><?php echo count($systemMnemonics); ?></span><span class="hstat-lbl">Mnemonics</span></div>
    </div>
  </div>

  <!-- ── TABS ── -->
  <div class="rich-tabs">
    <button class="rtab active" data-tab="lessons"><i data-lucide="book-open"></i> Lessons</button>
    <button class="rtab" data-tab="diagram"><i data-lucide="cpu"></i> System Diagram</button>
    <button class="rtab" data-tab="mnemonics"><i data-lucide="brain"></i> Memory Aids</button>
    <button class="rtab" data-tab="guide"><i data-lucide="bookmark"></i> Quick Reference</button>
    <button class="rtab" data-tab="notes"><i data-lucide="edit-3"></i> Notes</button>
  </div>

  <!-- ── LESSONS TAB ── -->
  <div class="tab-pane active" id="tab-lessons">
    <?php if (($system['ata_code'] ?? '') === 'ATA24'): ?>
      <?php include __DIR__ . '/ata24_chapters.php'; ?>
    <?php elseif (in_array($system['ata_code'] ?? '', ['ATA29','ATA28','ATA71','ATA61','ATA27','ATA32','ATA21','ATA36','ATA30','ATA26','ATA22','ATA34','ATA23','ATA31','ATA35','ATA33','ATA22B','CW','QRH'])): ?>
      <?php include __DIR__ . '/system_chapters.php'; ?>
    <?php elseif (!empty($lessons)): ?>
      <?php foreach ($lessons as $li => $lesson): ?>
        <?php
          $facts   = json_decode($lesson['key_facts'] ?? '[]', true) ?: [];
          $mustKnow = json_decode($lesson['must_know'] ?? '[]', true) ?: [];
          $traps   = json_decode($lesson['exam_traps'] ?? '[]', true) ?: [];
          $isDone  = $lesson['status'] === 'completed';
        ?>
        <div class="rich-lesson <?php echo $isDone ? 'lesson-done' : ''; ?>" style="--accent:<?php echo htmlspecialchars($system['color'] ?? '#3b82f6'); ?>">

          <!-- Lesson header -->
          <div class="rl-head">
            <div class="rl-num <?php echo $isDone ? 'num-done' : ''; ?>"><?php echo $isDone ? '✓' : ($li+1); ?></div>
            <div class="rl-title-block">
              <h2 class="rl-title"><?php echo htmlspecialchars($lesson['title']); ?></h2>
              <?php if (!empty($lesson['subtopic_title'])): ?>
                <span class="rl-sub"><?php echo htmlspecialchars($lesson['subtopic_title']); ?></span>
              <?php endif; ?>
            </div>
            <span class="rl-type-badge"><?php echo htmlspecialchars(ucfirst($lesson['content_type'] ?? 'overview')); ?></span>
          </div>

          <!-- Body text -->
          <?php if (!empty($lesson['body'])): ?>
            <div class="rl-body-text"><?php echo nl2br(htmlspecialchars($lesson['body'])); ?></div>
          <?php endif; ?>

          <!-- Animated component strip -->
          <div class="component-strip">
            <?php
              $icons = [
                'electrical' => [['zap','AC Generation','#f59e0b'],['battery','Battery Backup','#22c55e'],['cpu','TRU','#3b82f6'],['activity','Distribution','#8b5cf6']],
                'hydraulic'  => [['droplets','Main System','#3b82f6'],['gauge','3000 PSI','#ef4444'],['settings','Actuators','#f59e0b'],['shield','Emergency','#22c55e']],
                'fuel'       => [['fuel','Wing Tanks','#f59e0b'],['repeat','Crossfeed','#3b82f6'],['thermometer','Fuel Pumps','#22c55e'],['alert-triangle','Jettison','#ef4444']],
                'powerplant' => [['wind','Turboprop','#3b82f6'],['thermometer','ITT','#ef4444'],['monitor','EICAS','#22c55e'],['shield','Fire Prot.','#f59e0b']],
              ];
              $strip = $icons[$diagramType] ?? $icons['electrical'];
              foreach ($strip as $i => $ic): ?>
              <div class="cstrip-item" style="animation-delay:<?php echo $i*0.1; ?>s">
                <div class="cstrip-icon" style="background:<?php echo $ic[2]; ?>22; border:1px solid <?php echo $ic[2]; ?>66;">
                  <i data-lucide="<?php echo $ic[0]; ?>" style="color:<?php echo $ic[2]; ?>; width:20px;height:20px;"></i>
                </div>
                <span class="cstrip-label"><?php echo $ic[1]; ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Key Facts -->
          <?php if (!empty($facts)): ?>
            <div class="info-block info-blue">
              <div class="info-head"><i data-lucide="lightbulb"></i><strong>Key Facts</strong></div>
              <div class="facts-grid">
                <?php foreach ($facts as $fi => $fact): ?>
                  <div class="fact-chip" style="animation-delay:<?php echo $fi*0.08; ?>s">
                    <span class="fact-num"><?php echo $fi+1; ?></span>
                    <?php echo htmlspecialchars($fact); ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Must Know -->
          <?php if (!empty($mustKnow)): ?>
            <div class="info-block info-amber">
              <div class="info-head"><i data-lucide="star"></i><strong>Must Know for Exam</strong></div>
              <ul class="check-list">
                <?php foreach ($mustKnow as $point): ?>
                  <li><i data-lucide="check-circle-2"></i><?php echo htmlspecialchars($point); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <!-- Exam Traps -->
          <?php if (!empty($traps)): ?>
            <div class="info-block info-red">
              <div class="info-head"><i data-lucide="alert-triangle"></i><strong>Exam Traps — Don't Get Caught!</strong></div>
              <ul class="check-list">
                <?php foreach ($traps as $trap): ?>
                  <li><i data-lucide="x-circle"></i><?php echo htmlspecialchars($trap); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <!-- Footer actions -->
          <div class="rl-footer">
            <?php if ($lesson['time_spent_secs']): ?>
              <span class="time-pill"><i data-lucide="clock"></i> <?php echo floor($lesson['time_spent_secs']/60); ?> min studied</span>
            <?php endif; ?>
            <?php if (!$isDone): ?>
              <button class="btn-complete" onclick="markLessonComplete(<?php echo (int)$lesson['id']; ?>, this)">
                <i data-lucide="check"></i> Mark as Complete
              </button>
            <?php else: ?>
              <span class="done-badge"><i data-lucide="check-circle"></i> Completed</span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>

    <?php else: ?>
      <div class="empty-lessons">
        <i data-lucide="inbox"></i>
        <p>No lessons available for this system yet.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── SYSTEM DIAGRAM TAB ── -->
  <div class="tab-pane" id="tab-diagram">
    <div class="diagram-container">
      <h2 class="diagram-title"><?php echo htmlspecialchars($system['name']); ?> — System Schematic</h2>
      <p class="diagram-subtitle">Animated flow diagram showing how the system operates</p>

      <?php if ($diagramType === 'electrical'): ?>
      <!-- ELECTRICAL SYSTEM DIAGRAM -->
      <div class="svg-wrap">
        <svg viewBox="0 0 900 500" xmlns="http://www.w3.org/2000/svg" class="system-svg">
          <defs>
            <marker id="arr" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="#3b82f6"/></marker>
            <marker id="arr-g" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="#22c55e"/></marker>
            <marker id="arr-y" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="#f59e0b"/></marker>
            <filter id="glow"><feGaussianBlur stdDeviation="3" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
          </defs>
          <!-- Engines -->
          <rect x="60" y="60" width="130" height="70" rx="10" fill="#1e3a5f" stroke="#3b82f6" stroke-width="2" filter="url(#glow)"/>
          <text x="125" y="88" text-anchor="middle" fill="#93c5fd" font-size="11" font-weight="bold">ENGINE 1</text>
          <text x="125" y="105" text-anchor="middle" fill="#60a5fa" font-size="10">Alternator 1</text>
          <text x="125" y="120" text-anchor="middle" fill="#f59e0b" font-size="10">115V AC</text>

          <rect x="710" y="60" width="130" height="70" rx="10" fill="#1e3a5f" stroke="#3b82f6" stroke-width="2" filter="url(#glow)"/>
          <text x="775" y="88" text-anchor="middle" fill="#93c5fd" font-size="11" font-weight="bold">ENGINE 2</text>
          <text x="775" y="105" text-anchor="middle" fill="#60a5fa" font-size="10">Alternator 2</text>
          <text x="775" y="120" text-anchor="middle" fill="#f59e0b" font-size="10">115V AC</text>

          <!-- AC Bus -->
          <rect x="300" y="60" width="300" height="55" rx="8" fill="#1a2d4a" stroke="#f59e0b" stroke-width="2"/>
          <text x="450" y="83" text-anchor="middle" fill="#fbbf24" font-size="12" font-weight="bold">AC BUS — 115V AC</text>
          <text x="450" y="100" text-anchor="middle" fill="#fbbf24" font-size="10">AC Distribution</text>

          <!-- TRU blocks -->
          <rect x="180" y="190" width="120" height="60" rx="8" fill="#1a2d4a" stroke="#8b5cf6" stroke-width="2"/>
          <text x="240" y="215" text-anchor="middle" fill="#c4b5fd" font-size="11" font-weight="bold">TRU 1</text>
          <text x="240" y="232" text-anchor="middle" fill="#a78bfa" font-size="10">AC → 28V DC</text>

          <rect x="600" y="190" width="120" height="60" rx="8" fill="#1a2d4a" stroke="#8b5cf6" stroke-width="2"/>
          <text x="660" y="215" text-anchor="middle" fill="#c4b5fd" font-size="11" font-weight="bold">TRU 2</text>
          <text x="660" y="232" text-anchor="middle" fill="#a78bfa" font-size="10">AC → 28V DC</text>

          <!-- DC Bus -->
          <rect x="280" y="190" width="340" height="60" rx="8" fill="#1a2d4a" stroke="#22c55e" stroke-width="2"/>
          <text x="450" y="215" text-anchor="middle" fill="#4ade80" font-size="12" font-weight="bold">DC BUS — 28V DC</text>
          <text x="450" y="232" text-anchor="middle" fill="#86efac" font-size="10">Essential DC Distribution</text>

          <!-- Battery blocks -->
          <rect x="100" y="330" width="110" height="60" rx="8" fill="#1a2d4a" stroke="#22c55e" stroke-width="2"/>
          <text x="155" y="355" text-anchor="middle" fill="#4ade80" font-size="11" font-weight="bold">BATTERY 1</text>
          <text x="155" y="372" text-anchor="middle" fill="#86efac" font-size="10">NiCad Backup</text>

          <rect x="690" y="330" width="110" height="60" rx="8" fill="#1a2d4a" stroke="#22c55e" stroke-width="2"/>
          <text x="745" y="355" text-anchor="middle" fill="#4ade80" font-size="11" font-weight="bold">BATTERY 2</text>
          <text x="745" y="372" text-anchor="middle" fill="#86efac" font-size="10">NiCad Backup</text>

          <!-- Loads -->
          <rect x="310" y="330" width="280" height="55" rx="8" fill="#0f1f38" stroke="#475569" stroke-width="1.5"/>
          <text x="450" y="352" text-anchor="middle" fill="#94a3b8" font-size="11" font-weight="bold">AIRCRAFT LOADS</text>
          <text x="450" y="369" text-anchor="middle" fill="#64748b" font-size="10">Flight instruments · Avionics · Systems</text>

          <!-- ANIMATED FLOWS — AC from engines to AC bus -->
          <line x1="190" y1="95" x2="300" y2="88" stroke="#f59e0b" stroke-width="2.5" stroke-dasharray="8,4" marker-end="url(#arr-y)" opacity="0.9">
            <animate attributeName="stroke-dashoffset" values="0;-24" dur="0.8s" repeatCount="indefinite"/>
          </line>
          <line x1="710" y1="95" x2="600" y2="88" stroke="#f59e0b" stroke-width="2.5" stroke-dasharray="8,4" marker-end="url(#arr-y)" opacity="0.9">
            <animate attributeName="stroke-dashoffset" values="0;-24" dur="0.8s" repeatCount="indefinite"/>
          </line>

          <!-- AC bus to TRUs -->
          <line x1="330" y1="115" x2="260" y2="190" stroke="#f59e0b" stroke-width="2" stroke-dasharray="6,4" marker-end="url(#arr-y)" opacity="0.7">
            <animate attributeName="stroke-dashoffset" values="0;-20" dur="0.9s" repeatCount="indefinite"/>
          </line>
          <line x1="570" y1="115" x2="640" y2="190" stroke="#f59e0b" stroke-width="2" stroke-dasharray="6,4" marker-end="url(#arr-y)" opacity="0.7">
            <animate attributeName="stroke-dashoffset" values="0;-20" dur="0.9s" repeatCount="indefinite"/>
          </line>

          <!-- TRUs to DC bus -->
          <line x1="280" y1="220" x2="280" y2="225" stroke="#22c55e" stroke-width="2" stroke-dasharray="5,4" marker-end="url(#arr-g)" opacity="0.8">
            <animate attributeName="stroke-dashoffset" values="0;-18" dur="0.7s" repeatCount="indefinite"/>
          </line>
          <line x1="620" y1="220" x2="620" y2="225" stroke="#22c55e" stroke-width="2" stroke-dasharray="5,4" marker-end="url(#arr-g)" opacity="0.8">
            <animate attributeName="stroke-dashoffset" values="0;-18" dur="0.7s" repeatCount="indefinite"/>
          </line>

          <!-- DC bus to loads -->
          <line x1="450" y1="250" x2="450" y2="330" stroke="#22c55e" stroke-width="2.5" stroke-dasharray="7,4" marker-end="url(#arr-g)" opacity="0.9">
            <animate attributeName="stroke-dashoffset" values="0;-22" dur="0.6s" repeatCount="indefinite"/>
          </line>

          <!-- Batteries to DC bus (backup dashed) -->
          <line x1="210" y1="360" x2="310" y2="355" stroke="#22c55e" stroke-width="1.5" stroke-dasharray="4,6" opacity="0.5"/>
          <line x1="690" y1="360" x2="590" y2="355" stroke="#22c55e" stroke-width="1.5" stroke-dasharray="4,6" opacity="0.5"/>

          <!-- Legend -->
          <text x="30" y="460" fill="#f59e0b" font-size="10">━━ AC Power Flow</text>
          <text x="180" y="460" fill="#22c55e" font-size="10">━━ DC Power Flow</text>
          <text x="330" y="460" fill="#94a3b8" font-size="10">╌╌ Backup / Emergency</text>
        </svg>
      </div>

      <?php elseif ($diagramType === 'hydraulic'): ?>
      <!-- HYDRAULIC SYSTEM DIAGRAM -->
      <div class="svg-wrap">
        <svg viewBox="0 0 900 480" xmlns="http://www.w3.org/2000/svg" class="system-svg">
          <defs>
            <marker id="arr-b" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="#3b82f6"/></marker>
            <marker id="arr-r" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="#ef4444"/></marker>
            <filter id="glow2"><feGaussianBlur stdDeviation="3" result="b2"/><feMerge><feMergeNode in="b2"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
          </defs>

          <!-- Title -->
          <text x="450" y="30" text-anchor="middle" fill="#94a3b8" font-size="14" font-weight="bold">Q400 Hydraulic — 4 Independent Systems at 3000 PSI</text>

          <!-- Engine pumps -->
          <rect x="50" y="60" width="120" height="65" rx="8" fill="#1e3a5f" stroke="#3b82f6" stroke-width="2" filter="url(#glow2)"/>
          <text x="110" y="86" text-anchor="middle" fill="#93c5fd" font-size="11" font-weight="bold">ENG 1 PUMP</text>
          <text x="110" y="103" text-anchor="middle" fill="#60a5fa" font-size="10">No.1 Main System</text>
          <text x="110" y="118" text-anchor="middle" fill="#f59e0b" font-size="10">3000 PSI</text>

          <rect x="730" y="60" width="120" height="65" rx="8" fill="#1e3a5f" stroke="#3b82f6" stroke-width="2" filter="url(#glow2)"/>
          <text x="790" y="86" text-anchor="middle" fill="#93c5fd" font-size="11" font-weight="bold">ENG 2 PUMP</text>
          <text x="790" y="103" text-anchor="middle" fill="#60a5fa" font-size="10">No.2 Main System</text>
          <text x="790" y="118" text-anchor="middle" fill="#f59e0b" font-size="10">3000 PSI</text>

          <!-- No.3 system -->
          <rect x="360" y="60" width="180" height="65" rx="8" fill="#1a2d4a" stroke="#8b5cf6" stroke-width="2"/>
          <text x="450" y="86" text-anchor="middle" fill="#c4b5fd" font-size="11" font-weight="bold">No.3 SYSTEM</text>
          <text x="450" y="103" text-anchor="middle" fill="#a78bfa" font-size="10">Electric pump (AC)</text>
          <text x="450" y="118" text-anchor="middle" fill="#f59e0b" font-size="10">3000 PSI — Redundancy</text>

          <!-- Distribution manifold -->
          <rect x="150" y="185" width="600" height="50" rx="8" fill="#1a2d4a" stroke="#3b82f6" stroke-width="2"/>
          <text x="450" y="207" text-anchor="middle" fill="#60a5fa" font-size="12" font-weight="bold">HYDRAULIC DISTRIBUTION MANIFOLD — 3000 PSI</text>
          <text x="450" y="225" text-anchor="middle" fill="#93c5fd" font-size="10">Selective valve control — cross-system isolation capability</text>

          <!-- Consumer boxes -->
          <rect x="60" y="310" width="150" height="60" rx="8" fill="#0f1f38" stroke="#22c55e" stroke-width="2"/>
          <text x="135" y="334" text-anchor="middle" fill="#4ade80" font-size="11" font-weight="bold">FLIGHT CONTROLS</text>
          <text x="135" y="352" text-anchor="middle" fill="#86efac" font-size="10">Ailerons · Elevators · Rudder</text>

          <rect x="245" y="310" width="140" height="60" rx="8" fill="#0f1f38" stroke="#f59e0b" stroke-width="2"/>
          <text x="315" y="334" text-anchor="middle" fill="#fbbf24" font-size="11" font-weight="bold">LANDING GEAR</text>
          <text x="315" y="352" text-anchor="middle" fill="#fde68a" font-size="10">Extend / Retract</text>

          <rect x="420" y="310" width="130" height="60" rx="8" fill="#0f1f38" stroke="#ef4444" stroke-width="2"/>
          <text x="485" y="334" text-anchor="middle" fill="#fca5a5" font-size="11" font-weight="bold">BRAKES</text>
          <text x="485" y="352" text-anchor="middle" fill="#fca5a5" font-size="10">Wheel braking system</text>

          <!-- Emergency -->
          <rect x="590" y="310" width="240" height="60" rx="8" fill="#0f1f38" stroke="#ef4444" stroke-width="1.5" stroke-dasharray="6,3"/>
          <text x="710" y="334" text-anchor="middle" fill="#fca5a5" font-size="11" font-weight="bold">EMERGENCY (Hand Pump)</text>
          <text x="710" y="352" text-anchor="middle" fill="#fca5a5" font-size="10">Manual gear extension · Emergency braking</text>

          <!-- Flow lines - engine pumps to manifold -->
          <line x1="170" y1="125" x2="220" y2="185" stroke="#3b82f6" stroke-width="2.5" stroke-dasharray="8,4" marker-end="url(#arr-b)">
            <animate attributeName="stroke-dashoffset" values="0;-24" dur="0.7s" repeatCount="indefinite"/>
          </line>
          <line x1="730" y1="125" x2="680" y2="185" stroke="#3b82f6" stroke-width="2.5" stroke-dasharray="8,4" marker-end="url(#arr-b)">
            <animate attributeName="stroke-dashoffset" values="0;-24" dur="0.7s" repeatCount="indefinite"/>
          </line>
          <line x1="450" y1="125" x2="450" y2="185" stroke="#8b5cf6" stroke-width="2" stroke-dasharray="6,4" marker-end="url(#arr-b)">
            <animate attributeName="stroke-dashoffset" values="0;-20" dur="0.9s" repeatCount="indefinite"/>
          </line>

          <!-- Manifold to consumers -->
          <line x1="240" y1="235" x2="165" y2="310" stroke="#22c55e" stroke-width="2" stroke-dasharray="7,4" marker-end="url(#arr-b)">
            <animate attributeName="stroke-dashoffset" values="0;-22" dur="0.8s" repeatCount="indefinite"/>
          </line>
          <line x1="350" y1="235" x2="330" y2="310" stroke="#f59e0b" stroke-width="2" stroke-dasharray="7,4" marker-end="url(#arr-b)">
            <animate attributeName="stroke-dashoffset" values="0;-22" dur="0.8s" repeatCount="indefinite"/>
          </line>
          <line x1="450" y1="235" x2="470" y2="310" stroke="#ef4444" stroke-width="2" stroke-dasharray="7,4" marker-end="url(#arr-r)">
            <animate attributeName="stroke-dashoffset" values="0;-22" dur="0.8s" repeatCount="indefinite"/>
          </line>
          <line x1="620" y1="235" x2="690" y2="310" stroke="#ef4444" stroke-width="1.5" stroke-dasharray="5,6" marker-end="url(#arr-r)" opacity="0.5"/>

          <!-- Legend -->
          <text x="30" y="430" fill="#3b82f6" font-size="10">━━ Main Pressure Flow (3000 PSI)</text>
          <text x="260" y="430" fill="#22c55e" font-size="10">━━ Flight Control Supply</text>
          <text x="450" y="430" fill="#ef4444" font-size="10">╌╌ Emergency Supply</text>
        </svg>
      </div>

      <?php else: ?>
      <!-- GENERIC DIAGRAM -->
      <div class="svg-wrap">
        <svg viewBox="0 0 900 380" xmlns="http://www.w3.org/2000/svg" class="system-svg">
          <text x="450" y="40" text-anchor="middle" fill="#94a3b8" font-size="16" font-weight="bold"><?php echo htmlspecialchars($system['name']); ?> — System Overview</text>
          <?php
            $color = $system['color'] ?? '#3b82f6';
            $boxes = [
              ['Primary Source','70','140','Provides main system energy'],
              ['Distribution','330','140','Routes to all consumers'],
              ['Backup/Emergency','590','140','Secondary supply when primary fails'],
              ['Aircraft Systems','200','290','Flight systems powered'],
              ['Monitoring','510','290','EICAS / warning systems'],
            ];
            foreach($boxes as $bi => $box):
          ?>
            <rect x="<?php echo $box[1]; ?>" y="<?php echo $box[2]; ?>" width="200" height="70" rx="10" fill="#1a2d4a" stroke="<?php echo $color; ?>" stroke-width="1.5" opacity="<?php echo 0.6+$bi*0.08; ?>"/>
            <text x="<?php echo $box[1]+100; ?>" y="<?php echo $box[2]+28; ?>" text-anchor="middle" fill="#f1f5f9" font-size="12" font-weight="bold"><?php echo $box[0]; ?></text>
            <text x="<?php echo $box[1]+100; ?>" y="<?php echo $box[2]+48; ?>" text-anchor="middle" fill="#94a3b8" font-size="10"><?php echo $box[3]; ?></text>
          <?php endforeach; ?>
          <line x1="270" y1="175" x2="330" y2="175" stroke="<?php echo $color; ?>" stroke-width="2" stroke-dasharray="6,4" marker-end="url(#arr-b)">
            <animate attributeName="stroke-dashoffset" values="0;-20" dur="0.8s" repeatCount="indefinite"/>
          </line>
          <line x1="530" y1="175" x2="590" y2="175" stroke="<?php echo $color; ?>" stroke-width="2" stroke-dasharray="6,4" marker-end="url(#arr-b)">
            <animate attributeName="stroke-dashoffset" values="0;-20" dur="0.8s" repeatCount="indefinite"/>
          </line>
        </svg>
      </div>
      <?php endif; ?>

      <div class="diagram-notes">
        <div class="dnote"><i data-lucide="info"></i> This diagram shows the functional flow of the <?php echo htmlspecialchars($system['name']); ?> system. Animated dashes represent active power/fluid flow during normal operations.</div>
      </div>
    </div>
  </div>

  <!-- ── MEMORY AIDS TAB ── -->
  <div class="tab-pane" id="tab-mnemonics">
    <div class="mnemonics-header">
      <h2>Memory Aids — <?php echo htmlspecialchars($system['name']); ?></h2>
      <p>Mnemonics and memory techniques to lock in the key facts for your type rating exam</p>
    </div>

    <?php if (!empty($systemMnemonics)): ?>
      <div class="mnemonic-grid">
        <?php foreach ($systemMnemonics as $mi => $m): ?>
          <div class="mnemonic-card" style="animation-delay:<?php echo $mi*0.15; ?>s">
            <div class="mcard-top">
              <div class="mcard-tag">MNEMONIC <?php echo $mi+1; ?></div>
              <div class="mcard-topic"><?php echo htmlspecialchars($m['title']); ?></div>
            </div>
            <div class="mcard-acronym"><?php echo htmlspecialchars($m['acronym']); ?></div>
            <div class="mcard-phrase">"<?php echo htmlspecialchars($m['phrase']); ?>"</div>
            <div class="mcard-divider"></div>
            <div class="mcard-meaning">
              <i data-lucide="arrow-right"></i>
              <?php echo htmlspecialchars($m['meaning']); ?>
            </div>
            <button class="mcard-flip-btn" onclick="flipCard(this)">
              <i data-lucide="refresh-cw"></i> Test Yourself
            </button>
            <div class="mcard-test" style="display:none;">
              <p>Can you recall what <strong><?php echo htmlspecialchars($m['acronym']); ?></strong> stands for?</p>
              <button class="reveal-btn" onclick="revealAnswer(this, '<?php echo addslashes($m['meaning']); ?>')">Reveal Answer</button>
              <div class="reveal-answer" style="display:none;"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- General memory techniques -->
    <div class="memory-tips">
      <h3><i data-lucide="brain"></i> General Memory Techniques for Aviation Study</h3>
      <div class="tips-grid">
        <div class="tip-card">
          <div class="tip-icon">🔗</div>
          <h4>Chaining</h4>
          <p>Link each system component to the next in a story. E.g., "The battery <em>charges</em> the bus, which <em>powers</em> the TRU, which <em>feeds</em> DC loads."</p>
        </div>
        <div class="tip-card">
          <div class="tip-icon">🖼️</div>
          <h4>Visual Palace</h4>
          <p>Walk through the aircraft mentally. Imagine touching each component — electrical panel, battery bay, alternator — as you recall its function.</p>
        </div>
        <div class="tip-card">
          <div class="tip-icon">🔢</div>
          <h4>Number Anchors</h4>
          <p>Pair key numbers with vivid images. 3000 PSI = "three thousand angry bees pushing fluid." 28V DC = "28 candles lighting the DC bus."</p>
        </div>
        <div class="tip-card">
          <div class="tip-icon">🔄</div>
          <h4>Spaced Repetition</h4>
          <p>Review this material after 1 day, then 3 days, then 7, then 21. Each review cements the memory deeper — use the Flashcards section for this.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ── QUICK REFERENCE TAB ── -->
  <div class="tab-pane" id="tab-guide">
    <div class="guide-wrap">
      <h2>Quick Reference — <?php echo htmlspecialchars($system['name']); ?></h2>
      <?php foreach ($lessons as $lesson): ?>
        <?php if (!empty($lesson['summary'])): ?>
          <div class="guide-block">
            <h3><?php echo htmlspecialchars($lesson['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($lesson['summary'])); ?></p>
            <?php
              $kf = json_decode($lesson['key_facts'] ?? '[]', true) ?: [];
              if (!empty($kf)):
            ?>
              <div class="guide-facts">
                <?php foreach ($kf as $f): ?>
                  <span class="gfact"><i data-lucide="check"></i> <?php echo htmlspecialchars($f); ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ── NOTES TAB ── -->
  <div class="tab-pane" id="tab-notes">
    <div class="notes-wrap">
      <h2>My Study Notes</h2>
      <p class="notes-hint">Write down anything you want to remember — your own words stick better than textbook definitions.</p>
      <form method="POST" action="/api/notes/save">
        <input type="hidden" name="system_id" value="<?php echo htmlspecialchars($system['id']); ?>">
        <textarea name="content" class="notes-textarea" rows="14" placeholder="e.g. The Q400 electrical system uses TWO alternators (one per engine) producing 115V AC, which is then rectified by TRUs to 28V DC for the main DC bus. The NiCad batteries provide emergency power..."></textarea>
        <button type="submit" class="btn-save-notes"><i data-lucide="save"></i> Save Notes</button>
      </form>
    </div>
  </div>

</div><!-- end .study-rich-container -->

<style>
/* ── Container ── */
.study-rich-container { padding: 24px; max-width: 1100px; }

/* ── Hero ── */
.study-hero { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:24px; background:#111c2d; border-radius:14px; margin-bottom:28px; flex-wrap:wrap; }
.hero-left { display:flex; align-items:center; gap:18px; }
.hero-icon-wrap { width:64px; height:64px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.hero-ata { font-size:11px; color:#64748b; font-weight:600; letter-spacing:1px; text-transform:uppercase; margin-bottom:4px; }
.hero-title { margin:0 0 6px; font-size:1.8rem; font-weight:700; color:#f1f5f9; }
.hero-desc { margin:0; color:#94a3b8; font-size:.9rem; max-width:500px; }
.hero-stats { display:flex; gap:24px; }
.hstat { text-align:center; }
.hstat-val { display:block; font-size:2rem; font-weight:700; color:#f1f5f9; }
.hstat-lbl { font-size:.75rem; color:#64748b; text-transform:uppercase; letter-spacing:.5px; }

/* ── Tabs ── */
.rich-tabs { display:flex; gap:4px; margin-bottom:24px; background:#111c2d; padding:6px; border-radius:12px; flex-wrap:wrap; }
.rtab { background:none; border:none; padding:10px 18px; color:#64748b; cursor:pointer; border-radius:8px; display:flex; align-items:center; gap:8px; font-size:.9rem; transition:all .2s; }
.rtab:hover { color:#f1f5f9; background:rgba(255,255,255,0.06); }
.rtab.active { color:#f1f5f9; background:#1e3a5f; box-shadow:0 2px 8px rgba(0,0,0,.3); }
.tab-pane { display:none; }
.tab-pane.active { display:block; animation: fadeInUp .3s ease; }
@keyframes fadeInUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

/* ── Lesson Cards ── */
.rich-lesson { background:#111c2d; border:1px solid rgba(255,255,255,.07); border-radius:14px; padding:28px; margin-bottom:24px; border-left:4px solid var(--accent,#3b82f6); transition:box-shadow .2s; animation: fadeInUp .4s ease; }
.rich-lesson:hover { box-shadow:0 8px 32px rgba(0,0,0,.3); }
.lesson-done { opacity:.85; }
.rl-head { display:flex; align-items:center; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.rl-num { width:38px; height:38px; border-radius:50%; background:#1e3a5f; border:2px solid var(--accent,#3b82f6); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; color:#93c5fd; flex-shrink:0; }
.num-done { background:#14532d; border-color:#22c55e; color:#4ade80; }
.rl-title-block { flex:1; }
.rl-title { margin:0 0 4px; font-size:1.25rem; font-weight:700; color:#f1f5f9; }
.rl-sub { font-size:.8rem; color:#64748b; }
.rl-type-badge { padding:4px 12px; border-radius:20px; background:rgba(59,130,246,.15); color:#93c5fd; font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
.rl-body-text { color:#94a3b8; line-height:1.8; font-size:.95rem; margin-bottom:20px; padding:16px; background:rgba(255,255,255,.03); border-radius:8px; }

/* ── Component Strip ── */
.component-strip { display:flex; gap:12px; flex-wrap:wrap; margin:16px 0 20px; }
.cstrip-item { display:flex; flex-direction:column; align-items:center; gap:6px; animation: fadeInUp .4s ease both; }
.cstrip-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
.cstrip-label { font-size:.7rem; color:#64748b; text-align:center; max-width:60px; }

/* ── Info Blocks ── */
.info-block { padding:18px; border-radius:10px; margin:16px 0; }
.info-blue { background:rgba(59,130,246,.08); border:1px solid rgba(59,130,246,.2); }
.info-amber { background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.2); }
.info-red { background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2); }
.info-head { display:flex; align-items:center; gap:8px; margin-bottom:12px; font-size:.9rem; }
.info-blue .info-head { color:#60a5fa; }
.info-amber .info-head { color:#fbbf24; }
.info-red .info-head { color:#f87171; }

.facts-grid { display:flex; flex-wrap:wrap; gap:10px; }
.fact-chip { display:flex; align-items:center; gap:8px; padding:8px 14px; background:rgba(59,130,246,.12); border:1px solid rgba(59,130,246,.2); border-radius:8px; font-size:.85rem; color:#93c5fd; animation:fadeInUp .4s ease both; }
.fact-num { width:22px; height:22px; background:#3b82f6; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; color:#fff; flex-shrink:0; }

.check-list { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:8px; }
.check-list li { display:flex; align-items:flex-start; gap:10px; font-size:.88rem; line-height:1.5; color:#cbd5e1; }
.info-amber .check-list li { color:#fde68a; }
.info-red .check-list li { color:#fca5a5; }
.check-list li i { width:16px; height:16px; flex-shrink:0; margin-top:2px; }

.rl-footer { display:flex; align-items:center; justify-content:space-between; padding-top:18px; margin-top:18px; border-top:1px solid rgba(255,255,255,.07); flex-wrap:wrap; gap:12px; }
.time-pill { display:flex; align-items:center; gap:6px; font-size:.8rem; color:#64748b; background:rgba(255,255,255,.05); padding:6px 12px; border-radius:20px; }
.btn-complete { display:flex; align-items:center; gap:8px; padding:10px 24px; background:#3b82f6; color:#fff; border:none; border-radius:8px; font-size:.9rem; font-weight:600; cursor:pointer; transition:background .2s; }
.btn-complete:hover { background:#2563eb; }
.done-badge { display:flex; align-items:center; gap:6px; color:#22c55e; font-size:.9rem; font-weight:600; }

/* ── Diagram ── */
.diagram-container { background:#111c2d; border-radius:14px; padding:28px; }
.diagram-title { font-size:1.3rem; font-weight:700; color:#f1f5f9; margin:0 0 6px; }
.diagram-subtitle { color:#64748b; font-size:.9rem; margin:0 0 24px; }
.svg-wrap { background:#0a1628; border-radius:10px; padding:20px; overflow-x:auto; }
.system-svg { width:100%; max-width:900px; }
.diagram-notes { margin-top:16px; }
.dnote { display:flex; align-items:flex-start; gap:10px; font-size:.85rem; color:#64748b; padding:12px; background:rgba(255,255,255,.03); border-radius:8px; }

/* ── Mnemonics ── */
.mnemonics-header { margin-bottom:24px; }
.mnemonics-header h2 { font-size:1.4rem; color:#f1f5f9; margin:0 0 6px; }
.mnemonics-header p { color:#64748b; font-size:.9rem; }
.mnemonic-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; margin-bottom:40px; }
.mnemonic-card { background:#111c2d; border:1px solid rgba(255,255,255,.07); border-radius:14px; padding:22px; animation:fadeInUp .5s ease both; transition:box-shadow .2s; }
.mnemonic-card:hover { box-shadow:0 8px 32px rgba(0,0,0,.4); }
.mcard-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.mcard-tag { font-size:.7rem; color:#64748b; font-weight:600; letter-spacing:1px; text-transform:uppercase; }
.mcard-topic { font-size:.8rem; color:#3b82f6; font-weight:600; }
.mcard-acronym { font-size:1.8rem; font-weight:800; color:#f59e0b; letter-spacing:2px; margin-bottom:10px; }
.mcard-phrase { font-size:.9rem; color:#94a3b8; font-style:italic; line-height:1.5; margin-bottom:14px; }
.mcard-divider { height:1px; background:rgba(255,255,255,.07); margin-bottom:14px; }
.mcard-meaning { display:flex; align-items:flex-start; gap:8px; font-size:.85rem; color:#cbd5e1; line-height:1.5; margin-bottom:16px; }
.mcard-flip-btn { width:100%; padding:9px; background:rgba(59,130,246,.12); border:1px solid rgba(59,130,246,.2); border-radius:8px; color:#93c5fd; font-size:.85rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background .2s; }
.mcard-flip-btn:hover { background:rgba(59,130,246,.22); }
.mcard-test { margin-top:14px; padding:14px; background:rgba(255,255,255,.04); border-radius:8px; font-size:.85rem; color:#94a3b8; }
.reveal-btn { margin-top:10px; padding:8px 16px; background:#1e3a5f; border:1px solid #3b82f6; border-radius:6px; color:#93c5fd; cursor:pointer; font-size:.83rem; }
.reveal-answer { margin-top:10px; padding:10px; background:rgba(34,197,94,.1); border-radius:6px; color:#4ade80; font-size:.85rem; }

/* ── Memory Tips ── */
.memory-tips { background:#111c2d; border-radius:14px; padding:24px; }
.memory-tips h3 { display:flex; align-items:center; gap:10px; font-size:1.1rem; color:#f1f5f9; margin:0 0 20px; }
.tips-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; }
.tip-card { background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.07); border-radius:10px; padding:18px; }
.tip-icon { font-size:1.8rem; margin-bottom:10px; }
.tip-card h4 { margin:0 0 8px; font-size:.95rem; color:#f1f5f9; }
.tip-card p { margin:0; font-size:.82rem; color:#64748b; line-height:1.6; }

/* ── Guide ── */
.guide-wrap { max-width:800px; }
.guide-wrap h2 { font-size:1.4rem; color:#f1f5f9; margin:0 0 24px; }
.guide-block { background:#111c2d; border-radius:12px; padding:20px; margin-bottom:16px; border-left:3px solid #3b82f6; }
.guide-block h3 { margin:0 0 10px; font-size:1rem; color:#93c5fd; }
.guide-block p { margin:0 0 12px; color:#94a3b8; font-size:.9rem; line-height:1.7; }
.guide-facts { display:flex; flex-wrap:wrap; gap:8px; }
.gfact { display:flex; align-items:center; gap:6px; font-size:.8rem; color:#4ade80; background:rgba(34,197,94,.08); border:1px solid rgba(34,197,94,.15); border-radius:6px; padding:4px 10px; }

/* ── Notes ── */
.notes-wrap { max-width:800px; }
.notes-wrap h2 { font-size:1.4rem; color:#f1f5f9; margin:0 0 6px; }
.notes-hint { color:#64748b; font-size:.88rem; margin:0 0 20px; }
.notes-textarea { width:100%; background:#111c2d; border:1px solid rgba(255,255,255,.1); border-radius:10px; color:#f1f5f9; font-size:.9rem; line-height:1.7; padding:16px; resize:vertical; font-family:inherit; box-sizing:border-box; }
.notes-textarea:focus { outline:none; border-color:#3b82f6; }
.btn-save-notes { display:flex; align-items:center; gap:8px; margin-top:14px; padding:11px 24px; background:#3b82f6; color:#fff; border:none; border-radius:8px; font-size:.9rem; font-weight:600; cursor:pointer; transition:background .2s; }
.btn-save-notes:hover { background:#2563eb; }

.empty-lessons { text-align:center; padding:60px; color:#64748b; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const rtabs = document.querySelectorAll('.rtab');
    const panes = document.querySelectorAll('.tab-pane');
    rtabs.forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            rtabs.forEach(b => b.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');
        });
    });

    lucide.createIcons();
});

function markLessonComplete(lessonId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader"></i> Saving...';
    lucide.createIcons();
    fetch('/api/lessons/' + lessonId + '/complete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    }).then(r => {
        if (r.ok) location.reload();
        else { btn.disabled = false; btn.innerHTML = '<i data-lucide="check"></i> Mark as Complete'; lucide.createIcons(); }
    }).catch(() => { btn.disabled = false; });
}

function flipCard(btn) {
    const card = btn.closest('.mnemonic-card');
    const testDiv = card.querySelector('.mcard-test');
    if (testDiv.style.display === 'none') {
        testDiv.style.display = 'block';
        btn.innerHTML = '<i data-lucide="eye-off"></i> Hide Test';
    } else {
        testDiv.style.display = 'none';
        btn.innerHTML = '<i data-lucide="refresh-cw"></i> Test Yourself';
        const ans = card.querySelector('.reveal-answer');
        if(ans) ans.style.display = 'none';
    }
    lucide.createIcons();
}

function revealAnswer(btn, answer) {
    const div = btn.nextElementSibling;
    div.style.display = 'block';
    div.textContent = '✓ ' + answer;
    btn.style.display = 'none';
}
</script>
