<?php
/**
 * Universal System Chapters Template
 * Renders CBT-style chapter content for any Q400 system.
 * Used by detail.php for all systems except ATA24 (which has its own file).
 *
 * Each system returns an array with:
 *   chapters[]   – array of chapter content blocks
 *   qrh[]        – QRH procedures, memory items, limitations
 *   quiz[]        – 8-10 knowledge check questions
 */

$ataCode = $system['ata_code'] ?? '';
$systemColor = $system['color'] ?? '#3b82f6';

// ── Load system content ──────────────────────────────────────
switch ($ataCode) {
    case 'ATA29': $sc = ata29_content(); break;
    case 'ATA28': $sc = ata28_content(); break;
    case 'ATA71': case 'ATA72': $sc = ata71_content(); break;
    case 'ATA61': $sc = ata61_content(); break;
    case 'ATA27': $sc = ata27_content(); break;
    case 'ATA32': $sc = ata32_content(); break;
    case 'ATA21': $sc = ata21_content(); break;
    case 'ATA36': $sc = ata36_content(); break;
    case 'ATA30': $sc = ata30_content(); break;
    case 'ATA26': $sc = ata26_content(); break;
    case 'ATA22': $sc = ata22_content(); break;
    case 'ATA34': $sc = ata34_content(); break;
    case 'ATA23': $sc = ata23_content(); break;
    case 'ATA31': $sc = ata31_content(); break;
    case 'ATA35': $sc = ata35_content(); break;
    case 'ATA33': $sc = ata33_content(); break;
    case 'ATA22B': $sc = fms_content(); break;
    case 'CW':   $sc = cw_content(); break;
    case 'QRH':  $sc = qrh_content(); break;
    default:     $sc = generic_content($system); break;
}

$chapters  = $sc['chapters']  ?? [];
$qrh       = $sc['qrh']       ?? [];
$quiz      = $sc['quiz']       ?? [];
$chCount   = count($chapters) + (!empty($qrh) ? 1 : 0) + (!empty($quiz) ? 1 : 0);

// ── Shared CBT styles (only output once via CBT_STYLES_LOADED flag) ─────
if (!defined('CBT_STYLES_LOADED')) {
    define('CBT_STYLES_LOADED', true);
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
    <?php
}
?>
<div class="cbt-wrapper">

  <!-- ── LEFT NAV ── -->
  <nav class="cbt-nav">
    <div class="cbt-nav-title">Chapters</div>
    <?php foreach ($chapters as $ci => $ch): ?>
    <a class="cbt-nav-item <?php echo $ci===0?'active':''; ?>" href="#ch<?php echo $ci+1; ?>" onclick="setActiveNav(this)">
      <span class="nav-num"><?php echo $ci+1; ?></span>
      <?php echo htmlspecialchars($ch['navTitle'] ?? $ch['title']); ?>
    </a>
    <?php endforeach; ?>
    <?php if (!empty($qrh)): ?>
    <a class="cbt-nav-item" href="#ch-qrh" onclick="setActiveNav(this)">
      <span class="nav-num" style="background:#ef444422;color:#ef4444;">QRH</span> QRH Procedures
    </a>
    <?php endif; ?>
    <?php if (!empty($quiz)): ?>
    <a class="cbt-nav-item" href="#ch-quiz" onclick="setActiveNav(this)">
      <span class="nav-num" style="background:#22c55e22;color:#22c55e;">✓</span> Knowledge Check
    </a>
    <?php endif; ?>
    <div class="cbt-nav-progress">
      <div class="meta-pill">Progress</div>
      <div class="nav-prog-bar"><div class="nav-prog-fill" id="navProgress" style="width:0%"></div></div>
    </div>
  </nav>

  <!-- ── MAIN CONTENT ── -->
  <div class="cbt-content">

  <?php foreach ($chapters as $ci => $ch): ?>
    <div class="chapter-section" id="ch<?php echo $ci+1; ?>">
      <div class="chapter-badge">
        <span class="badge-num"><?php echo str_pad($ci+1, 2, '0', STR_PAD_LEFT); ?></span>
        <?php echo htmlspecialchars($ch['badge'] ?? 'Chapter'); ?>
      </div>
      <h2 class="chapter-title"><?php echo htmlspecialchars($ch['title']); ?></h2>
      <?php if (!empty($ch['subtitle'])): ?>
        <p class="chapter-subtitle"><?php echo htmlspecialchars($ch['subtitle']); ?></p>
      <?php endif; ?>
      <div class="chapter-meta">
        <span class="meta-pill">⏱ <?php echo $ch['time'] ?? '10 min'; ?> read</span>
      </div>
      <?php if (!empty($ch['objective'])): ?>
      <div class="chapter-objective">
        <span class="obj-icon">🎯</span>
        <p><?php echo $ch['objective']; ?></p>
      </div>
      <?php endif; ?>

      <?php if (!empty($ch['analogy'])): ?>
      <div class="analogy-box">
        <div class="analogy-label">💡 <?php echo htmlspecialchars($ch['analogy']['label'] ?? 'The Analogy'); ?></div>
        <p><?php echo $ch['analogy']['text']; ?></p>
      </div>
      <?php endif; ?>

      <?php if (!empty($ch['body'])): ?>
      <div class="cbt-body"><?php echo $ch['body']; ?></div>
      <?php endif; ?>

      <?php foreach ($ch['cards'] ?? [] as $card): ?>
        <div class="info-card <?php echo htmlspecialchars($card['type'] ?? 'blue'); ?>">
          <div class="card-head"><?php echo $card['head']; ?></div>
          <?php if (!empty($card['table'])): ?>
          <table class="spec-table">
            <thead><tr><?php foreach ($card['table']['headers'] as $h): ?><th><?php echo htmlspecialchars($h); ?></th><?php endforeach; ?></tr></thead>
            <tbody>
              <?php foreach ($card['table']['rows'] as $row): ?>
              <tr><?php foreach ($row as $ci2 => $cell): ?>
                <td class="<?php echo $ci2===0?'td-key':'td-val'; ?>"><?php echo $cell; ?></td>
              <?php endforeach; ?></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php elseif (!empty($card['html'])): ?>
            <?php echo $card['html']; ?>
          <?php elseif (!empty($card['list'])): ?>
          <ul style="margin:0;padding-left:20px;line-height:1.9;font-size:14px;">
            <?php foreach ($card['list'] as $item): ?><li><?php echo $item; ?></li><?php endforeach; ?>
          </ul>
          <?php elseif (!empty($card['items'])): ?>
          <ul style="margin:0;padding-left:20px;line-height:1.9;font-size:14px;">
            <?php foreach ($card['items'] as $item): ?><li><?php echo $item; ?></li><?php endforeach; ?>
          </ul>
          <?php elseif (!empty($card['steps'])): ?>
          <div class="flow-steps">
            <?php foreach ($card['steps'] as $si => $step): ?>
            <div class="flow-step" data-step="<?php echo $si+1; ?>">
              <div class="flow-step-content">
                <?php if (is_array($step)): ?>
                <p class="flow-step-title"><?php echo htmlspecialchars($step['title'] ?? ''); ?></p>
                <?php if (!empty($step['desc'])): ?><p class="flow-step-desc"><?php echo $step['desc']; ?></p><?php endif; ?>
                <?php if (!empty($step['state'])): ?>
                <span class="flow-step-state state-<?php echo $step['state']; ?>"><?php echo htmlspecialchars($step['stateLabel'] ?? ''); ?></span>
                <?php endif; ?>
                <?php else: ?>
                <p class="flow-step-title"><?php echo htmlspecialchars($step); ?></p>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <?php if (!empty($ch['components'])): ?>
      <div class="component-grid">
        <?php foreach ($ch['components'] as $comp): ?>
        <div class="comp-card" onclick="toggleComp(this)">
          <div class="comp-icon"><?php echo $comp['icon']; ?></div>
          <p class="comp-name"><?php echo htmlspecialchars($comp['name']); ?></p>
          <p class="comp-role"><?php echo htmlspecialchars($comp['role']); ?></p>
          <p class="comp-spec"><?php echo htmlspecialchars($comp['spec']); ?></p>
          <div class="comp-body"><?php echo $comp['detail']; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($ch['failures'])): ?>
      <div class="failure-grid">
        <?php foreach ($ch['failures'] as $fail): ?>
        <div class="failure-card">
          <div class="failure-header" onclick="toggleFailure(this)">
            <span class="failure-severity <?php echo $fail['sev']==='high'?'sev-high':'sev-med'; ?>">
              <?php echo $fail['sev']==='high'?'WARNING':'CAUTION'; ?>
            </span>
            <span class="failure-name"><?php echo htmlspecialchars($fail['name']); ?></span>
            <span class="failure-eicas"><?php echo htmlspecialchars($fail['eicas']); ?></span>
            <span style="color:#4a6080;font-size:18px;margin-left:auto">▼</span>
          </div>
          <div class="failure-body">
            <div class="failure-row">
              <div class="failure-col"><div class="failure-col-label">What Happened</div><div class="failure-col-text"><?php echo $fail['what']; ?></div></div>
              <div class="failure-col"><div class="failure-col-label">Auto Response</div><div class="failure-col-text"><?php echo $fail['auto']; ?></div></div>
              <div class="failure-col"><div class="failure-col-label">Pilot Action</div><div class="failure-col-text"><?php echo $fail['pilot']; ?></div></div>
            </div>
            <?php if (!empty($fail['note'])): ?>
            <div class="info-card <?php echo $fail['noteType']??'green'; ?>" style="margin-top:12px">
              <div class="card-head"><?php echo $fail['noteHead']??'Key Point'; ?></div>
              <p style="margin:0;font-size:13px;line-height:1.6;"><?php echo $fail['note']; ?></p>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($ch['quiz'])): ?>
      <div class="inline-quiz" data-quiz="iq<?php echo $ci; ?>">
        <div class="quiz-label">🧠 Quick Check — Chapter <?php echo $ci+1; ?></div>
        <p class="quiz-question"><?php echo htmlspecialchars($ch['quiz']['q']); ?></p>
        <div class="quiz-options">
          <?php foreach ($ch['quiz']['options'] as $oi => $opt): ?>
          <div class="quiz-opt" onclick="checkQuiz(this,'iq<?php echo $ci; ?>',<?php echo $oi===$ch['quiz']['correct']?'true':'false'; ?>)">
            <?php echo htmlspecialchars($opt); ?>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="quiz-explanation" id="iq<?php echo $ci; ?>-exp"><?php echo $ch['quiz']['explanation']; ?></div>
      </div>
      <?php endif; ?>

      <button class="mark-read-btn" onclick="markChapterRead(this,<?php echo $ci+1; ?>)">✓ Mark Chapter <?php echo $ci+1; ?> Complete</button>
    </div>
  <?php endforeach; ?>

  <!-- ── QRH CHAPTER ── -->
  <?php if (!empty($qrh)): ?>
  <div class="chapter-section" id="ch-qrh">
    <div class="chapter-badge" style="background:#ef444422;border-color:#ef444433;">
      <span class="badge-num" style="color:#ef4444;">QRH</span> Quick Reference Handbook
    </div>
    <h2 class="chapter-title">QRH — Procedures &amp; Memory Items</h2>
    <p class="chapter-subtitle">What the Q400 QRH says about this system — correlate with your system knowledge</p>
    <div class="chapter-objective">
      <span class="obj-icon">📋</span>
      <p><strong>How to use this section:</strong> The QRH is your in-flight reference. Learn the <em>reason behind</em> each procedure — not just the steps. When you know the system, the checklist makes sense. When the checklist makes sense, you execute it faster and safer.</p>
    </div>

    <?php foreach ($qrh as $qi => $item): ?>
    <div style="margin-bottom:20px;">
      <?php
        $cardType = 'red';
        $headIcon = '🚨';
        if ($item['type'] === 'memory') { $cardType = 'red'; $headIcon = '🧠 MEMORY ITEM — '; }
        elseif ($item['type'] === 'abnormal') { $cardType = 'amber'; $headIcon = '⚠️ ABNORMAL — '; }
        elseif ($item['type'] === 'limit') { $cardType = 'blue'; $headIcon = '📐 LIMITATION — '; }
        elseif ($item['type'] === 'eicas') { $cardType = 'purple'; $headIcon = '💻 EICAS — '; }
        elseif ($item['type'] === 'normal') { $cardType = 'green'; $headIcon = '✅ NORMAL PROC — '; }
      ?>
      <div class="info-card <?php echo $cardType; ?>">
        <div class="card-head"><?php echo $headIcon . htmlspecialchars($item['title']); ?></div>
        <?php if (!empty($item['eicasMsg'])): ?>
          <p style="font-family:monospace;font-size:13px;color:#f59e0b;margin:0 0 10px;background:#0a1628;padding:6px 10px;border-radius:6px;display:inline-block;">EICAS: <?php echo htmlspecialchars($item['eicasMsg']); ?></p>
        <?php endif; ?>
        <?php if (!empty($item['steps'])): ?>
        <ol style="margin:0;padding-left:20px;color:inherit;font-size:14px;line-height:2;">
          <?php foreach ($item['steps'] as $step): ?><li><?php echo $step; ?></li><?php endforeach; ?>
        </ol>
        <?php elseif (!empty($item['items'])): ?>
        <ul style="margin:0;padding-left:20px;color:inherit;font-size:14px;line-height:1.9;">
          <?php foreach ($item['items'] as $it): ?><li><?php echo $it; ?></li><?php endforeach; ?>
        </ul>
        <?php elseif (!empty($item['html'])): ?>
          <?php echo $item['html']; ?>
        <?php endif; ?>
        <?php if (!empty($item['why'])): ?>
        <div style="margin-top:12px;padding:10px 14px;background:rgba(255,255,255,.05);border-radius:8px;font-size:13px;color:#94a3b8;line-height:1.6;">
          <strong style="color:#60a5fa;">WHY:</strong> <?php echo $item['why']; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <button class="mark-read-btn" onclick="markChapterRead(this,'qrh')">✓ Mark QRH Section Complete</button>
  </div>
  <?php endif; ?>

  <!-- ── KNOWLEDGE CHECK ── -->
  <?php if (!empty($quiz)): ?>
  <div class="chapter-section full-quiz" id="ch-quiz">
    <div class="chapter-badge"><span class="badge-num">✓</span> Knowledge Check</div>
    <h2 class="chapter-title">Test Your Understanding</h2>
    <p class="chapter-subtitle"><?php echo count($quiz); ?> exam-style questions — prove you know this system</p>
    <div class="quiz-progress-bar">
      <div class="quiz-progress-fill" id="quizProgressFill" style="width:0%"></div>
    </div>
    <div id="quizContainer"></div>
    <div class="quiz-score-card" id="quizScoreCard">
      <div class="score-number" id="scoreNum">0/<?php echo count($quiz); ?></div>
      <div class="score-label">Knowledge Check Complete</div>
      <div class="score-message" id="scoreMsg"></div>
      <button onclick="restartQuiz()" style="margin-top:20px;padding:12px 32px;background:#f59e0b;color:#000;border:none;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer;">Restart Quiz</button>
    </div>
  </div>
  <?php endif; ?>

  </div><!-- end cbt-content -->
</div><!-- end cbt-wrapper -->

<script>
<?php echo "const __sysTotalChapters = " . $chCount . ";
"; ?>
// ── Shared CBT interaction functions ────────────────────────────────────────
// (Also defined in ata24_chapters.php — guarded to avoid double-definition)
if (typeof setActiveNav === 'undefined') {
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
  const pct = Math.round((chaptersRead.size / __sysTotalChapters) * 100);
  document.getElementById('navProgress').style.width = pct + '%';
}
}
// ── Quiz engine for this system ─────────────────────────────────────────

const __sysQuizQ = <?php echo json_encode($quiz); ?>;
const __sysTotal = __sysQuizQ.length;
let __sysIdx = 0, __sysScore = 0;

function renderQuestion(idx) {
  if (!__sysTotal) return;
  const q = __sysQuizQ[idx];
  const pct = Math.round((idx / __sysTotal) * 100);
  document.getElementById('quizProgressFill').style.width = pct + '%';
  document.getElementById('quizContainer').innerHTML = `
    <div class="quiz-card">
      <div class="quiz-q-num">Question ${idx + 1} of ${__sysTotal}</div>
      <p class="quiz-q-text">${q.q}</p>
      <div class="quiz-options">
        ${q.options.map((o,i) => `<div class="quiz-opt" onclick="answerQuestion(${i},${q.correct},'${escStr(q.explanation)}')">${o}</div>`).join('')}
      </div>
      <div class="quiz-result" id="quizResult"></div>
      <button class="quiz-next-btn" id="nextBtn" onclick="nextQuestion()">
        ${idx < __sysTotal - 1 ? 'Next Question →' : 'See My Score'}
      </button>
    </div>`;
}

function escStr(s) { return (s||'').replace(/'/g,"&#39;").replace(/"/g,'&quot;'); }

function answerQuestion(chosen, correct, explanation) {
  if (document.getElementById('nextBtn').classList.contains('show')) return;
  document.querySelectorAll('.quiz-opt').forEach((o,i) => {
    o.onclick = null;
    if (i === correct) o.classList.add('correct');
    else if (i === chosen) o.classList.add('incorrect');
  });
  if (chosen === correct) __sysScore++;
  const result = document.getElementById('quizResult');
  result.className = 'quiz-result ' + (chosen===correct ? 'correct-result' : 'wrong-result');
  result.innerHTML = (chosen===correct ? '✓ Correct! ' : '✗ Incorrect. ') + explanation;
  document.getElementById('nextBtn').classList.add('show');
}

function nextQuestion() {
  __sysIdx++;
  if (__sysIdx >= __sysTotal) {
    document.getElementById('quizContainer').style.display = 'none';
    document.getElementById('quizProgressFill').style.width = '100%';
    const card = document.getElementById('quizScoreCard');
    card.classList.add('show');
    document.getElementById('scoreNum').textContent = __sysScore + '/' + __sysTotal;
    const p = __sysScore / __sysTotal;
    document.getElementById('scoreMsg').textContent = p>=0.9
      ? '🏆 Excellent! You are ready for the oral exam on this system.'
      : p>=0.7 ? '✈️ Good work. Review the chapters covering incorrect answers and re-test.'
      : p>=0.5 ? '📖 Needs more study. Re-read chapters 3-5, then attempt again.'
      : '🔁 Start again from Chapter 1 and work through carefully.';
  } else {
    renderQuestion(__sysIdx);
    document.getElementById('quizProgressFill').style.width = Math.round((__sysIdx/__sysTotal)*100) + '%';
  }
}

function restartQuiz() {
  __sysIdx = 0; __sysScore = 0;
  document.getElementById('quizContainer').style.display = 'block';
  document.getElementById('quizScoreCard').classList.remove('show');
  renderQuestion(0);
}

if (__sysTotal > 0) {
  if (document.readyState !== 'loading') renderQuestion(0);
  else document.addEventListener('DOMContentLoaded', () => renderQuestion(0));
}
</script>

<?php
// ════════════════════════════════════════════════════════════════════════
// SYSTEM CONTENT FUNCTIONS
// ════════════════════════════════════════════════════════════════════════

// ── ATA29 HYDRAULIC POWER ─────────────────────────────────────────────
function ata29_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Why the Q400 has four hydraulic systems and what each one does',
      'time'=>'8 min','objective'=>'After this chapter you will understand the <strong>purpose and architecture</strong> of the Q400 hydraulic system — four independent circuits, what they power, and why four is safer than one.',
      'analogy'=>['label'=>'The Analogy — Bicycle Brakes','text'=>'When you squeeze a bicycle brake lever, fluid pressure travels down a cable to grip the wheel. Aircraft hydraulics work the same way — but instead of cable, high-pressure fluid flows through pipes at <strong>3000 PSI</strong> to move massive flight control surfaces, retract heavy landing gear, and apply powerful disc brakes. One system failing is no more disabling than one brake caliper leaking — you still have the others.'],
      'body'=>'<p>The Q400 Hydraulic Power System (ATA 29) has <strong>four completely independent circuits</strong> — No.1 Main, No.2 Main, No.3 Main, and the Emergency system. Each operates at <em>3000 PSI</em> using phosphate-ester fire-resistant fluid. The redundancy is intentional: any single system failure leaves three others to maintain full aircraft control.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ 5 Numbers You Must Know','table'=>['headers'=>['Parameter','Value','Importance'],
          'rows'=>[['Operating pressure (all mains)','3000 PSI','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Hydraulic fluid type','MIL-H-46000 (phosphate ester)','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Number of main systems','3 (No.1, No.2, No.3)','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Emergency system','1 (hand-operated)','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Fluid colour','Purple (MIL-H-46000)','<span class="spec-badge normal">KNOW</span>']]]]
      ],
      'quiz'=>['q'=>'What is the operating pressure of the Q400 main hydraulic systems?','options'=>['1500 PSI','2000 PSI','3000 PSI','5000 PSI'],'correct'=>2,'explanation'=>'All three main hydraulic systems on the Q400 operate at 3000 PSI. This is a fundamental limitation number that appears in the QRH and FCOM. Examiners regularly ask this — there is no variation between systems.']
    ],
    [
      'badge'=>'System Architecture','title'=>'Components & Power Sources','navTitle'=>'Components',
      'subtitle'=>'What pumps the fluid and how each system is powered','time'=>'12 min',
      'objective'=>'Understand each hydraulic system\'s <strong>pump type and power source</strong> — the key to knowing what survives when an engine fails.',
      'body'=>'<p>Each main hydraulic system has its own dedicated pump and power source. This independence means an engine failure does not take down more than one hydraulic system. No.1 and No.2 are engine-driven; No.3 uses an electric pump, providing hydraulic power even with both engines failed (as long as electrical power remains).</p>',
      'components'=>[
        ['icon'=>'🔵','name'=>'No.1 Main System','role'=>'Engine 1 driven pump','spec'=>'3000 PSI — Primary','detail'=>'Powered by an engine-driven pump on Engine 1. Primarily supplies the left aileron, left spoilers, and elevator. If Engine 1 fails, No.1 system loses its primary pump. No.2 and No.3 maintain full flight control authority.'],
        ['icon'=>'🔵','name'=>'No.2 Main System','role'=>'Engine 2 driven pump','spec'=>'3000 PSI — Primary','detail'=>'Powered by an engine-driven pump on Engine 2. Primarily supplies the right aileron, right spoilers, and some elevator. Full independence from No.1 — a No.2 failure is handled by No.1 and No.3.'],
        ['icon'=>'🟣','name'=>'No.3 Main System','role'=>'AC electric pump','spec'=>'3000 PSI — Redundancy','detail'=>'Powered by an AC electric motor (unlike No.1 and No.2 which are engine-driven). This is critical — No.3 remains operational even if both engines fail, as long as AC power is available (battery-powered via inverter in extreme emergency). Primarily backs up flight controls and brakes.'],
        ['icon'=>'🔴','name'=>'Emergency System','role'=>'Hand-operated pump','spec'=>'Manual — Last Resort','detail'=>'A manually-operated hand pump located in the cockpit. Provides limited hydraulic power for emergency landing gear extension and limited braking when all three main systems are unavailable. Crew must physically pump the handle — it is slow but functional.'],
      ],
      'quiz'=>['q'=>'Engine 1 fails completely. Which hydraulic systems are still operational?','options'=>['No.1 only (it\'s the backup for engine failure)','No.2 and No.3 main systems remain fully operational','All three main systems — hydraulic systems are independent of engine power','No.2 only — No.3 requires engine 1 bleed air'],'correct'=>1,'explanation'=>'No.1 main system is driven by Engine 1 — losing Engine 1 loses the No.1 pump. However, No.2 (Engine 2 driven) and No.3 (AC electric pump, independent of engine mechanical drive) both remain fully operational. All flight controls, landing gear, and brakes continue to function normally from No.2 and No.3.']
    ],
    [
      'badge'=>'Hydraulic Consumers','title'=>'What the Hydraulics Power','navTitle'=>'Consumers',
      'subtitle'=>'Flight controls, landing gear, brakes — which system powers what','time'=>'10 min',
      'objective'=>'Map out which hydraulic system powers which aircraft function, and understand <strong>what cross-supplies what</strong> for redundancy.',
      'body'=>'<p>Each hydraulic consumer (flight controls, gear, brakes) is supplied by multiple systems. This cross-supply ensures no single hydraulic failure removes any flight function entirely.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🗺️ Consumer Supply Matrix','table'=>['headers'=>['Consumer','Primary Supply','Backup Supply'],
          'rows'=>[['Ailerons','No.1 + No.2 (both sides)','No.3'],
                   ['Elevators','No.1 + No.2 (both systems)','No.3'],
                   ['Rudder','No.2','No.1 or No.3'],
                   ['Spoilers','No.1 + No.2','No.3'],
                   ['Landing Gear Retract/Extend','No.1','No.2 (alternate)'],
                   ['Normal Brakes','No.3','No.2'],
                   ['Emergency Gear Extension','Emergency (hand pump)','N/A'],
                   ['Flaps','No.2','No.3']]]],
        ['type'=>'red','head'=>'⚠️ Exam Trap — Gear Extension','html'=>'<p style="font-size:14px;color:#fca5a5;line-height:1.7;margin:0;">Candidates often say "if hydraulics fail, use the emergency extension." But remember — if <em>any one main system</em> is available, use it for gear extension. The emergency hand pump is the last resort, reserved for triple-system failure. Do not use it prematurely.</p>']
      ],
      'quiz'=>['q'=>'Which hydraulic system powers the normal wheel brakes on the Q400?','options'=>['No.1 Main System','No.2 Main System','No.3 Main System','Emergency hand pump'],'correct'=>2,'explanation'=>'No.3 Main System is the primary supply for the normal braking system. This is why No.3\'s electric pump is so important — it maintains braking even with both engines failed, as long as AC power is available. No.2 serves as the backup brake supply if No.3 fails.']
    ],
    [
      'badge'=>'Abnormal Procedures','title'=>'When Hydraulics Fail','navTitle'=>'Abnormals',
      'subtitle'=>'Every hydraulic failure scenario with pilot actions','time'=>'15 min',
      'objective'=>'Know the <strong>EICAS indications, automatic responses, and pilot actions</strong> for every hydraulic abnormal.',
      'body'=>'<p>Hydraulic failures are graduated — from minor (single system, no functional loss) to serious (multiple systems, degraded control). The key is understanding that one or even two system failures are manageable. Only with all three main systems failed is there a genuine emergency requiring immediate action.</p>',
      'failures'=>[
        ['sev'=>'med','name'=>'Single Main System Failure','eicas'=>'HYD 1(2)(3) FAIL','what'=>'One hydraulic system has lost pressure. Possible causes: pump failure, pipe fracture, fluid loss through leak.','auto'=>'Remaining systems continue to supply all consumers via cross-supply. No flight function is lost.','pilot'=>'1. Identify failed system. 2. Verify pressure loss (pressure gauge to zero). 3. Reduce demands on remaining systems. 4. Land at nearest suitable airport — not immediate, but without delay.','note'=>'A single system failure is managed but leaves reduced redundancy. With two systems remaining you are fully controllable — the third system is your safety margin.','noteType'=>'green','noteHead'=>'✅ Manageable'],
        ['sev'=>'high','name'=>'Two Main Systems Failed','eicas'=>'HYD 1 FAIL + HYD 2 FAIL (example)','what'=>'Two of the three main systems have lost pressure. Significantly degraded redundancy.','auto'=>'Remaining single system maintains essential consumers. Some non-essential functions may be inoperative.','pilot'=>'1. Declare emergency. 2. Configure aircraft for approach. 3. Plan for possible degraded braking on landing. 4. Land at nearest suitable airport — NOW.','note'=>'With only one main system, you are at the edge of certification limits. Some controls will feel heavier or have reduced authority. Do not delay landing.','noteType'=>'amber','noteHead'=>'⚠️ Urgent'],
        ['sev'=>'high','name'=>'All Main Systems Failed','eicas'=>'HYD 1 FAIL + HYD 2 FAIL + HYD 3 FAIL','what'=>'Complete loss of all three main hydraulic systems. This is an extreme emergency.','auto'=>'No automatic backup. All hydraulically-powered flight controls lose power assistance. Controls may become heavy or require significant physical effort.','pilot'=>'1. DECLARE EMERGENCY. 2. Use emergency hand pump for landing gear extension. 3. Plan for limited/no braking — use aerodynamic braking, runway length, and reverse propeller. 4. LAND IMMEDIATELY.','note'=>'Triple hydraulic failure is extremely rare — multiple independent systems would all need to fail simultaneously. Nevertheless, know the procedure: emergency gear extension, plan for no brakes, maximum runway length available.','noteType'=>'red','noteHead'=>'🚨 Emergency'],
      ]
    ],
    [
      'badge'=>'Limits & Numbers','title'=>'Hydraulic System Limitations','navTitle'=>'Limitations',
      'subtitle'=>'All hydraulic values and limits from the FCOM','time'=>'6 min',
      'cards'=>[
        ['type'=>'amber','head'=>'📐 Master Reference — Hydraulic Limitations','table'=>['headers'=>['Parameter','Value','Note'],
          'rows'=>[['System operating pressure','3000 PSI','All three main systems'],
                   ['Fluid type','MIL-H-46000 (phosphate ester)','Fire-resistant — NOT petroleum'],
                   ['Fluid colour','Purple (dyed)','Easy leak identification'],
                   ['Low pressure warning','Approx 1500 PSI','EICAS caution triggered'],
                   ['Pump type — No.1 & No.2','Engine-driven','Speed proportional to engine RPM'],
                   ['Pump type — No.3','Electric (AC motor)','Independent of engine RPM'],
                   ['Emergency system','Hand-operated pump','Manual — cockpit handle']]]]
      ],
      'quiz'=>['q'=>'What type of fluid is used in the Q400 hydraulic systems and why?','options'=>['Petroleum-based fluid — cheap and widely available','MIL-H-46000 phosphate ester — fire-resistant, required for certification','Synthetic aviation oil — same as engine oil for simplicity','Water-glycol mixture — prevents freezing at altitude'],'correct'=>1,'explanation'=>'MIL-H-46000 is a phosphate ester hydraulic fluid, specified because it is fire-resistant. If a hydraulic pipe fractures near a hot engine or brake assembly, petroleum-based fluid would ignite — phosphate ester fluid will not. This is a certification requirement. Note: phosphate ester fluid is purple (dyed for identification) and is NOT compatible with petroleum-based fluids.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'HYD SYS ALL FAIL — Memory Items','steps'=>['1. Flight controls — check (degraded authority expected)','2. Landing gear — emergency extension (use hand pump)','3. Brakes — plan for aerodynamic braking only','4. Squawk 7700 — declare emergency','5. Land at nearest suitable aerodrome'],'why'=>'When all hydraulic systems fail, you lose power-assisted flight controls and normal brakes simultaneously. Memory items ensure immediate configuration for survival landing before consulting checklists.'],
    ['type'=>'abnormal','title'=>'HYD SYS 1(2)(3) FAIL — Abnormal Checklist','eicasMsg'=>'HYD 1 FAIL / HYD 2 FAIL / HYD 3 FAIL','items'=>['Identify failed system from pressure gauge and EICAS','HYD SYS PUMP switch — check ON (confirm pump commanded on)','Hydraulic quantity — check (low qty confirms leak)','If quantity low — minimize hydraulic demands','Adjacent system demands — reduce where possible','Land at nearest suitable aerodrome'],'why'=>'The checklist confirms the failure is genuine, checks for fluid loss (leak vs pump failure have different implications), and initiates load reduction to preserve the remaining systems.'],
    ['type'=>'abnormal','title'=>'GEAR UNSAFE — Emergency Extension Procedure','eicasMsg'=>'GEAR UNSAFE','steps'=>['1. GEAR lever — DN (normal extension first attempt)','2. If unsafe indication persists — GEAR PUMP handle — pump until green lights/DN indication','3. Alternate gear extension — use emergency hand pump (minimum 30 strokes)','4. Gear down indication — confirm 3 greens','5. Land with care — gear may not be fully locked'],'why'=>'Emergency gear extension uses the hydraulic hand pump to extend gear when the normal hydraulic system has insufficient pressure. The 30-stroke minimum ensures enough fluid has been moved to fully extend and lock all three gear legs.'],
    ['type'=>'limit','title'=>'Hydraulic System Limitations','items'=>['Maximum system pressure: 3000 PSI (all main systems)','Fluid specification: MIL-H-46000 phosphate ester ONLY','Do not mix fluid types — incompatibility causes seal degradation','Low pressure warning: approximately 1500 PSI','No dispatch with all three main systems failed','Single system failure — dispatch with MEL restrictions']],
  ],
  'quiz' => [
    ['q'=>'How many hydraulic systems does the Q400 have in total?','options'=>['Two main systems plus one emergency','Three main systems only','Three main systems plus one emergency hand-pump system','Four main systems plus one emergency'],'correct'=>2,'explanation'=>'The Q400 has four hydraulic circuits total: No.1 Main (Engine 1 driven), No.2 Main (Engine 2 driven), No.3 Main (AC electric pump), and the Emergency system (manual hand pump). The three main systems each operate at 3000 PSI; the emergency system provides limited pressure through manual pumping.'],
    ['q'=>'What makes No.3 hydraulic system unique compared to No.1 and No.2?','options'=>['No.3 operates at higher pressure (4000 PSI) for critical systems','No.3 is powered by an AC electric motor rather than an engine-driven pump','No.3 uses a different hydraulic fluid for compatibility','No.3 can only be activated in an emergency'],'correct'=>1,'explanation'=>'No.3 system uses an AC electric motor to drive its pump rather than being mechanically driven by an engine. This means No.3 remains operational even if both engines fail (as long as AC electrical power is available). This is critical — it maintains braking and flight control backup power even in extreme scenarios.'],
    ['q'=>'What hydraulic fluid does the Q400 use and what is its key property?','options'=>['Standard petroleum hydraulic fluid — lightweight and widely available','MIL-H-46000 phosphate ester — fire-resistant','Aviation engine oil — same type as engine lubrication system','De-ionised water with corrosion inhibitors'],'correct'=>1,'explanation'=>'MIL-H-46000 is the specified fluid — a phosphate ester compound that is fire-resistant. This is safety-critical: hydraulic pipes near engines and hot brake assemblies could expose fluid to ignition sources. Phosphate ester fluid will not sustain combustion. It is purple-dyed for identification and must not be mixed with petroleum-based fluids.'],
    ['q'=>'Engine 2 fails completely in cruise. What is the hydraulic status?','options'=>['No.2 and No.3 systems both fail — only No.1 remains','No.2 system loses pressure — No.1 and No.3 remain fully operational','All three systems reduce to 1500 PSI reduced operation mode','No change — all three systems are independent of engine mechanical drive'],'correct'=>1,'explanation'=>'No.2 Main system is driven by Engine 2\'s mechanical power. When Engine 2 fails, No.2 loses its pump drive and loses pressure. No.1 (Engine 1 driven) and No.3 (AC electric pump, independent of engine mechanical drive) remain fully operational at 3000 PSI. All flight control functions are maintained through No.1 and No.3 cross-supply.'],
    ['q'=>'During an emergency gear extension using the hand pump, approximately how many pump strokes are required?','options'=>['5-10 strokes','15-20 strokes','Minimum 30 strokes','The pilot pumps until a resistance is felt — quantity not specified'],'correct'=>2,'explanation'=>'The emergency hand pump procedure requires a minimum of 30 strokes to ensure sufficient fluid displacement to fully extend and lock all three landing gear legs. This is a procedural number that appears in the QRH. Candidates often underestimate the effort required — in practice, 30+ strokes on a hand pump is physically demanding but necessary.'],
    ['q'=>'A hydraulic system failure is confirmed with pressure at zero but hydraulic quantity normal. What does this suggest?','options'=>['A major leak has caused both pressure and quantity loss simultaneously','The pump has failed but the fluid circuit is intact — no leak','The pressure gauge is faulty — treat as normal','An airloc in the system has caused temporary pressure loss'],'correct'=>1,'explanation'=>'Pressure at zero with normal quantity indicates the pump has failed but the hydraulic fluid circuit is intact (no leak). This is a better situation than low quantity (which indicates a leak and ongoing fluid loss). A failed pump means the system is non-functional but the fluid is still there — if the engine restarts or another pump is available, the system may recover.'],
    ['q'=>'Which aircraft functions remain available if the No.3 hydraulic system fails?','options'=>['All functions are lost — No.3 is the primary system','Only emergency braking is available','All functions except normal braking — No.1 and No.2 cover all other consumers','Only flight controls — all other functions are lost'],'correct'=>2,'explanation'=>'No.3 primarily supplies normal wheel brakes. If No.3 fails, normal braking is lost, but No.2 provides backup braking. All other hydraulic consumers (flight controls, landing gear, flaps) are supplied primarily by No.1 and No.2. No.3 is also a backup for flight controls, so losing it reduces redundancy on those systems but does not eliminate control.'],
    ['q'=>'What is the consequence of mixing MIL-H-46000 phosphate ester fluid with petroleum-based hydraulic fluid?','options'=>['No consequence — they are chemically compatible','Reduced fire resistance only — system still functions normally','Seal degradation and system damage — never mix fluids','Slight pressure reduction — system remains serviceable with reduced pressure'],'correct'=>2,'explanation'=>'MIL-H-46000 phosphate ester is chemically incompatible with petroleum-based hydraulic fluids. Mixing causes immediate degradation of rubber seals throughout the hydraulic system, leading to leaks and potential system failure. Once contaminated, the entire hydraulic system requires draining, flushing, and seal replacement. This is an expensive and time-consuming maintenance action.']
  ]
]; }

// ── ATA28 FUEL ────────────────────────────────────────────────────────
function ata28_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'How the Q400 stores, manages, and delivers fuel to both engines',
      'time'=>'8 min','objective'=>'Understand the <strong>fuel system architecture</strong> — where fuel is stored, how it gets to the engines, and what the crossfeed and jettison systems do.',
      'analogy'=>['label'=>'The Analogy — Your Car Fuel System','text'=>'Your car has one tank, one pump, one engine. Simple. Now imagine two fuel tanks (one in each door), two engines (one in each wheel well), and a valve that lets either engine drink from either tank. You can also dump fuel out the back if the car gets too heavy. That\'s the Q400 fuel system — with added complexity because altitude, temperature, and pressure all affect how fuel behaves.'],
      'body'=>'<p>The Q400 fuel system (ATA 28) stores jet fuel in <strong>two main wing tanks</strong> and feeds both PW150A engines. It includes fuel pumps, a crossfeed system, quantity indication, low-level warnings, a fuel jettison (dump) capability, and fuel heating to prevent icing. All fuel management is ultimately the crew\'s responsibility — automation assists but does not replace active monitoring.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ Key Numbers — Fuel System','table'=>['headers'=>['Parameter','Value','Importance'],
          'rows'=>[['Main tank capacity (each)','Approx 4,100 kg (typical — varies by aircraft)','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Total usable fuel','Approx 8,200 kg (two main tanks)','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Crossfeed','Single valve connecting both feed systems','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Fuel type','Jet A / Jet A-1','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Fuel jettison','Dump system available','<span class="spec-badge normal">KNOW</span>']]]]
      ],
      'quiz'=>['q'=>'What type of fuel does the Q400 use?','options'=>['AVGAS 100LL (aviation gasoline)','Jet A / Jet A-1 (kerosene-based)','Diesel — same as automotive diesel','JP-8 military fuel only'],'correct'=>1,'explanation'=>'The Q400 uses Jet A or Jet A-1, which are kerosene-based fuels used by virtually all turbine-powered aircraft. AVGAS (aviation gasoline) is for piston aircraft. The Q400\'s PW150A turboprop engines require jet fuel — using gasoline would immediately destroy the engines.']
    ],
    [
      'badge'=>'System Components','title'=>'Tanks, Pumps & Valves','navTitle'=>'Components',
      'subtitle'=>'Every fuel system component and what it does','time'=>'12 min',
      'objective'=>'Identify all fuel system components and understand how fuel flows from tanks to engines under normal and abnormal conditions.',
      'body'=>'<p>The fuel system is arranged symmetrically — each side mirrors the other. Left tank feeds Engine 1; right tank feeds Engine 2. The crossfeed valve links both sides, allowing either engine to feed from either tank.</p>',
      'components'=>[
        ['icon'=>'🛢️','name'=>'Main Wing Tanks (×2)','role'=>'Left tank (Engine 1) / Right tank (Engine 2)','spec'=>'~4,100 kg each','detail'=>'Integral wing tanks — the fuel is contained within the sealed wing structure itself, not in a separate bladder or tank. Each tank has quantity sensors, low-level sensors, and feed ports. The left tank feeds Engine 1 by default; right tank feeds Engine 2.'],
        ['icon'=>'⚡','name'=>'Boost Pumps (×2 per tank)','role'=>'Pressurize fuel lines to engines','spec'=>'AC electric pumps','detail'=>'Each wing tank has two boost pumps (primary and standby). Their job is to pressurize the fuel line to prevent cavitation (vapour bubble formation) at altitude. At high altitude, fuel pressure must be maintained above vapour pressure. If both boost pumps in one tank fail, gravity feed may still work at low altitude but is not reliable at cruise.'],
        ['icon'=>'🔀','name'=>'Crossfeed Valve','role'=>'Connects left and right fuel systems','spec'=>'Single valve — OPEN/CLOSED','detail'=>'When CLOSED (normal): each engine feeds only from its own tank. When OPEN: either engine can feed from either tank. Used for: fuel balancing (if tanks become asymmetric), emergency feeding (if one tank\'s pumps fail), and fuel jettison. Only one valve — its failure in the closed position isolates the two systems.'],
        ['icon'=>'🌡️','name'=>'Fuel Heater','role'=>'Prevents fuel icing','spec'=>'Engine bleed air heated','detail'=>'Jet fuel contains dissolved water which can crystallize at high altitude temperatures (down to -40°C or lower). The fuel heater uses engine bleed air to warm the fuel before it enters the engine, preventing ice crystals from blocking the fuel filters. Fuel filter icing triggers a differential pressure warning on EICAS.'],
        ['icon'=>'📊','name'=>'Fuel Quantity Indicating System (FQIS)','role'=>'Measures fuel quantity in each tank','spec'=>'Capacitance probes','detail'=>'Capacitance probes measure the dielectric constant of the fuel (which changes with quantity and density). The FQIS accounts for fuel density variations with temperature to give an accurate mass reading in kg or lbs. Critical: fuel quantity is shown in mass (kg), not volume — density changes with temperature.'],
      ],
      'quiz'=>['q'=>'What is the purpose of the fuel crossfeed valve?','options'=>['Prevents fuel from flowing backwards into the tanks','Connects the left and right fuel systems allowing either engine to feed from either tank','Regulates fuel pressure to exactly 50 PSI','Automatically balances fuel between tanks'],'correct'=>1,'explanation'=>'The crossfeed valve, when open, connects both fuel feed systems — allowing either engine to draw fuel from either wing tank. Normal operation has it closed (each engine feeds its own tank). It is opened for: fuel imbalance correction, emergency crossfeed if one side\'s pumps fail, and during fuel jettison operations.']
    ],
    [
      'badge'=>'Normal Operation','title'=>'Normal Fuel Management','navTitle'=>'Normal Ops',
      'subtitle'=>'How fuel flows and how pilots manage it in flight','time'=>'10 min',
      'objective'=>'Follow fuel from tanks to engines under normal conditions and understand <strong>fuel imbalance limits and management procedures</strong>.',
      'body'=>'<p>Under normal operation: crossfeed CLOSED, both boost pumps ON for each tank, engines feeding from their respective sides. The crew monitors fuel quantity, checks for imbalance, and verifies burn rate matches flight plan.</p>',
      'cards'=>[
        ['type'=>'green','head'=>'✅ Normal Fuel Flow — Step by Step','steps'=>[
          ['title'=>'Before Start','desc'=>'Both boost pumps selected ON. Fuel quantities verified against flight plan. Crossfeed valve CLOSED (normal). Fuel cocks CLOSED (will open on engine start).','state'=>'normal','stateLabel'=>'Boost pumps energized'],
          ['title'=>'Engine Start','desc'=>'Fuel cock opens → fuel flows from tank to engine through boost pumps → fuel control unit meters flow → light-off occurs. Boost pumps ensure adequate pressure during starting when engine-driven fuel pump is not yet spinning fast enough.','state'=>'normal','stateLabel'=>'Tank → pump → fuel cock → engine'],
          ['title'=>'Normal Cruise','desc'=>'Left tank → Engine 1. Right tank → Engine 2. Fuel burns equally (both engines at similar power). Monitor for imbalance every 30-60 min. FQIS shows quantities; crew checks against flight plan fuel.','state'=>'normal','stateLabel'=>'Parallel, independent fuel feeds'],
          ['title'=>'Fuel Imbalance Management','desc'=>'If tanks develop imbalance exceeding limits: either reduce power on the heavy side engine (burns more fuel) or open crossfeed valve and selectively boost from the heavy tank. Imbalance creates a lateral weight asymmetry that demands aileron trim.','state'=>'warn','stateLabel'=>'⚠️ Monitor and correct'],
        ]],
        ['type'=>'red','head'=>'⚠️ Fuel Imbalance Limit','html'=>'<p style="font-size:14px;color:#fca5a5;line-height:1.7;margin:0;">The Q400 has a maximum permitted fuel imbalance between left and right tanks. Exceeding this limit creates asymmetric loading that requires excessive aileron trim — eventually exceeding trim authority. <strong>Always monitor fuel quantities</strong> and take corrective action before approaching the imbalance limit. Exact figures are in the FCOM Limitations — your examiner will ask for the principle, if not the exact number.</p>']
      ],
      'quiz'=>['q'=>'If the left fuel tank shows significantly more fuel than the right tank during cruise, what is the most appropriate action?','options'=>['Open the crossfeed valve and allow the engines to balance automatically','Reduce Engine 1 power slightly so Engine 1 burns more fuel from the heavier left tank, correcting the imbalance','Transfer fuel from left to right using the fuel transfer pump','Declare an emergency — fuel imbalance is always an emergency situation'],'correct'=>1,'explanation'=>'Reducing Engine 1 power (which is fed by the left tank) causes Engine 1 to burn fuel faster relative to Engine 2 — burning down the heavier left tank toward the lighter right tank. Alternatively, the crossfeed valve can be opened to allow both engines to feed from the heavy side. The crossfeed approach burns the heavy tank equally across both engines. Either method is procedurally acceptable depending on the severity of imbalance.']
    ],
    [
      'badge'=>'Abnormal Procedures','title'=>'Fuel System Failures','navTitle'=>'Abnormals',
      'subtitle'=>'Fuel imbalance, low fuel, pump failures, and jettison','time'=>'12 min',
      'failures'=>[
        ['sev'=>'med','name'=>'Fuel Boost Pump Failure','eicas'=>'FUEL PUMP FAIL','what'=>'One or both boost pumps in a tank have failed. Engine may still feed by gravity/suction at low altitude but not reliable at cruise altitude.','auto'=>'EICAS caution. Engine continues to operate if suction feed is adequate at current altitude. No automatic crossfeed — crew must respond.','pilot'=>'1. Check remaining boost pump on same side. 2. If both pumps in a tank failed — open crossfeed valve (engine now feeds from opposite tank). 3. Monitor engine parameters. 4. Avoid high altitude if suction feed only.','note'=>'Gravity/suction feed works at low altitude but may be insufficient at cruise. If both pumps in one tank fail, the safest action is crossfeed from the opposite tank — confirmed by stable engine parameters.','noteType'=>'amber','noteHead'=>'⚠️ Cross-feed required'],
        ['sev'=>'med','name'=>'Fuel Low Level','eicas'=>'FUEL LOW (L or R)','what'=>'Fuel quantity in one tank is approaching the low-level threshold — typically around 300-400 kg remaining.','auto'=>'EICAS caution/warning displayed. No automatic action.','pilot'=>'1. Check actual fuel quantity. 2. Review fuel remaining vs. distance to destination. 3. Consider diversion. 4. Check for unexpected high fuel burn (possible leak indication).','note'=>'A low fuel warning that was not expected based on flight plan may indicate a fuel leak. If quantity is dropping faster than expected burn rate, treat as possible fuel leak and divert immediately.','noteType'=>'red','noteHead'=>'🚨 Check for fuel leak'],
        ['sev'=>'high','name'=>'Engine Fuel Feed Problem / Flame-Out Risk','eicas'=>'ENG FUEL FEED / FUEL FLOW LOW','what'=>'Engine is not receiving adequate fuel. Risk of engine flame-out if not corrected.','auto'=>'EICAS warning. Engine parameters (ITT, N1, N2, fuel flow) will show abnormal readings.','pilot'=>'1. Boost pumps — check ON. 2. Fuel cock — check OPEN. 3. Crossfeed — consider OPEN (feed from opposite tank). 4. If engine flame-out — follow engine restart checklist. 5. If restart not possible — single engine approach.','','',''],
      ]
    ],
    [
      'badge'=>'Limits & Numbers','title'=>'Fuel System Limitations','navTitle'=>'Limitations',
      'time'=>'6 min',
      'cards'=>[
        ['type'=>'amber','head'=>'📐 Master Reference — Fuel Limitations','table'=>['headers'=>['Parameter','Value'],
          'rows'=>[['Fuel type','Jet A / Jet A-1 (ASTM D1655)'],
                   ['Total usable fuel','~8,200 kg (two main tanks)'],
                   ['Single tank capacity','~4,100 kg'],
                   ['Minimum fuel for dispatch','Per MEL and fuel planning'],
                   ['Max imbalance','Per FCOM limitations — check specific value'],
                   ['Fuel temp operating range','-40°C to +49°C (Jet A-1)'],
                   ['Pressure fuelling','Single point refuelling available']]]],
      ],
      'quiz'=>['q'=>'Why does the Q400 fuel quantity system measure fuel in kilograms (mass) rather than litres (volume)?','options'=>['Kilograms are easier to calculate in flight planning','Fuel density changes with temperature, so mass is a more accurate measure of energy content','The fuel tanks are shaped irregularly, making volume measurement inaccurate','Regulatory requirement — all aircraft must use mass measurement'],'correct'=>1,'explanation'=>'Fuel density changes significantly with temperature — cold fuel is denser (more mass per litre) than warm fuel. Since the engines consume fuel based on mass flow (kg/hour), using mass measurement in the cockpit ensures accuracy regardless of fuel temperature. A fuel quantity display in litres would overestimate available fuel on a hot day and underestimate on a cold day — a potentially dangerous error.']
    ],
  ],
  'qrh' => [
    ['type'=>'abnormal','title'=>'FUEL IMBALANCE — Abnormal Checklist','eicasMsg'=>'FUEL IMBAL','steps'=>['1. Fuel quantities — note (identify heavier tank)','2. Engine power — consider reducing on engine fed by heavier tank','3. Crossfeed valve — OPEN (if imbalance exceeding limits)','4. Boost pumps — both sides ON','5. Monitor fuel quantities — confirm imbalance reducing','6. Crossfeed valve — CLOSE (when balance restored)','7. If imbalance persists — possible fuel leak, plan diversion'],'why'=>'Fuel imbalance causes lateral weight asymmetry requiring aileron trim. Beyond the certified imbalance limit, trim may be insufficient to maintain wings level. Prompt correction prevents structural and controllability issues.'],
    ['type'=>'abnormal','title'=>'FUEL LOW — Warning Checklist','eicasMsg'=>'FUEL LOW L / R','steps'=>['1. Fuel quantity — check both tanks','2. Fuel flow — compare against flight plan','3. Fuel remaining — calculate time to destination vs. remaining fuel','4. If unexpected low fuel — consider fuel leak','5. Crossfeed — OPEN if asymmetric','6. Divert to nearest suitable aerodrome if required'],'why'=>'Fuel low warning triggers when quantity approaches minimum reserves. Unexpected low fuel (not matching planned burn rate) suggests a leak — which demands immediate diversion regardless of destination proximity.'],
    ['type'=>'limit','title'=>'Fuel System Limitations','items'=>['Fuel type: Jet A / Jet A-1 only (no AVGAS)','Fuel imbalance limit: specified in FCOM Limitations section','Fuel boost pump failure: crossfeed required at cruise altitude','Minimum fuel for departure: per company fuel policy and MEL','No fuel jettison in icing conditions']],
    ['type'=>'eicas','title'=>'Key Fuel EICAS Messages','html'=>'<table class="spec-table"><thead><tr><th>Message</th><th>Meaning</th><th>Priority</th></tr></thead><tbody><tr><td class="td-key">FUEL LOW L/R</td><td class="td-val">Low fuel quantity — left/right tank</td><td><span class="spec-badge critical">WARNING</span></td></tr><tr><td class="td-key">FUEL IMBAL</td><td class="td-val">Tank imbalance exceeds limits</td><td><span class="spec-badge must">CAUTION</span></td></tr><tr><td class="td-key">FUEL PUMP FAIL</td><td class="td-val">Boost pump failure</td><td><span class="spec-badge must">CAUTION</span></td></tr><tr><td class="td-key">FUEL FILTER</td><td class="td-val">Fuel filter differential pressure high (possible icing)</td><td><span class="spec-badge must">CAUTION</span></td></tr></tbody></table>']
  ],
  'quiz' => [
    ['q'=>'How many main fuel tanks does the Q400 have?','options'=>['One central fuselage tank','Two main wing tanks','Three tanks — two wing plus one centre','Four tanks — two mains plus two collector tanks'],'correct'=>1,'explanation'=>'The Q400 has two main wing tanks — one in each wing. Each tank feeds its respective engine: left tank → Engine 1, right tank → Engine 2. There is no centre tank on the standard Q400.'],
    ['q'=>'What does "crossfeed OPEN" allow?','options'=>['Fuel to transfer automatically between tanks to maintain balance','Either engine to feed from either tank','The engines to feed from an external fuel source','Fuel jettison through a dedicated dump port'],'correct'=>1,'explanation'=>'Opening the crossfeed valve connects both fuel feed systems, allowing either engine to draw fuel from either wing tank. This is used for: fuel imbalance correction, emergency feeding if one side\'s boost pumps fail, and during fuel jettison. Normal operation has crossfeed closed.'],
    ['q'=>'What is the purpose of the fuel heater on the Q400?','options'=>['Increases fuel combustion efficiency at high altitude','Prevents ice crystals from forming in the fuel and blocking filters','Vaporizes fuel for improved atomization in the engine','Heats the wing tanks to prevent fuel freezing in extremely cold conditions'],'correct'=>1,'explanation'=>'Jet fuel contains dissolved water. At high altitude, temperatures can reach -40°C or lower, causing this water to crystallize into ice particles that can block fuel filters. The fuel heater uses engine bleed air to warm the fuel flow, preventing ice crystal formation before the fuel reaches the engine fuel control unit.'],
    ['q'=>'Fuel boost pump failure with engine still running — what is the immediate risk?','options'=>['Immediate engine flame-out — without boost pumps the engine cannot receive fuel','No risk — engines are self-fuelling through their own mechanical fuel pumps','Engine starvation at high altitude — suction feed alone is insufficient above certain altitude','Engine overfuelling — without boost pump pressure regulation, too much fuel reaches the engine'],'correct'=>2,'explanation'=>'Engines can continue to operate on suction feed (the engine-driven fuel pump draws fuel from the tank) at low altitude. At cruise altitude, the atmospheric pressure differential is insufficient for reliable suction feed — fuel vapour forms (cavitation), interrupting flow. This is why boost pumps are critical at altitude: they maintain positive pressure in the fuel lines above the vapour pressure of the fuel.'],
    ['q'=>'A fuel low warning appears on the EICAS but the fuel burn matches the flight plan exactly. What does this suggest?','options'=>['The FQIS is malfunctioning — actual quantity is higher than shown','The fuel was loaded incorrectly at departure — actual total was less than planned','A fuel leak has reduced the available fuel faster than normal burn','The fuel density was underestimated causing the quantity gauge to read lower than actual'],'correct'=>0,'explanation'=>'If the fuel low warning appears but the burn rate exactly matches the flight plan, the most likely explanation is an FQIS (fuel quantity indicating system) malfunction or incorrect fuel loading at departure. If there were a fuel leak, the burn rate would be higher than planned (fuel is being lost beyond what the engines are consuming). Investigate the FQIS and cross-check all fuel quantity indicators.'],
    ['q'=>'Maximum fuel imbalance between tanks is limited primarily because:','options'=>['Higher imbalance causes fuel starvation in the lighter tank','Structural limits on wing bending moment are exceeded','Lateral weight asymmetry requires excessive aileron trim, eventually exceeding trim authority','Fuel computer cannot accurately measure asymmetric quantities above the limit'],'correct'=>2,'explanation'=>'Fuel imbalance creates a lateral weight asymmetry — one wing is heavier than the other. The autopilot or pilot must use aileron input to maintain wings level, which requires aileron trim. Beyond the certified limit, the trim authority is insufficient to keep the aircraft level without continuous pilot input — and even then, control authority is degraded. This is a structural and controllability limit.'],
    ['q'=>'The Q400 fuel quantity system measures fuel in kilograms rather than litres because:','options'=>['Kg is simpler arithmetic for flight planning','Fuel density varies with temperature, so mass accurately represents available energy regardless of density','Volume measurement probes are unreliable in wing-shaped tanks','Regulation requires mass measurement for turbine aircraft'],'correct'=>1,'explanation'=>'Fuel energy content is proportional to mass, not volume. A litre of cold fuel has more mass (and energy) than a litre of warm fuel. Measuring in kg ensures the indicated quantity accurately represents the energy available to the engines regardless of fuel temperature. Using volume would cause errors — potentially dangerous under-fuel or over-fuel situations.'],
    ['q'=>'An engine flame-out occurs in flight due to fuel starvation. First action after confirming fuel starvation:','options'=>['Immediately declare Mayday and prepare for forced landing','Check: boost pumps ON, fuel cock open, crossfeed valve OPEN from opposite tank — then attempt restart','Open fuel jettison to reduce weight for single-engine performance','Shut down remaining engine to preserve fuel for second attempt at restart'],'correct'=>1,'explanation'=>'Fuel starvation flame-out is often recoverable if the fuel supply is restored promptly. The first actions are diagnostic and corrective: confirm boost pumps are ON, fuel cock is open, and if the tank pumps failed — open crossfeed from the opposite tank. With fuel supply restored, initiate air start procedure while maintaining aircraft control. Declaration of emergency is concurrent with these actions, not instead of them.']
  ]
]; }

// ── ATA71/72 POWERPLANT ───────────────────────────────────────────────
function ata71_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'The PW150A turboprop — how it works and why it\'s different from a jet',
      'time'=>'10 min','objective'=>'Understand the <strong>PW150A turboprop engine architecture</strong>, the difference between a turboprop and a turbojet, and the key performance parameters.',
      'analogy'=>['label'=>'The Analogy — A Jet Engine Spinning a Fan','text'=>'A jet engine works by sucking air in, compressing it, burning fuel, and blasting the hot gas out the back for thrust. A turboprop does the same thing — but instead of directing all that energy rearward, it uses most of it to spin a shaft, which spins a propeller. The propeller is far more efficient at lower speeds than a pure jet exhaust. Think of it as: a jet engine that decided to use a propeller fan instead of a brute-force exhaust.'],
      'body'=>'<p>Each Q400 is powered by two <strong>Pratt &amp; Whitney Canada PW150A turboprop engines</strong>, each producing up to <em>5,071 shaft horsepower</em> (at maximum takeoff rating). The PW150A is a free-turbine turboprop, meaning the propeller turbine section is aerodynamically (not mechanically) connected to the gas generator section.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ PW150A Key Specifications','table'=>['headers'=>['Parameter','Value','Importance'],
          'rows'=>[['Engine type','Free-turbine turboprop','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Max takeoff power','5,071 SHP each','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Gas generator (N1) RPM','100% = approx 1,020 RPM','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Power turbine (N2) RPM','100% = approx 1,200 RPM','<span class="spec-badge must">MUST KNOW</span>'],
                   ['Propeller RPM','Max 1,020 RPM','<span class="spec-badge must">MUST KNOW</span>'],
                   ['ITT limit','Various — check FCOM','<span class="spec-badge critical">CRITICAL</span>'],
                   ['Oil type','Synthetic turbine engine oil','<span class="spec-badge normal">KNOW</span>']]]]
      ],
      'quiz'=>['q'=>'What is the key difference between a turboprop engine and a turbojet engine?','options'=>['Turboprops use diesel fuel; turbojets use Jet A kerosene','Turboprops extract most engine energy to drive a propeller shaft; turbojets direct energy rearward as jet thrust','Turboprops have no compressor; turbojets have multiple compression stages','Turboprops are reciprocating engines; turbojets are rotary'],'correct'=>1,'explanation'=>'Both turboprops and turbojets use the same Brayton cycle (compress-heat-expand). The difference is what happens to the expanding gas energy: a turbojet directs it rearward to produce direct thrust via jet exhaust. A turboprop extracts most of that energy via a power turbine to drive a propeller shaft. Propellers are highly efficient at lower speeds (below ~Mach 0.7), which is why turboprops suit regional aircraft like the Q400.']
    ],
    [
      'badge'=>'Engine Architecture','title'=>'Inside the PW150A','navTitle'=>'Engine Architecture',
      'subtitle'=>'Gas generator, power turbine, reduction gearbox — how each section works',
      'time'=>'15 min','objective'=>'Map the PW150A\'s internal flow: air inlet → compressor → combustion → turbines → propeller shaft. Understand N1 and N2 measurements.',
      'body'=>'<p>The PW150A is described as a "free turbine" engine. This means the <strong>gas generator (N1)</strong> and the <strong>power turbine (N2)</strong> are NOT mechanically connected — they are aerodynamically linked by the gas flow. This is critical for understanding engine operation: the propeller can be stationary while the gas generator is running.</p>',
      'components'=>[
        ['icon'=>'🌀','name'=>'Compressor (Gas Generator — N1)','role'=>'Compresses intake air','spec'=>'Measured as N1 (%)','detail'=>'Multi-stage axial/centrifugal compressor driven by the gas generator turbine. N1 indicates gas generator speed as a percentage of design RPM. Compressor stall can occur at certain combinations of high power and abnormal airflow — indicated by abnormal sounds and engine parameter fluctuations.'],
        ['icon'=>'🔥','name'=>'Combustion Section','role'=>'Burns fuel with compressed air','spec'=>'Annular combustion chamber','detail'=>'Fuel is injected and ignited in the annular combustor. The extremely hot, high-pressure gas then expands through the turbine stages. Combustion temperature is not directly measured — Interstage Turbine Temperature (ITT) is monitored as a proxy for combustion section health.'],
        ['icon'=>'🌡️','name'=>'ITT — Interstage Turbine Temperature','role'=>'Primary engine health indicator','spec'=>'Limits vary by rating — see FCOM','detail'=>'ITT is measured between the gas generator turbine and the power turbine — the "interstage." It is the most important engine parameter for monitoring engine health and avoiding damage. Exceeding ITT limits causes turbine blade damage and reduces engine life dramatically. Never exceed ITT limits even momentarily.'],
        ['icon'=>'⚙️','name'=>'Power Turbine (N2) + Reduction Gearbox','role'=>'Drives the propeller','spec'=>'N2 measured as %; gearbox reduces to prop RPM','detail'=>'The power turbine extracts energy from the hot gas to drive the propeller shaft via a reduction gearbox. The gearbox reduces the turbine\'s high RPM to the propeller\'s usable RPM (max ~1,020 RPM). The PW150A\'s reduction gearbox also drives the alternators and hydraulic pumps for No.1 and No.2 systems.'],
        ['icon'=>'🔧','name'=>'Fuel Control System / FADEC','role'=>'Meters fuel flow to the engine','spec'=>'Full Authority Digital Engine Control','detail'=>'The PW150A uses a FADEC (Full Authority Digital Engine Control) system that automatically manages fuel flow, power, and limits. The crew sets the power lever position; FADEC delivers the appropriate fuel flow to achieve and protect the commanded power output. FADEC prevents exceeding ITT, torque, and RPM limits.'],
        ['icon'=>'🛡️','name'=>'Inlet Particle Separator (IPS)','role'=>'Protects compressor from debris','spec'=>'ON for all operations','detail'=>'The IPS creates a swirling airflow at the engine inlet that centrifugally separates dirt, sand, gravel, and ice particles from the intake air before they enter the compressor. Leaving IPS OFF in dusty or gravel-runway environments can cause rapid compressor erosion and blade damage.'],
      ],
      'quiz'=>['q'=>'ITT (Interstage Turbine Temperature) is monitored because:','options'=>['It directly controls FADEC fuel scheduling','It is the most accessible indicator of turbine section health — exceeding limits causes blade damage','It determines propeller pitch automatically','It controls the engine anti-ice system activation'],'correct'=>1,'explanation'=>'ITT measures temperature between the gas generator turbine and power turbine stages. It is the primary indicator of turbine section thermal loading. Exceeding ITT limits — even momentarily — causes metallurgical damage to turbine blades that is cumulative and irreversible. FADEC monitors and limits ITT, but crew awareness is essential for situations where FADEC cannot react fast enough (e.g., engine start hot start).']
    ],
    [
      'badge'=>'Engine Controls','title'=>'Power Levers & Engine Control','navTitle'=>'Engine Controls',
      'subtitle'=>'What the power lever does at each position and FADEC\'s role','time'=>'10 min',
      'objective'=>'Understand power lever positions, their effects, and how FADEC protects the engine through automatic limiting.',
      'body'=>'<p>The PW150A power lever has several distinct positions, each commanding a different engine/propeller state. Unlike older aircraft where the lever directly controls a fuel valve, on the Q400 the lever commands a <em>target</em> — FADEC delivers the fuel flow to achieve it while staying within all limits.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🎛️ Power Lever Positions','table'=>['headers'=>['Position','Engine State','Propeller State'],
          'rows'=>[['MAX (Takeoff)','Maximum certified power','Fine pitch — maximum thrust'],
                   ['CLB (Climb)','Climb power rating','Fine pitch'],
                   ['CRZ (Cruise)','Cruise power','Fine pitch'],
                   ['IDLE','Ground idle or flight idle','Coarse pitch or feather direction'],
                   ['DISC (Feather)','Fuel cut — engine shutdown','Feather (blades edge-on to airflow)'],
                   ['BETA','Propeller in beta range (ground only)','Flat pitch — no thrust'],
                   ['REVERSE','Reverse thrust','Negative pitch — reverse thrust']]]
        ],
        ['type'=>'red','head'=>'⚠️ Beta Range — Ground Only!','html'=>'<p style="font-size:14px;color:#fca5a5;line-height:1.7;margin:0;">The Beta range (power lever behind IDLE) is <strong>only permitted on the ground</strong>. Selecting beta range in flight causes the propeller to go to a flat/reverse pitch, which creates massive drag and structural loads that could cause loss of control. The Q400 has ground-sensing systems to prevent inadvertent beta selection in flight, but understanding this limit is critical for every pilot.</p>']
      ],
      'quiz'=>['q'=>'FADEC (Full Authority Digital Engine Control) limits which of the following engine parameters?','options'=>['Airspeed and altitude only — engine parameters are the pilot\'s responsibility','ITT, torque, and N1/N2 RPM — prevents exceedances through automatic fuel scheduling','Propeller pitch only — RPM is managed by the pilot','Fuel quantity and fuel flow — prevents fuel exhaustion'],'correct'=>1,'explanation'=>'FADEC has full authority over the engine fuel system and automatically limits ITT, torque, N1, and N2 to prevent exceedances. When the crew advances the power lever, FADEC delivers the fuel flow needed for the commanded power without exceeding any parameter limit. This is why "hot starts" are so unusual on FADEC-equipped engines — FADEC manages the fuel schedule during start to prevent ITT exceedances.']
    ],
    [
      'badge'=>'Abnormal Procedures','title'=>'Engine Failures & Emergencies','navTitle'=>'Abnormals',
      'subtitle'=>'Engine fire, failure, and abnormal indications — what to do','time'=>'15 min',
      'failures'=>[
        ['sev'=>'high','name'=>'ENGINE FIRE','eicas'=>'ENG 1(2) FIRE','what'=>'Fire detected in engine nacelle. Fire loop sensors have detected temperature above threshold.','auto'=>'EICAS fire warning — bell + red light + FIRE indication. Fire handle illuminates.','pilot'=>'MEMORY ITEM: 1. POWER LEVER — DISC. 2. CONDITION LEVER — FUEL OFF. 3. FIRE HANDLE — PULL (arms bottle). 4. FIRE AGENT — DISCHARGE (push handle). 5. If fire persists after 30s — second bottle if available.','note'=>'Engine fire is the most critical powerplant emergency. The memory items must be executed immediately and in order — no checklist reference until after memory items complete. PULL the fire handle to arm; PUSH to discharge.','noteType'=>'red','noteHead'=>'🚨 MEMORY ITEM — Execute Immediately'],
        ['sev'=>'high','name'=>'ENGINE FAILURE IN FLIGHT','eicas'=>'ENG 1(2) FAIL / ITT LOW / N1 DROP','what'=>'Engine has flamed out, seized, or lost significant power. N1, N2, ITT, and fuel flow all drop.','auto'=>'EICAS warnings. Propeller will move toward feather if NTS (Negative Torque Sensing) is triggered. Aircraft yaws toward failed engine.','pilot'=>'1. Control: rudder to maintain heading (right rudder for Engine 1 failure). 2. Identify and verify failed engine. 3. CONDITION LEVER — feather (if not auto-feathered). 4. Carry out engine failure checklist. 5. Plan for single-engine approach and landing.','note'=>'The Q400 is certified for single-engine operations. The live engine provides enough power for continued safe flight. KEY: rudder control first, then checklist. An uncontrolled yaw is more immediately dangerous than an unfeathered propeller.','noteType'=>'amber','noteHead'=>'⚠️ Control First, Checklist Second'],
        ['sev'=>'med','name'=>'ITT Over-Limit (Hot Start / In-Flight Exceedance)','eicas'=>'ENG ITT HIGH / ITT OVERLIMIT','what'=>'Turbine temperature has exceeded or is approaching the limit. Can occur during start (hot start) or flight (power demand + abnormal conditions).','auto'=>'EICAS caution/warning. FADEC may reduce fuel flow automatically to protect the engine.','pilot'=>'During start: if ITT reaches limit — CONDITION LEVER FUEL OFF immediately (abort start). In flight: reduce power. After any ITT exceedance — aircraft is unairworthy until engineering inspection clears the engine.','','',''],
      ]
    ],
    [
      'badge'=>'Engine Monitoring','title'=>'EICAS Engine Parameters','navTitle'=>'Monitoring',
      'subtitle'=>'Reading engine instruments correctly and recognising abnormal trends',
      'time'=>'8 min',
      'cards'=>[
        ['type'=>'blue','head'=>'📊 Engine EICAS Parameters — What They Mean','table'=>['headers'=>['Parameter','What It Measures','Normal Indication','Why It Matters'],
          'rows'=>[['N1 (%)','Gas generator speed','Varies with power setting','Compressor performance indicator'],
                   ['N2 (%)','Power turbine speed','Closely tracks propeller RPM','Propeller/gearbox health'],
                   ['ITT (°C)','Interstage Turbine Temperature','Varies with power — see limits','Primary engine health parameter'],
                   ['TRQ (%)','Engine torque (power output)','Varies with power setting','Actual power being produced'],
                   ['FF (kg/h)','Fuel flow','Varies with power','Burn rate check against plan'],
                   ['OIL P','Engine oil pressure','Green band','Lubrication system health'],
                   ['OIL T','Engine oil temperature','Green band','Oil cooling adequacy']]]
        ]
      ],
      'quiz'=>['q'=>'N1 drops suddenly to near zero but N2 remains at normal cruise RPM. What has happened?','options'=>['N2 sensor has failed — N1 is reading correctly','The gas generator (N1) has failed/flamed out; the power turbine (N2) is coasting down slowly due to inertia and propeller drag','Both engines have failed simultaneously','FADEC has automatically reduced power for economy reasons'],'correct'=>1,'explanation'=>'N1 measures gas generator speed and N2 measures power turbine speed. If N1 drops suddenly (flame-out or mechanical failure), N2 will coast down more slowly due to the inertia of the rotating power turbine and propeller system. N1 dropping first with N2 following indicates the gas generator has lost combustion — an engine failure. FADEC will attempt an auto-relight if configured. The NTS (Negative Torque Sensing) system may initiate auto-feather.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'ENGINE FIRE — Memory Items (CRITICAL)','eicasMsg'=>'ENG 1(2) FIRE','steps'=>['1. Power Lever — DISC (Disconnect/Flight Idle)','2. Condition Lever — FUEL OFF','3. Fire Handle — PULL (arms extinguisher)','4. Fire Agent — DISCHARGE (push handle — press button)','5. If fire continues after 30 seconds — second agent bottle (if installed)','6. Land at nearest aerodrome — IMMEDIATELY'],'why'=>'Engine fire requires immediate action before checklist use. Delay allows fire to spread to wing structure and fuel tanks. The sequence: cut power → cut fuel → seal the nacelle → discharge agent. Every second matters.'],
    ['type'=>'memory','title'=>'ENGINE FAILURE — Initial Actions','eicasMsg'=>'ENG FAIL / PROP DISC','steps'=>['1. Rudder — APPLY (toward live engine, maintain heading)','2. Power lever (failed engine) — DISC','3. Condition lever (failed engine) — FUEL OFF','4. Prop condition lever — FEATHER (if not auto-feathered)','5. Engine failure checklist — CARRY OUT','6. Declare emergency — MAYDAY'],'why'=>'Rudder FIRST — an uncorrected yaw is an immediate loss-of-control risk. An unfeathered propeller creates asymmetric drag but the aircraft remains controllable. Yaw progression to a stall does not.'],
    ['type'=>'abnormal','title'=>'HOT START — Abort Procedure','eicasMsg'=>'ITT HIGH during start','steps'=>['1. CONDITION LEVER — FUEL OFF immediately','2. Allow engine to motor (starter continues to run — cools engine)','3. Motor for minimum 30 seconds after ITT stops rising','4. Investigate cause before second start attempt','5. Record and report any ITT exceedance to maintenance'],'why'=>'A hot start occurs when too much fuel accumulates before light-off, causing a sudden high-energy combustion event. If not stopped immediately, the temperature spike destroys turbine blades. Cutting fuel and motoring (cranking without fuel) cools the turbine section.'],
    ['type'=>'limit','title'=>'Powerplant Limitations','items'=>['Max continuous power: per FCOM power ratings table','ITT limits: takeoff, max continuous, start — see FCOM limits','N1 max: 100% (do not exceed)','N2 max: 100% (do not exceed)','Engine oil temperature: within green band','Engine oil pressure: within green band','Single-engine: certified — carry out single-engine procedures','Engine fire: evacuate and secure IMMEDIATELY after landing']],
  ],
  'quiz' => [
    ['q'=>'What type of engine is the PW150A?','options'=>['Turbofan — uses a large front fan for most thrust','Turbojet — all thrust from jet exhaust','Free-turbine turboprop — jet core drives a separate power turbine that spins the propeller','Piston-turbine hybrid — combustion piston drives a turbine generator'],'correct'=>2,'explanation'=>'The PW150A is a free-turbine turboprop. "Free turbine" means the power turbine (which drives the propeller) is NOT mechanically connected to the gas generator — they are linked only by the aerodynamic coupling of the gas flow. This allows the propeller to be stationary while the gas generator runs during start, and allows independent speed control of each section.'],
    ['q'=>'Engine fire EICAS warning appears. What is the correct first action?','options'=>['Pull the fire handle immediately','Power lever to DISC (Flight Idle)','Discharge fire agent bottle','Declare MAYDAY on guard frequency'],'correct'=>1,'explanation'=>'The engine fire memory items follow a specific sequence: 1) Power lever DISC, 2) Condition lever FUEL OFF, 3) Fire handle PULL, 4) Fire agent DISCHARGE. The power lever goes first to reduce fuel and power before cutting fuel completely — this prevents a surge and ensures controlled shutdown. Pulling the fire handle without first reducing power is not the correct sequence.'],
    ['q'=>'What does N1 measure on the PW150A?','options'=>['Propeller RPM as a percentage of maximum','Gas generator speed as a percentage of design RPM','Fuel flow in kg per hour','Net thrust output as a percentage of maximum rated thrust'],'correct'=>1,'explanation'=>'N1 is the speed indicator for the gas generator section (compressor + gas generator turbine) expressed as a percentage of design RPM. N2 measures the power turbine speed (which is geared to the propeller). Both are shown on EICAS. N1 dropping to zero while N2 is still positive indicates gas generator failure — the power turbine is coasting.'],
    ['q'=>'What is ITT and why is it the most critical engine monitoring parameter?','options'=>['Inlet Total Temperature — monitors compressor inlet conditions for icing','Interstage Turbine Temperature — the primary indicator of turbine section thermal loading; exceedances damage turbine blades','Internal Throttle Trim — FADEC\'s internal power adjustment mechanism','Integrated Torque Transfer — measures how efficiently power is transferred from gas generator to propeller'],'correct'=>1,'explanation'=>'ITT (Interstage Turbine Temperature) is measured between the gas generator turbine and power turbine stages. It is the primary window into the thermal health of the turbine section. Exceeding ITT limits — even briefly — causes metallurgical damage to turbine blades. Turbine blade damage is cumulative, expensive to repair, and can lead to catastrophic engine failure. Every ITT exceedance must be recorded and reported.'],
    ['q'=>'What is the Inlet Particle Separator (IPS) and when must it be on?','options'=>['An air filter that prevents insects from entering the compressor during ground operations','A centrifugal system that separates dirt, sand, and ice particles from intake air — should be on for all operations','An icing prevention system for the engine inlet lips only — required in visible moisture only','A debris shield that protects the propeller disc — extends automatically on landing'],'correct'=>1,'explanation'=>'The IPS uses a swirling airflow to centrifugally separate contaminants (sand, gravel, ice crystals, insects) from the intake air. These particles, if ingested, cause rapid compressor blade erosion and can trigger compressor stalls. The IPS should be selected ON for all operations — the small performance penalty from slightly reduced airflow efficiency is insignificant compared to the engine protection provided.'],
    ['q'=>'An engine fails in cruise. The MOST important first action is:','options'=>['Immediately feather the propeller to reduce drag','Apply rudder to maintain aircraft heading and prevent yaw developing','Discharge the engine fire bottle as a precaution','Declare MAYDAY before taking any other action'],'correct'=>1,'explanation'=>'An uncontrolled yaw following engine failure can rapidly develop into a spiral dive or stall — especially if combined with speed reduction. Rudder application (toward the live engine) must be the first instinctive response to maintain directional control. An unfeathered propeller creates asymmetric drag but the aircraft remains controllable. An uncontrolled yaw does not allow time for any other actions.'],
    ['q'=>'What happens during a "hot start" and how is it prevented?','options'=>['Hot start occurs when an engine starts in high ambient temperature — prevented by shading the engine','Hot start is when ITT exceeds limits during engine start due to excess fuel accumulation — prevented by aborting immediately with condition lever to FUEL OFF','Hot start is when two starts are attempted within 30 seconds — prevented by observing inter-start cooling periods','Hot start refers to restarting a warm engine — prevented by waiting 5 minutes after shutdown'],'correct'=>1,'explanation'=>'A hot start occurs when fuel accumulates in the combustion chamber before ignition (e.g., due to delayed light-off), then ignites all at once, causing a sudden spike in ITT. FADEC manages fuel flow during start but cannot always prevent this. If ITT rises rapidly toward the limit during start, the condition lever must be moved to FUEL OFF immediately, and the engine motored (cranked without fuel) to cool. This is a time-critical action.'],
    ['q'=>'What does the FADEC system control on the PW150A?','options'=>['Only the ignition system','Only fuel flow in cruise — pilots manage all other parameters','Full authority over fuel metering, power scheduling, and engine parameter limiting (ITT, N1, N2, torque)','Propeller pitch only — the engine fuel system is manually controlled'],'correct'=>2,'explanation'=>'FADEC (Full Authority Digital Engine Control) has full authority over the engine fuel system. It: receives power lever commands, calculates required fuel flow, delivers that fuel flow via the fuel control unit, and prevents parameter exceedances by automatically reducing fuel flow before ITT, N1, N2, or torque limits are reached. The crew commands power; FADEC executes it safely within limits.']
  ]
]; }

// ── ATA61 PROPELLER ───────────────────────────────────────────────────
function ata61_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'How the Q400\'s six-blade propeller works and why it\'s controllable pitch',
      'time'=>'8 min','objective'=>'Understand the purpose of variable pitch on the Q400 propeller and the critical safety systems (feathering and NTS).',
      'analogy'=>['label'=>'The Analogy — A Ship\'s Variable-Pitch Propeller','text'=>'A ship\'s propeller can pivot its blades — flat pitch for slow manoeuvring, steep pitch for fast cruising, and reversed for docking. Aircraft propellers work the same way: <strong>fine pitch</strong> (biting deeply into air) for takeoff and high thrust; <strong>coarse pitch</strong> (less bite) for cruise efficiency; <strong>feathered</strong> (blades edge-on to airflow, zero drag) when the engine fails. This variable pitch is what makes turboprops so efficient across a wide speed range.'],
      'body'=>'<p>The Q400 uses <strong>Dowty R408 six-blade composite propellers</strong>, each with a diameter of approximately 3.93 metres. Six blades are used (versus the typical 4 on many turboprops) to reduce blade length while maintaining thrust area — this lowers ground clearance requirements and reduces propeller noise at the cabin.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ Propeller Key Facts','table'=>['headers'=>['Parameter','Value'],
          'rows'=>[['Propeller type','Dowty R408 six-blade composite variable-pitch'],
                   ['Propeller diameter','~3.93 metres'],
                   ['Blade material','Composite (glass/carbon fibre)'],
                   ['Normal max RPM','~1,020 RPM (100% N2)'],
                   ['Feather','Blades pitched to ~88° — edge-on to airflow'],
                   ['Beta range','Flat pitch — ground use only'],
                   ['Reverse','Negative pitch — reverse thrust for landing deceleration']]]],
      ],
      'quiz'=>['q'=>'Why does the Q400 use six propeller blades instead of the more common four?','options'=>['Six blades generate more total thrust at identical RPM','Shorter blades reduce ground clearance requirements and cabin noise while maintaining the same total thrust area','Six blades are required by the PW150A engine\'s higher power output','Six blades reduce propeller inertia, improving response time to power changes'],'correct'=>1,'explanation'=>'Six shorter blades can provide the same total disc area (and therefore thrust capacity) as four longer blades. The shorter blades require less ground clearance (important on low-wing clearance regional aircraft) and reduce propeller-generated cabin noise due to lower tip speeds. The six-blade configuration is optimised for passenger comfort and ground operations rather than being a pure performance choice.']
    ],
    [
      'badge'=>'Propeller Control','title'=>'Variable Pitch — From Feather to Reverse','navTitle'=>'Variable Pitch',
      'subtitle'=>'Every pitch position, when it\'s used, and what it feels like from the cockpit',
      'time'=>'12 min',
      'objective'=>'Know exactly what each propeller pitch position does, when it is used, and what protections prevent dangerous pitch selections.',
      'body'=>'<p>The propeller pitch control is achieved through a <strong>hydraulic pitch change mechanism</strong> driven by engine oil pressure. The condition levers in the cockpit command the propeller control unit (PCU), which adjusts blade angle to achieve the desired RPM or feather/beta condition.</p>',
      'components'=>[
        ['icon'=>'▶️','name'=>'Fine Pitch (Normal Flight)','role'=>'Maximum thrust generation','spec'=>'Fine angle — bites deeply into air','detail'=>'Fine pitch means the blades are at a smaller angle to the plane of rotation — they "bite" more aggressively into the air, generating maximum thrust per revolution. Used for takeoff, climb, and when high thrust is required. The propeller governor automatically adjusts within the fine-pitch range to maintain selected RPM.'],
        ['icon'=>'⏭️','name'=>'Coarse Pitch (Cruise)','role'=>'Reduced RPM, lower fuel burn','spec'=>'Coarser angle — efficient cruise','detail'=>'At cruise, the propeller pitch is coarsened (blades pitched to a larger angle), allowing the engine to maintain power at lower RPM. Lower RPM means less propeller noise, less mechanical wear, and better fuel efficiency. The propeller governor maintains RPM by adjusting pitch in response to power lever inputs.'],
        ['icon'=>'🔴','name'=>'Feather','role'=>'Engine-failed drag minimisation','spec'=>'~88° — edge-on to airflow','detail'=>'When an engine fails, the feathered propeller has its blades turned edge-on to the airflow (~88° pitch angle). A feathered propeller has minimal aerodynamic drag — it does not autorotate (windmill). This is critical: a windmilling propeller on a failed engine creates enormous drag that can overcome the thrust of the live engine. Feather is commanded by pulling the condition lever to feather, or automatically by the NTS/autofeather system.'],
        ['icon'=>'🔃','name'=>'Beta Range (Ground Only)','role'=>'Directional control and braking on ground','spec'=>'Flat pitch — near-zero thrust','detail'=>'In the beta range, blade pitch is reduced toward flat — the propeller creates almost no thrust in either direction. Used on the ground for slow taxiing and positioning. A protection system prevents beta selection in flight (weight-on-wheels sensing). Inadvertent beta range selection in flight would create enormous drag.'],
        ['icon'=>'⏪','name'=>'Reverse Pitch','role'=>'Reverse thrust for landing deceleration','spec'=>'Negative angle — reverse thrust','detail'=>'Moving power levers into the reverse zone sets negative blade pitch — blades push air forward rather than rearward, creating reverse thrust that decelerates the aircraft. Most effective at higher ground speeds; less effective as speed decreases. Creates significant noise and foreign object ingestion risk at low speeds — smooth, progressive reverse application is preferred.'],
      ],
      'quiz'=>['q'=>'Why is feathering a failed engine\'s propeller so important?','options'=>['Feathering restarts the engine by using airflow through the propeller to restart combustion','A feathered propeller creates minimal drag; a windmilling propeller creates enormous drag that reduces single-engine climb performance dramatically','Feathering prevents structural damage by stopping propeller rotation in frozen conditions','Feathering protects the gearbox by disconnecting the failed engine from the propeller shaft'],'correct'=>1,'explanation'=>'A windmilling (auto-rotating) propeller on a failed engine creates significant aerodynamic drag — sometimes equivalent to deploying a large airbrake. This drag reduces climb performance on the live engine, extends the time needed to reach a safe altitude, and can prevent the aircraft from maintaining controlled flight in marginal conditions. Feathering stops the rotation (or limits it) and minimises drag, allowing the live engine to provide net positive thrust for single-engine climb.']
    ],
    [
      'badge'=>'Safety Systems','title'=>'NTS and Autofeather','navTitle'=>'Safety Systems',
      'subtitle'=>'The systems that automatically protect against propeller-related emergencies',
      'time'=>'10 min','objective'=>'Understand NTS (Negative Torque Sensing) and the autofeather system — two critical safety features that activate automatically on engine failure.',
      'body'=>'<p>Two automatic systems protect the aircraft when an engine fails: <strong>NTS (Negative Torque Sensing)</strong> and <strong>Autofeather</strong>. Together they ensure the failed engine\'s propeller is quickly feathered without requiring pilot input — critical in the first few seconds after engine failure when workload is highest.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🔍 NTS — Negative Torque Sensing','html'=>'<div class="flow-steps"><div class="flow-step" data-step="1"><div class="flow-step-content"><p class="flow-step-title">Normal operation — engine producing positive torque</p><p class="flow-step-desc">Engine turns the propeller (positive torque). NTS is inactive.</p></div></div><div class="flow-step" data-step="2"><div class="flow-step-content"><p class="flow-step-title">Engine fails — propeller now drives engine (negative torque)</p><p class="flow-step-desc">Airflow through the propeller causes it to autorotate, turning the failed engine like a windmill. This is NEGATIVE torque — the propeller is now driving the engine, not vice versa.</p></div></div><div class="flow-step" data-step="3"><div class="flow-step-content"><p class="flow-step-title">NTS activates — moves pitch toward feather</p><p class="flow-step-desc">NTS senses the torque reversal and automatically moves the propeller pitch toward feather, reducing the autorotation and associated drag. This is the first line of automatic protection.</p></div></div></div>'],
        ['type'=>'green','head'=>'✅ Autofeather System','html'=>'<p style="font-size:14px;color:#86efac;line-height:1.7;margin:0;">The autofeather system provides a second, more definitive automatic feathering action. If engine torque drops below a set threshold during takeoff (when autofeather is armed), the system automatically commands full feather of the failed engine\'s propeller. This ensures rapid feathering even before the crew can manually respond. Autofeather is typically armed only for takeoff and initial climb — the phase of flight when engine failure is most critical and pilot workload is highest.</p>'],
        ['type'=>'red','head'=>'⚠️ NTS FAIL — Exam Trap','html'=>'<p style="font-size:14px;color:#fca5a5;line-height:1.7;margin:0;">If NTS fails, the automatic first line of protection against propeller windmilling is lost. A pilot who does not understand NTS might not appreciate why an "NTS FAIL" EICAS caution is significant. With NTS inoperative: if an engine fails, the propeller will windmill freely with maximum drag until the crew manually feathers. This significantly degrades single-engine performance and increases pilot workload during the most critical phase. NTS FAIL is a dispatch limitation — check MEL.</p>']
      ],
      'quiz'=>['q'=>'What is "negative torque" in the context of propeller operation?','options'=>['Torque applied in the reverse direction during reverse thrust operation','The condition where the propeller drives the engine rather than the engine driving the propeller — typically indicates engine failure','Reduced torque output during descent when engines are at low power','A propeller control system fault causing torque reversal'],'correct'=>1,'explanation'=>'Normal operation: the engine produces torque that drives the propeller (positive torque — engine drives propeller). When an engine fails, airflow through the propeller causes it to autorotate — the propeller then drives the failed engine (turning compressor stages, etc.). This is negative torque: the propeller is driving the engine. NTS detects this reversal and moves blade pitch toward feather to reduce the autorotation and its associated drag.']
    ],
    [
      'badge'=>'Abnormals','title'=>'Propeller Abnormal Procedures','navTitle'=>'Abnormals',
      'subtitle'=>'Propeller overspeed, NTS failure, and asymmetric conditions',
      'time'=>'10 min',
      'failures'=>[
        ['sev'=>'high','name'=>'PROPELLER OVERSPEED','eicas'=>'PROP OVERSPEED 1(2)','what'=>'Propeller RPM has exceeded the maximum certified limit. Most likely cause: propeller governor failure (unable to control pitch to maintain RPM).','auto'=>'EICAS warning. No automatic recovery — crew must respond immediately.','pilot'=>'1. Power lever — REDUCE immediately (reduces engine torque). 2. Condition lever — reduce (toward feather if RPM not controlled). 3. If RPM uncontrollable — FEATHER. 4. Land as soon as practical. 5. Do not overspeed propeller — structural failure risk.','note'=>'Propeller overspeed is serious. If propeller RPM is not controlled, centrifugal forces can cause blade failure — a catastrophic event. Power reduction is the first and most effective response. If the governor is failed, feathering may be the only option.','noteType'=>'red','noteHead'=>'🚨 Structural Risk'],
        ['sev'=>'med','name'=>'NTS FAILURE','eicas'=>'NTS FAIL 1(2)','what'=>'The Negative Torque Sensing system has failed. Automatic protection against propeller windmilling is lost.','auto'=>'EICAS caution only. No immediate effect on flight unless an engine then fails.','pilot'=>'1. Acknowledge caution. 2. Increase awareness — if engine fails, manual feathering is now required immediately. 3. Check MEL for dispatch status. 4. Brief crew on increased manual feathering workload if engine failure occurs.','note'=>'NTS fail alone is not an emergency — it is a loss of automatic protection. Your response is awareness and preparedness, not immediate action.','noteType'=>'amber','noteHead'=>'⚠️ Loss of Automation'],
      ]
    ],
    [
      'badge'=>'Limits','title'=>'Propeller Limitations','navTitle'=>'Limitations',
      'time'=>'6 min',
      'cards'=>[
        ['type'=>'amber','head'=>'📐 Propeller Limitations','table'=>['headers'=>['Parameter','Value'],
          'rows'=>[['Propeller type','Dowty R408 six-blade variable pitch'],
                   ['Maximum propeller RPM','~1,020 RPM (100% N2)'],
                   ['Minimum RPM for feather command','Governor controls automatically'],
                   ['Beta range','Ground use only (weight-on-wheels protection)'],
                   ['Reverse thrust','Ground use only — not for airborne deceleration'],
                   ['Feather angle','Approximately 88° blade angle']]]
        ]
      ],
      'quiz'=>['q'=>'The autofeather system is armed for which phases of flight?','options'=>['All phases of flight — always armed when engines are running','Takeoff and initial climb only — the most critical phase for engine failure','Approach and landing only — when overspeed risk is highest','Only in instrument meteorological conditions (IMC)'],'correct'=>1,'explanation'=>'Autofeather is armed specifically for takeoff and initial climb — the phase where: (1) an engine failure is most critical, (2) pilot workload is highest, (3) altitude is lowest leaving minimum time to respond, and (4) single-engine performance margin is most critical. In cruise, the altitude buffer and lower critical speed make manual feathering workload more manageable, so autofeather is not needed.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'ENGINE FAILURE — Propeller Feathering Memory Items','eicasMsg'=>'ENG FAIL / PROP WINDMILLING','steps'=>['1. Rudder — APPLY toward live engine','2. Power lever (failed engine) — DISC','3. Condition lever (failed engine) — FEATHER','4. Verify: propeller RPM reducing / feather indication','5. Engine failure checklist — CARRY OUT'],'why'=>'Rapid feathering of the failed engine\'s propeller is critical to reduce drag and restore single-engine climb performance. Every second of windmilling propeller drag reduces climb gradient and increases single-engine distance to terrain.'],
    ['type'=>'abnormal','title'=>'PROPELLER OVERSPEED — Abnormal Checklist','eicasMsg'=>'PROP OVERSPEED','steps'=>['1. Power lever — REDUCE (first response)','2. Condition lever — adjust toward feather if RPM not controlled','3. If RPM stabilises — continue with caution, land as soon as practical','4. If RPM uncontrollable — CONDITION LEVER to FEATHER','5. Land at nearest suitable aerodrome','6. Engineering inspection required before further flight'],'why'=>'Propeller overspeed risk: structural failure of blades due to centrifugal forces. A propeller blade leaving the disc is catastrophic — loss of control and potential fuselage penetration. Power reduction is the first line of control.'],
    ['type'=>'limit','title'=>'Propeller Limitations','items'=>['Max propeller RPM: 100% N2 (~1,020 RPM)','Beta range: ground only — weight-on-wheels protection prevents inflight selection','Reverse pitch: ground only — not for airborne braking','Feather: required within 10 seconds of engine failure for single-engine performance (autofeather achieves this automatically)','Propeller shaft: do not exceed max torque — risk of gearbox damage']],
  ],
  'quiz' => [
    ['q'=>'What is the purpose of variable pitch on the Q400 propeller?','options'=>['To allow the propeller to spin in reverse for counter-rotation if needed','To match propeller efficiency to different airspeeds and power demands, and to allow feathering on engine failure','To reduce noise by adjusting blade angle in densely populated areas','To compensate for wind and turbulence automatically for passenger comfort'],'correct'=>1,'explanation'=>'Variable pitch allows the propeller to be optimised for each phase of flight: fine pitch for takeoff (maximum thrust per RPM), coarser pitch for cruise efficiency (maintaining power at lower, quieter RPM), beta for ground manoeuvring, reverse for landing deceleration, and feather for minimising drag on a failed engine. Fixed-pitch propellers compromise — they can only be optimised for one speed.'],
    ['q'=>'A propeller is "feathered." What does this mean?','options'=>['The propeller is spinning at maximum RPM for takeoff','The propeller blades are rotated to approximately 88° — edge-on to the airflow — to minimise drag','The propeller is in reverse pitch for maximum braking','The propeller is in the beta range for ground manoeuvring'],'correct'=>1,'explanation'=>'Feathering rotates the propeller blades to approximately 88° blade angle — essentially edge-on to the relative airflow. At this angle, the blades create minimal aerodynamic drag and do not autorotate (windmill). Feathering is used when an engine fails in flight to prevent the failed engine\'s propeller from windmilling and creating excessive drag.'],
    ['q'=>'What does the NTS (Negative Torque Sensing) system detect?','options'=>['Propeller overspeed above the maximum RPM limit','The condition where the propeller is driving the engine rather than vice versa — indicating engine failure','Negative g-forces that could cause propeller cavitation','Incorrect pitch angle selection in the beta range'],'correct'=>1,'explanation'=>'NTS detects when torque reverses — the propeller is being driven by the airflow (windmilling) rather than being driven by the engine. This reversal is the signature of an engine failure. NTS automatically moves the pitch toward feather when this condition is detected, reducing windmilling drag. It is the first automatic protection system in the failure sequence.'],
    ['q'=>'Why is reverse thrust restricted to ground use only?','options'=>['Reverse thrust causes engine overtemperature in flight due to recirculated hot exhaust','At airborne speeds, reverse thrust creates aerodynamic instability and may cause loss of control; also increases FOD ingestion risk','Reverse thrust in flight would shut down the engine automatically via FADEC','The hydraulic pitch-change system cannot move the blades to negative angle at flight speeds'],'correct'=>1,'explanation'=>'Using reverse pitch in flight creates unpredictable aerodynamic effects and the risk of loss of control. At altitude, the reversed propeller creates thrust reversals and potential asymmetric loads. Additionally, ingestion of foreign objects from the ground (FOD) that is stirred up by reverse thrust is a risk at low speeds on the ground — at flight speeds, this risk is different but control risk is primary.'],
    ['q'=>'Engine 1 fails on takeoff. What sequence of events occurs automatically if the autofeather system is armed?','options'=>['Nothing automatic — the crew must manually feather immediately','NTS activates first (moves pitch toward feather), then autofeather commands full feather if torque drops below threshold — all within seconds of failure','Autofeather activates first, then NTS as a backup verification system','FADEC automatically shuts down the engine and feathers the propeller simultaneously'],'correct'=>1,'explanation'=>'The sequence: (1) Engine fails → torque drops → NTS detects negative torque → begins moving pitch toward feather. (2) Simultaneously, torque drops below autofeather threshold → autofeather commands full feather override. Both systems work together. NTS provides the initial drag reduction; autofeather ensures complete and definitive feathering. The crew\'s role is to verify feathering, apply rudder, and execute the engine failure checklist.'],
    ['q'=>'What is the immediate risk of propeller overspeed?','options'=>['Engine flameout due to aerodynamic disruption at the compressor inlet','Structural failure of propeller blades from centrifugal forces — catastrophic loss of blades possible','Gearbox overload causing immediate engine separation','Autopilot disconnect due to propeller vibration'],'correct'=>1,'explanation'=>'Propeller RPM above the certified limit subjects the blades to centrifugal forces exceeding their structural design limits. Carbon/glass composite propeller blades can fail catastrophically — a released blade has enormous kinetic energy and can penetrate the fuselage or engine nacelle. This is why propeller overspeed is a red-level emergency requiring immediate power reduction.'],
    ['q'=>'The beta range is described as "ground use only." What system enforces this restriction?','options'=>['Crew training and discipline — there is no physical protection','Weight-on-wheels sensors prevent beta range selection when the aircraft is airborne','FADEC locks out beta range at speeds above 30 knots','The propeller governor physically cannot achieve beta range pitch angles in flight'],'correct'=>1,'explanation'=>'Weight-on-wheels (WOW) sensors detect when the aircraft is on the ground. When WOW indicates airborne, the propeller control system prevents the power levers from moving into the beta range position. This is a physical protection against inadvertent beta selection in flight — which would instantly create enormous drag and potentially unrecoverable loss of control.']
  ]
]; }

// ── REMAINING SYSTEMS ─────────────────────────────────────────────────
// Content for ATA27, ATA32, ATA21, ATA36, ATA30, ATA26, ATA22, ATA34, ATA23, ATA31, ATA35, ATA33, FMS, CW, QRH
// ── ATA27 FLIGHT CONTROLS

function ata27_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Mechanical control, hydraulic power, and pilot authority on the Q400',
      'time'=>'8 min','objective'=>'After this chapter you will understand the Q400 control philosophy: mechanical linkage with hydraulic actuation, no fly-by-wire, and multiple layers of protection.',
      'analogy'=>['label'=>'The Analogy — Muscle-Assisted Steering','text'=>'The Q400 control system is like a car with power steering: the steering wheel is mechanically connected to the front wheels (you have direct control), but hydraulic pressure assists your inputs so you don\'t need superhuman strength. If hydraulics fail, you still have control—just heavier. This is fundamentally different from a fly-by-wire system where a computer interprets your inputs.'],
      'body'=>'<p>The Bombardier Q400 uses a <strong>mechanically-linked control system</strong> with hydraulic actuation. This means every control input you make travels directly from the control column to the control surfaces via cables, pushrods, and bellcranks—just like aircraft from the 1960s. However, the <strong>actuators that move these surfaces are powered by hydraulics</strong> (No.1 and No.2 systems), making control inputs light and responsive.</p><p>This architecture gives you several advantages: <strong>absolute pilot authority</strong> (the hydraulic power cannot override your inputs), <strong>redundancy</strong> (two hydraulic systems for primary controls), and <strong>graceful degradation</strong> (loss of hydraulics makes controls heavy but still fully controllable). The Q400 is not a fly-by-wire aircraft—your hands are always in the loop, directly commanding the flight path.</p><p>The primary controls—ailerons, elevators, and rudder—are backed by two hydraulic systems. Secondary controls like flaps and spoilers add operational capability. Trim tabs (electric) relieve control forces. Stall protection systems (stick shaker and pusher) prevent the most dangerous failure mode: an aerodynamic stall.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🔧 Control System Architecture','list'=>['Cables and pushrods mechanically link controls to surfaces','Hydraulic actuators powered by No.1 and No.2 systems','Electric trim tabs on elevator, rudder, aileron (three-axis)','Fully reversionary—heavy but controllable if hydraulics fail','No computer flight control (no FBW)']],
        ['type'=>'amber','head'=>'⭐ 5 Things You Must Know','table'=>['headers'=>['Item','Fact','Why It Matters'],
          'rows'=>[['Control Type','Mechanical linkage + hydraulic power','You have direct command authority; hydraulics assist'],
                   ['Primary Controls','Ailerons, Elevators, Rudder','Lateral, pitch, and yaw control'],
                   ['Hydraulic Backup','No.1 and No.2 systems','Two independent power sources—if one fails, you still have power'],
                   ['Manual Reversion','Heavy but fully controllable','Loss of all hydraulics is survivable'],
                   ['Trim System','Electric; three-axis tabs','Relieves control forces; aileron trim is automatic on flap extension']]]],
      ],
      'quiz'=>['q'=>'The Q400 flight control system is based on which architecture?','options'=>['A) Fly-by-wire with mechanical backup','B) Mechanical linkage with hydraulic actuation','C) Full hydraulic with no mechanical link','D) Electric servo controls only'],'correct'=>1,'explanation'=>'Correct: B) Mechanical linkage with hydraulic actuation. Your inputs travel directly to the surfaces via cables and pushrods. Hydraulic power makes the controls light and responsive. This is NOT fly-by-wire; you have direct authority.']
    ],
    [
      'badge'=>'System Architecture','title'=>'Primary Controls','navTitle'=>'Primary Controls',
      'subtitle'=>'How ailerons, elevators, and rudder are powered and controlled',
      'time'=>'12 min','objective'=>'Understand the mechanical and hydraulic routing for the three primary control axes and the critical role of hydraulic pressure.',
      'body'=>'<p>The <strong>primary flight controls</strong>—ailerons (roll), elevators (pitch), and rudder (yaw)—are powered by dual hydraulic systems. When you move the control column, cables transmit your input to the autopilot servos (if engaged) and to hydraulic actuators on each surface. The dual-system architecture means that <strong>loss of No.1 hydraulics does not disable ailerons or elevators</strong>; No.2 system takes over. Similarly, loss of No.2 still leaves you with No.1 for all primary controls.</p><p><strong>Aileron control</strong> is particularly interesting: the ailerons are not permanently fixed to the wing. At zero flaps, they operate symmetrically for roll control. However, when you extend flaps for landing, the <strong>ailerons automatically droop</strong> (move downward) to increase wing camber and improve low-speed lift. This drooping is achieved through a flap-interconnect mechanical linkage. Once drooped, your roll inputs still control aileron deflection, but from a drooped baseline.</p><p>The <strong>elevator and rudder</strong> function normally in all configurations. The elevator (paired surfaces on the horizontal stabilizer) controls pitch; the rudder controls yaw. Both are hydraulically powered and mechanically signaled. If hydraulic pressure is lost, manual reversion occurs immediately—the pilot is now fighting the aerodynamic loads directly, making control forces very heavy (especially at high airspeed), but the aircraft remains fully controllable.</p>',
      'components'=>[
        ['icon'=>'🔀','name'=>'Aileron System','role'=>'Roll (lateral) control via cable from control column','spec'=>'Dual hydraulic powered, automatic droop on flap extension','detail'=>'Ailerons are inboard and outboard sections on each wing. Hydraulic power steering units receive inputs from the control column via cables and bellcranks. Below ~7 knots on ground, aileron power is inhibited to prevent hard-over during low-speed taxi.'],
        ['icon'=>'↕️','name'=>'Elevator System','role'=>'Pitch (up/down) control; dual independent actuators','spec'=>'No.1 and No.2 systems; No.2 is primary source','detail'=>'The control column (or autopilot servo) sends mechanical signals to each elevator via cable. Dual actuators provide redundancy; if one system fails, the other maintains pitch control. Elevator trim tab (electric) relieves column forces.'],
        ['icon'=>'↔️','name'=>'Rudder System','role'=>'Yaw (directional) control; coordinated flight and crosswind capability','spec'=>'Dual hydraulic with mechanical interconnect','detail'=>'Rudder pedal inputs (via cable) command a hydraulic actuator on the vertical stabilizer. Rudder trim tab (electric) relieves pedal forces. The differential braking system (tiller and rudder pedals) works together for tight ground maneuvers.'],
        ['icon'=>'⚙️','name'=>'Hydraulic Actuators','role'=>'Convert hydraulic pressure into mechanical movement of control surfaces','spec'=>'Double-acting cylinders; return springs restore to neutral on pressure loss','detail'=>'Each control surface has one or more actuators (ailerons have dual, elevator and rudder typically dual/triple). Loss of hydraulic pressure returns all surfaces to neutral via springs, a safe degraded mode.'],
      ],
      'cards'=>[
        ['type'=>'blue','head'=>'Primary Control Routing','html'=>'<table style="width:100%;border-collapse:collapse;"><tr style="background:#f0f0f0;"><th style="border:1px solid #ccc;padding:8px;">Control</th><th style="border:1px solid #ccc;padding:8px;">Axis</th><th style="border:1px solid #ccc;padding:8px;">Input Device</th><th style="border:1px solid #ccc;padding:8px;">Power Source</th></tr><tr><td style="border:1px solid #ccc;padding:8px;">Aileron</td><td style="border:1px solid #ccc;padding:8px;">Roll</td><td style="border:1px solid #ccc;padding:8px;">Control Column knob (left/right)</td><td style="border:1px solid #ccc;padding:8px;">No.1 & No.2 Hydraulics</td></tr><tr style="background:#f9f9f9;"><td style="border:1px solid #ccc;padding:8px;">Elevator</td><td style="border:1px solid #ccc;padding:8px;">Pitch</td><td style="border:1px solid #ccc;padding:8px;">Control Column (push/pull)</td><td style="border:1px solid #ccc;padding:8px;">No.1 & No.2 Hydraulics</td></tr><tr><td style="border:1px solid #ccc;padding:8px;">Rudder</td><td style="border:1px solid #ccc;padding:8px;">Yaw</td><td style="border:1px solid #ccc;padding:8px;">Rudder Pedals</td><td style="border:1px solid #ccc;padding:8px;">No.1 & No.2 Hydraulics</td></tr></table>'],
        ['type'=>'amber','head'=>'⚠️ Aileron Droop on Flap Extension','list'=>['Ailerons automatically move downward when flaps extend','Improves low-speed lift and wing camber','Flap interconnect is mechanical—not dependent on hydraulics','Roll control still works normally from the drooped position','This is why aileron effectiveness changes subtly as you extend flaps']],
      ],
      'failures'=>[
        ['sev'=>'high','name'=>'Loss of No.1 Hydraulic System','eicas'=>'HYD NO.1 LOW/FAIL','what'=>'No.1 hydraulic pressure drops to zero or becomes unstable','auto'=>'Actuators powered by No.1 revert to No.2 hydraulic power','pilot'=>'Verify HYD NO.1 system failure via EICAS and instruments. Ailerons, elevators, rudder remain powered (No.2 backup). Flying characteristics unchanged. Continue to destination; land as soon as practical.','note'=>'Loss of one hydraulic system is not an emergency on the Q400—you have dual power for all primary controls.','noteType'=>'blue','noteHead'=>'Redundancy at Work'],
        ['sev'=>'high','name'=>'Loss of Both Hydraulic Systems','eicas'=>'HYD NO.1 FAIL + HYD NO.2 FAIL','what'=>'Both No.1 and No.2 systems lose pressure simultaneously (rare)','auto'=>'All hydraulic actuators immediately return to neutral via internal springs; control surfaces center','pilot'=>'Declare emergency. Aircraft is now in manual reversion mode: all controls (ailerons, elevators, rudder) are heavy and require large inputs. Flight is possible but demanding. Reduce speed to lighten control forces. Avoid aggressive maneuvering. Land at the nearest airport—use maximum flap (improves lift at low speed).','note'=>'Manual reversion is survivable. Control authority is maintained; forces are high but manageable.','noteType'=>'amber','noteHead'=>'Heavy but Controllable'],
      ],
      'quiz'=>['q'=>'When you lose No.1 hydraulic system, what happens to the primary controls?','options'=>['A) Elevators and rudder fail; only ailerons remain powered','B) All primary controls switch to No.2 hydraulic system','C) Aircraft enters manual reversion immediately','D) Only the elevator trim tab is powered'],'correct'=>1,'explanation'=>'Correct: B) All primary controls switch to No.2 hydraulic system. The Q400 is designed so that loss of either No.1 or No.2 does not degrade your ability to command ailerons, elevators, or rudder. The active system backs up the failed system automatically.']
    ],
    [
      'badge'=>'Secondary Control','title'=>'Flaps and Spoilers','navTitle'=>'Flaps & Spoilers',
      'subtitle'=>'Fowler flaps, speed brake, and how they change aircraft performance',
      'time'=>'11 min','objective'=>'Learn flap extension logic, speed limitations, spoiler operation, and the interaction between flap position and aileron droop.',
      'body'=>'<p><strong>Flaps on the Q400 are Fowler-type</strong>, meaning they slide backward and downward as they extend, changing both wing area and camber. The flap positions are <strong>0° (retracted), 5°, 10°, 15°, and 35°</strong>. Flap selection is via a continuous lever with detents; the flap system extends or retracts at a constant rate (~2.5° per second), so transitioning from 0° to 35° takes about 14 seconds.</p><p><strong>Flap speed limitations</strong> are critical: you may not extend flaps beyond 5° above 200 KIAS, nor beyond 10° above 180 KIAS, nor land configuration (35°) above 150 KIAS. These limits exist because flap extension increases drag sharply and changes the wing\'s aerodynamic characteristics. Below these speeds, flap extension does not cause excessive trim changes or control force upset. The flap actuators are hydraulically powered (No.3 system); if No.3 pressure is lost, flaps remain frozen at their current position—an in-flight flap failure is not catastrophic, but descent and landing are affected.</p><p><strong>Spoilers (speed brakes)</strong> are mounted on the upper wing surface. In flight, spoiler deployment increases drag and lowers the nose slightly. Spoiler selection is ON/OFF via a dedicated switch; extending spoilers at cruise effectively converts excess airspeed into descent rate. On touchdown, spoilers automatically deploy (squat switch activation) to reduce landing distance and prevent floating. If spoilers fail to auto-deploy, manual selection ensures they open. The spoiler actuators are powered by No.1 hydraulic system.</p>',
      'components'=>[
        ['icon'=>'📌','name'=>'Fowler Flap System','role'=>'Increase lift at lower speeds; adjust landing distance','spec'=>'Five positions: 0°, 5°, 10°, 15°, 35°; extension rate ~2.5°/sec','detail'=>'Flaps slide aft and down, increasing effective wing area and camber. Hydraulically actuated (No.3 system). Electrically signaled via flap lever. Automatic slat extension accompanies flap extension (leading edge devices for low-speed lift). Aileron droop couples to flap position (automatic via mechanical linkage).'],
        ['icon'=>'🛑','name'=>'Spoiler (Speed Brake)','role'=>'Increase drag, steepen descent, reduce landing distance','spec'=>'Binary: deployed or stowed; activated by switch or squat switch','detail'=>'Upper wing spoilers extend via No.1 hydraulic actuators. In-flight activation is manual (switch). On ground, squat switch (weight on wheels) triggers automatic deployment on landing. Spoiler deployment increases drag significantly—useful tool for high-altitude descent.'],
        ['icon'=>'🔄','name'=>'Flap-Aileron Interconnect','role'=>'Automatically droops ailerons as flaps extend to maintain roll effectiveness','spec'=>'Mechanical linkage; no hydraulic or electrical dependency','detail'=>'At zero flaps, ailerons are neutral. As flaps extend, a cam-follower linkage gradually droops the ailerons (up to ~7° down at flap 35°). This maintains adequate lateral control at high angles of attack and low speeds. Interconnect is purely mechanical and always operates when flaps extend.'],
      ],
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ Flap Speed Limits — YOU MUST MEMORIZE','table'=>['headers'=>['Flap Position','Max Speed (KIAS)','Configuration'],
          'rows'=>[['0° (Retracted)','No limit','Cruise'],
                   ['5°','200','Initial descent or takeoff config','<span class="spec-badge must">MUST KNOW</span>'],
                   ['10°','180','Descent with flap','<span class="spec-badge must">MUST KNOW</span>'],
                   ['15°','170','Approach'],
                   ['35° (Landing)','150','Landing final approach','<span class="spec-badge must">MUST KNOW</span>']]]],
        ['type'=>'blue','head'=>'Spoiler Operation','list'=>['Manual mode (in-flight): switch extends spoilers for descent/speed reduction','Auto mode (landing): squat switch triggers deployment on touchdown','Increases drag significantly—use for high-altitude cruise descents','If auto-deploy fails, manual operation ensures spoiler opening','Stowed position reduces drag during climb and cruise']],
        ['type'=>'green','head'=>'✓ Normal Flap Extension Procedure','steps'=>[
          ['title'=>'Select flap position','desc'=>'Move flap lever to desired position; system extends/retracts at constant rate'],
          ['title'=>'Monitor airspeed','desc'=>'Ensure current airspeed is below limit for the new position'],
          ['title'=>'Check trim','desc'=>'Flap extension may require pitch trim adjustment; anticipate and correct'],
          ['title'=>'Verify position','desc'=>'Flap position indicator (electrical) confirms extension; match lever position']
        ]],
      ],
      'quiz'=>['q'=>'You are cruising at 180 KIAS. What is the maximum flap position you may select?','options'=>['A) 0° (flaps up)','B) 5°','C) 10°','D) 15°'],'correct'=>2,'explanation'=>'Correct: C) 10°. At 180 KIAS, you may extend flaps to 10° maximum. If you try to extend beyond 10° (say, 15° or 35°), the system will not extend; the lever will not move to the detent, preventing an unsafe flap extension. Always respect speed limits by using the lever detents.']
    ],
    [
      'badge'=>'Protection','title'=>'Stall Protection Systems','navTitle'=>'Stall Protection',
      'subtitle'=>'How the Q400 detects and prevents aerodynamic stall',
      'time'=>'10 min','objective'=>'Understand the angle-of-attack (AOA) sensor, stick shaker, stick pusher, and the alerting sequence for stall protection.',
      'body'=>'<p>The Q400 is equipped with <strong>active stall protection</strong>: an angle-of-attack sensor feeds data to a stall warning and protection system. The system has two tiers: <strong>stick shaker</strong> (warning), then <strong>stick pusher</strong> (protection). The AOA sensor measures the angle between the oncoming airflow and the aircraft fuselage reference line. At approximately 1.05 times stall speed (Vs), the <strong>stick shaker activates</strong>, vibrating the control column to alert you: "You are approaching stall." This is a warning, not protection—you must respond by reducing angle of attack.</p><p>If you ignore the stick shaker and continue to increase angle of attack, at approximately 1.02 times stall speed, the <strong>stick pusher activates automatically</strong>, applying a nose-down pitch input to the elevator. The stick pusher is a one-time, automatic protective action: it forcefully pushes the control column forward, reducing pitch and breaking the stall. The stick pusher is powered by a small pneumatic bottle (compressed air) stored on the aircraft; it is not dependent on hydraulic power, ensuring protection even in dual-hydraulic failure. Once activated, the stick pusher must be reset on the ground before the next flight.</p><p>The stall protection system is designed to be <strong>non-defeatable</strong> (no "disable" switch), ensuring that even a distracted or incapacitated pilot cannot blunder into a stall. However, the system assumes the airspeed indicator and AOA sensor are functioning correctly. In the extremely rare case of instrument failure (false high AOA reading), the system might activate inappropriately, but this is preferable to an unprotected stall.</p>',
      'components'=>[
        ['icon'=>'📡','name'=>'Angle-of-Attack Sensor','role'=>'Measures aircraft pitch relative to relative wind; input to stall warning system','spec'=>'Mounted on fuselage nose; continuous analog signal to warning computer','detail'=>'The AOA sensor is a vane that aligns with the relative wind. As pitch increases and airflow becomes more perpendicular to the fuselage, AOA increases. Sensor data is processed and compared to speed-dependent stall threshold. Output drives stick shaker relay and stick pusher logic.'],
        ['icon'=>'📢','name'=>'Stick Shaker','role'=>'Stall warning: vibrates control column when AOA approaches stall','spec'=>'Activates at ~1.05 Vs (stall speed); dual motors in control column','detail'=>'Two electric motors mounted in the control yoke create high-frequency vibration. Vibration is unmistakable and immediately recognizable to pilots as a stall warning. Shaker motor can be tested on the ground (test switch on stall warning panel).'],
        ['icon'=>'✈️','name'=>'Stick Pusher','role'=>'Stall protection: automatic nose-down pitch to prevent stall','spec'=>'Activates at ~1.02 Vs; pneumatic actuator powered by pressurized air bottle','detail'=>'Stick pusher is a one-shot protection: when triggered by AOA computer, a solenoid valve releases compressed air to a piston-actuated rod, pushing the control column forward with ~200 lb force. Activation is intentionally harsh (immediate, strong) to break the stall. After activation, the system remains "armed" and can fire again if AOA increases again.'],
        ['icon'=>'🔋','name'=>'Pneumatic Bottle (Air Supply)','role'=>'Power source for stick pusher pneumatic actuator','spec'=>'200 PSI pressurized air; manually regulated and isolated','detail'=>'A small compressed air bottle maintains ~200 PSI to supply the stick pusher actuator. Ground crew must ensure bottle pressure is adequate before flight. Bottle pressure is indicated on the flight deck. Loss of bottle pressure disables stick pusher protection.'],
      ],
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Stall Warning Sequence','steps'=>[
          ['title'=>'1.05 Vs: Stick Shaker Activates','desc'=>'Control column vibrates loudly. You may still recover by reducing pitch and adding power. Immediate action required.','state'=>'warning','stateLabel'=>'WARNING'],
          ['title'=>'Pilot Action: Reduce Angle of Attack','desc'=>'Pitch forward (reduce pitch attitude), add power, establish descent. Get airspeed up.','state'=>'action','stateLabel'=>'YOUR ACTION'],
          ['title'=>'1.02 Vs: Stick Pusher Activates (if ignored)','desc'=>'Automatic nose-down pitch input (~200 lb force on column). Aircraft is now forced to reduce AOA and stall is prevented.','state'=>'protection','stateLabel'=>'AUTO PROTECTION'],
          ['title'=>'Post-Event: Reset Required','desc'=>'After stick pusher activation, system is reset and armed again. On ground, maintenance performs full system check before next flight.','state'=>'state','stateLabel'=>'GROUND ACTION']
        ]],
        ['type'=>'amber','head'=>'⚠️ What Activates Stall Warning?','list'=>['High angle of attack in any flight regime (approach, flare, slow flight)','Sudden pitch increase (e.g., abrupt control input)','Loss of airspeed (e.g., engine failure; shaker warns before stall develops)','Turn with low airspeed (aerodynamic stall in a climb turn)','Shaker and pusher are equally effective in level or turning flight']],
        ['type'=>'blue','head'=>'Stick Pusher Safety Features','list'=>['Powered by pressurized air bottle—NOT dependent on hydraulics','One-time automatic protection (non-defeatable by pilot)','Activation is harsh and immediate (ensures stall is broken)','System resets on ground; technician confirms function before next flight','Test switch allows functional check without actual stall risk']],
      ],
      'failures'=>[
        ['sev'=>'high','name'=>'Loss of Pneumatic Pressure (Stick Pusher Battery)','eicas'=>'STALL WARNING FAIL / PNEUMATIC PRESS LOW','what'=>'Compressed air bottle loses pressure; stick pusher is no longer operable','auto'=>'EICAS alert shows STALL WARNING FAIL. Stick shaker still functions (electric).','pilot'=>'Declare an issue to ATC. Stick pusher protection is lost, but you still have stick shaker warning. Avoid slow speed operations and never let shaker activate in-flight—the automated push will not occur. Be vigilant on approach and flare. Consider diverting to nearest airport if immediate landing not feasible. Land normally; avoid approach stalls or deep flares.','note'=>'Loss of stick pusher is not an immediate emergency, but it removes a critical safety layer. Treat the aircraft with respect and avoid the stall warning entirely.','noteType'=>'amber','noteHead'=>'Pilot Discipline Critical'],
        ['sev'=>'med','name'=>'False Stall Warning (AOA Sensor Failure)','eicas'=>'STALL WARNING ACTIVE (continuous)','what'=>'AOA sensor reads high (false); shaker activates continuously or stick pusher fires unexpectedly','auto'=>'System treats false high AOA as genuine high-AOA condition; shaker/pusher activate normally','pilot'=>'If shaker is continuous or pusher fires, it is likely a sensor failure. Observe airspeed indicator (which should show safe speed). If airspeed is adequate (e.g., 150+ knots), stall warning is false. Silence shaker by disconnecting stall warning system (circuit breaker on stall warning panel). Land at nearest airport. Do not attempt high-altitude cruise; descend to lower altitude where false warning is less likely to interfere.','note'=>'This is rare but has happened. Trusting your airspeed is key.','noteType'=>'blue','noteHead'=>'Verify with Airspeed'],
      ],
      'quiz'=>['q'=>'You are on approach at 90 KIAS (below stall speed for your weight and configuration). The stick shaker activates. What is your immediate action?','options'=>['A) Reduce pitch attitude and add power to increase airspeed','B) Wait for stick pusher to fire','C) Retract flaps immediately','D) Bank the aircraft to dump altitude quickly'],'correct'=>0,'explanation'=>'Correct: A) Reduce pitch attitude and add power to increase airspeed. The stick shaker is a warning that your AOA is too high. Pitch forward, add power (if available), and get airspeed up. The stick pusher is automatic protection only if you ignore the shaker and continue nose-up. Retracting flaps or banking will worsen the stall risk. Act on the warning immediately.']
    ],
    [
      'badge'=>'Abnormals','title'=>'Failures and Limitations','navTitle'=>'Failures & Limits',
      'subtitle'=>'What to do when flight controls malfunction and critical operational limits',
      'time'=>'9 min','objective'=>'Recognize control system failures, execute appropriate responses, and respect published limitations.',
      'body'=>'<p><strong>Flight control failures are rare</strong> on the Q400 because the system is mechanically simple and backed by redundant hydraulic power. However, several failure modes are possible: jam (cable/pushrod interference), loss of hydraulic power, control feel-different symptoms, or actuator malfunction. A jammed control is the most serious: if a cable breaks or a bellcrank seizes, that control axis may become unavailable, requiring you to adapt your flight technique to use other axes and bank angle to manage pitch.</p><p><strong>Control feel degradation</strong> is less obvious but important. If you notice that aileron response is mushy, elevator forces are extremely light, or rudder is unresponsive, land immediately. These symptoms may indicate hydraulic pressure loss, actuator failure, or cable rigging issues. Do not continue to climb or head for a distant airport; these are signs of structural integrity concern. Similarly, if a control surface appears to oscillate or flutter, reduce speed immediately and land.</p><p><strong>Operational limitations</strong> on the Q400 flight control system are published for a reason: they protect you from overstressing components and from flying into unsafe aerodynamic regimes. Exceeding the maximum operating speed (Vmo) stresses the entire airframe, including control surfaces and actuators. Exceeding flap extension speed limits risks actuator cavitation and control surface flutter. Attempting to land with a failed landing gear (and no alternate extension method) requires unusual control input to avoid a wing-strike—the Q400 can land on a single main gear, but this is an emergency technique.</p>',
      'components'=>[
        ['icon'=>'⚠️','name'=>'Control Jam (Cable/Pushrod)','role'=>'Single control axis becomes inoperable','spec'=>'Typically affects one axis (aileron, elevator, or rudder)','detail'=>'Cable breaks, bellcrank seizes, or interference jamming the mechanical linkage. Pilot feels solid resistance when attempting to move that control. Aircraft remains controllable using other axes (e.g., if elevator jams, you can pitch with ailerons and bank; if ailerons jam, use rudder for slow-speed roll control).'],
        ['icon'=>'🔌','name'=>'Hydraulic Power Loss','role'=>'Actuators lose pressure; manual reversion begins','spec'=>'Loss of No.1, No.2, or both systems','detail'=>'Actuators return to neutral via springs; control forces become very heavy. Aircraft is controllable but requires large inputs. Reduce airspeed to lighten control forces. Avoid aggressive maneuvers. Land at nearest airport.'],
        ['icon'=>'📊','name'=>'Flap Malfunction','role'=>'Flaps stuck or refuse to extend/retract','spec'=>'Hydraulic failure or electrical signal loss','detail'=>'If flaps freeze at 0°, approach and landing are normal (just longer landing distance). If flaps freeze at an intermediate position (e.g., 15°), landing is possible but approach performance changes (slower landing speed is available due to flap). If flaps freeze at 35°, normal cruise is not possible; you must descend.'],
      ],
      'cards'=>[
        ['type'=>'amber','head'=>'Flap Extension Speed Limits — NO EXCEPTIONS','table'=>['headers'=>['Flap Position','Max Airspeed (KIAS)','Consequence of Overspeed'],
          'rows'=>[['0°','No limit','—'],
                   ['5°','200','Actuator cavitation; potential control feel loss'],
                   ['10°','180','Flutter risk; structural stress'],
                   ['15°','170','Extreme buffeting; possible damage'],
                   ['35°','150','Loss of airspeed control; stall risk']]]],
        ['type'=>'red','head'=>'🚨 Flight Control Failure Recognition','list'=>['Stuck control: control column/pedal will not move; solid resistance','Feel degradation: control is mushy, spongy, or unresponsive','Asymmetric control: one aileron moves but not the other','Oscillation: control surface visible flutter or rhythmic shaking','Unusual trim requirement: need excessive trim to maintain altitude']],
      ],
      'failures'=>[
        ['sev'=>'high','name'=>'Elevator Control Jam','eicas'=>'CONTROL JAM / ELEVATOR INOP','what'=>'Elevator is mechanically jammed and cannot move','auto'=>'No automatic recovery; control column is locked or requires extreme force','pilot'=>'Declare emergency. Aircraft is still controllable using ailerons and rudder. Pitch control is possible (bank the aircraft to change pitch angle). This is a serious situation but not unrecoverable. Reduce speed to lighten control forces. Avoid aggressive maneuvers. Descend gradually using shallow banks and spiral descent if needed. Land on the longest available runway with crash fire rescue standing by.','note'=>'A jammed elevator is extremely serious. Expect poor pitch control and high control forces.','noteType'=>'red','noteHead'=>'Declare Emergency'],
        ['sev'=>'high','name'=>'Flap Stuck at Intermediate Position (e.g., 15°)','eicas'=>'FLAP FAIL / FLAP POSITION DISAGREE','what'=>'Flaps extend to 15° and refuse to move further or retract','auto'=>'Flap position indicator shows 15°; lever will not move','pilot'=>'Continue to destination if practical. Approach and landing are possible at 15° flap; touchdown speed is reduced compared to 0° but landing distance is longer than 35°. Descent is normal. You cannot further extend flaps (no 35° available), so plan for longer landing distance. Land on longest available runway. Post-flight maintenance inspection required.','note'=>'Flaps at intermediate position is not an emergency; planning is required.','noteType'=>'blue','noteHead'=>'Plan Longer Runway'],
      ],
      'quiz'=>['q'=>'You are descending through 8,000 ft at 200 KIAS. You select flap 5°. What do you expect to happen?','options'=>['A) Flaps extend to 5° normally; no airspeed issue','B) Flaps cannot extend; airspeed is at the limit for 5° flap','C) Flaps extend; you immediately feel buffeting and control degradation','D) System automatically reduces airspeed to 180 KIAS before extending'],'correct'=>0,'explanation'=>'Correct: A) Flaps extend to 5° normally; no airspeed issue. At 200 KIAS, you are exactly at the maximum speed for 5° flap extension. The system will extend flaps. To extend further flaps (say, 10°), you would need to reduce airspeed to 180 KIAS or below. The 200 KIAS limit is a hard speed limit—exceed it and you risk actuator cavitation or flutter.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'STALL RECOVERY — Memory Items','steps'=>['1. <strong>Pitch nose down</strong>—reduce angle of attack immediately','2. <strong>Add power</strong>—advance throttles to maximum (if available)','3. <strong>Retract flaps</strong>—move flap lever to 0° to reduce lift and lower stall speed','4. <strong>Gear up</strong> (if airborne)—retract landing gear to reduce drag','5. <strong>Check altitude</strong>—ensure sufficient height to recover before terrain'],'why'=>'Stall recovery must be automatic and immediate. Pitch is priority one (nose down breaks the stall). Power adds energy. Flap retraction reduces drag and lowers stall speed. Gear up removes drag interference. These steps are non-negotiable in a stall.'],
    ['type'=>'abnormal','title'=>'FLIGHT CONTROLS JAMMED — Abnormal Checklist','eicasMsg'=>'CONTROL JAM / [AILERON/ELEVATOR/RUDDER] INOP','items'=>['Identify which control is jammed (aileron/elevator/rudder)','Attempt gentle movement of control—do not force','If jammed: declare emergency to ATC immediately','Reduce airspeed to lighten control forces (if applicable)','Use alternate control axes to maintain aircraft attitude (e.g., use ailerons to pitch if elevator is jammed)','Plan landing on longest available runway','Request emergency equipment standing by (crash fire rescue)','Continue to nearest suitable airport; avoid maneuvering'],'why'=>'A jammed control removes one axis of flight control but does not render the aircraft unflyable. Gentle attempt is made to free the jam. Reduced speed makes remaining controls more effective. Landing on a long runway ensures safe touchdown despite reduced control authority.'],
    ['type'=>'abnormal','title'=>'STALL WARNING FAIL — Abnormal Checklist','eicasMsg'=>'STALL WARNING FAIL','items'=>['Verify airspeed indicator is functioning and reads accurately','Check that AOA sensor (vane on nose) is not visibly damaged','Confirm pneumatic pressure is adequate (stick pusher battery)','If false activation: isolate stall warning system (pull circuit breaker)','Avoid slow-speed flight; keep airspeed above stall margin (add 10+ knots)','Do not attempt high-altitude cruise; descend to lower altitude','Plan approach and landing with ample airspeed margin','Land at nearest airport and report to maintenance'],'why'=>'Stall warning system failure removes critical protection. Pilot must manually manage airspeed and angle of attack. Avoiding the stall warning entirely (via airspeed margin) is the safest course.'],
    ['type'=>'limit','title'=>'Flight Control System Limitations','items'=>['<strong>Flap speed limits:</strong> 0°/No limit, 5°/200 KIAS, 10°/180 KIAS, 15°/170 KIAS, 35°/150 KIAS','<strong>Spoiler deployment:</strong> Do not deploy above 200 KIAS; excessive drag','<strong>Aileron effectiveness:</strong> Decreases at high angles of attack (low speed); aileron droop reduces effectiveness below ~80 KIAS','<strong>Manual reversion:</strong> Control forces become very heavy if both hydraulic systems fail; speed reduction is essential','<strong>Trim authority:</strong> Electric trim (aileron, elevator, rudder) has limited authority; do not rely on trim alone to maintain attitude','<strong>Stall protection:</strong> Stick shaker at 1.05 Vs; stick pusher at 1.02 Vs; system is non-defeatable']],
    ['type'=>'eicas','title'=>'Key Flight Control EICAS Messages','html'=>'<table style="width:100%;border-collapse:collapse;"><tr style="background:#f0f0f0;"><th style="border:1px solid #ccc;padding:8px;">EICAS Message</th><th style="border:1px solid #ccc;padding:8px;">Severity</th><th style="border:1px solid #ccc;padding:8px;">Probable Cause</th><th style="border:1px solid #ccc;padding:8px;">Immediate Action</th></tr><tr><td style="border:1px solid #ccc;padding:8px;"><strong>CONTROL JAM / AILERON INOP</strong></td><td style="border:1px solid #ccc;padding:8px;color:red;"><strong>SEVERE</strong></td><td style="border:1px solid #ccc;padding:8px;">Cable/pushrod jam; actuator failure</td><td style="border:1px solid #ccc;padding:8px;">Declare emergency; reduce speed; use other controls</td></tr><tr style="background:#f9f9f9;"><td style="border:1px solid #ccc;padding:8px;"><strong>CONTROL JAM / ELEVATOR INOP</strong></td><td style="border:1px solid #ccc;padding:8px;color:red;"><strong>SEVERE</strong></td><td style="border:1px solid #ccc;padding:8px;">Elevator stuck; possible mechanical jam</td><td style="border:1px solid #ccc;padding:8px;">Declare emergency; pitch via roll; land longest runway</td></tr><tr><td style="border:1px solid #ccc;padding:8px;"><strong>CONTROL JAM / RUDDER INOP</strong></td><td style="border:1px solid #ccc;padding:8px;color:orange;"><strong>CAUTION</strong></td><td style="border:1px solid #ccc;padding:8px;">Rudder pedal jam; hydraulic loss</td><td style="border:1px solid #ccc;padding:8px;">Reduce crosswind; use ailerons for heading control</td></tr><tr style="background:#f9f9f9;"><td style="border:1px solid #ccc;padding:8px;"><strong>STALL WARNING FAIL</strong></td><td style="border:1px solid #ccc;padding:8px;color:orange;"><strong>CAUTION</strong></td><td style="border:1px solid #ccc;padding:8px;">Pneumatic pressure loss; AOA sensor failure</td><td style="border:1px solid #ccc;padding:8px;">Maintain airspeed margin; avoid slow flight; land soon</td></tr><tr><td style="border:1px solid #ccc;padding:8px;"><strong>HYD NO.1 FAIL</strong></td><td style="border:1px solid #ccc;padding:8px;color:orange;"><strong>CAUTION</strong></td><td style="border:1px solid #ccc;padding:8px;">No.1 hydraulic system loss</td><td style="border:1px solid #ccc;padding:8px;">All primary controls switch to No.2; continue to destination</td></tr><tr style="background:#f9f9f9;"><td style="border:1px solid #ccc;padding:8px;"><strong>FLAP FAIL / FLAP POSITION DISAGREE</strong></td><td style="border:1px solid #ccc;padding:8px;color:orange;"><strong>CAUTION</strong></td><td style="border:1px solid #ccc;padding:8px;">Flap actuator jam; electrical signal loss</td><td style="border:1px solid #ccc;padding:8px;">Plan landing with flaps at current position; land longest runway</td></tr></table>'],
  ],
  'quiz' => [
    ['q'=>'The Q400 flight control system is mechanically linked and hydraulically powered. Which statement is true?','options'=>['A) Loss of all hydraulic pressure makes the aircraft unflyable','B) You have direct mechanical control authority; hydraulic power assists your inputs','C) The system is fly-by-wire with mechanical backup','D) Hydraulic pressure can override pilot inputs'],'correct'=>1,'explanation'=>'Correct: B) You have direct mechanical control authority; hydraulic power assists your inputs. The mechanical linkage (cables, pushrods) is always connected to the control surfaces. Hydraulic power (actuators) makes the controls light. If hydraulics fail, the aircraft becomes heavy but remains fully controllable—this is called manual reversion.'],
    ['q'=>'You are descending at 220 KIAS and want to extend flaps to 10°. What must you do first?','options'=>['A) Extend flaps immediately; speed will stabilize','B) Reduce airspeed to 180 KIAS or below before extending','C) Retract landing gear to reduce drag','D) Select spoilers to increase descent rate'],'correct'=>1,'explanation'=>'Correct: B) Reduce airspeed to 180 KIAS or below before extending. Flap 10° has a maximum extension speed of 180 KIAS. Attempting to extend above 180 KIAS risks actuator cavitation and control feel loss. Respect flap speed limits—they are hard limits, not suggestions.'],
    ['q'=>'The stick shaker activates during your approach. What is the correct response?','options'=>['A) Wait for the stick pusher to automatically push the nose down','B) Immediately pitch down, add power, and increase airspeed','C) Reduce throttle and steepen the descent','D) Extend flaps to increase lift'],'correct'=>1,'explanation'=>'Correct: B) Immediately pitch down, add power, and increase airspeed. The stick shaker is a warning that your angle of attack is too high and stall is imminent. You must respond by reducing pitch (pitch forward) and adding power. Do not wait for the stick pusher—it is a last-resort automatic protection that fires if you ignore the shaker.'],
    ['q'=>'You lose No.1 hydraulic system in cruise. What happens to the ailerons and elevators?','options'=>['A) They immediately freeze at neutral position','B) They switch to No.2 hydraulic system; full control authority is maintained','C) You lose aileron control; elevators remain powered','D) Manual reversion occurs; controls become very heavy'],'correct'=>1,'explanation'=>'Correct: B) They switch to No.2 hydraulic system; full control authority is maintained. The Q400 has dual hydraulic systems for all primary controls specifically to provide redundancy. Loss of No.1 does not degrade your control; No.2 power automatically takes over. This is why dual-hydraulic aircraft are safer than single-hydraulic designs.'],
    ['q'=>'What is the purpose of the aileron droop system?','options'=>['A) To reduce aileron flutter at high speed','B) To automatically lower ailerons as flaps extend, maintaining roll effectiveness at low speed','C) To increase landing distance by deploying spoilers','D) To coordinate turns by linking aileron and rudder'],'correct'=>1,'explanation'=>'Correct: B) To automatically lower ailerons as flaps extend, maintaining roll effectiveness at low speed. When you extend flaps for landing, the wing stalls at a higher angle of attack (longer chord = more camber = earlier stall). The aileron droop is a mechanical linkage that lowers the ailerons as flaps extend, improving wing camber and roll effectiveness in the landing configuration.'],
    ['q'=>'You notice that elevator control feels very heavy and does not respond normally to your inputs. What should you do?','options'=>['A) Continue to climb; heavy controls are normal at high altitude','B) Reduce flaps to lighten the control feel','C) Reduce airspeed and plan to land at the nearest airport','D) Apply maximum trim to overcome the resistance'],'correct'=>2,'explanation'=>'Correct: C) Reduce airspeed and plan to land at the nearest airport. Heavy or unresponsive controls are a sign of potential hydraulic failure, actuator malfunction, or structural issue. Do not continue to climb or head for a distant airport. These symptoms warrant immediate landing at the nearest suitable runway with emergency equipment standing by.'],
    ['q'=>'The EICAS displays "HYD NO.1 FAIL" and "HYD NO.2 FAIL." You are at 5,000 feet. What configuration should you select for landing?','options'=>['A) Flaps 0° only; spoilers off','B) Flaps 35°; spoilers deployed for maximum braking','C) Gear up to reduce drag; flaps 0°','D) Flaps 35°; gear down; maximum power to extend glide'],'correct'=>1,'explanation'=>'Correct: B) Flaps 35°; spoilers deployed for maximum braking. Loss of both hydraulic systems means manual reversion: controls are now extremely heavy and slow to respond. You must reduce weight (extend flaps for lift and shorter landing distance) and use spoilers for descent control and landing distance. Full flaps (35°) is essential because you need maximum lift at low speed and minimum landing distance. Control forces will be very high, but the aircraft is controllable.'],
    ['q'=>'During cruise, a loud vibration appears in the control column. This is most likely:','options'=>['A) Normal autopilot servo chatter','B) Stick shaker activated—angle of attack is too high','C) Trim system malfunction','D) Aileron flutter at high speed'],'correct'=>1,'explanation'=>'Correct: B) Stick shaker activated—angle of attack is too high. The stick shaker uses two electric motors in the control column to produce a distinctive, loud vibration. This is the stall warning system alerting you that AOA is approaching stall. During cruise, this would indicate a pitch-too-high condition (perhaps from autopilot malfunction or unusual trim setting). Immediately pitch forward to reduce AOA and verify airspeed is adequate.']
  ]
]; }

// ── ATA32 LANDING GEAR

function ata32_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Retractable landing gear, dual hydraulic actuation, and what happens when you land',
      'time'=>'9 min','objective'=>'After this chapter you will understand the purpose of landing gear, the basic architecture, and why redundancy is critical.',
      'analogy'=>['label'=>'The Analogy — A Swiss Army Knife','text'=>'The landing gear is like a Swiss Army Knife: you extend it when you need it (approach/landing), stow it when you do not need it (cruise, to save drag), and if the normal deploy mechanism fails, you have a backup blade and a pliers tool (manual pump, free-fall). Each tool serves a purpose, and you have multiple ways to achieve the goal.'],
      'body'=>'<p>The Q400 landing gear is a <strong>retractable tricycle configuration</strong>: one nose wheel (steering-capable) and two main landing gear units (one per wing). In cruise, the gear retracts into the fuselage and wheel wells to reduce drag; this saves significant fuel and allows higher cruise speed. For approach and landing, you extend the gear to create landing points. The gear is hydraulically actuated (extended and retracted by hydraulic pressure), but the system includes a <strong>manual pump backup</strong> and <strong>free-fall emergency extension</strong> in case hydraulics fail.</p><p>The Q400 uses <strong>two independent hydraulic systems</strong> for landing gear: No.1 system is primary, No.2 is alternate. If No.1 fails, No.2 can extend or retract the gear. The <strong>squat switch</strong> (weight-on-wheels switch) is crucial: it prevents gear retraction when the aircraft is on the ground (preventing a nose-over) and triggers automatic spoiler deployment on landing. The nose gear has <strong>tiller steering</strong> for ground maneuvering (±65° lock-to-lock) and is also slaved to rudder inputs (±7°) for coordinated steering.</p><p>Gear <strong>speed limits</strong> are critical: maximum extension speed (Vlo) is 200 KIAS, and maximum extended speed (Vle) is also 200 KIAS. This means you cannot extend or cruise with gear down above 200 KIAS without risk of gear failure or shock loads. Landing gear doors are automatic (actuated by the gear itself), so no separate door selection is required.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🛬 Landing Gear Architecture','list'=>['Retractable tricycle: nose + two main wheels','Hydraulically actuated: primary (No.1) and alternate (No.2) systems','Mechanical actuation: cables/pushrods from gear lever','Manual pump: mechanical hydraulic backup for extension/retraction','Free-fall emergency: springs and gravity extend gear if hydraulics fail','Squat switch: weight-on-wheels safety inhibit and spoiler trigger']],
        ['type'=>'amber','head'=>'⭐ 5 Things You Must Know','table'=>['headers'=>['Item','Fact','Why It Matters'],
          'rows'=>[['Vlo (Max extension)','200 KIAS','Do not extend gear above this speed; risk of structural damage'],
                   ['Vle (Max gear extended)','200 KIAS','Do not cruise with gear down above 200 KIAS; shock loads on structure'],
                   ['Gear Down Indication','3 Green lights','Confirms all three gear are down and locked; necessary before landing'],
                   ['Unsafe Indication','RED light','Gear is not in a safe position (not fully down/locked or not fully up/locked)'],
                   ['Nose Gear Steering','Tiller ±65°, Rudder ±7°','Differential braking and tiller provide ground steering; rudder helps align on landing']]]],
      ],
      'quiz'=>['q'=>'The landing gear extension speed limit (Vlo) is 200 KIAS. What is the consequence of extending gear above this speed?','options'=>['A) The gear simply extends more slowly','B) Risk of structural damage, shock loads, and potential gear failure','C) Automatic speed brake deployment prevents overspeed','D) Gear extension is electronically inhibited above 200 KIAS'],'correct'=>1,'explanation'=>'Correct: B) Risk of structural damage, shock loads, and potential gear failure. The Vlo limit is a hard speed limit enforced by pilot discipline (and checklist discipline). Extending gear above 200 KIAS subjects the landing gear attach points to extreme shock loads; the gear may extend but could be damaged, leading to unsafe gear-down indication or collapse on landing. Always reduce speed below 200 KIAS before selecting gear down.']
    ],
    [
      'badge'=>'System Architecture','title'=>'Gear Components','navTitle'=>'Components',
      'subtitle'=>'Nose gear, main gear, actuators, doors, and the steering linkage',
      'time'=>'11 min','objective'=>'Understand the mechanical and hydraulic architecture of landing gear extension, retraction, and ground steering.',
      'body'=>'<p>The Q400 landing gear consists of three independent struts (nose and two main). Each main gear has a <strong>dual-wheel assembly</strong> (two wheels per main strut for load distribution and redundancy). The nose gear has a <strong>single steerable wheel</strong>. All three struts contain <strong>shock absorbers</strong> (oleo-pneumatic) to dissipate landing impact energy. Each gear unit is hydraulically actuated by a <strong>double-acting cylinder</strong> that extends (lowers) or retracts (raises) the gear in response to hydraulic pressure from the gear system.</p><p><strong>Landing gear doors</strong> are automatic and mechanically linked to the gear actuators. When you select gear down and the actuator extends, linkage also opens the wheel well doors. When you select gear up and the actuator retracts, the doors close as the gear retracts. There is no separate door control, but there are door position switches that signal whether doors are open/closed.</p><p><strong>Nose gear steering</strong> is achieved via two methods: (1) <strong>Tiller</strong>: a dedicated steering wheel in the cockpit (or on some aircraft a ground-only tiller) that directly commands the nose wheel to ±65°; (2) <strong>Rudder pedal override</strong>: rudder pedal inputs (±7°) are also fed to nose steering, allowing the pilot to steer via rudder. Above ~5 knots on takeoff roll, nose steering sensitivity changes to prevent violent ground maneuvers. The <strong>nose wheel is not steerable in flight</strong>—steering actuator is disabled by a ground-sense switch.</p><p>The <strong>brakes are independent of the landing gear system</strong> and are powered by No.3 hydraulic system. Anti-skid logic prevents wheel lockup during braking. The <strong>parking brake</strong> is hydraulically set (pilot selects via parking brake handle) and mechanically latched so it holds even if hydraulics fail.</p>',
      'components'=>[
        ['icon'=>'⛱️','name'=>'Nose Landing Gear','role'=>'Forward support and steering control for ground maneuvering','spec'=>'Single steerable wheel; oleo-pneumatic shock absorber; ±65° tiller steering; ±7° rudder-coupled steering','detail'=>'Nose gear extends and retracts via hydraulic double-acting cylinder. Steering is actuated by a small hydraulic servomotor (connected to tiller and rudder inputs via cables/pushrods). Steering is disabled in flight by ground-sense switch. Nose strut incorporates centering spring to return wheel to straight-ahead when steering inputs are neutral.'],
        ['icon'=>'⭕','name'=>'Main Landing Gear (Port and Starboard)','role'=>'Primary support and load-bearing for landing and taxi','spec'=>'Dual-wheel assemblies (two wheels per strut); oleo shock absorbers','detail'=>'Each main strut contains a large double-acting hydraulic cylinder for extension/retraction and oleo shock absorber for impact absorption. Dual wheels distribute braking and landing loads. Main gear is not steerable (wheels point forward in all configurations). Strut incorporate down-lock and up-lock mechanical latches for position security.'],
        ['icon'=>'🚪','name'=>'Landing Gear Doors','role'=>'Reduce aerodynamic drag when gear is retracted','spec'=>'Automatic; mechanically linked to gear actuation','detail'=>'Main wheel well doors and nose door open as gear extends (lower) and close as gear retracts (raises). No separate hydraulic or electrical door actuation—purely mechanical linkage. Door position switches provide feedback to indicate open/closed state. Stuck door is rare but would prevent gear from fully retracting.'],
        ['icon'=>'🔧','name'=>'Gear Actuators and Linkages','role'=>'Convert hydraulic pressure into gear extension/retraction','spec'=>'Double-acting cylinders; supply from No.1 hydraulic (primary) or No.2 (alternate)','detail'=>'Each gear (nose and two main) has one main actuator cylinder. Pilot selections (gear lever UP/DOWN) route hydraulic pressure to extend or retract each actuator. Mechanical down-lock (gravity) and up-lock (spring-loaded latch) hold gear in extended or retracted position after actuation. Loss of hydraulic pressure holds gear in last position (held by mechanical locks).'],
        ['icon'=>'🛞','name'=>'Steering Servomotor and Linkage','role'=>'Convert tiller and rudder inputs into nose wheel steering angle','spec'=>'Small hydraulic servomotor; ±65° max (tiller), ±7° (rudder)','detail'=>'Tiller (dedicated steering control) and rudder pedals both feed steering signals to a hydraulic servomotor, which actuates the nose wheel via a drag-link and oleo steering column. Steering is available only on ground (squat switch input). Pedal inputs (±7°) blend with tiller inputs to achieve coordinated steering.'],
      ],
      'cards'=>[
        ['type'=>'blue','head'=>'Gear Extension/Retraction Timeline','steps'=>[
          ['title'=>'Gear Lever Select DOWN','desc'=>'Pilot moves gear lever from UP to DOWN position; electrical signal routes to gear control valve','state'=>'action','stateLabel'=>'PILOT ACTION'],
          ['title'=>'Hydraulic Pressure Applied','desc'=>'Control valve opens; pressurized hydraulic fluid flows to extension actuators for all three gear units','state'=>'action','stateLabel'=>'~0 sec'],
          ['title'=>'Actuators Extend','desc'=>'Hydraulic pressure pushes actuator rods downward; mechanical linkage opens wheel well doors; gear extends at ~1 inch per second','state'=>'state','stateLabel'=>'~2-5 sec'],
          ['title'=>'Gear Bottoms Out (Fully Extended)','desc'=>'Each gear reaches full extension; mechanical down-lock latch engages, mechanically locking gear in place','state'=>'state','stateLabel'=>'~7-10 sec'],
          ['title'=>'Position Switches Activate','desc'=>'Micro-switches confirm each gear is fully extended and locked; green position lights illuminate on panel','state'=>'state','stateLabel'=>'~7-10 sec'],
          ['title'=>'Gear Down and Locked','desc'=>'Three green lights indicate all gear extended and locked. Aircraft is safe to land.','state'=>'success','stateLabel'=>'READY TO LAND']
        ]],
        ['type'=>'amber','head'=>'⚠️ Unsafe Indication (RED Light)','list'=>['RED light indicates gear NOT in a safe position','Possible causes: Gear is neither fully UP nor fully DOWN; retraction incomplete; mechanical latch failure','Do NOT attempt landing with RED light showing','Must troubleshoot and restore green lights (or verify RED light is false alarm)','If RED cannot be cleared, perform landing with manual extension or free-fall (backup procedures)']],
      ],
      'failures'=>[
        ['sev'=>'high','name'=>'Gear Will Not Extend (Mechanical Jam)','eicasMsg'=>'GEAR UNSAFE / NO.1 HYD FAIL','what'=>'Pilot selects gear down, but actuators do not respond; gear remains up','auto'=>'No extension occurs; RED light illuminates indicating unsafe configuration','pilot'=>'Declare emergency. Attempt manual extension via manual hydraulic pump (cockpit-located hand pump connected to gear system). If manual pump extends gear successfully, continue to destination. If manual extension fails, attempt free-fall extension (emergency procedure involving gravity and spring assist—specific procedure varies). Land on longest available runway with crash fire rescue standing by. Do not attempt aggressive descent.','note'=>'Gear failure is serious but has multiple backup methods for extension.','noteType'=>'red','noteHead'=>'Use Backup Methods'],
        ['sev'=>'med','name'=>'Unsafe Indication (False Red Light)','eicasMsg'=>'GEAR UNSAFE','what'=>'RED light illuminates but gear is actually fully extended and locked (position switch failure)','auto'=>'EICAS displays GEAR UNSAFE; no actual gear position change','pilot'=>'Verify actual gear position by visual inspection (landing light, or request control tower observation). If tower confirms gear is down and appears normal, the RED light is likely a position switch failure. Proceed with landing (gear is actually secure). Document switch failure for maintenance. Do NOT attempt manual extension or free-fall if gear is already down.','note'=>'A failed position switch is often the culprit; visual confirmation is key.','noteType'=>'blue','noteHead'=>'Verify Position Visually'],
      ],
      'quiz'=>['q'=>'You select gear down at 190 KIAS. The gear extends normally and you get three green lights. However, 2 minutes later, a RED light appears (unsafe indication) while the green lights are still illuminated. What is most likely?','options'=>['A) One gear is retracting unexpectedly','B) A position switch has failed; gear is actually down and locked','C) Hydraulic pressure is fluctuating, causing intermittent safety','D) You should immediately perform emergency retraction'],'correct'=>1,'explanation'=>'Correct: B) A position switch has failed; gear is actually down and locked. If gear was actually unsafe (retracting, or losing lock), you would typically not see green lights anymore. The most likely cause is a micro-switch failure in the down-lock sensing circuit. Request tower to visually confirm gear is extended. If tower confirms, proceed with landing—the gear is safe despite the RED light.']
    ],
    [
      'badge'=>'Secondary Systems','title'=>'Brakes and Anti-Skid','navTitle'=>'Brakes & Anti-Skid',
      'subtitle'=>'Carbon brake system, no-skid prevention, and parking brake mechanical latch',
      'time'=>'10 min','objective'=>'Understand brake actuation, anti-skid function, and why proper landing technique and brake cooling are critical.',
      'body'=>'<p><strong>Landing gear brakes</strong> on the Q400 are <strong>carbon disc brakes</strong> powered by <strong>No.3 hydraulic system</strong> (independent of landing gear hydraulics). When you press the toe brakes (foot pedals with braking surfaces), hydraulic pressure from No.3 system applies brake pressure to each wheel. The brake assembly consists of stator discs (stationary) and rotor discs (attached to the wheel) that friction together to absorb kinetic energy. Carbon brakes are lightweight and dissipate heat effectively, making them ideal for transport aircraft.</p><p><strong>Anti-skid system</strong> is a computer-controlled function that prevents wheel lockup during braking. Each main gear wheel has a <strong>wheel-speed sensor</strong> that continuously monitors rotational speed. If a wheel begins to slow (decelerate faster than surrounding wheels), the anti-skid computer automatically <strong>reduces brake pressure on that wheel</strong> to allow it to spin up and regain traction. This prevents skid and hydroplaning, especially on wet runways. Anti-skid function is automatic and transparent to the pilot, but the system only operates above approximately 15 knots groundspeed (below that speed, anti-skid is inhibited).</p><p><strong>Brake cooling</strong> is critical, especially after heavy braking or multiple landings. If brakes overheat, friction material may lose effectiveness. The Q400 procedures include a <strong>taxi cooling</strong> technique: after landing and during extended taxi, you may extend the landing gear (if feasible) to improve cooling airflow around brake assemblies. This is often done in hot climates during long taxi.</p><p>The <strong>parking brake</strong> is a separate system: it is hydraulically applied via a ground-only solenoid valve, which locks the brake actuators in place via mechanical pins. Once set, the parking brake holds even if No.3 hydraulic pressure is lost—the mechanical latch is the primary lock. Parking brake is released by depressing a switch (or lever) that de-energizes the solenoid, allowing pins to withdraw.</p>',
      'components'=>[
        ['icon'=>'🛑','name'=>'Carbon Disc Brake Assembly','role'=>'Dissipate kinetic energy during braking','spec'=>'Multiple stator/rotor disc pairs per wheel; No.3 hydraulic actuation','detail'=>'Each main wheel (four total) has a brake assembly containing stationary (stator) discs and rotating (rotor) discs. Brake pressure forces rotors against stators, creating friction. Heat is dissipated through disc cooling fins and airflow. Carbon material provides high friction coefficient and excellent thermal dissipation. Brake wear is gradual; inspection during maintenance checks disc thickness.'],
        ['icon'=>'⚡','name'=>'Anti-Skid Control Unit (ASCU / BSCU)','role'=>'Prevent wheel lockup by modulating brake pressure based on wheel speed','spec'=>'Monitors all main wheel speeds; adjusts brake pressure continuously','detail'=>'Wheel-speed sensor on each main wheel feeds rotational speed data to anti-skid computer. If wheel decelerates faster than threshold, anti-skid valve reduces pressure to that wheel, preventing skid. On wet runways, anti-skid significantly improves braking effectiveness and directional stability. System is fully automatic; pilot cannot disable (except via circuit breaker in emergency).'],
        ['icon'=>'🚨','name'=>'Wheel Speed Sensor','role'=>'Detect wheel rotation and send speed data to anti-skid computer','spec'=>'Magnetic pickup on each main wheel; output to BSCU','detail'=>'Sensor creates electrical pulse signal proportional to wheel rotation. Pulses are decoded by BSCU to determine wheel speed and deceleration rate. Sensor must be properly installed to avoid giving false signals (e.g., broken mounting can cause intermittent signal).'],
        ['icon'=>'🔐','name'=>'Parking Brake System','role'=>'Mechanically lock brakes on ground via hydraulic latch','spec'=>'Hydraulically applied; mechanically latched; No.3 system','detail'=>'Pilot selects parking brake (switch/lever); solenoid valve is energized, allowing hydraulic pressure to apply brake actuators. At the same time, mechanical pins insert into latch holes, mechanically locking the brake pads in place. If No.3 pressure is lost, mechanical latch holds. Release switch de-energizes solenoid, retracting pins and releasing brake pressure.'],
      ],
      'cards'=>[
        ['type'=>'green','head'=>'✓ Normal Landing Procedure (Braking)','steps'=>[
          ['title'=>'Touch Down','desc'=>'Main wheels contact runway; squat switch activates (weight-on-wheels); spoilers auto-deploy','state'=>'action','stateLabel'=>'~0 sec'],
          ['title'=>'Nose Wheel Touchdown','desc'=>'Nose wheel contacts runway; nose gear steering is now inhibited by squat switch','state'=>'state','stateLabel'=>'~1-2 sec'],
          ['title'=>'Apply Brakes','desc'=>'Press toe brakes smoothly; No.3 hydraulic pressure applies brake force to wheels','state'=>'action','stateLabel'=>'Pilot input'],
          ['title'=>'Anti-Skid Active','desc'=>'Anti-skid computer monitors wheel speeds; modulates pressure to prevent lockup','state'=>'state','stateLabel'=>'Automatic'],
          ['title'=>'Reverse Thrust (if equipped)','desc'=>'Deploy reverse thrust on engines (if available) for additional deceleration','state'=>'action','stateLabel'=>'Pilot input'],
          ['title'=>'Runway Exit','desc'=>'Reduce brake pressure as aircraft slows; taxi to parking at ~5-10 knots','state'=>'state','stateLabel'=>'Final']
        ]],
        ['type'=>'blue','head'=>'Anti-Skid System Logic','list'=>['Monitors wheel speed during braking','If wheel decelerates faster than threshold, anti-skid reduces brake pressure','Wheel is allowed to spin back up (regain traction)','Improves braking distance on wet/icy runways','Only operates above ~15 knots; inhibited during taxi and parking']],
        ['type'=>'amber','head'=>'⚠️ Brake Overheating Risk','list'=>['Risk is highest after heavy braking or consecutive landings','Heat dissipation is airflow-dependent; extend gear (if practical) during taxi to cool brakes','Long taxi in hot climates increases risk','Brake temperature is not directly displayed; use procedural awareness','If brakes overheat, braking effectiveness is reduced']],
      ],
      'failures'=>[
        ['sev'=>'med','name'=>'Anti-Skid Malfunction (ASCU Failure)','eicasMsg'=>'ANTI-SKID INOP / BSCU FAIL','what'=>'Anti-skid computer fails or loses wheel speed sensor input','auto'=>'Anti-skid function is lost; brakes operate in manual mode (no automatic modulation)','pilot'=>'Landing is safe but requires caution. Braking distance will be longer (no anti-skid assist). Apply brakes smoothly to avoid wheel lockup (which you must now prevent manually). On wet runways, landing distance is significantly increased—ensure adequate runway length. Avoid aggressive braking. Land on the longest available runway. After landing, brake cooling is essential before next departure.','note'=>'Anti-skid failure is manageable but reduces safety margin, especially on wet runways.','noteType'=>'blue','noteHead'=>'Extend Landing Distance'],
      ],
      'quiz'=>['q'=>'You are landing in heavy rain on a short runway. Anti-skid is inoperative. What is your best strategy?','options'=>['A) Apply maximum braking immediately upon touchdown to minimize landing distance','B) Land normally but apply brakes smoothly and gradually to avoid wheel lockup','C) Request longer runway or divert; landing distance will be significantly longer without anti-skid','D) Deploy maximum spoiler and reverse thrust; brakes are secondary'],'correct'=>2,'explanation'=>'Correct: C) Request longer runway or divert; landing distance will be significantly longer without anti-skid. Without anti-skid on a wet runway, you have no computer-assisted prevention of wheel lockup. Manual braking is difficult to modulate smoothly, and wet pavement reduces friction significantly. The landing distance will be much longer than normal. Request a longer runway or consider diverting to an airport with longer runway availability. Do not force a landing on a marginal runway without anti-skid in wet weather.']
    ],
    [
      'badge'=>'Abnormals','title'=>'Gear Failures and Emergencies','navTitle'=>'Failures & Abnormals',
      'subtitle'=>'Unsafe indications, emergency extension procedures, and when to declare an emergency',
      'time'=>'10 min','objective'=>'Recognize gear system failures, execute appropriate responses, and execute emergency extension if required.',
      'body'=>'<p><strong>Unsafe indication</strong> (RED light) is the most common abnormal gear event. The RED light means one or more gear is not in a safe position (not fully up and locked, or not fully down and locked). <strong>The RED light does not tell you which gear is unsafe</strong>—it is a summary indication. You must troubleshoot: Is the problem an actual gear position issue, or a failed position switch? If you have two or three green lights, at least those gear are down; if you have no green lights but the lever is down and you have good hydraulic pressure, a position switch is likely failed.</p><p><strong>Manual extension</strong> is your first backup. A hydraulic hand pump is installed in the cockpit (usually under a floor panel or accessible from the flight deck). The pump is mechanically connected to the landing gear system and allows you to manually pressurize and extend the landing gear if engine-driven hydraulic pressure is lost. This is a slow process (takes 5-10 minutes to extend all gear) but is effective. The procedure is in the Quick Reference Handbook (QRH).</p><p><strong>Free-fall emergency extension</strong> is the final backup. If manual pumping fails, you can activate the free-fall system (via emergency extension handle or lever), which disconnects the hydraulic locking mechanism and allows gravity and spring force to extend the gear. Free-fall is fast (takes seconds) but may result in rough extension. After free-fall, the gear cannot be retracted (it is mechanically locked in the down position until maintenance resets the mechanism).</p><p><strong>Declaring an emergency</strong> is appropriate if you have a RED light that cannot be resolved before landing. ATC needs to know you have a potential gear emergency so they can coordinate emergency services and ensure you have adequate runway. Land on the longest available runway, but do not delay unduly—extended flight with a gear problem can lead to hydraulic fluid loss (if the gear actuator has a leak).</p>',
      'components'=>[
        ['icon'=>'✋','name'=>'Manual Hydraulic Pump','role'=>'Extend or retract landing gear if engine hydraulics fail','spec'=>'Hand pump; mechanically linked to gear control valve','detail'=>'Located in flight deck (usually beneath floor panel or accessible from pilot seat area). Manual pump is connected via mechanical linkage to the main gear control valve. Pilot pumps handle up/down to pressurize the system; pressure extends or retracts gear just like normal operation. Process is slow (~5-10 min for full extension) but requires no power and no hydraulic system operation.'],
        ['icon'=>'⬇️','name'=>'Free-Fall Emergency Extension System','role'=>'Final backup to extend landing gear if hydraulics are completely lost','spec'=>'Spring-loaded and gravity-assisted; emergency handle activation','detail'=>'Emergency handle (cockpit-accessible) releases a mechanical lock, allowing gear actuators to extend via gravity and internal springs. This is very fast (seconds) but may be rough. After activation, gear cannot be retracted (locked in extended position). Used only when manual pump fails or is not available.'],
        ['icon'=>'🔌','name'=>'Gear Position Indicator Lights','role'=>'Provide visual feedback on gear position status','spec'=>'3 green lights (all down/locked); 1 red light (unsafe/uncertain)','detail'=>'Green lights indicate full extension and mechanical lock engaged. Red light indicates one or more gear not fully locked in either position. Landing with a RED light is unsafe unless you have visually confirmed (via camera or tower observation) that gear is actually down.'],
      ],
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Unsafe Indication (RED Light) Procedure','steps'=>[
          ['title'=>'RED Light Illuminates','desc'=>'Gear lever is in DOWN position but RED light indicates unsafe condition','state'=>'alert','stateLabel'=>'UNSAFE'],
          ['title'=>'Check Hydraulic Pressure','desc'=>'Verify No.1 and No.2 hydraulic systems have adequate pressure (green on system indications)','state'=>'action','stateLabel'=>'Verify'],
          ['title'=>'Attempt Gear Cycle','desc'=>'Move gear lever UP, wait 5 seconds, then move lever DOWN again (full cycle); check for position light change','state'=>'action','stateLabel'=>'Try Once'],
          ['title'=>'If RED Persists and Hydraulics OK','desc'=>'Likely a position switch failure, not an actual gear problem. Request tower visual check. If tower confirms gear down, you may land.','state'=>'decision','stateLabel'=>'Diagnose'],
          ['title'=>'If RED Persists and Hydraulics Failed','desc'=>'Execute manual extension (hand pump) or free-fall (if manual not available). This is an emergency.','state'=>'action','stateLabel'=>'Backup Proc'],
          ['title'=>'Declare Emergency','desc'=>'Contact ATC and declare gear emergency. Coordinate landing on longest available runway with emergency services standing by.','state'=>'action','stateLabel'=>'ATC Coord']
        ]],
        ['type'=>'amber','head'=>'Manual Extension Procedure (Overview)','list'=>['Access manual hydraulic pump (typically under floor panel at pilot station)','Attach pump handle if not permanently installed','Align pump valve lever to GEAR DOWN','Pump handle up/down repetitively (~60 strokes) until hydraulic pressure builds and gear extends','Monitor position lights; expect green lights after 5-10 minutes of continuous pumping','If RED light clears and greens illuminate, gear is down and locked (safe to land)','If manual extension fails, prepare for free-fall extension (final backup)']],
      ],
      'failures'=>[
        ['sev'=>'high','name'=>'Both Hydraulic Systems Fail (No.1 and No.2)','eicasMsg'=>'HYD NO.1 FAIL + HYD NO.2 FAIL','what'=>'Landing gear hydraulic pressure is completely lost; gear will not extend or retract','auto'=>'Mechanical down-locks hold gear in last known position; position lights go out (unreliable)','pilot'=>'Declare emergency. If gear is already down: landing is safe (gear is held by mechanical down-lock). Land on longest runway. If gear is up: execute manual extension immediately (see manual extension procedure). If manual fails, activate free-fall (emergency handle). Free-fall is last resort and results in hard extension, but provides assured gear-down state for landing.','note'=>'Dual hydraulic failure is extremely rare but this is why backup systems exist.','noteType'=>'red','noteHead'=>'Use Manual Pump or Free-Fall'],
        ['sev'=>'high','name'=>'Nose Gear Will Not Extend','eicasMsg'=>'GEAR UNSAFE / NOSE GEAR INOP','what'=>'Nose gear does not extend despite normal extension command; RED light illuminates','auto'=>'No.1 and No.2 hydraulics are providing pressure to other gear, but nose gear actuator is jammed or failed','pilot'=>'Main gear may be extended (two green lights). Nose gear failure is serious and makes landing very difficult (asymmetric load). Attempt manual extension of nose gear via hand pump (specific manual pump linkage). If unsuccessful, attempt free-fall. If nose gear cannot be extended, you may land on nose tire (if extended) or risk nose strike. Coordinate with ATC for foam on runway (to reduce fire risk). Land very slowly to minimize nose gear impact.','note'=>'Nose gear failure is an emergency but landing is still possible.','noteType'=>'red','noteHead'=>'Declare Emergency'],
      ],
      'quiz'=>['q'=>'You are descending through 5,000 feet. You select gear down, but after 15 seconds you see 2 green lights and a RED light. Hydraulic pressures are normal (No.1 and No.2 are green). What is the most likely problem?','options'=>['A) One gear is actually stuck in the intermediate position','B) A position switch has failed; the third gear is likely down and locked','C) Hydraulic pressure is about to drop; select gear up immediately','D) You must immediately execute manual extension'],'correct'=>1,'explanation'=>'Correct: B) A position switch has failed; the third gear is likely down and locked. If you have two green lights and one RED, and hydraulic pressures are normal, the most likely scenario is that the third gear is actually down and locked, but its position switch is failed. The RED light is a failed sensor, not an actual unsafe gear condition. Request tower visual confirmation. If tower confirms gear is down, proceed with landing. Do not unnecessarily activate manual extension or free-fall if the gear is already safely extended.']
    ],
    [
      'badge'=>'Operations','title'=>'Limitations and Procedures','navTitle'=>'Limitations',
      'subtitle'=>'Speed limits, ground operations, and emergency procedures summary',
      'time'=>'8 min','objective'=>'Master landing gear speed limitations and normal operating procedures.',
      'body'=>'<p><strong>Landing gear speed limits</strong> are strictly enforced: <strong>Vlo (Maximum extension speed) = 200 KIAS</strong>, <strong>Vle (Maximum gear-extended speed) = 200 KIAS</strong>. These limits are identical on the Q400, meaning once gear is extended, you should not exceed 200 KIAS. Exceeding Vlo during extension risks actuator shock-load failure; exceeding Vle during cruise risks structural fatigue and potential gear collapse. If you inadvertently extend gear above 200 KIAS, reduce speed immediately and continue monitoring green lights for any sign of position loss.</p><p><strong>Retraction speed limit</strong> is also 200 KIAS; you must complete gear retraction before accelerating above this speed. The retraction sequence takes approximately 7-10 seconds, so plan your descent and speed management accordingly. On a typical descent profile, you extend gear around 2,000-3,000 feet on approach, then retract gear after departure climb through ~500-800 feet.</p><p><strong>Gear landing procedures</strong> include: (1) reduce speed below 200 KIAS, (2) extend gear (green lights required), (3) configure flaps (matching speed limits), (4) on final approach, stabilize descent, (5) touchdown, (6) apply spoilers (automatic on squat), (7) apply brakes (anti-skid active). After landing, taxi at safe speed (~5-10 knots), then when parked, apply parking brake (hydraulic + mechanical latch).</p><p><strong>Hot weather/long taxi procedures</strong> include gear extension during taxi (if parking will be extended) to improve brake cooling. This is proceduralized for operations in hot climates. Brake temperature management is critical: overheated brakes lose friction and can fail unexpectedly.</p>',
      'components'=>[
        ['icon'=>'🛫','name'=>'Takeoff Procedure (Gear)','role'=>'Proper sequence for gear retraction after departure','spec'=>'Raise gear after positive rate (main wheels clear runway)','detail'=>'After rotation and positive rate: (1) announce "Positive rate," (2) move gear lever UP, (3) monitor gear position (expect 3 greens within ~10 seconds), (4) confirm "Gear up" on completion. Gear retraction completes within 7-10 seconds. Do not exceed 200 KIAS during retraction.'],
        ['icon'=>'🛬','name'=>'Landing Procedure (Gear)','role'=>'Proper sequence for gear extension before approach','spec'=>'Extend gear before 2,000 feet; confirm 3 greens','detail'=>'On descent: (1) Reduce speed below 200 KIAS, (2) Move gear lever DOWN, (3) Expect 3 green lights within ~10 seconds, (4) Announce "Gear down and locked," (5) Continue descent and configure flaps (match flap speed limits). Do not attempt landing with RED light unless gear is visually confirmed as down by tower.'],
        ['icon'=>'🔧','name'=>'Brake Cooling Procedure (Hot Weather)','role'=>'Extend landing gear during extended taxi to improve brake cooling airflow','spec'=>'Optional; used in hot climates with long taxi','detail'=>'After landing, if extended taxi is anticipated (e.g., long taxi to parking): (1) Once airspeed is below 50 knots, (2) Select gear DOWN (gear lowers again despite being already down), (3) This improves airflow around brakes, (4) After reaching parking, retract gear and apply parking brake. This is not standard but is used operationally in very hot environments.'],
      ],
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ Landing Gear Speed Limits','table'=>['headers'=>['Parameter','Speed (KIAS)','Consequence'],
          'rows'=>[['Vlo (Max extension)','200','Exceeding causes shock loads; risk of actuator damage'],
                   ['Vle (Max extended)','200','Exceeding during cruise risks structural fatigue'],
                   ['Vr (Rotation)','Varies','At rotation, you are still below 200 KIAS typically'],
                   ['Gear retraction speed','<200','Complete retraction before accelerating above 200 KIAS']]]],
        ['type'=>'green','head'=>'✓ Normal Gear Extension (Approach)','steps'=>[
          ['title'=>'Reduce to 200 KIAS or below','desc'=>'Accomplish this during descent; avoid high descent rates','state'=>'action','stateLabel'=>'Pilot Action'],
          ['title'=>'Select Gear DOWN','desc'=>'Move gear lever from UP to DOWN; expect immediate descent of gear','state'=>'action','stateLabel'=>'~2 sec'],
          ['title'=>'Monitor Gear Extension','desc'=>'Watch position indicators; expect 3 green lights within ~10 seconds','state'=>'state','stateLabel'=>'~7-10 sec'],
          ['title'=>'Confirm 3 Greens','desc'=>'All three gear down and locked; aircraft is safe to land','state'=>'success','stateLabel'=>'Ready']
        ]],
      ],
      'quiz'=>['q'=>'You are on approach at 190 KIAS, 3,000 feet altitude. You extend landing gear and get 3 green lights. What is the next action?','options'=>['A) Immediately reduce flaps to 35° for landing','B) Accelerate to 200 KIAS to make up for lost descent rate','C) Continue descent, reduce speed to 170 KIAS or below before extending flaps','D) Retract gear and try again'],'correct'=>2,'explanation'=>'Correct: C) Continue descent, reduce speed to 170 KIAS or below before extending flaps. With gear now extended, you can cruise at up to 200 KIAS, but flaps have different limits. To extend flaps to 15°, you need to be at 170 KIAS or below. To extend flaps to 35° (landing), you need to be at 150 KIAS or below. Continuing descent and reducing speed is the proper sequence.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'GEAR UNSAFE — Memory Items','steps'=>['1. <strong>Verify hydraulic pressure</strong>—check No.1 and No.2 pressure gauges (must be green)','2. <strong>Attempt gear cycle</strong>—move lever UP (5 sec), then DOWN; monitor position lights','3. <strong>If RED light persists</strong>—declare emergency and request tower visual check of gear','4. <strong>If tower confirms gear is DOWN</strong>—proceed to landing (likely position switch failure)','5. <strong>If hydraulics are LOST</strong>—execute manual extension (hand pump) or free-fall (emergency handle)'],'why'=>'Unsafe indication requires immediate troubleshooting. Hydraulic pressure check determines if the issue is electrical (switch) or hydraulic (actuator). Tower visual confirmation is authoritative. Manual/free-fall procedures are backups if hydraulics fail.'],
    ['type'=>'abnormal','title'=>'GEAR MANUAL EXTENSION — Abnormal Checklist','eicasMsg'=>'GEAR UNSAFE / UNABLE TO EXTEND','items'=>['Declare emergency to ATC; request longest available runway','Reduce altitude to 2,000 feet or below (allows manual pump operation)','Locate manual hydraulic pump (typically under floor panel)','Attach pump handle; align valve lever to GEAR DOWN','Pump repetitively (up/down) approximately 60 strokes to build pressure','Monitor position lights; expect 3 greens within 5-10 minutes of continuous pumping','If 3 greens are achieved: gear is locked, land normally on longest runway','If RED light persists after prolonged pumping: prepare for free-fall extension (final backup)'],'why'=>'Manual pump is the primary backup if hydraulic power is lost. Slow process but highly reliable. Provides assured gear extension without depending on engine-driven hydraulics.'],
    ['type'=>'abnormal','title'=>'ANTI-SKID SYSTEM FAILURE — Abnormal Checklist','eicasMsg'=>'ANTI-SKID INOP / BSCU FAIL','items'=>['Braking will be manual mode (no automatic wheel-speed modulation)','Request longest available runway; confirm runway condition (dry preferred)','Extend landing gear if not already extended','Plan longer landing distance; anti-skid loss increases stopping distance by ~30% (wet runway even more)','Apply brakes smoothly; avoid aggressive braking to prevent wheel lockup','Land smoothly; avoid bounces that could trigger skid','After landing: brake cooling essential before next flight; post-flight inspection of anti-skid system required'],'why'=>'Anti-skid loss is manageable but requires awareness of increased landing distance and manual braking discipline. Smooth brake application prevents wheel lockup.'],
    ['type'=>'limit','title'=>'Landing Gear System Limitations','items'=>['<strong>Vlo (Max extension):</strong> 200 KIAS — do not extend gear above this speed','<strong>Vle (Max extended):</strong> 200 KIAS — do not cruise above this speed with gear down','<strong>Gear extension time:</strong> 7-10 seconds from lever selection to 3 green lights','<strong>Gear retraction time:</strong> 7-10 seconds from lever selection to up-and-locked','<strong>Gear configuration:</strong> Manual lever (UP/DOWN only) — no neutral; landing is impossible if lever is stuck between positions','<strong>Nose wheel steering:</strong> ±65° tiller (full deflection), ±7° rudder pedals; steering inhibited in flight (squat switch dependent)','<strong>Anti-skid inhibit:</strong> System is disabled below ~15 knots; manual braking only during taxi']],
    ['type'=>'eicas','title'=>'Landing Gear EICAS Messages','html'=>'<table style="width:100%;border-collapse:collapse;"><tr style="background:#f0f0f0;"><th style="border:1px solid #ccc;padding:8px;">EICAS Message</th><th style="border:1px solid #ccc;padding:8px;">Severity</th><th style="border:1px solid #ccc;padding:8px;">Probable Cause</th><th style="border:1px solid #ccc;padding:8px;">Immediate Action</th></tr><tr><td style="border:1px solid #ccc;padding:8px;"><strong>GEAR UNSAFE</strong></td><td style="border:1px solid #ccc;padding:8px;color:red;"><strong>SEVERE</strong></td><td style="border:1px solid #ccc;padding:8px;">Gear not fully locked; position switch failure; or actuator jam</td><td style="border:1px solid #ccc;padding:8px;">Declare emergency; troubleshoot per checklist; request tower visual check</td></tr><tr style="background:#f9f9f9;"><td style="border:1px solid #ccc;padding:8px;"><strong>NO.1 HYD FAIL</strong></td><td style="border:1px solid #ccc;padding:8px;color:orange;"><strong>CAUTION</strong></td><td style="border:1px solid #ccc;padding:8px;">No.1 hydraulic system pressure loss</td><td style="border:1px solid #ccc;padding:8px;">No.2 system backs up gear function; landing is normal; land soon</td></tr><tr><td style="border:1px solid #ccc;padding:8px;"><strong>ANTI-SKID INOP</strong></td><td style="border:1px solid #ccc;padding:8px;color:orange;"><strong>CAUTION</strong></td><td style="border:1px solid #ccc;padding:8px;">BSCU failure or wheel speed sensor fault</td><td style="border:1px solid #ccc;padding:8px;">Plan longer landing distance; manual smooth braking required</td></tr><tr style="background:#f9f9f9;"><td style="border:1px solid #ccc;padding:8px;"><strong>BRAKE TEMP HIGH</strong></td><td style="border:1px solid #ccc;padding:8px;color:orange;"><strong>CAUTION</strong></td><td style="border:1px solid #ccc;padding:8px;">Brakes overheated from heavy braking or multiple landings</td><td style="border:1px solid #ccc;padding:8px;">Extend gear for cooling (if appropriate); avoid heavy braking; land soon</td></tr></table>'],
  ],
  'quiz' => [
    ['q'=>'What are the Vlo (maximum extension speed) and Vle (maximum gear-extended speed) for the Q400?','options'=>['A) Vlo 250 KIAS, Vle 200 KIAS','B) Vlo 200 KIAS, Vle 180 KIAS','C) Vlo 200 KIAS, Vle 200 KIAS','D) Vlo 180 KIAS, Vle 180 KIAS'],'correct'=>2,'explanation'=>'Correct: C) Vlo 200 KIAS, Vle 200 KIAS. Both limits are 200 KIAS. This means you must reduce to 200 KIAS or below before extending the gear, and you cannot exceed 200 KIAS with gear extended. These are hard speed limits; exceeding them risks structural damage and shock loads to the landing gear attachment points.'],
    ['q'=>'You select gear down at 180 KIAS on approach, but the position lights show 2 green and 1 red. Hydraulic pressures are normal. What is the most likely cause?','options'=>['A) One gear is actually stuck in transit','B) A landing gear actuator has failed','C) A position switch (micro-switch) has failed; one gear is likely down and locked','D) Immediate manual extension is required'],'correct'=>2,'explanation'=>'Correct: C) A position switch (micro-switch) has failed; one gear is likely down and locked. If hydraulic pressures are normal and two gears show green (meaning they are locked), the third gear is probably also down and locked, but its position sensing switch has failed. This is a common failure. Request tower visual confirmation. If tower confirms the gear is down, the aircraft is safe to land despite the RED light.'],
    ['q'=>'Both No.1 and No.2 hydraulic systems fail in cruise with landing gear extended. What holds the landing gear in place?','options'=>['A) Electric actuators take over','B) Mechanical down-lock latches hold the gear in extended position','C) Gravity alone (gear is held by weight)','D) Pneumatic backup system'],'correct'=>1,'explanation'=>'Correct: B) Mechanical down-lock latches hold the gear in extended position. Each landing gear unit has mechanical down-lock and up-lock latches that engage when the gear reaches its respective position. Loss of all hydraulic pressure does not change the gear position; the mechanical locks keep gear wherever it was. If gear is down, it stays down (safe to land). If gear is up (and hydraulics fail), manual pump or free-fall extension is needed.'],
    ['q'=>'What is the purpose of the squat switch on the landing gear?','options'=>['A) Detect if the aircraft is on the ground; inhibit gear retraction and trigger spoiler deployment','B) Prevent landing gear extension above 200 KIAS','C) Control nose wheel steering angle during flight','D) Monitor hydraulic pressure in the gear system'],'correct'=>0,'explanation'=>'Correct: A) Detect if the aircraft is on the ground; inhibit gear retraction and trigger spoiler deployment. The squat switch is a weight-on-wheels sensor. When aircraft weight is on the wheels (landing gear compressed), the switch activates and does two things: (1) inhibits the gear retraction function (preventing a nose-over crash), (2) triggers automatic spoiler deployment on landing. The squat switch is a critical safety device.'],
    ['q'=>'You are landing with anti-skid system inoperative. How does this affect your landing?','options'=>['A) Landing is impossible; you must divert','B) You have full braking authority but longer landing distance; requires smooth, gradual brake application','C) Wheel lockup will be automatic but controllable','D) Parking brake becomes the primary braking system'],'correct'=>1,'explanation'=>'Correct: B) You have full braking authority but longer landing distance; requires smooth, gradual brake application. Anti-skid system loss removes automatic wheel-speed modulation, so you must apply brakes smoothly and gradually to avoid wheel lockup (which you must now prevent manually). Landing distance increases (especially on wet runways). The landing is still safe if you use discipline and request a longer runway.'],
    ['q'=>'You have a RED light (unsafe indication) and no green lights. Hydraulic pressure is zero (both No.1 and No.2 failed). You are at 3,000 feet. What is your immediate action?','options'=>['A) Land immediately with current gear position','B) Reduce altitude to 2,000 feet and attempt manual hydraulic extension','C) Declare emergency and execute free-fall gear extension','D) Retract landing gear and attempt to fly on manual reversion controls'],'correct'=>1,'explanation'=>'Correct: B) Reduce altitude to 2,000 feet and attempt manual hydraulic extension. With both hydraulic systems failed, you cannot use normal or alternate hydraulic power. Manual extension (hand pump) is the appropriate first backup. Reduce altitude to ensure you have time to complete manual pumping before landing. If manual extension fails, then prepare for free-fall (final backup). Free-fall is not the first choice if manual pump is available.'],
    ['q'=>'You land with brakes applied, but one main wheel is sliding (skidding). Anti-skid is inoperative. What should you do?','options'=>['A) Apply more brake force to stop the slide','B) Release brake pressure on the skidding wheel (reduce braking)','C) Extend the spoiler to increase drag','D) Deploy reverse thrust to stop the slide'],'correct'=>1,'explanation'=>'Correct: B) Release brake pressure on the skidding wheel (reduce braking). A skid occurs when brake pressure exceeds tire grip. Increasing brake force will worsen the skid. Reducing (releasing) brake pressure allows the wheel to spin up and regain traction. This is why anti-skid is so valuable—it does this automatically. Without anti-skid, you must manually modulate brake pressure smoothly to avoid skids.'],
    ['q'=>'Nose landing gear will not extend during approach. Two green lights (main gear down), one red light (nose gear unsafe). Hydraulic pressures are normal. What action is best?','options'=>['A) Land with main gear only (nose gear up) and accept a nose strike','B) Attempt manual extension of nose gear via hand pump','C) Declare emergency and request vectors to higher altitude for more time','D) Retract all gear and go-around'],'correct'=>1,'explanation'=>'Correct: B) Attempt manual extension of nose gear via hand pump. Nose gear failure with main gear down is serious but not unrecoverable. The manual hydraulic pump can be redirected (via selectors) to pressurize just the nose gear circuit. Pump the nose gear actuator until it extends and locks (should see green light). If manual fails, free-fall is the last resort. Declaring an emergency and getting more time is also reasonable, but manual extension is the primary action.']
  ]
]; }

// ── ATA21 AIR CONDITIONING & PRESSURIZATION

function ata21_content() {
  return [
    'chapters' => [
      [
        'badge' => 'Introduction',
        'title' => 'The Big Picture',
        'navTitle' => 'Big Picture',
        'subtitle' => 'Why pressurization matters and how your Q400 keeps you comfortable',
        'time' => '8 min',
        'objective' => 'After this chapter you will understand the purpose of air conditioning and pressurization, why they are critical systems, and the basic components that make them work.',
        'analogy' => [
          'label' => 'The Analogy — Keeping the Cabin Alive',
          'text' => 'Think of pressurization like keeping a submarine comfortable at depth. At 25,000 feet, the outside air is so thin and cold that humans cannot survive exposed to it. The pressurization system is your submarine hull—it maintains an artificial environment inside. The air conditioning keeps the temperature bearable. Together, they transform a hostile environment into one where passengers can sit, relax, and have a coffee.'
        ],
        'body' => '<p>At cruise altitude on a Q400, the outside air pressure is less than 4 PSI and the temperature is below -50°C. These conditions are immediately lethal to humans. The air conditioning and pressurization systems work together to solve this fundamental problem.</p><p><strong>Pressurization</strong> maintains a cabin altitude equivalent of 6,000 feet even when the aircraft is at 25,000 feet. This keeps cabin pressure at roughly 10.9 PSI—high enough for comfortable human physiology but well below the 7.8 PSI structural limit of the fuselage.</p><p><strong>Air conditioning</strong> cools hot bleed air from the engines down to comfortable temperatures. Without it, bleed air entering the cabin would be over 350°C—hot enough to cause severe burns.</p><p>The Q400 uses a redundant dual-pack system: two independent air conditioning packs, each fed by bleed air from one engine (or APU). If one pack fails, the other can still maintain pressurization and conditioning, though performance degrades. The outflow valve at the tail modulates automatically to regulate cabin altitude and pressure differential.</p>',
        'cards' => [
          [
            'type' => 'amber',
            'head' => '⭐ Key Parameters',
            'table' => [
              'headers' => ['Parameter', 'Value', 'Importance'],
              'rows' => [
                ['Cabin Altitude Target (Cruise)', '6,000 ft equivalent', '<span class="spec-badge must">MUST KNOW</span>'],
                ['Max Cabin Differential Pressure', '7.8 PSI positive', '<span class="spec-badge must">MUST KNOW</span>'],
                ['Q400 Certified Altitude', '25,000 ft', '<span class="spec-badge must">MUST KNOW</span>'],
                ['Normal Cabin Climb Rate', '300–500 ft/min', '<span class="spec-badge normal">KNOW</span>'],
                ['Cabin Altitude Warning', '>10,000 ft', '<span class="spec-badge must">MUST KNOW</span>']
              ]
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the target cabin altitude maintained during cruise on a Q400?',
          'options' => ['3,000 feet', '6,000 feet', '10,000 feet', '12,000 feet'],
          'correct' => 1,
          'explanation' => 'The CPCS (Cabin Pressure Control System) automatically maintains a cabin altitude equivalent of 6,000 feet at max cruise altitude. This is a comfortable compromise between safety and passenger physiology.'
        ]
      ],
      [
        'badge' => 'Systems',
        'title' => 'Bleed Air & Air Conditioning Packs',
        'navTitle' => 'Bleed Air & Packs',
        'subtitle' => 'The journey from engine compressor to comfortable cabin',
        'time' => '10 min',
        'objective' => 'You will learn where bleed air comes from, how the two packs work, and why redundancy matters.',
        'analogy' => [
          'label' => 'The Analogy — Two Paths to Cool Air',
          'text' => 'Imagine two separate water cooling systems in a car. Each has its own compressor and radiator. If one breaks, the other can still keep the engine cool (though efficiency drops). The Q400 packs work the same way—two independent paths to cool, pressurize air.'
        ],
        'body' => '<p>Bleed air is tapped from the engine compressor at two stages: the 5th stage (lower pressure, ~30 PSI) and 9th stage (higher pressure, ~50+ PSI). This hot, high-pressure air is piped to both air conditioning packs. The APU can also supply bleed air for ground operations and low-altitude flight.</p><p>Each <strong>air conditioning pack</strong> contains an air cycle machine (ACM) with four main components:</p><p><strong>1. Primary Heat Exchanger:</strong> Cools bleed air using ram air from the engine nacelle. On cold days, this may cool the air too much, so hot bypass air is mixed in downstream.</p><p><strong>2. Compressor (part of ACM):</strong> Compresses the cooled air to increase its pressure and temperature.</p><p><strong>3. Turbine (part of ACM):</strong> The air then expands through a turbine, which cools it significantly. This turbine is mechanically linked to the compressor—they spin together.</p><p><strong>4. Secondary Heat Exchanger:</strong> Further cools the air before it enters the cabin. The pack outlet air is typically 40–50°C—comfortable and safe.</p><p>Each pack is fed by a <strong>pack valve</strong> (pneumatic solenoid shutoff). Closing a pack valve isolates that pack from bleed air. The system is designed so that one pack can feed both the left and right pressurization systems if necessary via the cross-bleed valve, though this is not the normal configuration.</p>',
        'cards' => [
          [
            'type' => 'blue',
            'head' => 'Pack Valve Control',
            'list' => [
              'Left Pack Valve fed by Engine 1 bleed (or APU on ground)',
              'Right Pack Valve fed by Engine 2 bleed (or APU on ground)',
              'Pilot can close either pack valve independently',
              'Closing a pack reduces bleed air load on that engine'
            ]
          ],
          [
            'type' => 'amber',
            'head' => '⚠ Bleed Air Temperature',
            'table' => [
              'headers' => ['Location', 'Temperature', 'Status'],
              'rows' => [
                ['Engine 5th stage bleed', '~300°C', 'Hot—primary source'],
                ['Primary HX outlet', '~10–30°C', 'Cooled by ram air'],
                ['Turbine outlet', '~-20 to 0°C', 'Cooled by expansion'],
                ['Pack outlet', '~40–50°C', 'Final conditioning']
              ]
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the main function of the turbine in an air conditioning pack?',
          'options' => [
            'To increase air pressure',
            'To cool the air through expansion',
            'To prevent ice formation',
            'To blend hot and cold air'
          ],
          'correct' => 1,
          'explanation' => 'The turbine expands the air, causing a significant temperature drop. This is a key cooling stage in the air cycle. The turbine is mechanically linked to the compressor, and together they form the air cycle machine (ACM).'
        ]
      ],
      [
        'badge' => 'Systems',
        'title' => 'Pressurization Control System',
        'navTitle' => 'Pressurization Control',
        'subtitle' => 'How the outflow valve keeps cabin pressure in check',
        'time' => '9 min',
        'objective' => 'You will understand the CPCS, outflow valve operation, manual pressurization backup, and the limits of the system.',
        'analogy' => [
          'label' => 'The Analogy — The Pressure Relief Valve',
          'text' => 'A pressurization system is like a pressure cooker with an automatic vent. As heat builds up (as you climb), the vent opens slightly to let steam escape, keeping internal pressure stable. The outflow valve is that vent.'
        ],
        'body' => '<p>The <strong>Cabin Pressure Control System (CPCS)</strong> automatically regulates cabin altitude using feedback from cabin pressure sensors. The system has one primary control: the <strong>outflow valve</strong> located at the tail of the fuselage.</p><p>The outflow valve modulates its opening based on the difference between cabin pressure and ambient pressure. During climb, as the aircraft goes higher, the CPCS commands the outflow valve to open progressively, allowing cabin air to escape. This limits the cabin altitude rise to a controlled rate (typically 300–500 feet per minute). During descent, the outflow valve closes gradually to prevent cabin pressure from dropping too quickly.</p><p><strong>Automatic Mode:</strong> The CPCS constantly monitors pressure and maintains it within ±50 feet of the selected target. The normal target is 6,000 feet at cruise.</p><p><strong>Manual Mode:</strong> The pilot can manually control outflow valve position if the automatic system fails. This requires continuous pilot attention and is not preferred for long cruise.</p><p><strong>Critical Limitation:</strong> The outflow valve is the only means of controlling cabin pressure. If both air conditioning packs fail, the single outflow valve cannot maintain pressurization above 10,000 feet. In such an emergency, immediate descent below 10,000 feet is required.</p><p><strong>Differential Pressure Limits:</strong> The fuselage structure is certified for a maximum positive pressure differential of 7.8 PSI (inside pressure higher than outside). A maximum negative differential of 0.5 PSI prevents cabin from creating a vacuum. These limits ensure structural integrity.</p>',
        'cards' => [
          [
            'type' => 'red',
            'head' => '🚨 System Failure Limits',
            'list' => [
              '<strong>Both packs fail:</strong> Descend immediately below 10,000 ft',
              '<strong>One pack fails:</strong> Operating pack can maintain pressurization (degraded performance)',
              '<strong>Outflow valve stuck closed:</strong> Cabin pressure climbs; select manual mode and open manually',
              '<strong>Outflow valve stuck open:</strong> Cabin altitude climbs rapidly; may exceed 10,000 ft; oxygen masks deploy'
            ]
          ],
          [
            'type' => 'green',
            'head' => 'Cabin Pressure Modes',
            'steps' => [
              '1. Automatic mode: CPCS adjusts outflow valve to maintain target altitude (normal)',
              '2. Manual mode: Pilot directly controls outflow valve position (fallback)',
              '3. Dump mode: Outflow valve fully opens (emergency depressurization for smoke/fumes, or if cabin pressure cannot be controlled)'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the maximum positive pressure differential the Q400 fuselage can safely withstand?',
          'options' => ['3.5 PSI', '5.0 PSI', '7.8 PSI', '10.0 PSI'],
          'correct' => 2,
          'explanation' => 'The Q400 fuselage is certified for a maximum positive differential of 7.8 PSI. Exceeding this risks structural failure. The CPCS is designed to never allow this limit to be approached.'
        ]
      ],
      [
        'badge' => 'Systems',
        'title' => 'Temperature Control',
        'navTitle' => 'Temperature Control',
        'subtitle' => 'Keeping the flight deck and cabin at the right temperature',
        'time' => '7 min',
        'objective' => 'You will learn how trim air, bypass air, and zone control maintain comfortable temperatures throughout the aircraft.',
        'analogy' => [
          'label' => 'The Analogy — Hot and Cold Taps',
          'text' => 'Adjusting cabin temperature is like mixing hot and cold water from two taps. One tap provides cold air from the pack outlet; the other provides hot bypass air. The temperature control valve blends them to achieve the desired temperature.'
        ],
        'body' => '<p>The air conditioning packs provide cold air, but blending with hot air is necessary to avoid over-cooling the cabin. The system uses <strong>trim air</strong> and <strong>bypass air</strong> to achieve this balance.</p><p><strong>Trim Air System:</strong> Hot bleed air is modulated by the trim air valve and mixed with cold pack air to achieve the target cabin temperature. Trim air is controlled independently for the flight deck and cabin. The flight deck typically operates at a slightly warmer set point than the main cabin.</p><p><strong>Bypass Air:</strong> In the pack itself, if the primary and secondary heat exchangers over-cool the air, hot bypass air from the compressor inlet is mixed in downstream to regulate outlet temperature.</p><p><strong>Temperature Control Logic:</strong> Cabin temperature is sensed by thermostats. If the sensed temperature is below the set point, the trim air valve opens more (adding hot air). If above, the trim air valve closes (reducing hot air). This creates a stable, comfortable environment.</p><p><strong>Zone Control:</strong> Some aircraft divide the cabin into zones (forward, aft). The Q400 typically has forward and aft temperature sensing, allowing slight variations in comfort. Trim air mixing can be adjusted separately for each zone.</p><p><strong>Overheat Protection:</strong> If trim air temperature or pack outlet temperature exceeds limits (e.g., due to heat exchanger failure), the trim air valve closes and warnings are generated.</p>',
        'cards' => [
          [
            'type' => 'blue',
            'head' => 'Temperature Setpoints',
            'table' => [
              'headers' => ['Zone', 'Typical Set Point', 'Range'],
              'rows' => [
                ['Flight Deck', '18–24°C', '16–27°C'],
                ['Cabin', '18–24°C', '16–27°C'],
                ['Pack Air (max outlet)', '~50°C', 'Limited by design']
              ]
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the purpose of trim air in the Q400 air conditioning system?',
          'options' => [
            'To reduce bleed air load on the engines',
            'To mix hot and cold air and achieve the desired cabin temperature',
            'To provide emergency ventilation if packs fail',
            'To cool the flight deck independently from the cabin'
          ],
          'correct' => 1,
          'explanation' => 'Trim air carries hot bleed air that is mixed with cold pack air to achieve the target temperature. This allows fine control of cabin comfort. Each zone (flight deck and cabin) has its own trim air valve for independent temperature control.'
        ]
      ],
      [
        'badge' => 'Emergencies',
        'title' => 'Abnormal Scenarios',
        'navTitle' => 'Abnormal Scenarios',
        'subtitle' => 'Recognition and response to pressurization and conditioning failures',
        'time' => '11 min',
        'objective' => 'You will recognize symptoms of rapid decompression, pack failure, pressurization loss, and excessive cabin altitude, and know the correct response to each.',
        'analogy' => [
          'label' => 'The Analogy — When Systems Fail',
          'text' => 'A rapid decompression is like a balloon suddenly popping—immediate, violent, and requiring instant crew action. A pack failure is a slow leak in that balloon—performance degrades, but you have time to respond.'
        ],
        'body' => '<p><strong>Rapid Decompression</strong> is the most critical scenario. It occurs when the fuselage is breached (structural failure, door separation, etc.). Cabin pressure and altitude change very rapidly—in seconds, cabin altitude can exceed 25,000 feet. The aural alarm sounds continuously. All occupants experience hypoxia, disorientation, and potential loss of consciousness.</p><p><strong>Crew Response:</strong> Don oxygen mask immediately (manual pull if needed), declare emergency, reduce to lower altitude below 10,000 feet as quickly as safely possible. Depressurize intentionally if fire/fumes suspected. Cabin pressure will equalize with ambient pressure; structural damage assessment occurs on ground.</p><p><strong>Pack Failure:</strong> If one pack fails (detected by EICAS message or loss of supply pressure), the other pack can maintain pressurization but performance is degraded. Cabin climb rate may be slower, and pressurization margin above 10,000 feet is reduced. If both packs fail, the aircraft must descend below 10,000 feet immediately.</p><p><strong>Pressurization Loss:</strong> If the outflow valve fails to close (stuck open), cabin altitude climbs uncontrolled. The CPCS attempts to compensate, but if the valve is mechanically stuck, the system cannot maintain pressure. Symptoms: cabin altitude climbing despite engine bleed available, EICAS caution/warning. Response: select manual pressurization and try to close the outflow valve by hand, or descent if unable to control.</p><p><strong>Cabin Altitude Warning:</strong> If cabin altitude exceeds 10,000 feet, an aural alarm sounds and EICAS displays a warning. This triggers automatic deployment of oxygen masks to passengers (14,000 ft cabin altitude nominal, but manual pull possible earlier). Crew must recognize the failure (pack failure, outflow valve stuck open) and take corrective action.</p><p><strong>Oxygen Mask Deployment:</strong> Passenger oxygen masks drop automatically at 14,000 feet cabin altitude (dual sensing for safety). Crew oxygen is available on demand or manually deployed. Oxygen supply is sufficient for a safe descent to 10,000 feet.</p>',
        'failures' => [
          [
            'sev' => 'high',
            'name' => 'Rapid Decompression',
            'eicas' => 'CABIN ALTITUDE HIGH (continuous tone)',
            'what' => 'Fuselage breach or door failure; cabin pressure bleeds to ambient rapidly',
            'auto' => 'Oxygen masks deploy at ~14,000 ft cabin altitude; CPCS cannot control',
            'pilot' => 'Don oxygen mask manually if needed; declare emergency; descend to 10,000 ft ASAP; assess damage on ground',
            'note' => 'Immediate action required. Hypoxia risk is severe. Expect rapid altitude loss and disorientation. Use autopilot if available to maintain control.',
            'noteType' => 'red',
            'noteHead' => 'CRITICAL'
          ],
          [
            'sev' => 'high',
            'name' => 'Loss of Pressurization (Both Packs)',
            'eicas' => 'PACK OFF (or AIR SOURCE OFF message)',
            'what' => 'Both pack valves closed, both bleed sources failed, or dual-pack compressor failure',
            'auto' => 'Cabin altitude climbs; pressurization cannot be maintained above 10,000 ft',
            'pilot' => 'Descend immediately to below 10,000 ft; verify pack valve position and bleed source; cycle valves if safe',
            'note' => 'Single pack can maintain normal pressurization. Both packs out is rare but requires immediate descent.',
            'noteType' => 'amber',
            'noteHead' => 'EMERGENCY DESCENT'
          ],
          [
            'sev' => 'high',
            'name' => 'Pressurization Loss (Outflow Valve Stuck Open)',
            'eicas' => 'CABIN ALT warning or continuous climb',
            'what' => 'Outflow valve mechanical failure; remains open regardless of CPCS command',
            'auto' => 'Cabin altitude climbs uncontrolled despite packs operating; CPCS ineffective',
            'pilot' => 'Switch to MANUAL pressurization mode; attempt to close outflow valve manually; if unsuccessful, descend; depressurize intentionally if smoke/fumes',
            'note' => 'Automatic descent may be required if manual control ineffective. Structural integrity not at risk (negative differential not exceeded).',
            'noteType' => 'amber',
            'noteHead' => 'MANUAL CONTROL'
          ],
          [
            'sev' => 'med',
            'name' => 'Single Pack Failure',
            'eicas' => 'PACK OFF (for the failed pack)',
            'what' => 'One pack compressor, turbine, or heat exchanger failure; or pack valve inadvertently closed',
            'auto' => 'Remaining pack assumes pressurization; performance degraded but pressurization maintained',
            'pilot' => 'Verify pack valve status on remaining pack; confirm bleed air available; descend to lower altitude if cruise altitude unattainable',
            'note' => 'One pack is sufficient for normal operations. Avoid high altitudes or long cruise if uncertain of remaining pack condition.',
            'noteType' => 'amber',
            'noteHead' => 'DEGRADED PERFORMANCE'
          ],
          [
            'sev' => 'med',
            'name' => 'High Cabin Temperature',
            'eicas' => 'CABIN TEMP HIGH or PACK OUTLET TEMP HIGH',
            'what' => 'Trim air valve open continuously, bypass air insufficient, or heat exchanger fouling',
            'auto' => 'Temperature control system may cycle trim air valve; air still conditioned but warmer than set point',
            'pilot' => 'Reduce trim air set point; verify pack outlet temp sensors; check for heat exchanger fouling or bypass air blockage',
            'note' => 'Comfort issue primarily. Verify cabin pressurization not affected. If pack outlet temp exceeds limits, consider pack shutdown and reliance on other pack.',
            'noteType' => 'amber',
            'noteHead' => 'MONITOR'
          ]
        ],
        'quiz' => [
          'q' => 'What is the correct first action if rapid decompression occurs at 23,000 feet?',
          'options' => [
            'Switch pressurization to manual mode',
            'Don oxygen mask immediately and declare emergency',
            'Close both pack valves to stop pressure loss',
            'Reduce power and level off'
          ],
          'correct' => 1,
          'explanation' => 'Rapid decompression is an immediate life threat. Crew must don oxygen first, then declare an emergency and initiate descent. There is no time to troubleshoot systems. Hypoxia incapacitation occurs quickly.'
        ]
      ]
    ],
    'qrh' => [
      [
        'type' => 'memory',
        'title' => 'RAPID DECOMPRESSION',
        'steps' => [
          '1. Don oxygen mask (manual pull if needed)',
          '2. Declare emergency to ATC',
          '3. Descend to below 10,000 feet as rapidly as safely possible',
          '4. Verify cabin pressurization cannot be restored',
          '5. If fire/fumes present, select DUMP and depressurize intentionally',
          '6. Land at nearest suitable airfield'
        ],
        'why' => 'Hypoxia risk is severe above 10,000 feet cabin altitude. Rapid descent is the only treatment. Oxygen is supportive but not a substitute for lower altitude.'
      ],
      [
        'type' => 'memory',
        'title' => 'CABIN ALTITUDE HIGH (Continuous Alarm)',
        'steps' => [
          '1. Don oxygen mask',
          '2. Verify CPCS is in AUTO mode',
          '3. Confirm one or both packs are supplying air (check pressure gauges)',
          '4. If pack pressure low: close affected pack valve and verify bleed source',
          '5. If both packs out: declare emergency and descend below 10,000 feet',
          '6. If pressurization cannot be restored: switch to MANUAL and attempt outflow valve control',
          '7. Continue descent if alarm persists'
        ],
        'why' => 'Cabin altitude above 10,000 feet causes hypoxia. Rapid crew action and descent are essential.'
      ],
      [
        'type' => 'abnormal',
        'title' => 'LOSS OF PRESSURIZATION',
        'eicasMsg' => 'PACK OFF (L or R) or CABIN ALT (continuous)',
        'items' => [
          '• Verify selected altitude on CPCS is appropriate for current flight phase',
          '• Check pack pressure: should be 40–55 PSI',
          '• If one pack is low: close its valve; other pack should assume full control',
          '• If both packs are low: check bleed air status (engine bleeds or APU)',
          '• If bleed air is available but pack pressure remains low: pack may be failed (compressor or turbine); select landing and consider single-pack capability',
          '• Monitor cabin altitude: should be climbing slowly (300–500 ft/min); if climbing faster, one pack may be inadequate at current altitude',
          '• If cabin altitude uncontrollable: select MANUAL pressurization and manage outflow valve position',
          '• Descend to lower altitude if pressurization cannot maintain cabin below 10,000 feet'
        ],
        'why' => 'Cabin altitude above 10,000 feet risks hypoxia. Bleed air and pack status must be verified to determine if pressurization can be maintained or descent is required.'
      ],
      [
        'type' => 'limit',
        'title' => 'PRESSURIZATION LIMITS',
        'items' => [
          '• Max cabin altitude: Cannot exceed 10,000 feet (due to oxygen supply limits and hypoxia risk)',
          '• Max positive differential: 7.8 PSI (structural limit)',
          '• Max negative differential: -0.5 PSI (prevents structural inward collapse)',
          '• Cabin climb rate (normal): 300–500 ft/min',
          '• Max cabin climb rate: Should not exceed 1,000 ft/min (indicates pack inadequacy)',
          '• APU bleed available up to certified APU altitude (check AFM)',
          '• Both packs failed: Must descend below 10,000 feet immediately'
        ]
      ],
      [
        'type' => 'eicas',
        'title' => 'PACK OVERHEAT',
        'steps' => [
          '1. EICAS message: PACK OVERHEAT (for affected pack)',
          '2. Close affected pack valve',
          '3. Remaining pack assumes pressurization',
          '4. Land at nearest suitable airfield to investigate'
        ],
        'why' => 'Overheat indicates heat exchanger or turbine malfunction. Continued operation risks fire or structural damage.'
      ],
      [
        'type' => 'normal',
        'title' => 'CABIN PRESSURE CHECK (Normal Operation)',
        'steps' => [
          '1. Verify cabin altitude within ±50 feet of selected altitude (AUTO mode)',
          '2. Verify differential pressure 0 to 7.5 PSI (positive)',
          '3. Verify cabin climb/descent rate 300–500 ft/min (normal)',
          '4. Check cabin temperature at set point (±2°C)',
          '5. Verify outflow valve position responds to mode changes',
          '6. Oxygen system: Pressure above minimum; crew masks accessible'
        ],
        'why' => 'Preflight and periodic checks ensure pressurization system readiness and normal operation.'
      ]
    ],
    'quiz' => [
      [
        'q' => 'At what cabin altitude do passenger oxygen masks deploy automatically?',
        'options' => ['8,000 feet', '10,000 feet', '12,000 feet', '14,000 feet'],
        'correct' => 3,
        'explanation' => 'Passenger oxygen masks are deployed automatically at approximately 14,000 feet cabin altitude (dual sensing for safety). Crew oxygen is manually pulled or available on demand.'
      ],
      [
        'q' => 'What is the function of the turbine in an air conditioning pack?',
        'options' => [
          'To increase air pressure for duct distribution',
          'To cool air by allowing controlled expansion',
          'To remove moisture from cabin air',
          'To prevent compressor surge'
        ],
        'correct' => 1,
        'explanation' => 'The turbine allows air to expand after being compressed, which causes significant cooling. This is the primary cooling stage in the air cycle machine (ACM). The turbine is mechanically linked to the compressor.'
      ],
      [
        'q' => 'If both air conditioning packs fail, what is the aircraft minimum safe altitude?',
        'options' => ['15,000 feet', '12,000 feet', '10,000 feet', '8,000 feet'],
        'correct' => 2,
        'explanation' => 'With both packs failed, the single outflow valve cannot maintain pressurization above 10,000 feet. The aircraft must descend to and remain below 10,000 feet until repairs are made.'
      ],
      [
        'q' => 'What is the maximum positive differential pressure the Q400 fuselage is certified for?',
        'options' => ['5.0 PSI', '6.5 PSI', '7.8 PSI', '9.0 PSI'],
        'correct' => 2,
        'explanation' => 'The Q400 fuselage is certified for a maximum positive pressure differential of 7.8 PSI. Exceeding this limit risks structural failure. The CPCS maintains operation well below this limit.'
      ],
      [
        'q' => 'In the air conditioning pack, what is the primary function of the primary heat exchanger?',
        'options' => [
          'To increase bleed air pressure',
          'To cool hot bleed air using ram air',
          'To eliminate moisture from air',
          'To regulate pack outlet temperature'
        ],
        'correct' => 1,
        'explanation' => 'The primary heat exchanger cools hot bleed air (300°C+) using ram air from the engine nacelle. It reduces air temperature significantly before the air enters the compressor of the air cycle machine.'
      ],
      [
        'q' => 'What is the normal target cabin altitude during cruise operations?',
        'options' => ['4,000 feet', '6,000 feet', '8,000 feet', '10,000 feet'],
        'correct' => 1,
        'explanation' => 'The CPCS maintains a cabin altitude equivalent of 6,000 feet during cruise. This provides a comfortable environment while ensuring pressurization stays well below the 7.8 PSI structural limit.'
      ],
      [
        'q' => 'Which of the following is NOT a source of bleed air for the Q400?',
        'options' => [
          'Engine 1 5th stage compressor',
          'Engine 2 9th stage compressor',
          'APU compressor',
          'Wing pneumatic ducting'
        ],
        'correct' => 3,
        'explanation' => 'Bleed air is supplied from engine compressor stages (5th and 9th) and from the APU. Wing pneumatic ducting does not supply bleed air; instead, it is a consumer of bleed air for wing anti-ice systems.'
      ],
      [
        'q' => 'If an outflow valve fails and remains stuck open, what mode should be selected?',
        'options' => [
          'AUTO pressurization mode',
          'DUMP pressurization mode',
          'MANUAL pressurization mode',
          'Standby mode'
        ],
        'correct' => 2,
        'explanation' => 'If the outflow valve is mechanically stuck open, the CPCS cannot control pressurization automatically. Switching to MANUAL mode allows the crew to manually modulate the valve position using manual controls to maintain pressure.'
      ]
    ]
  ];
}

// ── ATA36 PNEUMATICS

function ata36_content() {
  return [
    'chapters' => [
      [
        'badge' => 'Introduction',
        'title' => 'The Big Picture',
        'navTitle' => 'Big Picture',
        'subtitle' => 'Why compressed air is vital to every major system',
        'time' => '8 min',
        'objective' => 'After this chapter you will understand what pneumatic (bleed) air is, where it comes from, and why it is essential for air conditioning, pressurization, engine start, and anti-ice protection.',
        'analogy' => [
          'label' => 'The Analogy — Compressed Air as Power',
          'text' => 'Bleed air is like the beating heart of the aircraft—it supplies high-energy compressed air to multiple vital organs. Cut off the supply, and several systems fail simultaneously. The pneumatic system is the distribution network that routes this air to where it is needed.'
        ],
        'body' => '<p>The <strong>pneumatic system</strong> is powered by compressed air (bleed air) tapped from the engine compressors. This high-pressure, high-temperature air is the lifeblood of several critical systems:</p><p><strong>1. Air Conditioning & Pressurization:</strong> Bleed air supplies the air conditioning packs, which cool and condition cabin air and maintain cabin pressure.</p><p><strong>2. Engine Start:</strong> Pneumatic power starts the engines (APU or cross-bleed supplies air to pneumatic starters).</p><p><strong>3. Wing Anti-Ice:</strong> Bleed air flows into the leading edges of wings to prevent ice accumulation (the largest consumer of bleed air).</p><p><strong>4. Hydraulic Pump Drive (indirect):</strong> Some aircraft use bleed air; Q400 uses electric hydraulic pumps, but pneumatics support other systems.</p><p>The bleed air from the engines is high-pressure (50+ PSI) and extremely hot (300°C+). It must be regulated, distributed, and sometimes isolated to prevent overpressure, excessive heat, or loss of critical functions.</p><p><strong>Key Components:</strong> Sources (engine bleeds, APU), isolation valves, cross-bleed valve, pressure reducing valve (PRV), duct overheat detection, and consumer systems (packs, anti-ice, starters).</p>',
        'cards' => [
          [
            'type' => 'amber',
            'head' => '⭐ Bleed Air Fundamentals',
            'table' => [
              'headers' => ['Parameter', 'Value', 'Importance'],
              'rows' => [
                ['Source Pressure (engine)', '50–60 PSI', '<span class="spec-badge must">MUST KNOW</span>'],
                ['Source Temperature', '300°C+', '<span class="spec-badge must">MUST KNOW</span>'],
                ['Regulated Pressure (PRV outlet)', '~45 PSI', '<span class="spec-badge normal">KNOW</span>'],
                ['Max Duct Temp (alarm)', '~200°C', '<span class="spec-badge must">MUST KNOW</span>'],
                ['Wing Anti-Ice Load', 'Largest consumer', '<span class="spec-badge must">MUST KNOW</span>']
              ]
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the primary source of bleed air in the Q400?',
          'options' => [
            'The cargo door air inlet',
            'Engine compressor stages (5th and 9th)',
            'The hydraulic pump discharge',
            'The cabin pressurization exhaust'
          ],
          'correct' => 1,
          'explanation' => 'Bleed air is tapped from the engine compressor at two stages: the 5th stage (lower pressure, ~30 PSI) and 9th stage (higher pressure, ~50+ PSI). The APU can also supply bleed air on ground and at low altitudes.'
        ]
      ],
      [
        'badge' => 'Systems',
        'title' => 'Sources and Valves',
        'navTitle' => 'Sources & Valves',
        'subtitle' => 'Understanding the bleed air network and control',
        'time' => '10 min',
        'objective' => 'You will learn about the bleed air sources, isolation valves, cross-bleed valve, and pressure regulation that govern pneumatic system operation.',
        'analogy' => [
          'label' => 'The Analogy — A Smart Water Distribution System',
          'text' => 'The pneumatic system uses isolation and cross-bleed valves like a smart plumbing network. Isolation valves separate left and right, and the cross-bleed valve allows either side to supply both if necessary. Pressure reducing valves act like regulators to keep pressure safe.'
        ],
        'body' => '<p><strong>Bleed Air Sources:</strong></p><p>The Q400 has two main bleed air sources: Engine 1 and Engine 2 (or APU on ground and during low-altitude flight). Each source is tapped at compressor stages to provide high-pressure, high-temperature air.</p><p><strong>Left Pneumatic System:</strong> Normally fed by Engine 1 bleed air. Contains isolation valve to prevent backflow if Engine 1 fails.</p><p><strong>Right Pneumatic System:</strong> Normally fed by Engine 2 bleed air. Contains isolation valve to prevent backflow if Engine 2 fails.</p><p><strong>Cross-Bleed Valve:</strong> A solenoid-operated valve that connects left and right pneumatic systems. Normally closed. Can be commanded open to allow one engine to supply both sides. Used when one engine is started, or if one bleed source fails.</p><p><strong>APU Bleed (Ground & Low Altitude):</strong> The APU can supply pneumatic air for engine starts and air conditioning packs during ground operation and climb (up to certified APU bleed altitude, typically 25,000 feet). On ground, APU bleed can feed left, right, or cross-bleed valve. In flight, APU bleed is only available below a certain altitude.</p><p><strong>Pressure Reducing Valve (PRV):</strong> Reduces high-pressure bleed air (50+ PSI) from the engine to a regulated pressure (approximately 45 PSI) suitable for downstream consumers. If bleed pressure exceeds limits, the PRV opens to dump excess air overboard or reduce downstream pressure.</p><p><strong>Isolation Valve:</strong> Each pneumatic system (left and right) has an isolation valve downstream of the source. These prevent backflow if one source fails. They can also be closed manually to isolate a system if needed (e.g., for maintenance or if severe overpressure is detected).</p>',
        'cards' => [
          [
            'type' => 'green',
            'head' => 'Bleed Air Source Architecture',
            'steps' => [
              '1. Engine 1 (5th/9th stage) supplies Left Pneumatic System via Isolation Valve',
              '2. Engine 2 (5th/9th stage) supplies Right Pneumatic System via Isolation Valve',
              '3. APU (ground/low alt) supplies cross-bleed valve connection point',
              '4. Cross-Bleed Valve can connect left and right systems (normally closed)',
              '5. Each system includes Pressure Reducing Valve to regulate to ~45 PSI',
              '6. Bleed air then flows to packs, anti-ice, and starters'
            ]
          ],
          [
            'type' => 'blue',
            'head' => 'Valve Functions',
            'table' => [
              'headers' => ['Valve', 'Function', 'Normal State'],
              'rows' => [
                ['Isolation Valve (L&R)', 'Prevents backflow; closes if source fails', 'Open (when source pressurized)'],
                ['Cross-Bleed Valve', 'Connects L & R systems for single-engine operation', 'Closed (auto open for engine start)'],
                ['Pressure Reducing Valve', 'Regulates high pressure to ~45 PSI', 'Modulating (maintains outlet pressure)'],
                ['Pack Valve (L&R)', 'Shutoff valve for each AC pack', 'Open (pilot controlled)']
              ]
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the function of the cross-bleed valve?',
          'options' => [
            'To reduce bleed air pressure to safe levels',
            'To allow one engine to supply bleed air to both pneumatic systems',
            'To prevent overpressure in the aft fuselage',
            'To isolate the APU from the pneumatic system'
          ],
          'correct' => 1,
          'explanation' => 'The cross-bleed valve (normally closed) connects the left and right pneumatic systems. When commanded open, it allows one engine to supply both sides. This is essential for engine starting (one engine running supplies air for the other) and for redundancy if one bleed source fails.'
        ]
      ],
      [
        'badge' => 'Systems',
        'title' => 'Bleed Air Distribution and Load',
        'navTitle' => 'Distribution & Load',
        'subtitle' => 'How bleed air is consumed and why the wing anti-ice system is critical',
        'time' => '9 min',
        'objective' => 'You will understand the consumers of bleed air, how wing anti-ice affects system performance, and the trade-offs between different operational modes.',
        'analogy' => [
          'label' => 'The Analogy — Power Budget',
          'text' => 'Think of bleed air supply as your monthly electrical power budget. Wing anti-ice is the biggest consumer. If anti-ice is on, less air is available for air conditioning, and cabin cooling performance suffers. Managing the load is essential.'
        ],
        'body' => '<p><strong>Primary Consumers of Bleed Air:</strong></p><p><strong>1. Wing Anti-Ice (LAI):</strong> Hot bleed air is ducted into the leading edges of both wings to prevent ice accumulation. This is a <strong>continuous, high-demand consumer</strong> when icing conditions are encountered. Anti-ice is controlled by the pilot and typically turned on when visible moisture and low temperature are present.</p><p><strong>2. Air Conditioning Packs:</strong> Both packs require bleed air to operate. During cruise with anti-ice on, the air conditioning performance may degrade if insufficient bleed air is available after anti-ice demand is satisfied.</p><p><strong>3. Engine Start:</strong> Pneumatic starters require high-pressure bleed air from APU (ground) or from the running engine (cross-bleed). Engine start is a brief, high-power event.</p><p><strong>4. Pressurization (indirect):</strong> Packs pressurize the cabin; pressurization depends on pack operation, which requires bleed air.</p><p><strong>Bleed Air Load Management:</strong> During flight, the pilot must manage the load on the bleed air system. If visible icing and low temperature occur at a high altitude where air conditioning is also needed, both anti-ice and packs will compete for bleed air. The system is designed to support both, but in extreme scenarios (e.g., anti-ice at 25,000 feet with hot day), cabin cooling may be insufficient.</p><p><strong>Wing Anti-Ice Control:</strong> Anti-ice is pilot-selected. It is turned on when:</p><p>1. Visible moisture is present (clouds, rain, wet air)</p><p>2. OAT is below +10°C</p><p>3. Ice is visible on windscreen or wings</p><p>Anti-ice uses significant bleed air (several PSI flow) and can reduce available bleed air for packs by 10–20 percentage points. On hot days with high cooling demand, this trade-off is noticeable.</p><p><strong>Single-Engine Bleed Operation:</strong> If one engine fails or is shut down, that bleed source is lost. The remaining engine bleed (via cross-bleed) must supply all pneumatic consumers. This is feasible for air conditioning and normal operations, but wing anti-ice operation may need to be limited or managed carefully to avoid excessive load on the surviving engine bleed.</p>',
        'cards' => [
          [
            'type' => 'red',
            'head' => '⚠ Bleed Air Load Priority',
            'list' => [
              '1. <strong>Engine Start (priority):</strong> Required for flight; APU or cross-bleed supplies',
              '2. <strong>Air Conditioning:</strong> Required for pressurization and cabin comfort',
              '3. <strong>Wing Anti-Ice:</strong> Required in icing conditions but competing load on packs',
              '4. <strong>Other:</strong> Auxiliary systems (landing gear actuators, cabin heat, etc.) have lower priority'
            ]
          ],
          [
            'type' => 'amber',
            'head' => 'Wing Anti-Ice Activation Criteria',
            'table' => [
              'headers' => ['Condition', 'Action', 'Effect'],
              'rows' => [
                ['Visible moisture + OAT ≤ +10°C', 'Turn anti-ice ON', 'Bleed load increases ~10–20%'],
                ['Visible moisture + OAT > +10°C', 'Leave anti-ice OFF', 'No risk; no bleed consumption'],
                ['Dry air (no visible moisture)', 'Keep anti-ice OFF', 'Unnecessary bleed load'],
                ['High altitude + hot day + ice detected', 'Anti-ice ON, limit cooling demand', 'Packs may degrade performance']
              ]
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the largest consumer of bleed air in the Q400 pneumatic system?',
          'options' => [
            'The air conditioning packs',
            'The engine start system',
            'The wing anti-ice system',
            'The pressurization dump valve'
          ],
          'correct' => 2,
          'explanation' => 'Wing anti-ice (LAI) is the largest pneumatic consumer. When activated in icing conditions, it diverts a significant portion of available bleed air to heat the wing leading edges. This can reduce bleed air available for other consumers, including air conditioning.'
        ]
      ],
      [
        'badge' => 'Systems',
        'title' => 'Abnormal Operations',
        'navTitle' => 'Abnormal Operations',
        'subtitle' => 'Recognizing and managing bleed air system failures',
        'time' => '10 min',
        'objective' => 'You will recognize symptoms of bleed duct overheat, bleed air pressure loss, and learn the correct procedures for single-engine bleed operations.',
        'analogy' => [
          'label' => 'The Analogy — A Leak in the Hose',
          'text' => 'A bleed duct leak or overheat is like a ruptured water hose. If the leak is small, you notice reduced flow. If it overheats, you know something is wrong and you must stop using that section of hose. The pneumatic system has alarms to detect this.'
        ],
        'body' => '<p><strong>Bleed Duct Overheat</strong> is a common pneumatic system abnormality. The bleed air ducts are insulated, but if insulation fails or a duct develops a small rupture, bleed air escapes and heats surrounding structure. Temperature sensors on the ducts detect this condition.</p><p><strong>Symptoms:</strong> EICAS caution "DUCT OVERHEAT L" or "DUCT OVERHEAT R"—indicates the left or right bleed duct is overheating. Ground may also report smoke or smell near the affected area.</p><p><strong>Cause:</strong> Duct insulation failure, duct rupture, bleed air pressure regulator malfunction, or heat exchanger leak.</p><p><strong>Pilot Response:</strong> Close the affected bleed source immediately (close the isolation valve or pack valve, depending on which system). If DUCT OVERHEAT L: close Engine 1 bleed or APU bleed (on ground). If DUCT OVERHEAT R: close Engine 2 bleed. Cross-bleed should automatically open (if auto-enabled) to supply the other side from the remaining engine. Continue flight on remaining bleed source. Land at nearest suitable airfield to inspect.</p><p><strong>Bleed Air Pressure Loss</strong> occurs when bleed air supply is lost or severely reduced. This can happen if:</p><p>1. One or both engine bleeds fail (compressor damage, bleed valve stuck)</p><p>2. Both pack valves are closed by pilot action</p><p>3. APU fails during ground operation</p><p><strong>Symptoms:</strong> EICAS caution "PACK OFF" or low pack inlet pressure. Air conditioning packs operate sluggishly or not at all. Cabin altitude climbs if at altitude.</p><p><strong>Pilot Response:</strong> Verify pack valve positions (should be open). Verify bleed source available. Check whether both engines are running (if one failed, cross-bleed can supply from the other). If both bleed sources are out and aircraft is airborne at altitude, initiate descent below 10,000 feet immediately.</p><p><strong>Single-Engine Bleed Operations</strong> occur when one engine is shut down (due to failure, shutdown, or single-engine landing). In this case, the remaining engine\'s bleed supplies all pneumatic consumers via the cross-bleed valve.</p><p><strong>Procedure:</strong> If Engine 1 fails, Engine 2 bleed via cross-bleed supplies both packs and anti-ice. If Engine 2 fails, Engine 1 bleed supplies both. Performance is adequate for normal operations. Anti-ice operation may need to be managed (limit time or load) to avoid excessive engine bleed demand.</p>',
        'failures' => [
          [
            'sev' => 'high',
            'name' => 'Bleed Duct Overheat (Left)',
            'eicas' => 'DUCT OVERHEAT L (caution)',
            'what' => 'Duct insulation failure, duct rupture, or bleed air pressure regulator malfunction on left pneumatic system',
            'auto' => 'Engine 1 bleed is isolated; cross-bleed opens automatically (if configured)',
            'pilot' => 'Close affected bleed source (Engine 1 isolation valve or pack valve); verify cross-bleed open; continue flight on Engine 2 bleed via cross-bleed; land to inspect duct',
            'note' => 'Do not ignore overheat warning—continued operation risks fire. Close bleed source promptly to stop leak and heat source.',
            'noteType' => 'red',
            'noteHead' => 'CLOSE BLEED'
          ],
          [
            'sev' => 'high',
            'name' => 'Bleed Duct Overheat (Right)',
            'eicas' => 'DUCT OVERHEAT R (caution)',
            'what' => 'Duct insulation failure, duct rupture, or bleed air pressure regulator malfunction on right pneumatic system',
            'auto' => 'Engine 2 bleed is isolated; cross-bleed opens automatically (if configured)',
            'pilot' => 'Close affected bleed source (Engine 2 isolation valve or pack valve); verify cross-bleed open; continue flight on Engine 1 bleed via cross-bleed; land to inspect duct',
            'note' => 'Do not ignore overheat warning. Close bleed source promptly. Fire risk if overheat continues.',
            'noteType' => 'red',
            'noteHead' => 'CLOSE BLEED'
          ],
          [
            'sev' => 'high',
            'name' => 'Loss of All Bleed Air',
            'eicas' => 'PACK OFF (both) or no pack pressure indication',
            'what' => 'Both engine bleeds failed, both pack valves closed, or APU bleed lost on ground',
            'auto' => 'Air conditioning packs cannot operate; pressurization cannot be maintained',
            'pilot' => 'If airborne: declare emergency and descend below 10,000 feet immediately. If ground: close all packs and attempt to use ground air cart if available for engine start.',
            'note' => 'Both packs out is a critical emergency at altitude. Immediate descent required.',
            'noteType' => 'red',
            'noteHead' => 'EMERGENCY DESCENT'
          ],
          [
            'sev' => 'med',
            'name' => 'Single Engine Bleed Loss (In-Flight)',
            'eicas' => 'PACK OFF (L or R) or indication of one bleed source failure',
            'what' => 'One engine bleed source failed or isolated; other engine bleed remains available',
            'auto' => 'Remaining engine bleed supplies both sides via cross-bleed (auto-open or pilot command)',
            'pilot' => 'Verify remaining engine is operating normally; confirm cross-bleed open; monitor pack pressure (should be normal); continue flight; land at suitable airfield to investigate',
            'note' => 'Single-engine bleed is designed-for condition. Pressurization and air conditioning can be maintained on one engine bleed. Manage wing anti-ice carefully to avoid excessive load.',
            'noteType' => 'amber',
            'noteHead' => 'DEGRADED REDUNDANCY'
          ],
          [
            'sev' => 'med',
            'name' => 'High Bleed Air Pressure',
            'eicas' => 'BLEED PRESS HIGH (if monitored) or caution',
            'what' => 'Pressure reducing valve (PRV) malfunction; bleed air pressure exceeds normal limits',
            'auto' => 'Relief valve (if equipped) may open to prevent duct overpressure',
            'pilot' => 'Monitor duct temperatures for overheat indication; consider closing affected bleed source if temp rises; land to inspect PRV and relief valve',
            'note' => 'High pressure can damage downstream components. Duct overheat may follow if relief is ineffective.',
            'noteType' => 'amber',
            'noteHead' => 'MONITOR TEMP'
          ]
        ],
        'quiz' => [
          'q' => 'If DUCT OVERHEAT L is indicated, what is the primary pilot action?',
          'options' => [
            'Open the cross-bleed valve',
            'Close the affected bleed source (Engine 1 isolation or pack valve)',
            'Increase cabin altitude to reduce duct temperature',
            'Switch to APU bleed immediately'
          ],
          'correct' => 1,
          'explanation' => 'Duct overheat indicates a duct rupture or insulation failure with bleed air leaking and heating surrounding structure. Closing the affected bleed source stops the leak and the heat source. Cross-bleed will open automatically (if configured) to supply from the other engine.'
        ]
      ],
      [
        'badge' => 'Operations',
        'title' => 'Limitations and Operating Rules',
        'navTitle' => 'Limitations',
        'subtitle' => 'Understanding the boundaries of pneumatic system operation',
        'time' => '7 min',
        'objective' => 'You will know the operational limits of the Q400 pneumatic system, including APU bleed altitude limits, single-engine restrictions, and bleed air pressure constraints.',
        'analogy' => [
          'label' => 'The Analogy — Staying Within Your Engine Limits',
          'text' => 'Just as you cannot demand full bleed air at 45,000 feet, the pneumatic system has a ceiling. Operating beyond limits risks system failure or inadequate performance. Know your limits and operate within them.'
        ],
        'body' => '<p><strong>APU Bleed Air Altitude Limit:</strong> The APU can supply bleed air on the ground and during flight, but only up to a certified maximum altitude (typically 25,000 feet for the Q400). Above this altitude, APU bleed is not available. This means on-ground APU-powered air conditioning works fine, but in flight at high altitude, both engines must be available to supply bleed for packs and anti-ice.</p><p><strong>Bleed Air Pressure Limits:</strong> Normal bleed pressure from engines is 50–60 PSI. The PRV reduces this to approximately 45 PSI. If bleed pressure drops below ~30 PSI, pack operation becomes marginal. If it exceeds ~55 PSI, relief valves open to dump excess air.</p><p><strong>Single-Engine Bleed Operation:</strong> The Q400 is designed to operate on single-engine bleed. One engine\'s bleed supply (via cross-bleed) can support both air conditioning packs and pressurization. However, simultaneous wing anti-ice and maximum cooling demand on a single engine bleed may result in degraded pack performance. The crew should be prepared to manage anti-ice activation and altitude limits if operating on single-engine bleed.</p><p><strong>No Bleed Air Available (Both Engines Failed):</strong> If both engine bleeds are lost (both engines inoperative, which is extremely unlikely), no bleed air is available. Pressurization cannot be maintained above 10,000 feet. Immediate descent below 10,000 feet is mandatory.</p><p><strong>APU Bleed During Engine Start:</strong> On the ground, the APU bleed supplies air to the pneumatic starter for engine start. The cross-bleed valve opens to allow APU air to reach the engine starter motor. Once the engine is running, its own bleed becomes available, and cross-bleed can be closed.</p><p><strong>Maximum Bleed Duct Temperature:</strong> Bleed air ducts have insulation rated for continuous operation up to approximately 200°C. Above this, insulation integrity is compromised and overheat warnings are triggered. Overheat must be treated as an immediate threat.</p>',
        'cards' => [
          [
            'type' => 'blue',
            'head' => 'Pneumatic System Limits',
            'table' => [
              'headers' => ['Limit', 'Value', 'Consequence if Exceeded'],
              'rows' => [
                ['APU bleed altitude ceiling', '~25,000 ft', 'APU bleed not available; both engines must supply'],
                ['Normal bleed pressure', '45 PSI (regulated)', 'Pressure relief opens if >55 PSI; packs marginal if <30 PSI'],
                ['Max duct temperature', '~200°C', 'Overheat alarm; duct integrity compromised'],
                ['Both bleed sources out', 'Incompatible with altitude', 'Descend below 10,000 ft immediately'],
                ['Max cabin altitude (no packs)', '10,000 ft', 'Hypoxia risk; oxygen masks deploy']
              ]
            ]
          ],
          [
            'type' => 'green',
            'head' => 'Single-Engine Bleed Operation (Designed Capability)',
            'list' => [
              'One engine bleed via cross-bleed can supply both packs',
              'Pressurization can be maintained during single-engine operations',
              'Wing anti-ice can be operated but may reduce bleed available for cooling',
              'On hot days with high cooling demand, cabin temperature may be difficult to control',
              'Not a permanent limit—crews can operate single-engine bleed as long as pressurization is maintained'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the maximum altitude at which APU bleed air is available in the Q400?',
          'options' => ['15,000 feet', '20,000 feet', '25,000 feet', '30,000 feet'],
          'correct' => 2,
          'explanation' => 'APU bleed is limited to approximately 25,000 feet (or specific value per AFM). Above this altitude, APU bleed is not available, and the aircraft must rely on engine bleeds. On the ground, APU bleed is available unrestricted.'
        ]
      ]
    ],
    'qrh' => [
      [
        'type' => 'memory',
        'title' => 'DUCT OVERHEAT',
        'steps' => [
          '1. Identify affected duct: DUCT OVERHEAT L or R on EICAS',
          '2. Close affected bleed source immediately:',
          '   - If left: close Engine 1 isolation valve (or pack valve if isolation unavailable)',
          '   - If right: close Engine 2 isolation valve (or pack valve if isolation unavailable)',
          '3. Verify cross-bleed open (or command open)',
          '4. Confirm remaining bleed source supplies both packs',
          '5. Monitor duct temperature for return to normal',
          '6. Land at nearest suitable airfield',
          '7. Ground inspection of affected duct required'
        ],
        'why' => 'Bleed duct overheat indicates rupture or insulation failure. Continued bleed air flow risks fire. Closing the affected source stops the leak. Cross-bleed allows the remaining engine to supply both packs.'
      ],
      [
        'type' => 'abnormal',
        'title' => 'LOSS OF BLEED AIR',
        'eicasMsg' => 'PACK OFF (L or R) or both',
        'items' => [
          '• Verify pack valve position: should be OPEN',
          '• Confirm bleed air source available:',
          '  - Check that Engine 1 is running (for left pack)',
          '  - Check that Engine 2 is running (for right pack)',
          '  - Check cross-bleed valve position (should be OPEN if one engine out)',
          '• If bleed pressure gauge available: verify ~45 PSI at pack inlet',
          '• If one pack is out and other is operating: continue flight; land to investigate failed pack',
          '• If both packs are out:',
          '  - If airborne at altitude: declare emergency and descend below 10,000 feet immediately',
          '  - If on ground: attempt to restore bleed source (APU start, engine restart, ground air cart)'
        ],
        'why' => 'Bleed air supplies the air conditioning packs. Without it, pressurization cannot be maintained. Bleed source loss or pack valve closure are primary causes. At altitude, loss of both packs is a critical emergency.'
      ],
      [
        'type' => 'limit',
        'title' => 'BLEED AIR SYSTEM LIMITATIONS',
        'items' => [
          '• APU bleed available up to certified altitude (~25,000 feet); above this, engine bleed only',
          '• Normal bleed pressure: 45 PSI (regulated from engine 50–60 PSI)',
          '• Maximum bleed duct temperature: ~200°C (alarm if exceeded)',
          '• Single-engine bleed operations: Designed for; one engine can supply both packs',
          '• Both bleed sources lost: Must descend below 10,000 feet immediately',
          '• Bleed air is mandatory for engine start (APU bleed on ground, cross-bleed for second engine)',
          '• Wing anti-ice is largest consumer; reduces bleed available for air conditioning if activated',
          '• Pressurization depends on bleed air; loss of bleed = loss of pressurization'
        ]
      ],
      [
        'type' => 'normal',
        'title' => 'SINGLE-ENGINE BLEED OPERATIONS',
        'steps' => [
          '1. If Engine 1 fails: cross-bleed open, Engine 2 bleed supplies both packs',
          '2. If Engine 2 fails: cross-bleed open, Engine 1 bleed supplies both packs',
          '3. Monitor pack inlet pressures: should be normal (~45 PSI)',
          '4. Monitor cabin altitude: should climb/descend at normal rate',
          '5. Wing anti-ice: can be operated but manage carefully to avoid excessive load on remaining engine bleed',
          '6. Continue flight; land at suitable airfield to investigate failed engine',
          '7. Single-engine bleed is a designed capability; pressurization is adequate'
        ],
        'why' => 'One engine bleed can supply both air conditioning packs via cross-bleed. This is a designed redundancy. Monitor pressurization and pack performance; manage anti-ice load.'
      ],
      [
        'type' => 'eicas',
        'title' => 'PACK PRESSURE LOW',
        'steps' => [
          '1. EICAS message: PACK PRESS LOW (L or R)',
          '2. Verify pack valve is OPEN',
          '3. Confirm bleed source is available (engine running, APU operating, or cross-bleed open)',
          '4. If bleed pressure is below ~30 PSI: pack outlet flow may be insufficient; check inlet pressure gauge',
          '5. If pressure does not improve: pack may be internally failed; consider closing that pack valve and relying on other pack',
          '6. Monitor cabin altitude: should be controllable on remaining pack',
          '7. Land to investigate'
        ],
        'why' => 'Low pack pressure indicates inadequate bleed air supply or internal pack failure. Verify bleed source and pressure gauge before assuming pack failure. On remaining pack, pressurization is still possible but performance may be degraded.'
      ],
      [
        'type' => 'normal',
        'title' => 'WING ANTI-ICE (LAI) OPERATION',
        'steps' => [
          '1. Wing anti-ice (LAI) ON when: visible moisture present + OAT ≤ +10°C',
          '2. LAI ON drains ~10–20% of available bleed air',
          '3. If cooling demand is high (hot day, high altitude): air conditioning performance may degrade with LAI on',
          '4. Monitor cabin temperature set point and pack flow',
          '5. LAI can be toggled (on/off) to manage bleed load during climb (e.g., on during cloud passage, off when clear)',
          '6. Single-engine bleed: manage LAI time to avoid excessive load on surviving engine bleed',
          '7. Continuous monitoring of pressurization is required during high anti-ice demand'
        ],
        'why' => 'Wing anti-ice is the largest bleed consumer. Balancing it with air conditioning demand is necessary to maintain system performance, especially at altitude or on a single engine bleed.'
      ]
    ],
    'quiz' => [
      [
        'q' => 'At what altitude does APU bleed air become unavailable?',
        'options' => ['15,000 feet', '20,000 feet', '25,000 feet', '30,000 feet'],
        'correct' => 2,
        'explanation' => 'APU bleed air has an altitude ceiling of approximately 25,000 feet (per AFM). Above this, only engine bleed is available. This is why high-altitude operations depend on both engines being available.'
      ],
      [
        'q' => 'What is the largest consumer of bleed air in the Q400?',
        'options' => [
          'Engine starters',
          'Air conditioning packs',
          'Wing anti-ice system',
          'Cabin humidity control'
        ],
        'correct' => 2,
        'explanation' => 'Wing anti-ice (LAI) is the largest consumer of bleed air. When activated in icing conditions, it diverts significant pneumatic flow to heat the wing leading edges, reducing bleed available for other systems like air conditioning.'
      ],
      [
        'q' => 'What is the normal regulated bleed air pressure after the PRV?',
        'options' => ['25 PSI', '35 PSI', '45 PSI', '55 PSI'],
        'correct' => 2,
        'explanation' => 'The Pressure Reducing Valve (PRV) reduces high-pressure engine bleed air (50–60 PSI) to approximately 45 PSI, which is safe for downstream consumers (packs, anti-ice, starters).'
      ],
      [
        'q' => 'If DUCT OVERHEAT L is indicated, what is the primary immediate action?',
        'options' => [
          'Open the cross-bleed valve',
          'Increase cabin altitude to cool the duct',
          'Close the affected bleed source (Engine 1 isolation valve)',
          'Reduce engine power to minimum'
        ],
        'correct' => 2,
        'explanation' => 'Duct overheat indicates a duct rupture or insulation failure. The primary action is to close the affected bleed source to stop the leak and the heat source. Cross-bleed will provide bleed from the other engine.'
      ],
      [
        'q' => 'Can the Q400 operate on single-engine bleed air?',
        'options' => [
          'No, both engines must supply bleed air',
          'Yes, but only on the ground',
          'Yes, in flight, via cross-bleed valve',
          'Only during descent below 10,000 feet'
        ],
        'correct' => 2,
        'explanation' => 'Yes, the Q400 is designed to operate on single-engine bleed via the cross-bleed valve. One engine bleed can supply both air conditioning packs and maintain pressurization. Anti-ice operation should be managed carefully to avoid excessive load.'
      ],
      [
        'q' => 'What happens if both engine bleeds are lost at 20,000 feet?',
        'options' => [
          'Pressurization is maintained by the outflow valve alone',
          'Immediate descent below 10,000 feet is required',
          'The APU automatically supplies bleed air',
          'Cabin pressurization will degrade slowly over 30 minutes'
        ],
        'correct' => 1,
        'explanation' => 'If both bleed sources are lost, air conditioning packs cannot operate, and pressurization cannot be maintained above 10,000 feet. This is a critical emergency requiring immediate descent below 10,000 feet.'
      ],
      [
        'q' => 'What is the function of the cross-bleed valve in the Q400 pneumatic system?',
        'options' => [
          'To regulate bleed air pressure to 45 PSI',
          'To connect left and right pneumatic systems for single-engine operation',
          'To isolate the APU bleed from the engine bleeds',
          'To prevent overpressure in the aft fuselage'
        ],
        'correct' => 1,
        'explanation' => 'The cross-bleed valve (normally closed) connects the left and right pneumatic systems. When open, it allows one engine\'s bleed to supply both sides. It automatically opens during engine start (one engine supplies air to start the other) and during single-engine bleed operations.'
      ],
      [
        'q' => 'When should wing anti-ice (LAI) be activated?',
        'options' => [
          'Continuously during cruise above 10,000 feet',
          'When visible moisture is present and OAT is ≤ +10°C',
          'Only during approach and landing',
          'Whenever cloud tops are visible'
        ],
        'correct' => 1,
        'explanation' => 'Wing anti-ice should be activated when visible moisture (clouds, rain, wet air) is present AND outside air temperature is at or below +10°C. This prevents ice accumulation on the wing leading edges. Below +10°C, there is no risk of icing even in dry air.'
      ]
    ]
  ];
}

// ── ATA30 ICE & RAIN PROTECTION

function ata30_content() { return [
  'chapters' => [
    [
      'badge'=>'Chapter 1','title'=>'Ice & Rain Protection: The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Why aircraft ice protection systems matter',
      'time'=>'8 min','objective'=>'Understand icing hazards, protection philosophy, and the Q400 approach',
      'analogy'=>['label'=>'The Analogy — Ice Armor','text'=>'<strong>Your aircraft is like a knight in battle:</strong> ice and rain are incoming arrows. Some armor (anti-ice) prevents arrows from hitting you. Other armor (de-ice) lets the arrows hit but breaks them apart before they damage you. The Q400 uses both strategies in different places.'],
      'body'=>'<p>Icing is one of aviation\'s most dangerous weather phenomena. When supercooled water droplets encounter your aircraft\'s leading edges, they freeze on contact, changing aerodynamics and adding weight. Ice protection systems use two fundamental approaches:</p><p><strong>Anti-icing</strong> prevents ice formation by heating surfaces continuously (like engine inlets and windshields). <strong>De-icing</strong> allows ice to form, then sheds it in cycles (like wing boots). The Q400 uses both: pneumatic rubber de-ice boots on wings and tail, hot bleed air anti-ice on engines, and electric heat on windshield and sensors.</p><p>Proper ice protection requires crew discipline. Boots and anti-ice systems must be activated <strong>before significant ice accumulates</strong>—waiting causes ice bridging where ice forms over deflated boots, preventing shedding. Modern research shows early activation is safe; the old 1/4-inch rule was conservative guidance, not a hard requirement.</p><p>Detection is visual and procedural. The Q400 has no automatic ice detector. Crew detect icing by visible moisture plus OAT/SAT at or below +5°C, or by observing ice accumulation on reference surfaces (windshield, wiper arms). In severe icing or Supercooled Large Droplets (SLD), exit immediately and monitor stall margin closely.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⚠️ Icing Conditions Defined','html'=>'<p><strong>Icing exists when:</strong></p><ul><li>Visible moisture (clouds, rain, snow)</li><li>OAT or SAT ≤ +5°C</li></ul><p>If both conditions are true, treat as icing and activate protection.</p>'],
        ['type'=>'red','head'=>'🛑 Severe Icing Threat','list'=>['Supercooled Large Droplets (SLD) exceed Q400 certification','Exit icing immediately if severe','Increase speed, monitor stall margin','Activate all de-ice and anti-ice systems']]
      ],
      'quiz'=>['q'=>'What two fundamental ice protection approaches does the Q400 use?','options'=>['Only anti-ice (heating)','Only de-ice (boots)','Both anti-ice and de-ice in different locations','Neither; Q400 avoids icing'],'correct'=>2,'explanation'=>'The Q400 combines anti-ice (hot bleed on engines, electric heat on windshield/probes) with de-ice boots (pneumatic on wings/tail). This multi-strategy approach provides the best protection against diverse icing scenarios.']
    ],
    [
      'badge'=>'Chapter 2','title'=>'Wing & Stabilizer De-ice Boots','navTitle'=>'De-ice Boots',
      'subtitle'=>'Pneumatic rubber boots that cycle to shed ice',
      'time'=>'10 min','objective'=>'Understand how pneumatic de-ice boots work and proper activation',
      'analogy'=>['label'=>'The Analogy — Inflatable Cracking','text'=>'<strong>Think of ice boots like an inflatable balloon under a sheet:</strong> when you blow up the balloon, the sheet buckles and cracks the ice on top. Deflate, and the cracked ice falls off. Repeat the cycle. That\'s exactly how Q400 boots work—engine bleed air inflates them on demand.'],
      'body'=>'<p>The Q400 is equipped with <strong>inflatable rubber de-ice boots</strong> on the leading edges of both wings and the horizontal stabilizer. These boots are NOT like hot bleed air anti-ice; they use a cyclic inflate-deflate approach powered by engine bleed air.</p><p><strong>How They Work:</strong> When activated by the crew via the ICE PROTECTION panel, pressurized bleed air flows into the boots, inflating them. The expansion cracks ice adhering to the surface. After 6 seconds, a pneumatic timer automatically exhausts the air, and the boots deflate, allowing cracked ice to shed due to aerodynamic forces. This cycle repeats continuously until deactivated or airspeed becomes too low (below 100 knots).</p><p><strong>Activation Protocol:</strong> Boots must be turned ON <strong>before significant ice accumulates</strong>. This is critical. If ice is allowed to build too thick, it can bridge over the deflated boot surfaces, preventing the boots from making contact and breaking the ice. Modern understanding shows <strong>early activation is safe and effective</strong>—activate at first sign of icing, do not wait for accumulation. Boots are most effective when 1/4 to 1/2 inch of ice has formed; this provides good cracking action without bridging risk if activated promptly.</p><p>Ice bridging was once a serious concern with older boot designs, but modern pneumatic systems activate early enough to prevent it. If boots fail or are inoperative, you must <strong>avoid icing conditions entirely</strong>—exit or descend to warmer air.</p><p><strong>Manual Control:</strong> Boot activation is entirely crew-controlled; there is no automatic boot deployment. Pilots decide when icing conditions exist and manually select the boots ON. This gives crews flexibility but demands vigilance.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ Boot Activation Rules','table'=>['headers'=>['Scenario','Action','Timing'],
          'rows'=>[['Icing encountered','Turn boots ON','Immediately, do not wait for ice buildup'],['Ice accumulating slowly','Boots ON','Prompt, before significant buildup'],['Airspeed < 100 knots','Boots may not shed','Increase speed or continue cycling'],['Boots fail','Divert','Cannot safely continue in icing']]]],
        ['type'=>'blue','head'=>'🔧 Boot System Components','steps'=>['Engine bleed air feeds pneumatic panel','Pilot selects ICE PROTECTION (boots ON)','Pneumatic timer cycles inflate (6 sec) → deflate','Boots inflate, crack ice, deflate, ice sheds','Cycle repeats until boots turned OFF or airspeed too low']]
      ],
      'failures'=>[['sev'=>'high','name'=>'Boot System Failure','eicas'=>'ICE PROT SYS FAIL (if detection available)','what'=>'One or both de-ice boots become inoperative','auto'=>'No automatic recovery','pilot'=>'Check ICE PROTECTION switches; if boots unresponsive, exit icing','note'=>'Loss of both boots = must avoid icing','noteType'=>'red','noteHead'=>'Critical Loss']],
      'quiz'=>['q'=>'What is the correct procedure if you encounter icing and de-ice boots are available?','options'=>['Wait 2–3 minutes for ice to accumulate, then activate boots','Activate boots immediately upon detection of icing','Activate boots only if ice is visible on windshield','Never activate boots; they cause worse icing'],'correct'=>1,'explanation'=>'Activate boots immediately when icing is detected. Early activation prevents ice bridging and ensures effective shedding. Waiting for ice to accumulate is outdated practice and increases risk of bridging.']
    ],
    [
      'badge'=>'Chapter 3','title'=>'Engine & Windshield Anti-ice','navTitle'=>'Anti-ice Systems',
      'subtitle'=>'Continuous heating to prevent ice formation',
      'time'=>'10 min','objective'=>'Understand engine inlet anti-ice and windshield heat operation',
      'analogy'=>['label'=>'The Analogy — Always-On Heating','text'=>'<strong>Unlike boots that cycle on/off, anti-ice is like leaving the heater running in your car:</strong> it heats continuously so ice never forms in the first place. The Q400 heats engine inlets with bleed air and the windshield with electricity to prevent ice buildup before it starts.'],
      'body'=>'<p><strong>Engine Inlet Anti-ice:</strong> The Q400 uses hot engine bleed air to keep the engine inlet guide vanes warm, preventing ice from forming on them. This is <strong>continuous anti-icing</strong>, not cyclic de-icing. Each engine has an ICE ANNUNCIATOR switch (ENG 1 ICE and ENG 2 ICE) on the flight deck. Pilots must turn these switches ON before or immediately upon entering icing conditions.</p><p>Why is engine inlet protection critical? Ice can accumulate on the inlet, reducing airflow and causing engine surge, compressor stall, or power loss. By keeping the inlet warm with bleed air, ice never forms, and the engine operates normally. Bleed air flow is modulated by the pneumatic system and controlled by temperature sensors; this is largely automatic once the switch is set to ON.</p><p><strong>Windshield Heat:</strong> The Q400 windshield is equipped with electric heating elements behind the glass. Unlike engines (which use bleed air), the windshield uses electrical power. Three heat settings are available: LOW, NORMAL, and HIGH. In light icing, LOW or NORMAL is sufficient. In moderate to heavy icing or rain, HIGH is appropriate. <strong>Side windows do NOT have heat</strong>—they are non-heated transparent panels. Wipers help clear rain; they do not clear ice effectively, so heat activation prevents ice from forming in the first place.</p><p><strong>Manual Control:</strong> Both engine anti-ice and windshield heat are manually selected by the crew. They are not automatic. Pilots must recognize icing conditions and activate these systems proactively. This is a key point: <strong>you must turn anti-ice ON; it will not activate itself.</strong></p><p>If engine anti-ice fails (inoperative switch or blocked bleed air), the engine may ice up in icing conditions. If windshield heat fails, visibility degradation may force a descent or diversion. These are serious failures that limit flight into icing conditions.</p>',
      'cards'=>[
        ['type'=>'green','head'=>'✅ Engine Anti-ice Activation','steps'=>['Enter icing conditions (or forecast icing ahead)','Select ENG 1 ICE switch to ON','Select ENG 2 ICE switch to ON','Monitor engine parameters for normal operation','Leave switches ON until exiting icing']],
        ['type'=>'amber','head'=>'⭐ Windshield Heat Levels','table'=>['headers'=>['Condition','Heat Setting','Notes'],
          'rows'=>[['Light icing or rain','LOW or NORMAL','Sufficient for most conditions'],['Moderate to heavy icing','HIGH','Maximum heating; increases electrical load'],['Heavy rain','NORMAL to HIGH','Depends on accumulation rate'],['No precipitation','OFF','Saves electrical power']]]]
      ],
      'quiz'=>['q'=>'When should engine inlet anti-ice be activated?','options'=>['Only if visible ice is on the engine cowling','Before or immediately upon entering icing conditions','After the engine has stalled once','Only at night'],'correct'=>1,'explanation'=>'Engine anti-ice must be activated proactively before or immediately when entering icing. It prevents ice from forming on inlet guide vanes. Waiting until visible ice forms risks engine surge or stall.']
    ],
    [
      'badge'=>'Chapter 4','title'=>'Probe Heat & Icing Detection','navTitle'=>'Probes & Detection',
      'subtitle'=>'Keeping air data sensors functional and detecting icing visually',
      'time'=>'9 min','objective'=>'Understand automatic probe heating and manual icing detection methods',
      'analogy'=>['label'=>'The Analogy — Temperature Sensors','text'=>'<strong>Pitot, static, and AOA probes are like thermometers sticking out in the wind:</strong> ice can block them and give false readings. Heating them prevents ice buildup so your air data stays accurate. The Q400 heats these probes automatically whenever engines run.'],
      'body'=>'<p><strong>Automatic Probe Heat:</strong> The Q400 has several external probes: Pitot tube, static port, Angle of Attack (AOA) vane, and Total Air Temperature (TAT) probe. All are equipped with electric heating elements. Critically, <strong>probe heat is AUTOMATIC</strong> and activates whenever at least one engine is running. You do not need to manually enable it. This protects airspeed, altitude, attitude, and stall warning data from icing.</p><p>If probe heat is lost or failed, air data may become unreliable, and stall warning may not function properly. A failed Pitot causes false airspeed indications. A failed AOA probe affects stall margin calculation. These are significant safety concerns and warrant immediate action: descend to warmer air, declare an emergency if necessary, and land as soon as possible.</p><p><strong>Manual Icing Detection:</strong> The Q400 does NOT have an automatic icing detector (some larger aircraft have them; the Q400 does not). Detection is <strong>visual and procedural</strong>. Crew detect icing by observing:</p><ul><li><strong>Visible moisture</strong> (clouds, rain, snow) and <strong>OAT/SAT ≤ +5°C</strong>—textbook icing condition</li><li><strong>Ice accumulation on reference surfaces</strong>—windshield, wiper arms, antenna, propeller spinners—visible signs of active icing</li><li><strong>Reduced visibility or surface degradation</strong> not explained by weather alone</li></ul><p>In marginal conditions, activate ice protection early and monitor accumulation. Trust your thermometers and observations. Do not rely on "it doesn\'t look that cold" or "there\'s no visible moisture yet." If OAT is within icing range and moisture is present, treat it as icing.</p><p><strong>Stall Strip Surfaces:</strong> The Q400 wing has passive stall strips (no heating). These aid stall warning by inducing flow separation at high angles of attack. Ice accumulation on stall strips can affect stall margin characteristics but is secondary to main wing protection.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🔥 Probe Heat System','list'=>['Pitot tube: electric heat element','Static port: sometimes open, sometimes heated','AOA (Angle of Attack): electric heat','Total Air Temperature (TAT): electric heat','Activation: AUTOMATIC when any engine running','Loss: Declares unreliable air data—descend and land']],
        ['type'=>'amber','head'=>'⭐ How to Detect Icing','steps'=>['Observe OAT/SAT gauge','Note visible moisture (rain, clouds, snow)','If OAT ≤ +5°C AND moisture present → ICING CONDITIONS','Scan windshield, wiper arms, antenna for ice accumulation','If accumulation observed → activate de-ice boots and anti-ice immediately']]
      ],
      'quiz'=>['q'=>'Probe heat in the Q400 is controlled how?','options'=>['Pilot must switch probe heat ON manually','Probe heat activates automatically when engines run','Probe heat only works above 10,000 feet','Probe heat is not installed on Q400'],'correct'=>1,'explanation'=>'Probe heat is automatic and activates whenever at least one engine is running. Pilots do not control it manually. This ensures air data reliability from startup through flight.']
    ],
    [
      'badge'=>'Chapter 5','title'=>'Abnormals & Ice Protection Limitations','navTitle'=>'Abnormals & Limits',
      'subtitle'=>'Failures, severe icing, and operational boundaries',
      'time'=>'8 min','objective'=>'Recognize failures, execute responses, and understand dispatch limitations',
      'analogy'=>['label'=>'The Analogy — Armor Breach','text'=>'<strong>If your armor is damaged, you cannot fight the same battle:</strong> loss of de-ice or anti-ice systems means you cannot safely enter icing. Stay away from icing, or land and repair.'],
      'body'=>'<p><strong>Severe Icing Response:</strong> If you encounter severe icing (unusually large droplets, rapid accumulation, or wing leading edge icing despite boots ON and anti-ice ON), this indicates conditions beyond Q400 certification or Supercooled Large Droplets (SLD). <strong>Exit icing immediately</strong>: climb, descend, or turn to warmer air. Increase airspeed to reduce lifting forces and stall margin margin burden. Do not attempt to penetrate severe icing; exit laterally or vertically. Monitor stall margin—ice adds weight and changes aerodynamics, increasing stall speed. Maintain airspeed margin.</p><p><strong>Boot Failure:</strong> If boots fail in flight, check switches and electrical supply. If unresponsive, wings are unprotected. If light icing is present and you are near destination, maintain speed and monitor carefully. If icing is moderate or heavy, exit icing conditions immediately by descent or turn. Land as soon as possible and do not dispatch until boots are repaired.</p><p><strong>Anti-ice Failure (Engine or Windshield):</strong> Loss of engine anti-ice means inlet can ice; loss of windshield heat means visibility may degrade. Either failure limits or prohibits flight into icing. If anti-ice fails, exit icing, descend, and divert to maintenance facility.</p><p><strong>Probe Heat Failure:</strong> If air data becomes unreliable (erratic airspeed, altitude, or stall warning), probe heating may be lost. Declare an emergency, descend to warmer air (below icing), and land immediately. Air data integrity is essential for safe flight.</p><p><strong>Dispatch Limitations:</strong> The Q400 must NOT be dispatched into forecast icing without all ice protection systems operative:</p><ul><li>De-ice boots: both wings and tail must be functional</li><li>Engine anti-ice: both ENG 1 and ENG 2 switches must be operable</li><li>Windshield heat: must have at least one functional heating element</li><li>Probe heat: automatic system must function (verified by preflight test)</li></ul><p>If any protection system is inoperative, file flight plan to avoid icing (fly above/below freezing layer) or do not dispatch.</p>',
      'failures'=>[
        ['sev'=>'high','name'=>'Severe Icing Encounter','eicas'=>'No specific EICAS; crew detection required','what'=>'Rapid ice accumulation, large droplets, or SLD conditions','auto'=>'No automatic resolution','pilot'=>'Exit icing immediately (climb, descend, or turn); increase speed; monitor stall margin','note'=>'Do not attempt penetration','noteType'=>'red','noteHead'=>'Emergency Action'],
        ['sev'=>'high','name'=>'Both De-ice Boots Fail','eicas'=>'No EICAS; detected by unresponsiveness to switch commands','what'=>'Pneumatic de-ice boot system completely inoperative','auto'=>'No automatic recovery','pilot'=>'If in icing, exit immediately or descend; divert to nearest suitable airport','note'=>'Cannot continue in icing without de-ice','noteType'=>'red','noteHead'=>'Divert Required'],
        ['sev'=>'high','name'=>'Engine Anti-ice System Fail','eicas'=>'ENG 1(2) ANTI-ICE FAIL (if equipped)','what'=>'Engine inlet anti-ice bleed air not available','auto'=>'No automatic recovery','pilot'=>'Exit icing; descend to warmer air; divert for repairs','note'=>'Engine inlet unprotected; cannot enter icing','noteType'=>'red','noteHead'=>'System Failure'],
        ['sev'=>'medium','name'=>'Windshield Heat Fail','eicas'=>'Possible annunciator depending on installation','what'=>'Windshield heating element(s) inoperative','auto'=>'No automatic recovery','pilot'=>'Reduce visibility risk: exit icing if present, or reduce airspeed and increase margin','note'=>'Windshield will ice up in icing; visibility compromised','noteType'=>'amber','noteHead'=>'Degraded System']
      ],
      'quiz'=>['q'=>'You are in light icing and both de-ice boots suddenly fail (no response to switch). What is the correct action?','options'=>['Continue flight and cycle boots every 5 minutes','Exit icing immediately by descent or turn; land ASAP','Increase airspeed to blow ice off','Activate windshield heat to compensate'],'correct'=>1,'explanation'=>'With no de-ice boots, you cannot shed ice. Exit icing immediately (descend below freezing level or turn away). Land at the nearest suitable airport. Do not continue flight in icing without functional de-ice protection.']
    ]
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'SEVERE ICING IN FLIGHT','steps'=>[
      '1. Exit icing immediately — climb, descend, or turn to warmer air',
      '2. Increase airspeed — reduce ice loads and stall margin burden',
      '3. Verify de-ice BOOTS — switch ON (if not already)',
      '4. Verify ENGINE ANTI-ICE — both ENG 1 and ENG 2 ICE switches ON',
      '5. Windshield HEAT — select HIGH',
      '6. Monitor stall margin — watch airspeed and stall warning',
      '7. If conditions persist — declare emergency, prepare to divert'
    ],'why'=>'Severe icing (SLD or beyond certification) requires immediate exit. All protection activated and stall margin maintained prevent secondary failures.'],
    ['type'=>'abnormal','title'=>'ICING IN VMC (Unexpected)','eicasMsg'=>'Visual detection of icing; no single EICAS alert','items'=>[
      'Visible moisture + OAT ≤ +5°C → Icing exists',
      'Activate ICE PROT (de-ice boots) → Select ON',
      'Activate ENG 1 ICE and ENG 2 ICE → Select ON',
      'Windshield HEAT → Select NORMAL or HIGH',
      'Monitor accumulation on windshield and leading edges',
      'If accumulation continues rapidly → Exit icing or increase altitude/descent',
      'If clearing → Continue monitoring; maintain protection activated'
    ],'why'=>'Early detection and activation prevent ice bridging and engine inlet icing. Continuous monitoring allows early exit if conditions worsen.'],
    ['type'=>'limit','title'=>'Ice Protection System Limitations','items'=>[
      'De-ice boots: max effectiveness at 1/4 to 1/2 inch ice; activate EARLY',
      'Do not enter icing with inoperative de-ice boots or engine anti-ice',
      'Windshield heat: cannot clear ice once formed; prevents formation when activated early',
      'Probe heat: automatic but critical; failure requires descent below freezing',
      'Q400 not approved for flight into known severe icing or SLD',
      'Supercooled Large Droplets (SLD): exceed certification; exit immediately',
      'Below 100 knots: de-ice boot shedding effectiveness degrades',
      'Dispatch without all ice protection operative is prohibited in forecast icing']
    ]
  ],
  'quiz' => [
    ['q'=>'The Q400 wing de-ice system uses which method to shed ice?','options'=>['Hot bleed air anti-ice','Electrothermal heating','Pneumatic boots that cycle inflate and deflate','Bouncing wing to crack ice'],'correct'=>2,'explanation'=>'De-ice boots use pneumatic cycling: engine bleed air inflates them (cracking ice), then a timer deflates them (shedding). This is different from anti-ice (continuous heating).'],
    ['q'=>'When must you activate engine inlet anti-ice (ENG ICE switches)?','options'=>['After detecting ice on the engine cowl','Before or immediately upon entering icing conditions','Only if airspeed exceeds 200 knots','Never; it activates automatically'],'correct'=>1,'explanation'=>'Activate engine anti-ice proactively before or immediately when entering icing. It prevents ice formation on inlet guide vanes. Waiting risks engine surge or stall.'],
    ['q'=>'How does the Q400 detect icing conditions? (Select the primary method)','options'=>['Automatic icing detector alarm','Visual observation + OAT/SAT ≤ +5°C + visible moisture','Engine overheat warning','Radio altimeter threshold'],'correct'=>1,'explanation'=>'The Q400 has no automatic icing detector. Crew detect icing by recognizing visible moisture (clouds, rain) AND observing OAT or SAT at or below +5°C. This is the icing condition criterion.'],
    ['q'=>'You are climbing in light rain and OAT is +3°C. Boots are still OFF. What is the correct action?','options'=>['Continue climbing; boots not needed until ice is visible','Activate de-ice boots immediately; you meet icing criteria','Request warmer altitude','Descend below the cloud'],'correct'=>1,'explanation'=>'OAT +3°C and visible moisture (rain) meet the icing condition definition. Activate boots immediately to prevent ice from bridging over deflated boots.'],
    ['q'=>'If de-ice boots become inoperative in flight during icing conditions, what is the primary action?','options'=>['Increase airspeed to blow ice off','Activate engine anti-ice only and continue','Exit icing immediately by descent or turn; land ASAP','Switch to autopilot and monitor'],'correct'=>2,'explanation'=>'Without de-ice boots, you cannot shed ice. Exit icing by descending below the freezing level or turning away. Land at the nearest suitable airport to repair the system.'],
    ['q'=>'Windshield heat operates on which principle?','options'=>['Engine bleed air (same as boots)','Electrical heating elements behind the glass','Friction from high-speed airflow','Passive thermal radiation'],'correct'=>1,'explanation'=>'Windshield uses electric heating elements (not bleed air). Pilots select LOW, NORMAL, or HIGH. Heat prevents ice from forming; it does not clear existing ice effectively.'],
    ['q'=>'How is probe heat controlled in the Q400?','options'=>['Pilot manually selects probe heat ON/OFF on the ICE PROTECTION panel','Automatic: activates whenever any engine is running','Manual: must be selected during preflight','Controlled by autopilot based on altitude'],'correct'=>1,'explanation'=>'Probe heat is automatic and activates whenever at least one engine runs. Pilots do not control it manually. This protects air data integrity throughout the flight.'],
    ['q'=>'You suspect severe icing or Supercooled Large Droplets (SLD). Boots and anti-ice are ON, but ice is still accumulating rapidly. What is the immediate action?','options'=>['Reduce airspeed to minimize wing loads','Exit icing immediately; climb, descend, or turn to warmer air','Increase engine anti-ice to HIGH power','Continue; just maintain stall margin'],'correct'=>1,'explanation'=>'Severe icing or SLD conditions exceed Q400 certification. Exit immediately by any available means (climb, descend, or turn). Maintain airspeed margin and monitor stall warning. Do not attempt to penetrate.']
  ]
]; }

// ── ATA26 FIRE PROTECTION

function ata26_content() { return [
  'chapters' => [
    [
      'badge'=>'Chapter 1','title'=>'Fire Protection: The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Why fire detection and suppression are critical',
      'time'=>'8 min','objective'=>'Understand fire hazards, detection philosophy, and the Q400 approach',
      'analogy'=>['label'=>'The Analogy — Watchdog & Leash','text'=>'<strong>Fire protection has two jobs: a watchdog (detection) and a leash (suppression).</strong> The watchdog alerts you the moment danger is detected. The leash (fire bottles and cutoffs) lets you isolate and extinguish the threat. The Q400 has both: thermistor fire loops detect heat, and Halon bottles extinguish flames.'],
      'body'=>'<p>Aircraft fire is rare but catastrophic. A fire in the engine nacelle, cargo hold, or APU can disable systems, spread rapidly, and render the aircraft unflyable in seconds. Modern aircraft prioritize <strong>early detection</strong> and <strong>rapid suppression</strong>.</p><p>The Q400 uses <strong>thermistor-type fire detection loops</strong> around each engine and nacelle. These loops are essentially temperature-sensitive circuits: when they detect excessive heat, they trigger warnings and arm fire suppression bottles. Detection is fast—within seconds of heat exceeding the fire threshold. Once detected, crews have a few critical seconds to act: pull the FIRE HANDLE to isolate the engine (shutting fuel, hydraulics, electrical, and bleed air), then activate fire bottles to suppress the flames.</p><p><strong>Philosophy: Pull First, Shoot Second.</strong> When a fire is confirmed, the first action is to isolate the engine—pull the FIRE HANDLE. This stops fuel flow (fuel shutoff valve), isolates hydraulic systems, removes electrical power, and stops bleed air. These actions deny the fire its "food." Only after isolation do you discharge the fire bottles (Halon) to extinguish remaining flames and cool the nacelle.</p><p>The Q400 carries two fire suppression bottles per engine (redundancy for safety). Each bottle contains Halon 1301 or equivalent modern agent (HFC-227ea). Bottle discharge is rapid—~1 second per bottle. If the first bottle does not extinguish the fire, immediately discharge the second. If fire persists, land immediately and evacuate.</p><p>Modern fire suppression has shifted from relying solely on bottles to a systems approach: detection → isolation (remove fuel and power) → suppression (bottles). This three-step method is far more effective than bottles alone.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🔥 Fire Detection Philosophy','html'=>'<p><strong>The Q400 uses dual-loop thermistor detection:</strong></p><ul><li>Two separate temperature-sensing loops around each engine/nacelle</li><li>One loop detects fire (red warning + bell + FIRE HANDLE armed)</li><li>Both loops must detect → confirmed fire</li><li>One loop detects → possible fire / fault (amber warning)</li></ul><p>Dual-loop design prevents false positives while ensuring real fires are caught early.</p>'],
        ['type'=>'amber','head'=>'⭐ Fire Response Steps','steps'=>['1. Recognize fire warning (red EICAS, bell, master warning light)','2. Identify affected engine (ENG 1 or ENG 2)','3. Pull FIRE HANDLE (red T-handle) for affected engine','4. Confirm isolation (fuel shutoff, hydraulic isolated, electrical off)','5. Select AGENT 1 switch → discharge first bottle','6. If fire persists → AGENT 2 switch → discharge second bottle','7. If fire still burning → Land immediately, evacuate']]
      ],
      'quiz'=>['q'=>'What is the fundamental philosophy of Q400 fire response?','options'=>['Shoot fire bottles immediately upon detection','Pull first (isolate) then shoot (suppress) with bottles','Descend and hope fire burns out','Fire bottles are discharged automatically'],'correct'=>1,'explanation'=>'The correct approach is "Pull first, shoot second." Pulling the FIRE HANDLE isolates the engine (stops fuel, hydraulics, electrical, bleed), denying the fire its fuel and power. Only then discharge bottles. Isolation is more effective than suppression alone.']
    ],
    [
      'badge'=>'Chapter 2','title'=>'Engine Fire Detection & Response','navTitle'=>'Engine Fire',
      'subtitle'=>'Thermistor loops and FIRE HANDLE operation',
      'time'=>'10 min','objective'=>'Understand engine fire detection and immediate response procedures',
      'analogy'=>['label'=>'The Analogy — Overheat Alarm','text'=>'<strong>A fire detection loop is like a home smoke detector:</strong> it senses dangerous heat and sounds an alarm. The Q400 has two sensitive loops per engine—if one detects fire, you get a warning. If both detect it, the fire is confirmed. Either way, you react immediately.'],
      'body'=>'<p><strong>Thermistor Fire Detection Loops:</strong> The Q400 engine and nacelle area (including wheel well and landing gear compartment in some cases) are equipped with dual thermistor-type fire detection loops. These loops are essentially continuous wires with temperature-sensitive properties. When temperature exceeds a specific threshold (typically around 260°C / 500°F for fire, and lower for overheat warnings), electrical resistance changes, triggering detection circuits.</p><p><strong>Dual-Loop Logic:</strong> Each loop is independent. If <strong>one loop detects fire</strong>, the system outputs an amber or caution indication (possible fire / fault). If <strong>both loops detect fire</strong> simultaneously, the system declares a confirmed fire (red warning, continuous bell, Master Warning light). This redundancy prevents nuisance alerts due to a single sensor failure while ensuring real fires are immediately recognized. In practice, both loops usually detect within seconds of each other during a real fire because both sense the same rising heat.</p><p><strong>Fire Handle (Engine Fire Switch):</strong> Each engine has a red T-shaped FIRE HANDLE on the flight engineer\'s or pilot\'s panel (depending on cockpit design; many modern Q400s have it accessible to both pilots). When pulled, the FIRE HANDLE mechanically and/or electrically triggers a series of automatic isolations:</p><ul><li><strong>Fuel Shutoff Valve:</strong> Closes immediately, stopping fuel flow to the engine</li><li><strong>Hydraulic Shutoff (Engine Bleed Isolation):</strong> Isolates engine bleed air from pneumatic systems</li><li><strong>Electrical Power:</strong> Removes electrical power to the engine (ignition, fuel pump, starters)</li><li><strong>Fire Bottle Arming:</strong> Readies the fire suppression bottle(s) for discharge</li></ul><p>Pulling the FIRE HANDLE does NOT discharge the fire bottle. It only arms it and isolates the engine. This is intentional: after isolation, the crew manually selects AGENT 1 (or AGENT 2) to actually discharge the bottle. This two-step process (mechanical isolation, then manual suppression) ensures deliberate action and prevents accidental bottle discharge.</p><p><strong>Fire Extinguisher System:</strong> The Q400 carries two fire suppression bottles per engine. Each contains Halon 1301 (or equivalent like HFC-227ea, a newer ozone-safe alternative). When the AGENT 1 switch is selected to DISCHARGE, pressurized Halon flows into the engine nacelle. A squib (explosive charge) punctures the bottle\'s seal. The entire discharge takes ~1 second. The Halon floods the nacelle, suppressing combustion and cooling the area.</p><p><strong>If Fire Persists:</strong> If after AGENT 1 discharge the fire warning persists (red light, bell still sounding, or visible fire out window), immediately select AGENT 2 and discharge the second bottle. If the fire is still not out, you must <strong>land immediately</strong> and evacuate. Two bottles should suppress any aircraft fire; if they don\'t, the situation is beyond firefighting.</p><p><strong>Post-Fire Actions:</strong> After fire suppression (successful or attempted), do not restart the engine. Keep engine bleed and electrical off. Descend and land as soon as possible. Monitor engine temperature (if gauge is available) and thermal imaging (if equipped). Be prepared for secondary effects: loss of hydraulic pressure, electrical systems, or pneumatics depending on what systems were fed from the affected engine.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Engine Fire Detection','table'=>['headers'=>['Loop Status','Indication','Meaning'],
          'rows'=>[['One loop detecting','Amber caution','Possible fire; monitor closely','Both loops detecting','Red FIRE warning + Bell','Confirmed fire; execute FIRE HANDLE']]]],
        ['type'=>'amber','head'=>'⭐ FIRE HANDLE Sequence','steps'=>['1. Engine fire detected (red EICAS, bell, Master Warning)','2. Identify affected engine (ENG 1 or ENG 2)','3. Grip red T-shaped FIRE HANDLE (left side for ENG 1, right for ENG 2)','4. Pull handle firmly (mechanical action triggers isolations)','5. Fuel shutoff closes, hydraulic isolates, electrical kills, bottle arms','6. Select AGENT 1 switch → DISCHARGE (manual action)','7. Halon flows into nacelle (~1 second discharge)','8. Monitor for fire extinguishment','9. If fire persists → AGENT 2 switch → DISCHARGE','10. Land ASAP']],
        ['type'=>'red','head'=>'🔥 Fire Suppression Bottles','list'=>['Two bottles per engine (redundancy)','Each bottle: Halon 1301 or HFC-227ea','Bottle discharge time: ~1 second','Squib (explosive) punctures bottle on discharge','First bottle should suppress most fires','Second bottle available if first unsuccessful','Low bottle pressure detected on preflight = bottle expired or squib fired']]
      ],
      'failures'=>[['sev'=>'high','name'=>'Engine Fire (Confirmed)','eicas'=>'ENG 1(2) FIRE (red warning + continuous bell)','what'=>'Fire detected in engine nacelle by dual thermistor loops','auto'=>'Fire bottle automatically armed when FIRE HANDLE is pulled; discharge is manual','pilot'=>'1. Pull FIRE HANDLE 2. Select AGENT 1 → DISCHARGE 3. If fire persists, select AGENT 2 → DISCHARGE 4. Land immediately','note'=>'Do not restart engine; operate remaining engine with caution','noteType'=>'red','noteHead'=>'CRITICAL']]
    ],
    [
      'badge'=>'Chapter 3','title'=>'Fire Suppression System','navTitle'=>'Suppression',
      'subtitle'=>'Halon bottles, discharge mechanism, and system testing',
      'time'=>'9 min','objective'=>'Understand fire suppression hardware and maintenance checks',
      'analogy'=>['label'=>'The Analogy — Fire Extinguisher','text'=>'<strong>Each fire suppression bottle is like a handheld fire extinguisher:</strong> it contains pressurized Halon, has a puncturing mechanism (squib), and delivers the agent into the nacelle. Two per engine means redundancy—if one fails, you have a backup.'],
      'body'=>'<p><strong>Halon 1301 Agent:</strong> Halon 1301 has been the aviation standard for decades. It is a non-corrosive, non-conductive, electrically safe suppression agent that rapidly cools flames and chemically interrupts combustion. Although Halon affects the ozone layer, it remains approved for aviation use because no perfect replacement has been found. Newer alternatives like HFC-227ea (FM-200) are being introduced on some aircraft but are less common on Q400. Regardless of agent type, the principle is the same: pressurize and discharge into the fire volume.</p><p><strong>Bottle Pressure & Maintenance:</strong> Fire suppression bottles are stored at high pressure (typically 360 psi or higher, depending on design). Pressure is maintained by a ground crew and verified during preflight inspection. A low-pressure indication during preflight checkout means:</p><ul><li><strong>Squib has fired (bottle discharged) → </strong>Replace immediately before dispatch</li><li><strong>Seal has leaked → </strong>Bottle integrity compromised; replace</li><li><strong>Pressure check failed → </strong>Maintenance action required; do not dispatch</li></ul><p><strong>Discharge Mechanism (Squib):</strong> When the AGENT switch is selected to DISCHARGE, an electrical signal fires a squib (a small explosive charge) inside the bottle. The squib ruptures the bottle\'s internal seal, allowing pressurized Halon to flow through discharge tubing into the engine nacelle. The entire process is rapid—approximately 1 second for full bottle discharge. Once fired, a squib cannot be re-fired; if discharge is incomplete or unsuccessful, you must rely on the second bottle or land for fire-fighting assistance.</p><p><strong>Discharge Confirmation:</strong> Crew monitoring for fire suppression relies on:</p><ul><li><strong>Fire warning extinguishment</strong> (red light goes out, bell stops)</li><li><strong>Engine temperature stabilization</strong> (if gauge available)</li><li><strong>Cabin crew report</strong> (no visible flames outside windows)</li><li><strong>Absence of continued electrical or mechanical anomalies</strong> (failure of the fire to spread)</li></ul><p>If fire is not suppressed after AGENT 1, AGENT 2 discharge is mandatory. If fire persists after both bottles, the situation is life-threatening and the aircraft cannot be safely continued in flight.</p><p><strong>System Testing & Dry-Run:</strong> Before flight, maintenance performs a fire detection system test. This typically includes:</p><ul><li>Continuity check of both detection loops per engine</li><li>Test of fire warning lights, bells, and annunciators</li><li>Verification of FIRE HANDLE mechanical function</li><li>Bottle pressure check (must be in green range)</li><li>AGENT switch operation (without actual discharge)</li></ul><p>These tests confirm that the system is functional and ready. A failed test means the aircraft is not airworthy and cannot dispatch.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🔧 Fire Bottle System','steps'=>['Halon (or HFC-227ea) stored at high pressure in bottle','Electrical squib holds bottle seal','Pilot selects AGENT 1 → electrical signal fires squib','Squib ruptures seal; Halon is forced through discharge tube','Halon floods engine nacelle in ~1 second','Fire is suppressed; pressure vessel becomes empty']],
        ['type'=>'amber','head'=>'⭐ Bottle Pressure Checks','table'=>['headers'=>['Bottle Status','Preflight Result','Action'],
          'rows'=>[['Fully charged','Green / normal pressure','Cleared for dispatch'],['Low pressure','Caution / below green','Do not dispatch; investigate and replace bottle'],['Squib fired previously','Zero or very low pressure','Bottle exhausted; replace before dispatch'],['Mechanical leak','Pressure drops during ground check','Seal compromise; replace bottle']]]]
      ],
      'quiz'=>['q'=>'A fire suppression bottle shows low pressure during preflight. What does this indicate?','options'=>['The bottle is just warming up; pressure will rise','Squib has fired or seal has leaked; bottle requires replacement','Pressure is irrelevant; discharge will work anyway','This is normal; low pressure bottles are replaced in-flight'],'correct'=>1,'explanation'=>'Low bottle pressure indicates the squib has fired (bottle previously discharged) or the seal has leaked. Either way, the bottle cannot reliably discharge and must be replaced before dispatch.']
    ],
    [
      'badge'=>'Chapter 4','title'=>'APU & Other Fire Protection','navTitle'=>'APU & Other Fires',
      'subtitle'=>'APU fire detection, suppression, and cargo/avionics protection',
      'time'=>'9 min','objective'=>'Understand APU fire system and auxiliary fire protection measures',
      'analogy'=>['label'=>'The Analogy — Mini Engine','text'=>'<strong>The APU (Auxiliary Power Unit) is a small engine that supplies power on the ground.</strong> It can catch fire just like the main engines. The Q400 APU has its own fire detection loop and suppression bottle, dedicated to rapid detection and extinguishment.'],
      'body'=>'<p><strong>APU Fire Detection:</strong> The Q400 APU compartment (located in the tail section) is equipped with its own thermistor fire detection loop, similar to engine detection but optimized for APU temperatures and geometry. If excessive heat is detected, the system triggers an APU FIRE warning (red light, bell, Master Warning).</p><p><strong>APU Auto-Shutdown:</strong> A key difference between APU and engine fire response is that the APU automatically shuts down when fire is detected. The detection circuit triggers automatic fuel shutoff and ignition cutoff to the APU. This happens instantly, with no crew action required. This is much faster than manual engine isolation and significantly increases the chance of fire suppression success.</p><p><strong>APU Fire Handle & Bottle:</strong> The APU FIRE HANDLE (similar red T-handle, but labeled for APU) is pulled by the crew to manually arm the fire suppression bottle. Unlike engines (which have two bottles), the APU typically has one suppression bottle. After pulling the APU FIRE HANDLE, the crew selects the APU AGENT switch to discharge the bottle into the APU compartment. If fire is suppressed, the APU remains shut down and disabled until maintenance can inspect and clear the aircraft for further operation.</p><p><strong>Cargo Hold Fire Detection:</strong> The Q400 cargo hold (baggage compartment) is equipped with smoke detectors rather than heat detection loops. Smoke detection provides earlier warning of smoldering fires, which may not immediately trigger high temperatures. If smoke is detected, an amber annunciator alerts the crew. The cargo hold has no automatic suppression; crews are trained to descend and land as soon as possible if cargo smoke is detected. Some cargo holds have manual fire bottle access, but the Q400\'s design typically relies on early detection and rapid descent/landing.</p><p><strong>Flight Deck & Avionics Bay Smoke Detection:</strong> The flight deck (cockpit area) and avionics bay below may have smoke detectors to catch electrical fires early. If smoke is detected, the system alerts the crew. Electrical fires in these compartments are extremely serious because they can degrade flight control systems and navigation. Detection and early descent/land procedures are critical.</p><p><strong>Ground Crew Fire Bottle Access:</strong> Some aircraft, including some Q400 variants, allow ground personnel to activate engine fire bottles via external panels during emergency situations on the ground (e.g., if an engine catches fire while parked and engines are shut down). This capability is limited and not available in all models, but it represents an additional safety layer for ground emergencies.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ APU Fire Response','steps'=>['1. APU fire detection triggers automatically','2. APU shuts down automatically (fuel and ignition cut)','3. Crew receives APU FIRE warning (red light + bell)','4. Crew pulls APU FIRE HANDLE','5. Select APU AGENT switch → DISCHARGE','6. Monitor for fire suppression','7. Descend and land ASAP','8. APU remains disabled; maintenance inspection required']],
        ['type'=>'green','head'=>'✅ Cargo & Avionics Fire Detection','list'=>['Cargo hold: smoke detectors (not heat)','Smoke detected → amber annunciator','No automatic suppression; descend and land','Avionics bay: smoke detectors for electrical fires','Flight deck: smoke detection for cockpit fires','Electrical fires are time-critical; land immediately']]
      ],
      'quiz'=>['q'=>'How does APU fire response differ from engine fire response?','options'=>['APU fire requires no crew action; it is fully automatic','APU auto-shuts down on detection; engine must be manually shut off by crew','They are identical in all respects','APU has two fire bottles; engines have one'],'correct'=>1,'explanation'=>'APU auto-shuts down immediately upon fire detection (fuel and ignition cut automatically). Engines require manual FIRE HANDLE pull. APU shutdown is faster and a major advantage in APU fire suppression.']
    ],
    [
      'badge'=>'Chapter 5','title'=>'Fire System Abnormals & Limitations','navTitle'=>'Abnormals & Limits',
      'subtitle'=>'Fire system failures and operational dispatch rules',
      'time'=>'8 min','objective'=>'Recognize fire system failures and understand dispatch restrictions',
      'analogy'=>['label'=>'The Analogy — Vigilance Waning','text'=>'<strong>If your alarm system (detection) or extinguisher (suppression) is broken, you cannot safely fight a fire:</strong> dispatch with failed fire detection or bottles is not permitted. Your safety depends on these systems being ready.'],
      'body'=>'<p><strong>Fire Detection System Failure:</strong> If a fire detection loop is inoperative (failed sensor, broken wiring, or electrical circuit failure), the system cannot reliably detect a fire in that compartment. A failed ENG 1 or ENG 2 fire loop means that engine is essentially unprotected. The aircraft cannot be safely dispatched into conditions where fire risk is elevated (e.g., over-water operations, known terrain with high terrain risk). Maintenance must repair the loop before dispatch, or the affected engine must be declared inoperative and the aircraft dispatched on the remaining engine (if twins; not applicable to Q400 which is twin-engine).</p><p><strong>Fire Bottle Expiration & Low Pressure:</strong> Fire suppression bottles have a certified service life and pressure maintenance schedule. Over time, seal degradation can cause slow leaks. Preflight pressure checks are mandatory. A bottle showing low or zero pressure must be replaced before dispatch. Expired bottles (past certification date) must be replaced. A bottle that has been discharged (squib fired) must be replaced with a new charged bottle. Do not dispatch with a known inoperative or expired bottle.</p><p><strong>FIRE Handle Mechanical Failure:</strong> If a FIRE HANDLE cannot be pulled (mechanical jam, cable break, etc.), the engine cannot be isolated in case of fire. This is a critical system failure. The aircraft cannot be dispatched until the FIRE HANDLE is repaired and tested functional.</p><p><strong>Dispatch Without Fire Protection:</strong> Modern regulations prohibit dispatch of any aircraft with inoperative fire detection or suppression systems. The Q400 must have:</p><ul><li>Both engine fire detection loops functional (dual-loop per engine)</li><li>Both FIRE HANDLEs operational and testable</li><li>Both fire suppression bottles pressurized and within service life</li><li>APU fire detection functional (if APU is to be used)</li><li>APU suppression bottle charged (if APU is to be used)</li></ul><p>If any component is inoperative, the aircraft is not airworthy and cannot dispatch.</p><p><strong>In-Flight Fire System Failure:</strong> If a fire detection component fails in flight (e.g., a fire warning light burns out but loop is OK), the crew should note the failure, continue to destination, and report for maintenance. If a fire loop itself fails (inoperative detection), crew should divert to nearest suitable airport and land; the system is degraded and further flight increases risk.</p><p><strong>False Fire Alarms:</strong> Occasionally, a single fire detection loop may sense abnormal heat (e.g., from maintenance work, bird strike, or electrical fault) and trigger an amber caution without a confirmed fire (red). If only one loop detects, crew should:</p><ul><li>Observe the affected engine (visual inspection if possible)</li><li>Check engine parameters (temperature, pressure)</li><li>If no visible fire or abnormal parameters → the alert may be spurious</li><li>Descend and land to allow maintenance to investigate</li><li>Do not ignore the alert, but do not panic if it is an isolated loop detection with normal engine parameters</li></ul><p>A confirmed red fire warning (both loops detecting) requires immediate action: pull FIRE HANDLE and discharge bottles.</p>',
      'failures'=>[
        ['sev'=>'high','name'=>'Engine Fire Detection Loop Failure','eicas'=>'No warning (detection is inoperative)','what'=>'One or both fire detection loops for ENG 1 or ENG 2 become inoperative','auto'=>'No automatic backup','pilot'=>'Aircraft cannot safely dispatch; report for maintenance and repair before flight','note'=>'Engine is unprotected without functional detection','noteType'=>'red','noteHead'=>'Do Not Dispatch'],
        ['sev'=>'high','name'=>'Fire Suppression Bottle Expended or Low Pressure','eicas'=>'Possible annunciator (depends on installation)','what'=>'Fire bottle shows zero pressure (squib fired or leak) or expired service life','auto'=>'No automatic reset','pilot'=>'Do not dispatch; replace bottle with new charged bottle before flight','note'=>'No suppression capability if bottle is not charged','noteType'=>'red','noteHead'=>'Do Not Dispatch'],
        ['sev'=>'high','name'=>'FIRE Handle Jammed or Inoperative','eicas'=>'No warning during normal flight','what'=>'Engine fire cutoff handle cannot be pulled or operates with excessive force','auto'=>'No workaround','pilot'=>'Do not dispatch; maintenance must repair and test FIRE HANDLE function','note'=>'Engine cannot be isolated in emergency without functional handle','noteType'=>'red','noteHead'=>'Do Not Dispatch'],
        ['sev'=>'medium','name'=>'Single Fire Loop Caution (Not Confirmed Fire)','eicas'=>'Amber fire caution (one loop detects, not both)','what'=>'Single loop senses heat rise but does not meet confirmed fire threshold','auto'=>'No automatic action','pilot'=>'Observe engine parameters and appearance; if normal, descend and land for inspection','note'=>'Not necessarily a real fire, but demands investigation','noteType'=>'amber','noteHead'=>'Land and Investigate']
      ],
      'quiz'=>['q'=>'Can you dispatch if a fire suppression bottle shows low pressure during preflight?','options'=>['Yes, pressure will build up during flight','No, the bottle must be charged and replaced before dispatch','Yes, as long as one bottle per engine is charged','Only for short flights over land'],'correct'=>1,'explanation'=>'Low pressure indicates the bottle is discharged or leaking. It cannot reliably suppress a fire. You must replace the bottle with a new charged bottle before dispatch. Do not fly with a known failed fire suppression bottle.']
    ]
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'ENGINE FIRE IN FLIGHT (CRITICAL)','steps'=>[
      '1. Engine fire detected (red EICAS, bell, Master Warning light)',
      '2. Identify affected engine (check EICAS ENG 1 or ENG 2 FIRE)',
      '3. Pull affected FIRE HANDLE (red T-handle, left for ENG 1, right for ENG 2)',
      '4. Hold handle in position (confirms mechanical isolations)',
      '5. Select AGENT 1 switch → DISCHARGE (fire bottle #1 discharges)',
      '6. Monitor fire warning light (if extinguished, fire is suppressed)',
      '7. If fire warning persists → AGENT 2 switch → DISCHARGE (bottle #2)',
      '8. If fire still burning → LAND IMMEDIATELY and EVACUATE'
    ],'why'=>'Immediate isolation stops fuel and power; bottle suppression extinguishes fire. Two bottles provide redundancy. Landing is mandatory if suppression unsuccessful.'],
    ['type'=>'abnormal','title'=>'ENGINE FIRE ON GROUND (Engine Running)','eicasMsg'=>'ENG 1(2) FIRE red warning','items'=>[
      'Immediately stop engine (throttle to IDLE, then fuel shutoff)',
      'Pull affected FIRE HANDLE',
      'Select AGENT 1 → DISCHARGE',
      'If fire visible → AGENT 2 → DISCHARGE',
      'Evacuate aircraft if fire is not suppressed',
      'Ground crew to standby with fire trucks'
    ],'why'=>'On-ground engine fire can spread rapidly to fuselage. Early cutoff and suppression prevent catastrophic loss. Evacuation may be necessary.'],
    ['type'=>'memory','title'=>'APU FIRE (IN FLIGHT or ON GROUND)','steps'=>[
      '1. APU fire detected → automatic APU shutdown occurs',
      '2. EICAS shows APU FIRE (red warning)',
      '3. Pull APU FIRE HANDLE (rear of flight deck)',
      '4. Select APU AGENT switch → DISCHARGE',
      '5. If fire persists → Do not retry; land immediately',
      '6. APU remains inoperative until maintenance clearance'
    ],'why'=>'APU auto-shutdown is very effective. One bottle usually suppresses APU fire. Landing and maintenance inspection are necessary before resuming operations.'],
    ['type'=>'abnormal','title'=>'CARGO HOLD SMOKE DETECTION','eicasMsg'=>'CARGO SMOKE detected (amber annunciator)','items'=>[
      'No automatic suppression on Q400 cargo hold',
      'Descend immediately to landing altitude',
      'Declare emergency if necessary',
      'Land at nearest suitable airport ASAP',
      'Do not re-enter cargo hold unless fire is extinguished by external means',
      'Evacuation may be required on ground'
    ],'why'=>'Cargo fire is difficult to fight in-flight. Early detection and rapid landing prevent spread to main fuselage. Ground fire-fighting support is essential.'],
    ['type'=>'limit','title'=>'Fire Protection System Limitations','items'=>[
      'Fire detection: dual-loop per engine (one loop = caution; both = red fire)',
      'Do not dispatch with inoperative fire detection loop',
      'Do not dispatch with low or expired fire suppression bottle',
      'Do not dispatch if FIRE HANDLE cannot be pulled or tested',
      'Engine fire: always pull FIRE HANDLE first, then discharge bottles',
      'APU fire: APU auto-shuts down; then pull FIRE HANDLE and discharge',
      'Cargo fire: no in-flight suppression; descend and land immediately',
      'Fire bottle expended (squib fired): replace bottle before next flight',
      'Fire system test (preflight): all loops, lights, handles, and bottle pressure must check good'
    ]]
  ],
  'quiz' => [
    ['q'=>'What is the first action when an engine fire is confirmed in flight?','options'=>['Discharge fire bottle immediately','Pull the affected engine\'s FIRE HANDLE to isolate it','Descend as rapidly as possible','Shut off windshield heat'],'correct'=>1,'explanation'=>'Pull the FIRE HANDLE first to isolate the engine (fuel shutoff, hydraulic isolation, electrical off). This removes the fire\'s fuel and power source. Only after isolation do you discharge the fire bottles.'],
    ['q'=>'The Q400 has how many fire suppression bottles per engine?','options'=>['One','Two','Three','Four'],'correct'=>1,'explanation'=>'Two fire suppression bottles per engine provide redundancy. If the first bottle (AGENT 1) does not suppress the fire, the second bottle (AGENT 2) is available for immediate discharge.'],
    ['q'=>'What does pulling the FIRE HANDLE accomplish?','options'=>['It immediately discharges the fire bottle','It closes the fuel shutoff valve and isolates electrical and hydraulic power','It starts the fire detection system','It activates emergency oxygen'],'correct'=>1,'explanation'=>'The FIRE HANDLE is a mechanical and electrical isolator. Pulling it shuts fuel flow (fuel shutoff valve), isolates hydraulic and electrical systems, removes bleed air, and arms the fire bottles (but does not discharge them).'],
    ['q'=>'During preflight, a fire suppression bottle shows low pressure. What does this mean?','options'=>['The bottle is cold; pressure will rise in-flight','The squib has fired (bottle was previously discharged) or seal has leaked; must be replaced','This is normal; all bottles start at low pressure','Low pressure is monitored continuously in-flight'],'correct'=>1,'explanation'=>'Low bottle pressure indicates the squib fired previously (bottle exhausted) or a seal leak developed. The bottle cannot reliably discharge and must be replaced before dispatch.'],
    ['q'=>'How does APU fire detection and response differ from engine fire detection?','options'=>['APU has no fire detection','APU automatically shuts down on fire detection; engine requires manual isolation','Engines auto-shutdown; APU requires manual shutdown','APU has three fire bottles; engines have two'],'correct'=>1,'explanation'=>'APU fire detection triggers automatic APU shutdown (fuel and ignition cut off instantly). Engine fires require manual FIRE HANDLE pull. APU auto-shutdown is faster and provides a major safety advantage.'],
    ['q'=>'If fire suppression (AGENT 1 and AGENT 2) does not extinguish an engine fire, what is the correct action?','options'=>['Attempt AGENT 1 and AGENT 2 again','Increase airspeed to cool the engine','Land immediately and evacuate','Continue flight to nearest airport'],'correct'=>2,'explanation'=>'Two fire bottles should suppress any aircraft fire. If they do not, the situation is beyond firefighting capability. Land immediately and evacuate. Fighting continues only on the ground with external fire-fighting equipment.'],
    ['q'=>'Fire detection loops are dual systems. What is the logic?','options'=>['One loop is backup only if the first fails','One loop detects = caution (amber); both loops = confirmed fire (red)','Both loops must detect simultaneously or fire alert is ignored','Each loop covers a different engine'],'correct'=>1,'explanation'=>'Single-loop detection = amber caution (possible fault or fire; monitor). Dual-loop detection = red fire warning (confirmed fire; execute FIRE HANDLE and suppression). Dual-loop logic prevents false positives while ensuring real fires are caught.'],
    ['q'=>'Can you dispatch if a fire detection loop is inoperative?','options'=>['Yes, as long as one loop per engine works','No, aircraft must not be dispatched with failed fire detection','Yes, if the other fire protection systems are working','Only for flights under 2 hours'],'correct'=>1,'explanation'=>'Fire detection system failure means the affected engine is unprotected. Modern regulations prohibit dispatch without fully functional detection. Maintenance must repair the loop before flight.']
  ]
]; }

// ── ATA22 AUTOFLIGHT

function ata22_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Understanding the Autoflight System on the Q400',
      'time'=>'8 min','objective'=>'Learn what the autopilot system does, when to use it, and why it matters for crew workload management',
      'analogy'=>['label'=>'The Analogy — A Dedicated Copilot','text'=>'The autopilot is like having a perfectly trained, tireless copilot who can hold a heading, maintain altitude, or track a navigation source all day without getting fatigued. But just like any copilot, it has limits—it cannot land the aircraft, and you must watch it constantly and be ready to take over instantly.'],
      'body'=>'<p>The Q400 autopilot system is a dual-channel, independent flight control computer that reduces crew workload during cruise, instrument approaches, and navigation-heavy phases. It consists of two main components: the <strong>Autopilot computers (AP1 and AP2)</strong> that actually move the flight controls, and the <strong>Flight Director (FD)</strong> that displays command bars on the Primary Flight Display to guide the pilot whether the autopilot is engaged or not.</p><p>The heart of the system is the <strong>Flight Guidance Control Panel (FGCP)</strong> located on the glareshield between the two pilots. This panel is where you select modes, arm the autopilot, and manage the system. The autopilot can work in two planes: <strong>lateral (heading, course, localizer)</strong> and <strong>vertical (altitude, climb rate, glideslope)</strong>, and these modes are selected independently.</p><p>In addition to lateral and vertical guidance, the Q400 is equipped with <strong>Autothrottle (AT)</strong>, which automatically manages engine power to maintain a selected airspeed or vertical speed profile. The autothrottle can be armed before takeoff and will engage at 400 feet above ground level.</p><p>Here\'s the critical point: <strong>the Q400 autopilot cannot land the aircraft</strong>. It is certified for Category I Instrument Landing System (ILS) approaches only, meaning it can guide you down to decision altitude on an ILS approach, but a pilot must take control and land manually. Additionally, the autopilot has strict altitude engagement limits—it cannot be engaged below 400 feet AGL in approach mode, and minimum 1000 feet AGL for cruise engagement.</p><p>The system includes multiple automatic protections: the autopilot will automatically disconnect if it detects a failure, it limits pitch attitude to prevent unusual attitudes, and there is a large red button on the control column for immediate manual disconnect at any time. Understanding when to engage the autopilot, when to disconnect it, and how to recognize and respond to failures is essential for safe Q400 operations.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⭐ Key Numbers','table'=>['headers'=>['Parameter','Value','Critical Note'],
          'rows'=>[
            ['Minimum Engagement Altitude (Approach)','400 ft AGL','<span class="spec-badge must">MUST KNOW</span>'],
            ['Minimum Engagement Altitude (Cruise)','1000 ft AGL','Always respect this limit'],
            ['Maximum Altitude','Aircraft ceiling (25,000 ft)','Above this, AP may disconnect'],
            ['AP Disconnect Button','Red button on control column','Always accessible for immediate disconnect'],
            ['Autothrottle Engagement','400 ft AGL','Armed before takeoff'],
          ]]],
        ['type'=>'blue','head'=>'🎯 System Components','list'=>[
          'Autopilot Computer 1 (AP1) — Primary lateral control',
          'Autopilot Computer 2 (AP2) — Independent backup (only one engaged at a time)',
          'Flight Director — Command bars on PFD (works with or without AP engaged)',
          'Flight Guidance Control Panel (FGCP) — Mode selections and engagement buttons',
          'Autothrottle module — Speed control via power lever automation',
          'Air Data and Inertial sensors — Feed position, altitude, attitude to AP computers'
        ]]
      ],
      'quiz'=>['q'=>'The Q400 autopilot is certified for what category of ILS approach?','options'=>['Category I (CAT I)','Category II (CAT II)','Category III (CAT III)','Category IIIA (CAT IIIA)'],'correct'=>0,'explanation'=>'The Q400 autopilot is certified for Category I approaches only. It can guide the aircraft to decision altitude on an ILS, but manual landing is required. The system does NOT have autoland capability.']
    ],
    [
      'badge'=>'Lateral Modes','title'=>'Autopilot Modes — Lateral Plane','navTitle'=>'Lateral Modes',
      'subtitle'=>'Controlling heading and course tracking',
      'time'=>'10 min','objective'=>'Understand each lateral mode and when to use it for efficient navigation',
      'analogy'=>['label'=>'The Analogy — Different Roads to the Same Destination','text'=>'The lateral modes are like different ways to reach your destination: HDG is like driving straight on a highway (holding a fixed compass direction), LNAV is like following a GPS route (tracking a defined path), VOR is like tuning in a ground station and flying toward or away from it, and LOC is like the instrument approach guideline that keeps you centered on the runway.'],
      'body'=>'<p>The autopilot provides five lateral modes in the Q400. Each mode governs how the aircraft maintains or tracks a lateral course.</p><p><strong>Heading Select (HDG) Mode:</strong> This is the simplest lateral mode. When engaged, the autopilot will hold a constant magnetic heading selected on the FGCP. The aircraft will bank to capture the selected heading if not already on it, then maintain that heading regardless of wind. This mode is useful during initial climb, cruise when not flying a structured route, or during vectoring by ATC. There is no course tracking; the autopilot simply holds the nose of the aircraft at the selected heading.</p><p><strong>LNAV Mode:</strong> This mode couples the autopilot to the Flight Management System (FMS). If you have loaded a route into the FMS, engaging LNAV will make the autopilot follow the FMS-calculated lateral path. This is the primary mode for en route operations and structured approaches. The FMS continuously updates the desired track based on waypoints, and the autopilot follows. LNAV requires a functional FMS and a properly loaded flight plan.</p><p><strong>VOR Mode:</strong> The autopilot will capture and track a VOR radial selected on the VOR receiver panel. You tune the desired VOR station and select the course (inbound or outbound), and the autopilot tracks that course. This is useful when flying en route without FMS, or as a backup to LNAV. The autopilot will intercept the selected radial and maintain it with minimal deviations.</p><p><strong>Localizer (LOC) Mode:</strong> When approaching an airport with an ILS, engaging LOC mode captures the localizer beam. The autopilot will intercept the localizer and track it precisely to the runway. This mode is normally armed before descent and will automatically activate when the localizer signal is strong enough. LOC mode is essential for coupled ILS approaches.</p><p><strong>Back Course (BC) Mode:</strong> Some ILS approaches have a back course (reciprocal heading). BC mode works similarly to LOC but with reversed sense (the autopilot banks opposite to normal LOC mode to stay centered on the back-course localizer). This mode is rarely used but must be selected if executing a back-course approach.</p><p>Mode transitions are important: you can transition from HDG to LNAV when your aircraft is aligned with the next leg of the FMS route. You can transition from LNAV to LOC when the localizer becomes active and the mode annunciator indicates LOC capture. Always anticipate mode changes and be prepared to take manual control if the transition does not occur as expected.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📍 Lateral Mode Quick Reference','table'=>['headers'=>['Mode','Usage','Requires','Autopilot Action'],
          'rows'=>[
            ['HDG','Cruise, vectoring, climb-out','FGCP heading entry','Holds selected magnetic heading'],
            ['LNAV','En route, structured routes','FMS with loaded route','Follows FMS lateral path between waypoints'],
            ['VOR','En route without FMS, backup','VOR receiver tuning','Intercepts and tracks selected VOR radial'],
            ['LOC','ILS approach','Active localizer signal','Captures and tracks runway centerline'],
            ['BC','Back-course approach (rare)','Active back-course signal','Tracks back-course with reversed sense'],
          ]]],
        ['type'=>'green','head'=>'✈️ Mode Engagement Tips','steps'=>[
          '1. Select the desired lateral mode on the FGCP',
          '2. If LNAV: confirm FMS route is loaded and CDU shows active leg',
          '3. If VOR or LOC: confirm the navigation aid is tuned and signal is strong',
          '4. Wait for the mode annunciator to show the selected mode is armed or active',
          '5. Monitor the flight path and course deviation indicator (CDI) on the PFD',
          '6. Be ready to disconnect autopilot and hand-fly if the mode does not engage as expected'
        ]]
      ],
      'quiz'=>['q'=>'You are navigating with FMS en route. Which lateral mode should you select?','options'=>['HDG','LNAV','VOR','LOC'],'correct'=>1,'explanation'=>'LNAV (lateral navigation) couples the autopilot to the FMS and makes it track the loaded flight plan. This is the primary mode for structured, FMS-based navigation en route.']
    ],
    [
      'badge'=>'Vertical & Speed','title'=>'Autopilot Modes — Vertical & Autothrottle','navTitle'=>'Vertical Modes',
      'subtitle'=>'Managing altitude, climb/descent, and airspeed automatically',
      'time'=>'12 min','objective'=>'Master vertical modes and autothrottle for efficient vertical flight management',
      'analogy'=>['label'=>'The Analogy — Cruise Control with Elevation Control','text'=>'The vertical modes work like cruise control on a car: ALT holds you at a fixed altitude (cruise control at a specific elevation), VS lets you climb or descend at a selected rate (like slowly accelerating uphill), and IAS holds a speed (the traditional cruise control). VNAV is like having a pre-programmed descent profile that the car follows automatically.'],
      'body'=>'<p>Vertical modes control the pitch of the aircraft to maintain or change altitude. Unlike lateral modes (which can be independent), vertical modes work in conjunction with the autothrottle to maintain your selected speed profile.</p><p><strong>Altitude Hold (ALT) Mode:</strong> This is the workhorse of vertical autopilot modes. When engaged, the autopilot will maintain the current altitude (or the selected altitude on the FCU). It uses pitch control to level off and hold altitude to within about 50 feet. ALT mode is used during cruise and holding patterns. Once level, ALT mode requires very little pitch movement and is stable and fuel-efficient. If you are not yet at the selected altitude, the autopilot will climb or descend at a modest rate until reaching it, then level off.</p><p><strong>Vertical Speed (VS) Mode:</strong> This mode allows you to select a specific climb or descent rate (in feet per minute) on the FGCP. The autopilot will then adjust pitch to achieve that rate. For example, you might select VS mode with 1000 feet per minute descent during initial descent. The autopilot will gradually adjust pitch to establish and maintain that descent rate. VS mode is useful during transitions (climb-out, descent planning) because you have precise control over the rate of vertical movement.</p><p><strong>Indicated Airspeed (IAS) Mode:</strong> The autopilot adjusts pitch to maintain a selected airspeed (instead of altitude or vertical speed). This mode is less commonly used but can be selected during descent if you wish to maintain a specific speed rather than a specific altitude change rate. IAS mode changes pitch as altitude changes to keep speed constant.</p><p><strong>VNAV Mode:</strong> This is the FMS-coupled vertical mode. If you have loaded a VNAV profile into the FMS (which defines your desired descent or climb path with speed restrictions at waypoints), engaging VNAV will make the autopilot follow that profile. The FMS calculates optimal descent rates and speeds, and the autopilot executes them. VNAV is highly efficient for structured descents and approaches.</p><p><strong>Glideslope (GS) Mode:</strong> When flying an ILS approach with the localizer (LOC) captured, GS mode is automatically or manually engaged to track the ILS glideslope beam down to the runway. The autopilot will follow the 3-degree glideslope, descending in sync with lateral tracking. GS mode is essential for a coupled ILS approach and is normally part of the approach automation sequence.</p><p><strong>Autothrottle (AT):</strong> The autothrottle is technically separate from vertical modes but works in tandem. Once armed (before takeoff), the AT will engage at 400 feet AGL and automatically manage engine power to maintain a selected speed or vertical speed profile. If the autopilot is in VS mode, the AT will adjust power to achieve that descent rate while maintaining the selected speed. If in ALT mode, the AT maintains a cruise speed. The autothrottle includes overspeed protection: if airspeed exceeds VMO, the AT will automatically reduce power. Unlike older systems, the Q400 AT requires the autopilot to be engaged; AT alone cannot hand-fly the aircraft.</p><p>Vertical mode transitions require attention: when descending from cruise altitude using VS mode, you will eventually need to transition to ALT mode at your assigned altitude. When on final approach, you need LOC + GS modes engaged. Always crosscheck that the vertical mode is appropriate for the current phase of flight.</p>',
      'cards'=>[
        ['type'=>'amber','head'=>'⚡ Vertical Mode Reference','table'=>['headers'=>['Mode','Purpose','Pitch Control','Speed Control'],
          'rows'=>[
            ['ALT','Hold altitude','Auto levels to selected alt','Autothrottle maintains speed'],
            ['VS','Climb/descend at fixed rate','Maintains selected rate','Speed may vary with AT'],
            ['IAS','Maintain selected airspeed','Auto-adjusts to hit speed','Maintains selected speed'],
            ['VNAV','Follow FMS descent profile','Follows FMS path','FMS-optimized speeds'],
            ['GS','Track ILS glideslope','Follows 3° descent','Maintains IAS with AT'],
          ]]],
        ['type'=>'red','head'=>'🔴 Autothrottle Critical Facts','list'=>[
          '<strong>Engagement altitude:</strong> AT arms before takeoff, engages at 400 ft AGL',
          '<strong>Speed selection:</strong> AT maintains speed via power lever automation',
          '<strong>Overspeed protection:</strong> AT automatically reduces power if airspeed exceeds VMO',
          '<strong>Requires AP engaged:</strong> AT cannot operate alone; autopilot must be controlling pitch',
          '<strong>Manual override:</strong> Pilot can manually move power levers to override AT at any time',
          '<strong>Disconnect:</strong> Disconnect AP button also disengages AT'
        ]],
        ['type'=>'blue','head'=>'📊 Descent Profile Example','steps'=>[
          '1. Cruise in ALT mode at assigned altitude with AT maintaining cruise speed',
          '2. Receive descent clearance; engage VS mode with -2000 fpm rate',
          '3. AT reduces power; aircraft descends at 2000 feet per minute while maintaining speed',
          '4. As you approach assigned altitude, transition to ALT mode',
          '5. ALT mode levels aircraft and maintains new altitude; AT maintains cruise speed',
          '6. Plan descent to approach altitude; repeat VS → ALT transition as needed'
        ]]
      ],
      'quiz'=>['q'=>'You are descending in VS mode at -1500 feet per minute. The autothrottle is active. What happens to power as you descend?','options'=>['Power increases to fight the descent','Power reduces to enable the descent','Power stays at cruise setting','Power cycles up and down'],'correct'=>1,'explanation'=>'As you descend in VS mode, the autothrottle reduces engine power to allow the aircraft to descend at the selected rate while maintaining the selected airspeed. The combination of reduced power, altitude loss, and pitch control from autopilot creates the desired descent rate.']
    ],
    [
      'badge'=>'Abnormal Ops','title'=>'Autopilot Failures & Abnormal Procedures','navTitle'=>'Abnormal Ops',
      'subtitle'=>'Recognition, response, and recovery from autopilot malfunctions',
      'time'=>'10 min','objective'=>'Quickly recognize autopilot failures and execute appropriate procedures to maintain safe flight',
      'analogy'=>['label'=>'The Analogy — When Your Copilot Gets Tired','text'=>'If your autopilot fails mid-flight, it\'s like your copilot suddenly going on break without warning. You don\'t panic—you just hand-fly the aircraft using the flight director bars as guidance, and you continue toward your destination. The flight director will still show you what to do; the autopilot just isn\'t doing it for you anymore.'],
      'body'=>'<p>Autopilot failures are relatively rare on the Q400, but when they occur, the crew must recognize the failure quickly and respond appropriately. The most common failure mode is an autopilot disconnect, which can be automatic (system detects a fault) or manual (pilot presses the disconnect button).</p><p><strong>Autopilot Disconnect:</strong> When the autopilot disconnects—whether automatically due to a fault or manually by pilot action—the autopilot computers immediately release control of the aircraft. The control column returns to neutral, and the aircraft transitions to manual flight. Critically, the Flight Director bars remain visible on the PFD and continue to provide guidance. The audio warning sounds (three loud beeps), and the disconnect light illuminates on the FGCP. The pilot must immediately grasp the control column and hand-fly the aircraft using the FD bars. The transition should be smooth; however, if the disconnect was unexpected, the aircraft may be in a transient state (climbing, descending, turning), and the pilot must quickly assess and recover to stable flight.</p><p><strong>Autopilot Runaway:</strong> A runaway autopilot is a failure where the autopilot continues to operate but moves the flight controls in an unwanted or excessive manner. This might manifest as continuous pitching, uncommanded banking, or a mode that will not disengage. The immediate response to a runaway autopilot is to <strong>press the red disconnect button</strong> and hand-fly the aircraft. Do not hesitate—if the autopilot is behaving erratically, disconnect it immediately. Once disconnected, evaluate the aircraft attitude and trim, and fly manually to the nearest suitable airport.</p><p><strong>Mode Engagement Failure:</strong> Sometimes the autopilot will not arm or will not capture a mode. For example, you select LNAV, but the mode does not annunciate as armed. Common causes include: FMS route not loaded (LNAV will not arm without a valid route), VOR/LOC receiver not tuned or signal too weak, autopilot altitude not set, or a system fault. If a mode will not engage, verify the preconditions for that mode. If preconditions are correct but the mode still will not engage, disconnect the autopilot and hand-fly, then declare the problem to ATC and plan to land at a suitable airport.</p><p><strong>Unstable Mode Transition:</strong> Occasionally, the autopilot will not smoothly transition between modes (e.g., from LOC to GS during an approach). If a mode transition is rough or the autopilot rolls or pitches uncommanded, manually disconnect and hand-fly the aircraft immediately. Unstable mode transitions are rare with modern autopilots but can occur due to signal loss or sensor disagreements. The safest response is always to take manual control and hand-fly.</p><p><strong>Flight Director Failure:</strong> If the Flight Director bars disappear from the PFD but the autopilot is still engaged, the autopilot will continue to control the aircraft, but you have lost visual guidance. Disconnect the autopilot immediately because you cannot see where to hand-fly. Coordinate with ATC and plan to land at an airport with weather suitable for hand-flying.</p><p><strong>Recovery Procedures:</strong> After any autopilot disconnect or failure, the immediate steps are: (1) Grasp the control column and stabilize aircraft attitude, (2) Check autopilot disconnect light on FGCP and verify it is extinguished (or red if disconnect is active), (3) Assess trim state and adjust as needed, (4) Navigate to nearest suitable airport if system cannot be reset, (5) Report the problem to ATC. If you successfully hand-fly and the flight director bars are visible and stable, you can consider re-engaging the autopilot only after you have confirmed the original fault has cleared (e.g., restart of an autopilot channel, restoration of a navigation signal). Do not re-engage the autopilot unless you are confident the problem has been resolved.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Emergency Response Steps','steps'=>[
          '1. DISCONNECT autopilot immediately (red button on control column)',
          '2. Hand-fly aircraft using Flight Director bars as reference',
          '3. Stabilize aircraft attitude and check trim',
          '4. Verify descent is not occurring and altitude is stable',
          '5. Declare situation to ATC: "AUTOPILOT FAILURE, requesting vectors to nearest airport"',
          '6. Plan to land at nearest suitable airport',
          '7. Do NOT re-engage autopilot without explicit confirmation the fault has cleared'
        ]],
        ['type'=>'amber','head'=>'⚠️ Common Failure Scenarios','table'=>['headers'=>['Failure Type','Symptoms','Immediate Action','Root Cause Check'],
          'rows'=>[
            ['Autopilot Disconnect','Disconnect light on, FD bars visible, control column neutral','Grasp column, hand-fly using FD bars','Overspeed, sensor fault, or manual press'],
            ['Runaway Autopilot','Continuous pitch/roll movement, mode will not disengage','Press disconnect button immediately','Control linkage issue or sensor disagreement'],
            ['Mode Engagement Failure','Mode does not annunciate as armed','Verify FMS/VOR/LOC preconditions, retry','Invalid FMS route, weak nav signal, or system fault'],
            ['Unstable Transition','Rough mode capture, uncommanded aircraft movement','Disconnect and hand-fly to nearest airport','Signal loss or sensor mismatch during transition'],
          ]]],
        ['type'=>'blue','head'=>'💡 Prevention & Awareness','list'=>[
          'Always have hand-flying skills sharp; do not rely solely on autopilot',
          'Monitor autopilot mode annunciators and cross-check with PFD position',
          'Verify navigation signals are strong before engaging mode-coupled flight',
          'Review autopilot disconnect procedures in the QRH before flight',
          'Brief the approach: decide when you will disconnect autopilot for manual landing',
          'If autopilot is behaving oddly (not capturing mode smoothly, mode hunting), disconnect early and hand-fly'
        ]]
      ],
      'quiz'=>['q'=>'The autopilot unexpectedly disconnects in cruise, and the Flight Director bars are visible. What is your first action?','options'=>['Press the AP arm button to re-engage immediately','Grasp the control column and hand-fly using the FD bars','Check the FMS route and restart the autopilot','Declare an emergency to ATC'],'correct'=>1,'explanation'=>'When the autopilot disconnects unexpectedly, grasp the control column immediately and hand-fly the aircraft using the Flight Director bars. Stabilize the aircraft first. Only after you have confirmed the aircraft is stable and you understand why it disconnected should you consider re-engaging the autopilot.']
    ],
    [
      'badge'=>'Limits & Exam','title'=>'Autopilot Limitations & Operational Constraints','navTitle'=>'Limits',
      'subtitle'=>'Know when the autopilot cannot be used and what the operational rules are',
      'time'=>'8 min','objective'=>'Understand the operational limits of the Q400 autopilot to ensure safe operations',
      'analogy'=>['label'=>'The Analogy — Every Tool Has Limits','text'=>'Just as a hammer is great for nails but useless for screws, the Q400 autopilot is great for cruise and approaches but cannot land the aircraft, and it has strict altitude limits below which it cannot operate. Knowing these limits prevents over-reliance on automation and ensures you always have a backup plan.'],
      'body'=>'<p>The Q400 autopilot is a robust system, but it has important limitations that every pilot must know and respect.</p><p><strong>Autoland Capability:</strong> The Q400 autopilot is <strong>NOT approved for autoland</strong>. Category I (CAT I) is the maximum certification. The system can guide you down to decision altitude on an ILS approach (typically 200 feet above ground level), but a pilot must take control and land the aircraft manually. There is no automatic landing mode, flare, or touchdown guidance. This is a fundamental operational constraint. If you are planning a low-visibility approach, verify that the aircraft is approved for CAT I operations and that the airport has an operational CAT I ILS. Always brief the approach with the assumption that a manual landing will be performed.</p><p><strong>Minimum Engagement Altitude (Approach):</strong> The autopilot cannot be engaged below 400 feet AGL during approach procedures. This ensures the autopilot is not commanding large pitch changes very close to terrain. If you wish to use autopilot guidance during an approach, you must engage it before reaching 400 feet AGL, and then it will follow the selected modes (typically LOC and GS) down to decision altitude. However, most Q400 operators recommend disconnecting the autopilot at around 500 feet AGL to transition to manual flight for landing.</p><p><strong>Minimum Engagement Altitude (Cruise):</strong> The autopilot cannot be reliably engaged below 1000 feet AGL in cruise mode. During initial climb, the autopilot may not engage until reaching 1000 feet. This allows the aircraft to establish a stable climb without autopilot interference during the critical climb-out phase.</p><p><strong>Maximum Altitude:</strong> The autopilot is certified to operate up to the aircraft ceiling (approximately 25,000 feet on the Q400). Above this altitude, the autopilot may disconnect due to thin air, reduced aerodynamic effectiveness, or system design limits. If you are operating near the ceiling, monitor autopilot performance carefully.</p><p><strong>Wind Limits:</strong> The autopilot has implicit wind limits. In very strong wind shear or turbulence, the autopilot may not track smoothly, or it may disconnect. Severe turbulence can cause control column movements that exceed the autopilot\'s authority, and the system will automatically disconnect. If you encounter severe turbulence, expect the autopilot to disconnect, and be ready to hand-fly.</p><p><strong>Navigation Signal Requirements:</strong> LNAV requires a valid FMS route. VOR mode requires a strong VOR signal. LOC/GS modes require strong localizer and glideslope signals. If navigation signals are weak or unavailable, the corresponding autopilot mode will not engage or will capture weakly and may lose track. Always verify signal strength on the PFD before expecting mode engagement.</p><p><strong>FMS Database Currency:</strong> LNAV mode relies on the FMS navigation database (which defines waypoints, airways, and procedures). The FMS database expires every 28 days and must be updated before flying LNAV with an expired database. If the database is expired, LNAV mode may not be reliable, and VOR navigation should be used as a backup.</p><p><strong>Single Autopilot Operation:</strong> Only one autopilot (AP1 or AP2) can be engaged at a time. You cannot engage both simultaneously (unlike some larger aircraft with Category II autopilot capability). If one autopilot is in use and fails, that autopilot will disconnect, and you must fly manually until it can be restored or switched. There is no automatic switchover to the second autopilot.</p><p><strong>Approach Planning:</strong> Always plan every approach with the assumption that you will hand-fly the aircraft for landing. Use the autopilot to reduce workload during cruise and approach guidance down to decision altitude, but expect to disengage the autopilot and take manual control for the landing. Brief this plan with your first officer and ensure both pilots are ready to hand-fly smoothly.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚫 Hard Limits — Never Exceed','table'=>['headers'=>['Limit','Value','Reason'],
          'rows'=>[
            ['Autoland Capability','NOT APPROVED','Q400 is CAT I only; manual landing required'],
            ['Minimum Altitude (Approach)','400 ft AGL','Cannot engage below this; disconnect at ~500 ft for landing'],
            ['Minimum Altitude (Cruise)','1000 ft AGL','May not engage reliably below this during climb'],
            ['Maximum Altitude','~25,000 ft (aircraft ceiling)','System limits; performance degrades above ceiling'],
            ['Single AP Operation','One AP at a time (AP1 OR AP2)','Cannot engage both; no automatic switchover'],
          ]]],
        ['type'=>'amber','head'=>'✅ Pre-Approach Autopilot Checklist','steps'=>[
          '1. Brief the approach: "Autopilot disconnect at 500 feet; manual landing from there"',
          '2. Verify localizer and glideslope signals are strong on PFD',
          '3. Confirm ILS frequency is correctly tuned on the navigation receiver',
          '4. Set decision altitude in the autopilot FCU (typically 200 feet for CAT I)',
          '5. Plan mode transitions: HDG → LNAV → LOC (mode annunciator shows armed)',
          '6. At glideslope capture, expect GS mode to engage automatically',
          '7. At 500 feet AGL, disengage autopilot and hand-fly to landing'
        ]],
        ['type'=>'green','head'=>'📋 Exam Tips & Key Concepts','list'=>[
          '<strong>Autoland:</strong> Q400 does NOT have autoland; CAT I only; manual landing required',
          '<strong>Altitude limits:</strong> 400 ft approach, 1000 ft cruise—memorize these',
          '<strong>Disconnect button:</strong> Red button on control column; always accessible; immediate disconnect',
          '<strong>Flight Director:</strong> Shows guidance even when AP disconnected; hand-fly using FD bars',
          '<strong>LNAV requirement:</strong> FMS route must be loaded; database must be current',
          '<strong>VOR/LOC signal:</strong> Must be strong; weak signal = weak mode capture or no capture',
          '<strong>Overspeed protection:</strong> AT reduces power if VMO exceeded; automatic protection',
          '<strong>Runaway autopilot:</strong> Disconnect immediately; do not hesitate'
        ]]
      ],
      'quiz'=>['q'=>'What is the maximum altitude at which the Q400 autopilot is certified to operate?','options'=>['15,000 feet','20,000 feet','Aircraft ceiling (approximately 25,000 feet)','Unlimited'],'correct'=>2,'explanation'=>'The Q400 autopilot is certified to the aircraft ceiling, which is approximately 25,000 feet. Above this altitude, the autopilot may not function reliably due to thin air and reduced aerodynamic effectiveness.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'AUTOPILOT RUNAWAY','steps'=>['1. Press RED DISCONNECT button on control column','2. Hand-fly aircraft using Flight Director bars','3. Stabilize aircraft attitude and trim','4. Declare situation to ATC','5. Land at nearest suitable airport','6. Do NOT re-engage autopilot'],'why'=>'A runaway autopilot may command erratic pitch or roll movements that are unsafe. Immediate disconnection and hand-flying is the safest response. Do not attempt to troubleshoot in flight.'],
    ['type'=>'abnormal','title'=>'AUTOPILOT DISCONNECT (UNEXPECTED)','eicasMsg'=>'AP DISCONNECT','items'=>['Flight Director bars remain visible','Control column is neutral and free to move','Audio warning sounds (three beeps)','Disconnect light illuminates on FGCP','Aircraft may be in transient attitude (climbing, descending, or turning)'],'why'=>'An unexpected disconnect indicates either an automatic fault or inadvertent manual disconnect. Immediate hand-flying with FD reference is required. Stabilize the aircraft first before attempting to re-engage.'],
    ['type'=>'limit','title'=>'Autopilot Operational Limits','items'=>['No autoland: CAT I only; manual landing required','Minimum engagement altitude (approach): 400 ft AGL','Minimum engagement altitude (cruise): 1000 ft AGL','Maximum altitude: Aircraft ceiling (~25,000 ft)','Single AP operation: AP1 or AP2 only; not both simultaneously','LNAV requires valid FMS route and current database','Strong navigation signals required for VOR, LOC, GS capture','Cannot engage in severe turbulence or shear']]
  ],
  'quiz' => [
    ['q'=>'During a coupled ILS approach, the autopilot is in LOC mode. Which mode should engage next as you descend toward the runway?','options'=>['ALT mode','VS mode','GS (glideslope) mode','VNAV mode'],'correct'=>2,'explanation'=>'GS (glideslope) mode engages when the aircraft is tracking the localizer and the glideslope signal becomes available. GS mode tracks the 3-degree descent path down to the runway.'],
    ['q'=>'You select LNAV mode, but it does not annunciate as armed. What is the most likely cause?','options'=>['The autopilot disconnect button was pressed','The FMS route has not been loaded into the CDU','The aircraft is above 25,000 feet','The wind speed exceeds system limits'],'correct'=>1,'explanation'=>'LNAV mode requires a valid FMS route to be loaded in the Flight Management System. If no route is active, LNAV will not arm. Verify the FMS CDU shows an active flight plan before attempting to engage LNAV.'],
    ['q'=>'What is the minimum altitude at which the Q400 autopilot can be engaged during a cruise climb?','options'=>['200 ft AGL','400 ft AGL','1000 ft AGL','1500 ft AGL'],'correct'=>2,'explanation'=>'The minimum engagement altitude for cruise mode is 1000 feet AGL. During initial climb from takeoff, wait until 1000 feet before engaging the autopilot to ensure stable climb is established.'],
    ['q'=>'The autopilot is flying a coupled ILS approach in LOC + GS modes. At what altitude should the pilot prepare to disconnect the autopilot and take manual control for landing?','options'=>['Decision altitude (typically 200 feet)','400 feet AGL','500 feet AGL','100 feet AGL'],'correct'=>2,'explanation'=>'Most Q400 operators recommend disconnecting the autopilot at approximately 500 feet AGL to transition to manual flight for landing. This gives the pilot time to establish a stable descent and visual references while hand-flying the aircraft.'],
    ['q'=>'You experience an autopilot runaway where the pitch continuously changes despite mode selection. What is your immediate action?','options'=>['Attempt to select ALT mode to stabilize pitch','Press the red disconnect button on the control column','Reset the FGCP and re-engage the autopilot','Call for maintenance to troubleshoot the system'],'correct'=>1,'explanation'=>'When the autopilot is running away (commanding erratic or continuous control movements), immediately press the red disconnect button. Do not attempt to troubleshoot or re-engage the autopilot while flying; hand-fly the aircraft to the nearest airport.'],
    ['q'=>'The Flight Director bars on your PFD show a command to turn left, but the autopilot is not turning. The autopilot is engaged, and the disconnect light is not illuminated. What should you do?','options'=>['Manually turn the aircraft to follow the FD bars','Wait for the autopilot to respond; it may be capturing slowly','Disconnect the autopilot and check the PFD display','Push the autopilot mode buttons to re-establish the mode'],'correct'=>1,'explanation'=>'The Flight Director may show a command before the autopilot has begun to execute. If the autopilot is engaged and the disconnect light is not on, allow a brief moment for the autopilot to respond. If the aircraft does not begin turning within a few seconds, then disconnect and hand-fly.'],
    ['q'=>'You are descending in VS mode at 1500 fpm with autothrottle active. As you descend, your airspeed increases. What is most likely happening?','options'=>['The autothrottle is malfunctioning and not reducing power','The aircraft is in a descent and airspeed naturally increases with descent rate','The VS mode has automatically switched to IAS mode','This is abnormal; disconnect autopilot immediately'],'correct'=>1,'explanation'=>'During a descent, airspeed naturally increases as the aircraft loses altitude and gravity assists the motion. The autothrottle manages power to maintain the selected speed as much as possible, but a slight increase in speed during descent is expected and normal. The FD pitch command and AT power adjustment work together to maintain your selected speed profile.'],
    ['q'=>'When is the autothrottle (AT) engaged during a Q400 flight?','options'=>['Engaged immediately at takeoff','Engaged at 400 feet AGL during climb','Engaged at 1000 feet AGL','Engaged manually by the pilot at any time'],'correct'=>1,'explanation'=>'The autothrottle is armed before takeoff and engages automatically at 400 feet AGL during the initial climb. Once engaged, it manages engine power to maintain the selected speed or vertical speed profile. The pilot can disengage it at any time by pressing the disconnect button or manually moving the power levers.'],
  ]
]; }

// ── ATA34 NAVIGATION

function ata34_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Understanding the Q400 Navigation System Suite',
      'time'=>'10 min','objective'=>'Learn the components of the modern Q400 navigation system and how they work together to guide safe flight',
      'analogy'=>['label'=>'The Analogy — Multiple Maps and Compasses','text'=>'Modern navigation is like having multiple maps, compasses, and signposts all working together: IRS tells you where you are, GPS pinpoints your location from satellites, the FMS is your smart navigation computer, VOR and ILS are ground-based radio guides, and TCAS/GPWS are warning systems to keep you safe from other aircraft and terrain. When one system fails, others take over.'],
      'body'=>'<p>The Q400 navigation system is composed of multiple independent and complementary subsystems that work together to provide the flight crew with accurate position, altitude, attitude, and safety information. Unlike older aircraft that relied on a single primary navigation source, the Q400 uses a redundant architecture where multiple systems provide backup to one another.</p><p>At the heart of the system is the <strong>Air Data Inertial Reference System (ADIRS)</strong>, of which there are two: Left and Right ADIRS. These systems provide the fundamental measurements of airspeed, altitude, attitude (pitch, roll, yaw), and vertical speed. Additionally, each ADIRS contains an inertial measurement unit (IMU) that allows the system to track aircraft position and ground speed even when not receiving external navigation updates.</p><p>The <strong>Flight Management System (FMS)</strong> is the crew interface and flight guidance computer. The FMS receives information from ADIRS, GPS, VOR/DME, and ILS receivers, and it uses this data to calculate aircraft position, compute optimal routes, predict performance, and provide guidance to the autopilot and flight director. The FMS is controlled via a keyboard-like <strong>Control Display Unit (CDU)</strong> and stores a comprehensive navigation database containing airways, waypoints, airport information, and navigation aid frequencies.</p><p>The Q400 includes <strong>two independent IRS (Inertial Reference Systems)</strong> that provide position and attitude data. These systems align (become accurate) over approximately 10 minutes on the ground. Once aligned, the IRS provides reliable position, altitude, attitude, vertical speed, and acceleration data throughout the flight. In the air, IRS position accuracy degrades slowly (drift) over time unless updated by external navaids.</p><p><strong>Ground-based radio navigation aids</strong> include <strong>VOR (VHF Omnidirectional Range)</strong> stations, which broadcast a signal that the aircraft can use to determine its magnetic bearing from the station. The Q400 has two independent VOR/DME receivers. <strong>ILS (Instrument Landing System)</strong> provides precision guidance for approaches, with localizer (lateral) and glideslope (vertical) components.</p><p>The Q400 is equipped with an integrated <strong>GPS receiver</strong> that provides autonomous position via satellite signals. GPS on the Q400 is used as a backup and cross-check to other navigation sources; it is not approved as the sole primary navigation source for approaches. The GPS must pass a <strong>RAIM (Receiver Autonomous Integrity Monitoring)</strong> check before flight to ensure sufficient satellite geometry.</p><p>Safety systems include <strong>TCAS (Traffic Collision Avoidance System)</strong>, which tracks nearby aircraft via transponder signals and issues Traffic Advisories (TA) and Resolution Advisories (RA) if collision risk is detected. TCAS RA commands must be obeyed immediately, even if they contradict ATC instructions or autopilot guidance. The <strong>GPWS/TAWS (Ground Proximity Warning System / Terrain Awareness Warning System)</strong> monitors altitude above terrain and provides warnings if the aircraft is descending into terrain. GPWS "PULL UP" warnings demand immediate climb action.</p><p>Understanding how these systems complement and backup one another is essential for safe navigation. The best practices for Q400 navigation are: (1) use the FMS as your primary source of navigation guidance when available, (2) cross-check FMS position with VOR fixes or GPS, (3) always monitor the navigation display and be ready to hand-navigate if a system fails, (4) verify that navigation databases and RAIM are current before flight, (5) treat TCAS RA and GPWS alerts as safety-critical commands.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🧭 System Components Overview','list'=>[
          'ADIRS (Air Data Inertial Reference Systems) × 2 — Position, altitude, attitude, airspeed',
          'FMS (Flight Management System) — Route planning, performance, guidance',
          'CDU (Control Display Unit) — Pilot interface for FMS',
          'VOR/DME receivers × 2 — Ground-based navigation aid tuning',
          'ILS receivers × 2 — Localizer and glideslope for approaches',
          'GPS — Autonomous position (backup only, not primary for approaches)',
          'TCAS — Traffic collision avoidance',
          'GPWS/TAWS — Ground proximity warning',
          'Radio Altimeters × 2 — Precise height above terrain'
        ]],
        ['type'=>'amber','head'=>'⭐ Key System Characteristics','table'=>['headers'=>['System','Role','Accuracy','Backup By'],
          'rows'=>[
            ['ADIRS','Fundamental attitude, airspeed, altitude','Within 100 feet altitude','Cross-checked by altitude selections'],
            ['FMS','Route guidance, performance planning','0.1 nm when updated by navaids','VOR, GPS, manual navigation'],
            ['VOR/DME','Radio navigation, position fixing','Within 5 nm from station','LNAV, GPS, NDB'],
            ['ILS','Precision approach guidance','CAT I: 200 feet DH','Manual hand-flying of approach'],
            ['GPS','Position backup, cross-check','Within 10 meters (typical)','IRS drift check, VOR verification'],
            ['TCAS','Traffic warning and resolution','Real-time transponder data','See-and-avoid, ATC separation'],
            ['GPWS','Terrain collision avoidance','Terrain database + radar','Pilot awareness, visual references']]]],
      ],
      'quiz'=>['q'=>'What is the primary purpose of the ADIRS (Air Data Inertial Reference System) on the Q400?','options'=>['To provide weather radar imagery','To calculate fuel consumption','To provide airspeed, altitude, attitude, and position information','To manage the autopilot modes'],'correct'=>2,'explanation'=>'The ADIRS is the fundamental air data and inertial system. It measures airspeed (from pitot/static probes), altitude (from static pressure), attitude (from accelerometers and gyros), and provides inertial position/velocity information. All other systems rely on ADIRS data as a baseline.']
    ],
    [
      'badge'=>'ADIRS','title'=>'Air Data & Inertial Reference System (ADIRS)','navTitle'=>'ADIRS Details',
      'subtitle'=>'The foundation of position, altitude, and attitude information',
      'time'=>'12 min','objective'=>'Understand ADIRS components, alignment, operation, and failure modes',
      'analogy'=>['label'=>'The Analogy — The Aircraft\'s Inner Ear and Altimeter','text'=>'The ADIRS is like an aircraft\'s inner ear (inertial measurement—sensing acceleration and rotation) combined with a super-accurate altimeter and airspeed indicator. The inner ear tells you if you\'re tilting or accelerating even in clouds, and the altimeter tells you how high you are. Over time, the inner ear (IMU) can become slightly off (drift), so you occasionally recalibrate it by referencing other instruments or a GPS.'],
      'body'=>'<p>Each Q400 is equipped with two independent ADIRS units: Left ADIRS and Right ADIRS. Each unit contains three main components: (1) <strong>Air Data Probe (pitot/static)</strong>, which measures airspeed and ambient pressure, (2) <strong>Inertial Measurement Unit (IMU)</strong>, which contains accelerometers and gyroscopes to measure acceleration and rotation, and (3) <strong>Central Processing Unit (CPU)</strong>, which combines data and outputs position, velocity, attitude, and altitude.</p><p><strong>Air Data Function:</strong> The pitot-static probe on the nose and fuselage of the aircraft measures dynamic pressure (airspeed) and static pressure (altitude). The ADIRS processes this data and computes true airspeed (TAS), Mach number, and altitude. This information is then broadcast to the autopilot, FMS, and all flight instruments. The ADIRS also calculates vertical speed and rate of change of Mach number.</p><p><strong>Inertial Function:</strong> The IMU contains three-axis accelerometers and three-axis ring laser gyroscopes (or similar) that measure the aircraft\'s acceleration and rotation in three dimensions. During takeoff, when you accelerate down the runway, the accelerometers sense this acceleration and begin computing your velocity relative to the ground. As you turn, the gyroscopes sense the rotation and update your heading. This inertial "dead reckoning" allows the ADIRS to continuously calculate the aircraft\'s position even without external references like GPS or VOR. The advantage is that inertial data is always available and works anywhere on Earth. The disadvantage is that over time, small measurement errors accumulate, causing the calculated position to drift away from true position.</p><p><strong>ADIRS Alignment:</strong> Before flight, the ADIRS must be aligned on the ground. During alignment, the ADIRS system initializes its gyroscopes and accelerometers by knowing you are stationary (ground level, not moving). This takes approximately 10 minutes. Once aligned, the ADIRS position and attitude are accurate. If the aircraft is moved before alignment is complete (pushed by a tug), alignment is disrupted and must be restarted. Most Q400 flight planning includes the ADIRS alignment time in the pre-flight sequence.</p><p><strong>In-Flight Position Updates:</strong> Once airborne, the ADIRS inertial position slowly drifts (error grows) due to small errors in accelerometers and gyroscopes. To maintain accuracy, the ADIRS receives periodic updates from external sources: when the FMS computes a position based on VOR or DME fixes, this position can be input into the ADIRS as a position update, correcting the drift. Similarly, GPS position can be used to update the ADIRS. Some FMS systems can do this automatically; in other cases, the crew must manually update the ADIRS position via the CDU. Without these external updates, ADIRS position accuracy degrades by approximately 1-2 nautical miles per hour of flight.</p><p><strong>Dual ADIRS Operation:</strong> The two ADIRS units operate independently. In normal operation, one ADIRS is typically selected as the "active" source, and the other is in standby or provides backup. If the active ADIRS fails (e.g., pitot probe icing, IMU failure), the flight crew can switch to the other ADIRS and continue flying. However, if both ADIRS units fail or become unreliable, the aircraft has no source of accurate altitude, airspeed, or attitude information, and flight safety is severely compromised. In such cases, emergency procedures including descent to a lower altitude or landing immediately are required.</p><p><strong>ADIRS Failures:</strong> Common ADIRS failures include: (1) Pitot probe icing in severe icing conditions, which causes airspeed data to become erratic, (2) IMU gyro or accelerometer failure, which causes attitude or inertial position to become unreliable, (3) Loss of air pressure signal, which causes altitude data to fail. If the crew suspects an ADIRS failure (e.g., airspeed indicator is erratic, altitude disagrees with autopilot setting, or aircraft attitude seems wrong), the first step is to verify the problem by cross-checking the PFD with the backup ADIRS or with manual calculations. If the ADIRS failure is confirmed, switch to the other ADIRS, declare the problem to ATC, and plan to land at the nearest suitable airport.</p><p><strong>ADIRS and Navigation Accuracy:</strong> A properly aligned ADIRS provides initial position accuracy of within approximately 50 meters. After 1 hour of flight without external updates, position error grows to 1-2 nm. This is why the FMS continuously receives position updates from navigation aids (VOR, DME, GPS) to correct ADIRS drift and maintain position accuracy for approach guidance. The synergy between ADIRS inertia and external navaids creates a robust, accurate, and redundant navigation system.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📊 ADIRS Function & Updates','table'=>['headers'=>['Function','Data Source','Output','Update Source'],
          'rows'=>[
            ['Air Data','Pitot/static probe','Airspeed, Mach, altitude, vertical speed','Continuous from probes'],
            ['Inertial Measurement','Accelerometers & gyros','Attitude, acceleration, inertial position','Continuous; position updated by navaids'],
            ['Position Calculation','IMU + gravity model','Aircraft latitude/longitude/altitude','Updated by VOR, DME, GPS fixes'],
            ['Heading Determination','Gyroscopes + Earth\'s mag field','True and magnetic heading','Refined by FMS course guidance'],
          ]]],
        ['type'=>'amber','head'=>'⏱️ ADIRS Alignment Timeline','steps'=>[
          '1. Ground: Power on ADIRS 10+ minutes before departure',
          '2. Ground: ADIRS initializes in stationary state (takes ~10 minutes for alignment)',
          '3. Ground: Do NOT move aircraft until alignment is complete',
          '4. Ground: Check ADIRS page on CDU; confirm "ALIGNED" status',
          '5. Flight: ADIRS provides accurate position and attitude',
          '6. Flight: Position slowly drifts without external updates',
          '7. Flight: FMS receives VOR/DME/GPS updates to correct position drift',
          '8. Flight: Crew can manually input position fix to reset drift (if needed)'
        ]],
        ['type'=>'green','head'=>'✅ Pre-Flight ADIRS Checklist','list'=>[
          'Power on ADIRS at least 10 minutes before pushback',
          'Verify ADIRS alignment is complete; CDU shows "ALIGNED"',
          'Do NOT move aircraft during alignment period',
          'Check both ADIRS Left and Right are initialized',
          'Verify airspeed and altitude data match on both sides',
          'Confirm inertial position is close to departure airport location',
          'If position update available (VOR, GPS), input into ADIRS via CDU',
          'Brief crew: which ADIRS is active; what to do if ADIRS fails'
        ]]
      ],
      'quiz'=>['q'=>'You are on the ground, and you power up the ADIRS for flight. How long must you wait before departure to allow ADIRS alignment to complete?','options'=>['2 minutes','5 minutes','10 minutes','20 minutes'],'correct'=>2,'explanation'=>'ADIRS alignment on the ground takes approximately 10 minutes. The system must be stationary (aircraft not moving) during this time. If the aircraft is pushed or moved before alignment is complete, the alignment is disrupted and must be restarted. Always plan for at least 10 minutes of ADIRS alignment time in your pre-flight sequence.']
    ],
    [
      'badge'=>'FMS & GPS','title'=>'Flight Management System & GPS Navigation','navTitle'=>'FMS & GPS',
      'subtitle'=>'Planning routes, calculating performance, and managing navigation guidance',
      'time'=>'12 min','objective'=>'Understand FMS operation, GPS integration, and limitations',
      'analogy'=>['label'=>'The Analogy — Your In-Cockpit Travel Companion','text'=>'The FMS is like having a smart travel companion with detailed maps, a fuel calculator, and the ability to update your position in real-time using GPS and radio signals. You tell the FMS your destination, and it plans the route, calculates how much fuel you\'ll need, estimates arrival time, and guides the autopilot on the most efficient path. GPS is the companion\'s satellite connection—incredibly accurate but sometimes temporarily unavailable.'],
      'body'=>'<p>The <strong>Flight Management System (FMS)</strong> is the brain of modern navigation on the Q400. It is a specialized computer system that integrates information from multiple sources (ADIRS, VOR/DME, ILS, GPS) and uses this data to calculate aircraft position, predict performance, and provide navigation guidance to the autopilot and flight director.</p><p><strong>FMS Components:</strong> The FMS consists of two main parts: (1) the <strong>FMS Computer</strong>, which is the processing unit hidden behind the panel, and (2) the <strong>Control Display Unit (CDU)</strong>, which is the pilot interface. The CDU is a keyboard-like device with a small display screen where the crew enters flight plan data, manages the FMS database, and monitors FMS calculations and guidance.</p><p><strong>Navigation Database:</strong> The FMS contains a comprehensive <strong>navigation database</strong> that includes all airways, waypoints, airport information, navaids (VOR, NDB, GPS waypoints), and approach procedures worldwide. This database is regularly updated by the aircraft operator and expires every 28 days. Before each flight, the crew must verify that the FMS database is current. If the database is expired, the FMS may not provide accurate guidance, and VOR-based navigation should be used as a backup. Similarly, the FMS contains a <strong>performance database</strong> with aircraft performance charts that allow the FMS to calculate optimal cruise altitudes, descent profiles, and fuel predictions.</p><p><strong>Flight Plan Entry:</strong> Before flight, the crew loads a flight plan into the FMS via the CDU. The flight plan specifies the departure airport, initial route (often a SID—standard instrument departure), en-route waypoints and airways, and the destination airport with approach procedure. The FMS calculates the total distance, estimated flight time, and fuel requirements. Once airborne, the FMS guides the autopilot along the planned route via LNAV (lateral navigation) mode.</p><p><strong>FMS Position Calculation:</strong> The FMS receives raw data from the ADIRS (inertial position, altitude, airspeed) and external sources (VOR, DME, GPS). The FMS continuously triangulates the aircraft\'s position using these multiple sources. When the aircraft passes near a VOR station, the FMS can calculate a precise position fix based on the aircraft\'s bearing relative to that station. Similarly, GPS signals provide autonomous position. The FMS then compares the inertial position (from ADIRS) with these external fixes and corrects any drift. This integrated approach provides robust and accurate position information.</p><p><strong>GPS Integration:</strong> The Q400 is equipped with an integrated <strong>GPS receiver</strong> that connects directly to the FMS. GPS provides highly accurate position (within 10-30 meters, depending on satellite geometry) and is an excellent source for updating the FMS and correcting ADIRS drift. However, on the Q400, GPS is used as a backup and cross-check, not as the primary navigation source for approaches. This is a fundamental operational rule. The reason is that GPS signals can be jammed, spoofed, or temporarily unavailable, making it less reliable than ground-based radio navigation (VOR, ILS) for precision approach guidance.</p><p><strong>RAIM (Receiver Autonomous Integrity Monitoring):</strong> Before flight, the FMS performs a RAIM check using GPS satellites in view. RAIM verifies that there are enough satellites and good geometry to provide reliable GPS position. If RAIM fails (e.g., only 3 satellites available, or poor geometry), GPS is not available for that flight, and VOR-based navigation is the primary source. Many flight crews check RAIM during pre-flight to confirm GPS availability for the intended flight.</p><p><strong>VNAV (Vertical Navigation):</strong> In addition to lateral guidance (LNAV), the FMS can provide vertical guidance (VNAV). The crew loads a cruise altitude and a descent profile into the FMS. The FMS calculates when to begin descent, at what rate, and what speed to maintain to arrive at the destination at the desired altitude. If the autopilot is in VNAV mode, it will follow this FMS-computed descent profile automatically, reducing crew workload and improving fuel efficiency.</p><p><strong>FMS Failure Modes:</strong> If the FMS fails (e.g., computer hardware failure, software crash), the crew loses automated route guidance and performance calculations. However, the ADIRS continues to provide inertial guidance, and VOR/DME navigation is still available for hand navigation. In a complete FMS failure, the crew would navigate using VOR radials plotted on a chart, estimate fuel and time manually, and fly the ILS approach using raw ILS data. Modern FMS systems are highly reliable, and complete FMS failure is rare. More common is partial loss (e.g., CDU display fails but FMS computer continues to work), in which case the crew can work around the failure.</p><p><strong>Best Practices for FMS Navigation:</strong> (1) Always load and verify the flight plan in the FMS before flight, (2) cross-check FMS-calculated position with VOR fixes or GPS ground truth, (3) before departure, confirm the navigation database is current, (4) monitor the FMS display during flight to ensure the aircraft is following the planned route, (5) if FMS guidance seems wrong, switch to VOR navigation or hand navigation as a backup, (6) before approach, verify that the approach procedure loaded in the FMS matches the assigned approach by ATC.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🗺️ FMS Database & Life Cycle','table'=>['headers'=>['Data Type','Update Cycle','Criticality','If Expired'],
          'rows'=>[
            ['Navigation Database (Airways, Waypoints, Navaids)','Every 28 days','Critical for LNAV guidance','May provide incorrect guidance; use VOR backup'],
            ['Performance Database (Aircraft performance charts)','Every 28 days','Important for fuel/descent predictions','Calculations may be inaccurate; revert to manual methods'],
            ['Approach Procedures','Every 28 days','Critical for approach guidance','May not have latest procedure changes; brief from chart'],
          ]]],
        ['type'=>'amber','head'=>'⭐ GPS vs. VOR for Approaches','steps'=>[
          'GPS Benefits: Highly accurate (within 10-30 meters), available worldwide, automatic position updates, useful for fixing ADIRS drift',
          'GPS Limitations: Can be jammed or spoofed (rare but possible), satellite signals blocked near terrain/buildings, not approved as sole source for approach on Q400',
          'VOR Benefits: Ground-based, always available at tuned station (no satellite dependency), approved for precision approaches, time-tested and reliable',
          'Q400 Rule: GPS is backup and cross-check ONLY; VOR or ILS is primary for approach guidance',
          'Best Practice: Use GPS for position updates and drift correction en route; use VOR or ILS for approach guidance'
        ]],
        ['type'=>'green','head'=>'📋 FMS Pre-Flight Checklist','list'=>[
          'Verify FMS navigation database is current (not expired)',
          'Verify FMS performance database is current',
          'Load flight plan into FMS CDU (departure, SID, en-route airways/waypoints, approach)',
          'Verify FMS-calculated distance, time, and fuel predictions are reasonable',
          'Check RAIM status; confirm GPS is available (or accept VOR-only navigation)',
          'Brief crew: which navaids are tuned, primary navigation source, backup procedures',
          'Once airborne, cross-check FMS position with VOR fixes periodically',
          'Monitor FMS guidance and ensure autopilot is tracking the planned route'
        ]]
      ],
      'quiz'=>['q'=>'You are planning a departure, and you discover the FMS navigation database expired 5 days ago. What should you do?','options'=>['Use GPS for navigation instead; it is not affected by database expiration','Proceed with caution; use VOR navigation as backup','Update the database before flight; flying with expired database is not approved','Declare this as a maintenance issue and delay the flight'],'correct'=>2,'explanation'=>'Flying with an expired FMS database is not approved because the database may not contain current waypoints, airways, or approach procedures. Before flight, the FMS database must be updated. If a database update is not available, VOR-based navigation (hand navigation using VOR radials) is the approved alternative.']
    ],
    [
      'badge'=>'Radio Navaids','title'=>'ILS, VOR, ADF & Radio Navigation','navTitle'=>'Radio Navaids',
      'subtitle'=>'Ground-based radio aids for position fixing and precision approaches',
      'time'=>'11 min','objective'=>'Understand each radio navigation aid and how to use it safely',
      'analogy'=>['label'=>'The Analogy — Radio Beacons and Approach Guides','text'=>'VOR and NDB stations are like radio beacons scattered across the landscape, each sending out a unique signal that allows you to know your magnetic bearing from that station. ILS is like a precise, lighted runway guide that tells you exactly where the centerline is and how far above the runway surface you are. When you tune these signals and interpret them correctly, they create a detailed map of your position and guide you safely to the airport.'],
      'body'=>'<p><strong>VOR (VHF Omnidirectional Range):</strong> A VOR station is a ground-based radio transmitter that broadcasts a signal in all directions. By tuning a VOR receiver to a station\'s frequency and reading the receiver, the pilot (or FMS) can determine the aircraft\'s magnetic bearing FROM the station. For example, if you tune a VOR and the display shows "090," it means the aircraft is currently on the 090-degree magnetic radial FROM that station (or equivalently, flying northeast of the station, away from it). Each VOR station broadcasts its identifier (typically a three-letter code) in Morse code, which the crew uses to verify correct station identification. The Q400 has two independent VOR/DME receivers, allowing crew to cross-check or use a backup VOR if one fails.</p><p><strong>DME (Distance Measuring Equipment):</strong> Many VOR stations are co-located with DME stations. By tuning the VOR frequency, the VOR/DME receiver automatically tunes the corresponding DME frequency. The DME provides the slant range distance from the aircraft to the station. Combined with the VOR bearing and altitude, this allows the FMS to calculate a precise position fix. For example, if you are on the 090 radial at 25 nm DME from a VOR/DME station at your current altitude, the FMS can compute your position very accurately.</p><p><strong>Position Fixing Using VOR/DME:</strong> In VOR-based navigation, the crew tunes multiple VOR/DME stations and collects their bearings and distances. The FMS then triangulates the position using these multiple fixes. This is how en route navigation and position updates are accomplished in areas without GPS or when GPS is unavailable. VOR position fixes are accurate to within approximately 5 nm from the station, less accurate than GPS but very reliable because the ground stations are under direct ATC control.</p><p><strong>ILS (Instrument Landing System):</strong> An ILS provides the most precise guidance for approaches. Each ILS consists of two radio beams: (1) the <strong>Localizer (LOC)</strong>, which provides lateral guidance (left-right) to align with the runway centerline, and (2) the <strong>Glideslope (GS)</strong>, which provides vertical guidance (up-down) to descend along a 3-degree angle to the runway. The Q400 is equipped with two independent ILS receivers, typically tuned to the same ILS frequency so both receivers track the same localizer and glideslope. ILS guidance is accurate to within approximately 30-50 feet laterally and can guide the aircraft all the way to decision altitude (typically 200 feet above ground level for Category I approaches).</p><p><strong>ILS Procedure:</strong> To fly an ILS approach, the crew must: (1) tune the ILS frequency on both receivers, (2) verify that the localizer and glideslope signal flags indicate valid signals, (3) arm the autopilot to LOC and GS modes, (4) intercept the localizer beam, (5) once localized, the glideslope should capture automatically and begin guidance downward, (6) monitor descent and landing cues as you approach minimum descent altitude, (7) at decision altitude, either continue with autopilot guidance to the runway or disconnect autopilot and hand-fly to landing.</p><p><strong>ADF (Automatic Direction Finder) & NDB (Non-Directional Beacon):</strong> The Q400 is equipped with two ADF receivers and can tune NDB (Non-Directional Beacon) stations. An NDB is a low-frequency radio transmitter, and the ADF receiver indicates the bearing to the NDB station. ADF is less precise than VOR and is primarily used as a backup navigation aid or for approach guidance to airports that do not have VOR or ILS. Some older airports still rely on ADF/NDB approaches. However, the trend in modern aviation is away from NDB; many countries are decommissioning NDB stations in favor of RNAV (satellite-based) approaches.</p><p><strong>Radio Altimeter:</strong> The Q400 is equipped with two radio altimeters. Unlike the ADIRS altitude (which is barometric pressure-based and cannot measure height above terrain), the radio altimeter sends a radar pulse to the ground and measures the time for the pulse to return, calculating exact height above ground level (AGL). Radio altimeters are accurate to within a few feet and are essential for landing (they provide accurate "height above runway" information). They function reliably up to about 2500 feet AGL. Below 2500 feet on approach, the radio altimeter is the authoritative source of altitude above terrain and is used by the flight crew to cross-check barometric altitude and monitor descent rate.</p><p><strong>Limitations and Failure Modes:</strong> VOR/DME signals are line-of-sight (blocked by mountains) and weaken with distance (reliable within about 200 nm). ILS signals are also line-of-sight and can be degraded by nearby terrain or storms. Radio altimeter signals can be blocked by ground obstacles or very rough terrain. If any of these systems fail, the crew must use alternative navigation. For approach, if ILS is unavailable or unreliable, the crew can execute a VOR approach (less precise) or a GPS-based approach if the aircraft and airport support it. Always have a backup approach procedure briefed before descent.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📡 Radio Navigation Aids Comparison','table'=>['headers'=>['Navaid','Accuracy','Range','Use Case','Q400 Receivers'],
          'rows'=>[
            ['VOR/DME','±5 nm from station','~200 nm','En route position fixes, approach','2 independent'],
            ['NDB/ADF','±10 nm from station','~100 nm','Backup navigation, some approaches','2 independent'],
            ['ILS Localizer','±30-50 feet lateral','~20 nm','Precision lateral guidance to runway','2 independent'],
            ['ILS Glideslope','±30-50 feet vertical','~20 nm','Precision vertical guidance to runway','Part of ILS receivers'],
            ['GPS','±10-30 meters','Global','Position updates, cross-check (not primary for approach)','1 integrated'],
            ['Radio Altimeter','±5 feet AGL','0-2500 feet','Height above ground on approach','2 independent']]]]
        ,['type'=>'amber','head'=>'🎯 ILS Approach Steps','steps'=>[
          '1. Brief approach: ILS frequency, runway alignment, decision altitude (typically 200 ft)',
          '2. Tune ILS frequency on both receivers; verify localizer and glideslope flags show valid signals',
          '3. Request approach clearance from ATC; obtain localizer and glideslope final approach fix',
          '4. Engage autopilot LOC mode; wait for localizer capture signal on the FD',
          '5. Once localized (centered on runway), engage GS mode or wait for automatic engagement',
          '6. Monitor descent; glideslope should guide you down at 3-degree angle',
          '7. At 500 feet AGL, prepare to disconnect autopilot',
          '8. At decision altitude (typically 200 feet), either continue descent for landing or execute go-around',
          '9. At 50 feet, you should have visual references for landing',
          '10. Disconnect autopilot and land manually'
        ]],
        ['type'=>'green','head'=>'✅ Radio Navigation Pre-Approach Checklist','list'=>[
          'Verify all navaids (VOR, ILS, ADF) planned for flight are operational via NOTAMs',
          'Tune ILS frequency and verify both localizer and glideslope signals are valid on ground',
          'Verify radio altimeter is functioning (cross-check with ADIRS altitude)',
          'Brief approach procedure: runway, ILS frequency, decision altitude, go-around plan',
          'Set decision altitude in autopilot FCU',
          'Ensure GPS is available as backup (if using FMS LNAV for en route)',
          'Monitor radio altimeter during approach; it provides accurate height AGL'
        ]]
      ],
      'quiz'=>['q'=>'You are tuning the ILS for an approach, and the glideslope flag shows invalid. What should you do?','options'=>['Proceed with the approach; the localizer is valid and that is sufficient','Cycle the ILS receiver power and try tuning again','Request an alternative approach (VOR or visual); ILS glideslope failure requires abort of the approach','Continue descent manually without glideslope guidance'],'correct'=>2,'explanation'=>'If the glideslope is invalid or inoperative, the ILS approach cannot be flown as a precision approach. You must request an alternative approach procedure (non-precision VOR approach or visual approach if weather permits). Attempting to descend below the localizer without glideslope guidance can result in a premature descent or collision with terrain.']
    ],
    [
      'badge'=>'Safety Systems','title'=>'TCAS, GPWS, and Safety Avoidance Systems','navTitle'=>'Safety Systems',
      'subtitle'=>'Traffic and terrain awareness, immediate hazard response',
      'time'=>'11 min','objective'=>'Understand TCAS and GPWS operation and mandatory response procedures',
      'analogy'=>['label'=>'The Analogy — Your Aircraft\'s Guardian Angels','text'=>'TCAS is like having a personal traffic controller watching for other aircraft and warning you to avoid them. GPWS is like having a terrain radar watching the ground and alerting you if you are descending unsafely. Both systems demand immediate compliance when they issue warnings—do not question them, do not wait for ATC approval, just respond immediately.'],
      'body'=>'<p><strong>TCAS (Traffic Collision Avoidance System):</strong> TCAS is a safety system that operates independently of ATC and provides collision warnings to the flight crew. TCAS works by receiving transponder signals from nearby aircraft and computing the relative position, altitude, and velocity of those aircraft. If another aircraft is detected and the risk of collision is assessed as high, TCAS issues either a <strong>Traffic Advisory (TA)</strong> or a <strong>Resolution Advisory (RA)</strong>.</p><p><strong>Traffic Advisory (TA):</strong> A TA alerts the crew that another aircraft has been detected within a certain distance (typically within 5-10 nm) and at a collision-threatening altitude. The crew should locate the traffic on the display or visually, and monitor it. A TA does not require any immediate action; it is an awareness alert.</p><p><strong>Resolution Advisory (RA):</strong> An RA is a more serious warning indicating that an imminent collision is possible. TCAS computes an avoidance maneuver (typically a climb or descent command) that will resolve the collision threat. When an RA is issued, the flight crew MUST comply immediately. The crew should <strong>follow the RA even if it contradicts ATC instructions</strong>, even if it seems to conflict with the planned route, and even if the conflict was not visible. TCAS RA is a safety-critical command with the highest priority. Typical TCAS RA language includes "CLIMB" (increase pitch to climb) or "DESCEND" (decrease pitch to descend). The crew responds by adjusting pitch as commanded until the RA is resolved (indicated by "CLEAR OF CONFLICT" or similar message).</p><p><strong>TCAS Modes:</strong> TCAS has multiple modes: STANDBY (system powered but not actively detecting), ALT (altitude reporting to other aircraft via transponder), TRA (traffic advisory only—detects threats but provides no vertical guidance), and TA/RA (active collision avoidance with RAs). In normal operations, TCAS is set to TA/RA mode so all threats are detected and RAs are issued as needed.</p><p><strong>GPWS (Ground Proximity Warning System) / TAWS (Terrain Awareness and Warning System):</strong> GPWS and TAWS are complementary systems that monitor the aircraft\'s altitude relative to the ground and terrain. These systems maintain a database of terrain elevation and can warn the crew if the aircraft is descending dangerously close to the ground.</p><p><strong>GPWS Seven Modes:</strong> GPWS operates in seven detection modes: (1) Excessive descent rate, (2) Terrain clearance floor, (3) Approach terrain, (4) Glide slope deviation (if on an ILS approach), (5) Bank angle excessive, (6) Windshear warning (if equipped), (7) Ceilings and visibility (if equipped on some systems). Each mode monitors a specific hazard.</p><p><strong>GPWS Alerts and Responses:</strong> GPWS provides different levels of alerts: <strong>"Terrain, Terrain"</strong> is a caution-level alert indicating elevated terrain ahead; <strong>"Sink Rate, Sink Rate"</strong> warns of excessive descent rate; <strong>"Pull Up, Pull Up"</strong> is the most urgent warning indicating immediate danger of terrain collision. When a <strong>"Pull Up"</strong> alert is issued, the crew must immediately increase pitch and climb. This is a safety-critical command—do not question it, do not wait for clearance from ATC, do not check the radar; simply climb. GPWS "Pull Up" warnings are generated when the system predicts an imminent terrain collision, and the only appropriate response is immediate climb.</p><p><strong>GPWS Limitations:</strong> GPWS cannot predict all terrain hazards, especially in areas with very rough terrain or deep valleys. GPWS may generate false alarms in some situations (e.g., when passing over a mountain range, the "terrain clearance" warning may alert even though there is actually safe clearance). Crews must understand that GPWS is a backup warning system and does not replace situational awareness, terrain charts, approach planning, and good judgment.</p><p><strong>TAWS (Terrain Awareness and Warning System):</strong> TAWS is an advanced version of GPWS that includes terrain database mapping and provides enhanced situational awareness. TAWS displays terrain on the navigation display (if equipped) showing terrain colors (green = safe, yellow = caution, red = danger). This allows crews to see terrain graphically during approaches and plan accordingly. Some TAWS systems also provide "forward-looking" terrain avoidance, predicting terrain hazards based on the aircraft\'s trajectory.</p><p><strong>Integration with Navigation:</strong> Both TCAS and GPWS are integrated with the FMS and autopilot such that warnings can be issued in real time as the aircraft navigates. However, the systems are independent safety nets; they are not approved as primary guidance for navigation. TCAS and GPWS work best when crews maintain good situational awareness, cross-check systems, and follow approved procedures.</p><p><strong>Operational Best Practices:</strong> (1) Ensure TCAS is in TA/RA mode during all flights, (2) When a TA is issued, locate the traffic and monitor; inform ATC of the traffic, (3) When an RA is issued, comply immediately without hesitation, (4) When a GPWS alert is issued (especially "Pull Up"), respond immediately with climb, (5) Review terrain maps and approach charts before descent to anticipate potential GPWS terrain alerts, (6) Do not silence GPWS alerts before understanding them—they are safety warnings.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Critical Response Procedures','table'=>['headers'=>'System','Alert','Crew Action','Priority'],
          'rows'=>[
            ['TCAS','Traffic Advisory (TA)','Locate traffic visually; monitor situation; inform ATC','Low—awareness only'],
            ['TCAS','Resolution Advisory (RA) — "Climb"','Immediately increase pitch and climb; ignore AP mode; ignore ATC if conflict','HIGHEST—comply immediately'],
            ['TCAS','Resolution Advisory (RA) — "Descend"','Immediately decrease pitch and descend; ignore AP mode; ignore ATC if conflict','HIGHEST—comply immediately'],
            ['GPWS','Terrain Terrain','Assess approach altitude; be prepared to climb if warning persists','Medium—caution alert'],
            ['GPWS','Sink Rate Sink Rate','Check descent rate; reduce descent rate if excessive','Medium—caution alert'],
            ['GPWS','Pull Up Pull Up','IMMEDIATELY CLIMB; increase pitch; do NOT question or delay','HIGHEST—immediate action required'],
          ]],
        ['type'=>'amber','head'=>'⚠️ TCAS RA Decision Tree','steps'=>[
          '1. TCAS issues RA with command (Climb or Descend)',
          '2. Immediately acknowledge the RA to crew (call-out)',
          '3. Disengage autopilot if necessary to allow manual control',
          '4. Apply pitch change to climb or descend as commanded',
          '5. Monitor TCAS display; continue climbing/descending until RA is resolved',
          '6. Expect "CLEAR OF CONFLICT" message when safe separation is achieved',
          '7. Once clear, resume ATC-assigned altitude or request new clearance',
          '8. Inform ATC of the RA event and recovery: "ATC, RA event, (aircraft call sign), climbing to [altitude] to resolve collision threat"'
        ]],
        ['type'=>'green','head'=>'📋 TCAS & GPWS Operational Checklist','list'=>[
          'TCAS Mode: Set to TA/RA during all flights (not STANDBY or ALT only)',
          'TCAS Display: Verify TCAS-II display shows aircraft position and traffic count',
          'GPWS Mode: Ensure GPWS is in normal operating mode (not inhibited or test)',
          'GPWS Terrain Database: Confirm database is current; terrain data updates periodically',
          'Test: Review test results from aircraft systems startup (GPWS and TCAS self-test)',
          'Brief: Inform crew of TCAS RA and GPWS "Pull Up" procedures before flight',
          'Awareness: Monitor traffic targets on TCAS display and respond to TAs proactively',
          'Descent: Brief approach; anticipate potential GPWS alerts near terrain; plan descent safely'
        ]]
      ],
      'quiz'=>['q'=>'During cruise at 10,000 feet, TCAS issues an RA commanding "DESCEND" to avoid another aircraft. ATC has just issued a climb clearance to 12,000 feet. What should you do?','options'=>['Acknowledge ATC clearance and climb to 12,000 feet','Comply with TCAS RA first; descend; then resolve with ATC after the threat is clear','Request a hold from ATC while evaluating the threat','Ignore TCAS and follow ATC since ATC has higher authority'],'correct'=>1,'explanation'=>'TCAS RA has the highest priority. Even if ATC has issued conflicting instructions, you must comply with the TCAS RA immediately. Descend as commanded, resolve the collision threat (indicated by "CLEAR OF CONFLICT"), then contact ATC and explain the RA event. ATC will understand the RA took precedence and will re-coordinate traffic.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'TCAS RESOLUTION ADVISORY (RA)','steps'=>['1. Acknowledge RA and call-out command (Climb or Descend)','2. Disengage autopilot if required for control authority','3. Apply pitch control to climb or descend as commanded by TCAS','4. Monitor TCAS display for "CLEAR OF CONFLICT" or RA resolution','5. Once clear, resume normal operations','6. Inform ATC of the RA event and recovery action','7. Log the event for post-flight debrief'],'why'=>'TCAS RA is a safety-critical collision avoidance command with highest priority. It takes precedence over ATC instructions, autopilot mode, and planned route. Immediate compliance is non-negotiable.'],
    ['type'=>'memory','title'=>'GPWS "PULL UP" WARNING','steps'=>['1. IMMEDIATELY increase pitch (apply aft control pressure)','2. Climb at maximum safe rate without stalling','3. Retract flaps if deployed and safe to do so','4. Retract landing gear if down','5. Do NOT question the warning; comply instantly','6. Continue climb until warning ceases','7. Once clear, declare situation to ATC and land at nearest airport'],'why'=>'GPWS "Pull Up" warning indicates predicted terrain collision within seconds. Immediate climb is the only appropriate response. Any hesitation or delay can result in catastrophic impact.'],
    ['type'=>'abnormal','title'=>'TCAS FAILURE OR DEGRADATION','eicasMsg'=>'TCAS FAIL or TA/RA INHIBITED','items'=>['TCAS display may show "FAIL" or "NOT FUNCTIONAL"','Traffic advisories may not be issued','Resolution advisories will not be available','Alternate traffic separation: rely on ATC radar and visual scanning','Inform ATC of TCAS outage; expect to maintain greater separation','Proceed to nearest airport for maintenance if TCAS is non-functional'],'why'=>'TCAS failure removes a critical safety layer (collision avoidance). Until restored, the aircraft is dependent on ATC separation and crew visual scanning. TCAS outage does not prevent flight but increases collision risk.'],
    ['type'=>'abnormal','title'=>'GPWS FALSE ALERT / NUISANCE WARNING','eicasMsg'=>'Terrain Terrain (or Sink Rate)','items'=>['Verify actual altitude and terrain elevation on chart','Check radio altimeter reading; compare with ADIRS barometric altitude','Assess descent rate; if excessive, reduce descent rate','If alert persists but chart shows adequate clearance, likely nuisance alert','Silence alert by pressing GPWS acknowledgment button','Continue approach cautiously; monitor descent carefully','If alert does not clear, consider aborting approach'],'why'=>'GPWS sometimes generates false alarms (especially in terrain with deep valleys or peaks). However, always verify with terrain chart and altimeter; do not assume the alert is false without confirmation. When in doubt, climb and request lower approach altitude.'],
    ['type'=>'limit','title'=>'Navigation System Limitations','items'=>['TCAS: Limited to aircraft with operational transponders; does not detect non-transponder traffic; provides relative avoidance (assumes other aircraft maneuver)','GPWS: Terrain database may not be perfectly current; cannot predict all terrain hazards; can generate false alarms in mountainous regions','ILS: Requires localizer and glideslope signals; signals degrade with distance and terrain blockage; limited to line-of-sight','VOR/DME: Line-of-sight limited; accurate to ±5 nm; unavailable below horizon','GPS: Subject to jamming or spoofing (rare); not approved as sole source for approach; must have RAIM check before flight','Radio Altimeter: Accurate only below ~2500 feet; can be blocked by ground obstacles or very rough terrain','FMS: Requires current navigation database; degraded accuracy if database is expired; relies on external navaids for position updates']]
  ],
  'quiz' => [
    ['q'=>'What is RAIM and why is it important for GPS navigation on the Q400?','options'=>['Radar Altitude Indication Measurement—confirms radio altimeter accuracy','Receiver Autonomous Integrity Monitoring—verifies sufficient GPS satellite geometry and signal quality for reliable position','Range and Altitude Integrated Measurement—combines VOR and altitude data','Radar Accuracy Improvement Method—enhances weather radar display'],'correct'=>1,'explanation'=>'RAIM (Receiver Autonomous Integrity Monitoring) checks that there are enough GPS satellites available with good geometry to provide reliable position data. Before flight, the crew checks RAIM to confirm GPS is available. If RAIM fails, GPS is not available for that flight, and VOR-based navigation is the primary source.'],
    ['q'=>'You are flying LNAV on the FMS, and you notice the FMS-calculated position differs significantly from your VOR fix. What is the most likely cause?','options'=>['The autopilot has drifted off course','ADIRS inertial position has drifted due to lack of external updates','The VOR station is malfunctioning and providing incorrect data','The FMS database is corrupted'],'correct'=>1,'explanation'=>'ADIRS inertial position slowly drifts over time without external updates. VOR fixes provide accurate position, and the FMS can use these fixes to correct ADIRS drift. If the FMS position disagrees significantly with a fresh VOR fix, ADIRS drift has accumulated, and the FMS should be updated with the VOR position to reset the drift.'],
    ['q'=>'Your FMS navigation database expired 10 days ago. Can you legally depart with expired database?','options'=>['Yes, the database is still accurate for 28 days from issue','No, the database must be current; use VOR navigation as alternative','Yes, expired database only affects performance calculations, not navigation','No, must delay until database is updated'],'correct'=>3,'explanation'=>'The FMS navigation database expires every 28 days. Flying with an expired database is not approved because waypoints, airways, and approach procedures may have changed. Before departure, the database must be updated, or the crew must plan to navigate using VOR radials (hand navigation) instead of FMS LNAV.'],
    ['q'=>'During an ILS approach, the localizer is valid but the glideslope signal is invalid. What should you do?','options'=>['Proceed with the approach; use the localizer for guidance and descent manually','Request an alternative approach; ILS approach requires both localizer and glideslope','Land using the localizer only; glideslope is optional','Request a go-around and try the approach again'],'correct'=>1,'explanation'=>'An ILS requires both localizer and glideslope for a precision approach. If either component is inoperative, the approach cannot be flown as an ILS. The crew must request an alternative approach (non-precision VOR or visual approach if weather permits).'],
    ['q'=>'TCAS issues a "DESCEND" Resolution Advisory while you are in cruise at an ATC-assigned altitude. ATC has not issued any descent clearance. What should you do?','options'=>['Request descent clearance from ATC first, then comply with TCAS','Immediately descend as commanded by TCAS, then inform ATC of the RA','Ignore TCAS because ATC has assigned this altitude','Check the TCAS display to verify the threat before descending'],'correct'=>1,'explanation'=>'TCAS RA is the highest priority. You must comply immediately with the resolution advisory (climb or descend as commanded), regardless of ATC clearance. After the threat is resolved and the RA is clear, inform ATC of the RA event. ATC will coordinate traffic and re-clear you.'],
    ['q'=>'You are descending on approach, and GPWS issues a "PULL UP" warning. At the same time, ATC clears you to descend further. What should you do?','options'=>['Ask ATC to clarify the clearance before responding to GPWS','Comply with ATC; GPWS warnings are often false alarms','Immediately climb in response to GPWS; inform ATC of the warning and recovery','Descend as cleared and monitor GPWS; climb if warning persists'],'correct'=>2,'explanation'=>'GPWS "PULL UP" warning is a safety-critical alert indicating predicted terrain collision. You must respond immediately with climb, regardless of ATC clearance. After recovering from the warning, inform ATC of the event. ATC will understand and coordinate further clearances.'],
    ['q'=>'You want to use GPS for primary navigation guidance during an ILS approach on the Q400. Is this approved?','options'=>['Yes, GPS is the most accurate navigation system available','No, GPS is backup and cross-check only; ILS, VOR, or other ground-based nav is required for approach guidance','Only if RAIM check passes','Only if the FMS database is current'],'correct'=>1,'explanation'=>'On the Q400, GPS is approved as a backup and cross-check but NOT as a sole primary source for approach guidance. ILS or VOR-based approaches are the approved methods. GPS can be used to update FMS position and correct ADIRS drift en route, but for landing guidance, ILS or VOR is required.'],
    ['q'=>'What is the primary function of the ADIRS inertial measurement unit (IMU)?','options'=>['To measure outside air temperature','To detect aircraft acceleration and rotation, computing inertial position and attitude','To calculate fuel consumption','To provide real-time weather data'],'correct'=>1,'explanation'=>'The IMU contains accelerometers and gyroscopes that measure the aircraft\'s acceleration and rotation in three axes. This data is used to compute aircraft position (latitude/longitude), velocity, heading, and attitude. Over time, inertial position drifts without external updates from navaids or GPS.']
  ]
]; }

// ── ATA23 COMMUNICATIONS
function ata23_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'How the Q400 stays connected to ATC, company, and cabin crew',
      'time'=>'8 min','objective'=>'After this chapter, understand the communication systems installed on the Q400 and when each is used.',
      'analogy'=>['label'=>'The Analogy — Radio Layers','text'=>'Imagine the Q400 as a ship with multiple radios: VHF is like the ship-to-ship channel (local, clear), HF is the maritime band (global, crackly), and ACARS is like email (sent while you fly). You select which one to use for each message.'],
      'body'=>'<p>The Q400 is equipped with a comprehensive suite of communication systems designed for reliable crew-to-ATC coordination, passenger safety, and company operations. Unlike earlier aircraft, modern systems integrate datalink (ACARS) alongside traditional voice radios, reducing radiotelephony workload and improving data accuracy.</p><p>The three VHF radios provide primary contact with Air Traffic Control. The HF system enables transoceanic flight. The interphone system connects flight deck to cabin and ground crews. And the CVR (Cockpit Voice Recorder) provides the legal and safety record of every flight.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📻 Communication Systems at a Glance','list'=>['VHF Radios (x3) — 118–136.975 MHz, line-of-sight up to ~200 nm at altitude','HF Radio (x1) — 2–30 MHz, global via ionospheric bounce; oceanic flights','ACARS — Digital datalink for clearances, ATIS, company messages','Interphone — Crew coordination across flight deck, cabin, ground crew','CVR — 2-hour loop recorder; cockpit area + boom microphones','PA System — Pilot announcements to passengers']],
        ['type'=>'amber','head'=>'⭐ Golden Rules','list'=>['Emergency frequency 121.5 MHz is guarded automatically — no pilot action needed','SELCAL on HF means you can silence the radio during oceanic cruise; SELCAL tone wakes you','Loss of all VHF = immediate abnormal; VHF1 operative required for dispatch']],
      ],
      'quiz'=>['q'=>'Which communication system is used for transoceanic flight?','options'=>['VHF3','HF radio','ACARS','Interphone'],'correct'=>1,'explanation'=>'HF (High Frequency) radio provides global range via ionospheric bounce. VHF is line-of-sight and cannot reach beyond ~200 nm at cruise altitude.']
    ],
    [
      'badge'=>'Chapter 2','title'=>'VHF & HF Radios','navTitle'=>'VHF & HF',
      'subtitle'=>'Voice communication with Air Traffic Control',
      'time'=>'12 min','objective'=>'Understand the operating frequencies, coverage, and selection of the Q400 VHF and HF radios.',
      'analogy'=>['label'=>'The Analogy — VHF vs HF','text'=>'VHF is like a cell phone tower: high quality, local range. HF is like a CB radio bounced off the ionosphere: global reach, lower quality. Choose the right tool for the job.'],
      'body'=>'<p>The Q400 is equipped with three independent VHF transceivers operating in the 118–136.975 MHz band with 25 kHz channel spacing. VHF1 is the primary radio for all ATC communication; VHF2 serves as secondary; VHF3 is a spare. Each radio has its own antenna and can be independently selected at the audio panel by the pilot flying or pilot not flying.</p><p>VHF communication is line-of-sight and reliable up to approximately 200 nautical miles at cruise altitude (30,000 feet), diminishing with descent. In mountainous terrain, range may be significantly reduced.</p><p>The HF radio operates across 2–30 MHz and relies on ionospheric reflection to reach distant destinations. HF signals are subject to propagation delays, noise, and interference, requiring slower data transmission. SELCAL (Selective Calling) is an HF feature that allows the flight crew to silence the radio during long oceanic legs; when the airline\'s dispatch center sends your aircraft\'s unique 4-tone code, an alert sounds and the radio automatically comes alive.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📡 VHF Radio Details','table'=>['headers'=>['Specification','Value','Notes'],'rows'=>[['Frequency range','118.000–136.975 MHz','Aviation only'],['Channel spacing','25 kHz','8.33 kHz may be available in future'],['Number of radios','3 (VHF1, 2, 3)','VHF1 is primary ATC'],['Coverage','~200 nm at altitude','Line-of-sight; reduces over terrain'],['Antennas','3 separate','Installed top + bottom of fuselage']]]],
        ['type'=>'green','head'=>'🌍 HF Radio Details','table'=>['headers'=>['Specification','Value','Notes'],'rows'=>[['Frequency range','2–30 MHz','Propagation dependent on time/season'],['Coverage','Global','Via ionospheric reflection'],['Number of radios','1','Dedicated to oceanic ops'],['SELCAL feature','4-tone selective calling','Allows silent monitoring during cruise'],['Typical range','Oceanic routes (Atlantic, Pacific)','Required for RVSM + oceanic airspace']]]],
        ['type'=>'amber','head'=>'⭐ Emergency Frequency','list'=>['121.5 MHz is the International Aviation Distress frequency','Guarded automatically by all aircraft when receiving','No pilot action required; radio monitors continuously','Use only in emergency or as directed by ATC']],
      ],
      'quiz'=>['q'=>'At FL250 over flat terrain, what is the approximate VHF communication range?','options'=>['50 nm','100 nm','200 nm','500 nm'],'correct'=>2,'explanation'=>'VHF is line-of-sight; at cruise altitudes over flat terrain, expect ~200 nm range. Mountains or terrain reduce this significantly.']
    ],
    [
      'badge'=>'Chapter 3','title'=>'Datalink & Interphone Systems','navTitle'=>'Datalink & Interphone',
      'subtitle'=>'ACARS, Audio Panel, Quick-Don mask integration',
      'time'=>'10 min','objective'=>'Learn how ACARS, interphone, and the audio panel integrate crew communication.',
      'analogy'=>['label'=>'The Analogy — ACARS as Email','text'=>'ACARS is like sending an email: you send your route request, and dispatch sends clearances back. It is quiet, reliable, and reduces radio blocking. Voice radio is a phone call — you need the frequency clear.'],
      'body'=>'<p>ACARS (Aircraft Communication Addressing and Reporting System) is a digital datalink that allows the flight crew to send and receive structured messages without tying up voice frequencies. Typical ACARS uses include pre-departure clearance receipt, ATIS retrieval, company position reports, and weather uplinks. On the Q400, ACARS reduces voice radio workload, especially during busy departure or approach phases.</p><p>The Interphone system connects the flight deck, cabin crew, and ground crew (when connected). The audio panel is the central hub where each pilot selects which radio to listen to and transmit on. The panel includes headphone connectors, microphone switches, and volume controls. When the quick-don oxygen mask is donned, the microphone automatically disconnects from the panel headset and connects to the mask boom, ensuring uninterrupted communication even on supplemental oxygen.</p><p>The CVR (Cockpit Voice Recorder) captures all communication: radio transmissions, interphone calls, alerts, and ambient flight deck noise. The 2-hour continuous recording provides the definitive record of crew actions and communications during an incident or accident investigation.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📲 ACARS Typical Functions','list'=>['Pre-departure clearance (PDC) — receive ATC clearance automatically','ATIS — automatic weather downlink','Company dispatch messages — position reports, fuel planning','Weather uploads — en route and destination forecasts']],
        ['type'=>'green','head'=>'🎙️ Interphone System','list'=>['Flight deck ↔ Cabin crew — normal flight operations','Flight deck ↔ Ground crew — pre-flight & post-flight','Isolated headset — crew member can be called individually']],
        ['type'=>'blue','head'=>'📋 Audio Panel — Key Controls','list'=>['VHF1, VHF2, VHF3 selector — choose which radio to listen & transmit','HF selector — oceanic and long-range communication','Interphone — cabin crew call button','Mask mic switch — auto-engages when quick-don dons']],
      ],
      'quiz'=>['q'=>'What automatically switches your microphone from the headset to the quick-don mask?','options'=>['Pressing the mask mic button','Donning the oxygen mask','Pulling the oxygen regulator handle','Selecting 100% oxygen'],'correct'=>1,'explanation'=>'When you don the quick-don mask, the harness inflates and an electrical switch automatically connects the mask boom microphone to the intercom. No pilot action is required.']
    ],
    [
      'badge'=>'Chapter 4','title'=>'Abnormals & Limitations','navTitle'=>'Abnormals',
      'subtitle'=>'Loss of comms, COMM FAIL checklist, dispatch requirements',
      'time'=>'9 min','objective'=>'Understand the procedures and limitations for communication system failures.',
      'analogy'=>['label'=>'The Analogy — VHF1 is Your Lifeline','text'=>'Losing VHF1 is like cutting a phone line during an emergency call. You have backups (VHF2, VHF3), but dispatch won\'t release you without at least one working. Plan accordingly.'],
      'body'=>'<p>Loss of communication with Air Traffic Control is a serious abnormal. The Q400 must have at least VHF1 operative for dispatch; loss of all three VHF radios during flight requires immediate declaration of emergency and descent to land as soon as safely possible.</p><p>If primary VHF fails, switch to VHF2. If both fail, select VHF3. If all three fail, continue on the filed route, descend to land at the nearest suitable airport, and declare "Mayday" on emergency frequency 121.5 MHz. ATC may hear you even if you cannot hear them; continue broadcast transmissions at the top of each hour and on logical frequencies.</p><p>CVR failures must be logged but do not prevent flight dispatch. However, prolonged CVR failure may trigger a Minimum Equipment List (MEL) entry limiting the aircraft to daylight VFR or specific alternate airports. Know your operator\'s MEL.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Communication Failures','table'=>['headers'=>['Failure','Action','Dispatch Impact'],'rows'=>[['VHF1 fails','Switch to VHF2; notify ATC','Acceptable if VHF2 OK'],['VHF1 & VHF2 fail','Switch to VHF3; declare abnormal','Acceptable if VHF3 OK'],['All VHF fails','121.5 MHz broadcast; declare Mayday','Flight must divert to land ASAP'],['HF inop','Not required for domestic; note in log','No dispatch impact (domestic)'],['CVR fails','Log defect; check MEL','Possible flight time restriction']]]],
        ['type'=>'amber','head'=>'⭐ Key Limitations','list'=>['VHF1 operative required for dispatch; if inop, aircraft is unserviceable','Quick-don oxygen mask must be available if crew O2 system in use','SELCAL must be functional for all oceanic flights (HF monitoring)','Interphone to cabin crew must be functional before door closure']],
      ],
      'quiz'=>['q'=>'All three VHF radios fail in flight. What is your immediate action?','options'=>['Wait 10 minutes; they may reset','Switch to HF','Declare "Mayday" and descend to land at nearest airport','Continue to destination; report after landing'],'correct'=>2,'explanation'=>'Loss of all VHF in flight is an emergency. Declare Mayday on 121.5 MHz (even if you cannot hear a response, ATC may hear you), and divert to the nearest suitable airport immediately.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'LOST COMMS','steps'=>['1. Notify pilot flying of lost communication','2. Attempt radio tune-up on VHF1, VHF2, VHF3 in sequence','3. Try interphone to ground crew or cabin crew to check intercom','4. If all voice comms lost, transmit on 121.5 MHz: "Mayday, Mayday, [callsign], position [lat/lon], climbing/descending to [altitude]"','5. Follow filed flight plan; maintain assigned altitude','6. Approach to land at nearest suitable airport; broadcast position at top of hour and on logical frequencies'],'why'=>'Loss of ATC communication requires coordinated descent and navigation without clearances. Broadcast position helps ATC provide traffic advisories.'],
    ['type'=>'abnormal','title'=>'COMM FAIL EICAS','eicasMsg'=>'COMM FAIL (radio automatic detection)','items'=>['Check audio panel — verify VHF1 selected and volume is adequate','Try alternate VHF radio (VHF2 or VHF3)','Check interphone to ground crew — confirm intercom system working','If all radios inop, switch main electrical bus; attempt power cycle on failed radio','Transmit on 121.5 MHz; broadcast position and intentions'],'why'=>'EICAS alert directs crew to diagnose and declare abnormal promptly.'],
    ['type'=>'limit','title'=>'Communication System Limitations','items'=>['Minimum dispatch requirement: VHF1 must be operative and functional','HF radio: Required for oceanic/RVSM airspace; SELCAL must be armed before flight','Interphone to cabin: Required for safe crew coordination; must be functional before door closure','CVR: Inoperative CVR may limit operations per MEL; consult operator MEL','Quick-don O2 mask microphone: Must automatically engage when mask donned']],
  ],
  'quiz' => [
    ['q'=>'You are climbing to FL300 over flat terrain. What is the expected VHF1 communication range?','options'=>['100 nm','150 nm','200+ nm','400 nm'],'correct'=>2,'explanation'=>'VHF line-of-sight range at FL300 over flat terrain is approximately 200 nm. Mountains significantly reduce this range.'],
    ['q'=>'During oceanic cruise on HF, your SELCAL sounds. What happened?','options'=>['Your transponder stopped working','Dispatch sent your aircraft\'s unique 4-tone code to wake up the radio','A nearby aircraft transmitted on your frequency','You have reached your destination waypoint'],'correct'=>1,'explanation'=>'SELCAL (Selective Calling) is an HF feature that allows silent monitoring. When dispatch sends your tail number\'s unique 4-tone code, the alert wakes the crew and brings the radio to life.'],
    ['q'=>'VHF1 fails in flight. What is your immediate action?','options'=>['Declare emergency immediately','Switch to VHF2 and notify ATC','Land at the nearest airport','Wait 5 minutes for VHF1 to reset'],'correct'=>1,'explanation'=>'VHF1 failure is an abnormal, not an emergency. Switch to VHF2 (or VHF3 if VHF2 also fails), notify ATC of the failure, and continue to destination if safe to do so.'],
    ['q'=>'Which system allows crew communication between the flight deck and cabin crew?','options'=>['VHF radio','ACARS','Interphone','PA system'],'correct'=>2,'explanation'=>'The Interphone system connects flight deck to cabin crew, ground crew, and isolated handheld units. ACARS is datalink; VHF is for ATC; PA is for passenger announcements.'],
    ['q'=>'What is the minimum crew oxygen pressure required before dispatch?','options'=>['As specified in your operator\'s MEL or Dispatch Manual','1000 PSI','1850 PSI (full cylinder)','500 PSI'],'correct'=>0,'explanation'=>'Minimum crew O2 pressure varies by operator and is specified in the Dispatch Manual or MEL. Do not attempt to memorize a specific number; always consult your operator\'s documentation.'],
    ['q'=>'Your quick-don oxygen mask is donned. Where is the microphone signal routed?','options'=>['Still to the audio panel headset','Automatically to the mask boom microphone','Disabled until you remove the mask','Routed to both the mask and the headset'],'correct'=>1,'explanation'=>'When the quick-don mask is donned, an electrical switch automatically connects the mask boom microphone to the intercom, bypassing the audio panel headset. This ensures uninterrupted communication on supplemental oxygen.'],
    ['q'=>'You lose all three VHF radios in flight. What is your next action?','options'=>['Switch to HF radio','Continue to destination and report after landing','Declare Mayday on 121.5 MHz and divert to nearest airport','Check the circuit breakers and reset the radio'],'correct'=>2,'explanation'=>'Loss of all VHF in flight is an emergency. Declare Mayday (even if you cannot hear ATC, they may hear you), broadcast position on 121.5 MHz, and divert to the nearest suitable airport immediately.'],
    ['q'=>'What does ACARS stand for?','options'=>['Airborne Communications And Reporting System','Aircraft Communication Addressing and Reporting System','Advanced Cockpit Alert and Response System','Automatic Crew Alert and Response System'],'correct'=>1,'explanation'=>'ACARS = Aircraft Communication Addressing and Reporting System. It is a digital datalink used for clearances, ATIS, weather, and company messages, reducing voice radio congestion.'],
  ]
]; }

// ── ATA31 INDICATING & RECORDING
function ata31_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Electronic flight instruments and crew alerting on the Q400',
      'time'=>'8 min','objective'=>'After this chapter, understand the EFIS suite, EICAS alerting system, and flight data recording.',
      'analogy'=>['label'=>'The Analogy — Glass Cockpit as Dashboard','text'=>'The PFD (Primary Flight Display) is like your car\'s speedometer and compass combined. The ND (Navigation Display) is your map. The MFD (Multi-Function Display) is your engine gauges. EICAS tells you what\'s broken (like a check-engine light). Together, they replace steam gauges.'],
      'body'=>'<p>The Q400 flight deck is equipped with an electronic flight instrument system (EFIS) consisting of three large, identical LCD displays. Two serve as Primary Flight Displays (PFDs); one serves as a Multi-Function Display (MFD). The displays are fully reversionary: if the left PFD fails, the MFD can be reconfigured to show PFD data on the right side, and vice versa.</p><p>The Engine Indication and Crew Alerting System (EICAS) continuously monitors engine parameters, aircraft systems, and external conditions. Alerts are color-coded and prioritized: Red warnings (Level 3) are the most urgent; Amber cautions (Level 2) require crew attention; Cyan advisories (Level 1) are informational. White status messages (Level 0) are non-alerting.</p><p>All flight data is recorded on the Digital Flight Data Recorder (DFDR) and Cockpit Voice Recorder (CVR), ensuring a complete record of every flight for safety and training purposes.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📊 EFIS at a Glance','list'=>['PFD (Primary Flight Display) × 2 — Attitude, airspeed, altitude, heading, VSI, FD bars','ND (Navigation Display) × 1 — Map, navigation sources, weather radar, TCAS','MFD (Multi-Function Display) × 1 — Engines, fuel, hydraulics, electrical synoptics','EICAS — Continuous system monitoring and alerting']],
        ['type'=>'amber','head'=>'⭐ Alert Priorities','table'=>['headers'=>['Level','Color','Type','Aural'],'rows'=>[['Level 3','Red','WARNING','Continuous bell'],['Level 2','Amber','CAUTION','Single chime'],['Level 1','Cyan','ADVISORY','None'],['Level 0','White','STATUS','None']]]],
      ],
      'quiz'=>['q'=>'The left PFD fails. Where can you display PFD information?','options'=>['Only on the right PFD (limited view)','On the ND (Navigation Display)','On the MFD in reversionary mode','Impossible—you must divert immediately'],'correct'=>2,'explanation'=>'The EFIS system is fully reversionary. If the left PFD fails, the MFD can be reconfigured to display PFD information, allowing continued flight with minimal crew workload increase.']
    ],
    [
      'badge'=>'Chapter 2','title'=>'PFD & Navigation Display','navTitle'=>'PFD & ND',
      'subtitle'=>'Primary instruments and navigation overview',
      'time'=>'12 min','objective'=>'Learn the layout and function of the Primary Flight Display and Navigation Display.',
      'analogy'=>['label'=>'The Analogy — PFD is Your Instrument Scan','text'=>'The PFD consolidates the old \"T\" scan (attitude, airspeed, altitude) plus heading, VSI, and vertical guidance. The ND is your moving map: it shows where you\'ve been, where you\'re going, and what\'s around you.'],
      'body'=>'<p>The Primary Flight Display (PFD) presents aircraft attitude as a digital artificial horizon at the center, with airspeed on the left and altitude on the right. The vertical speed indicator (VSI) is integrated on the altitude tape. The heading indicator appears at the bottom. Flight director bars (if engaged) overlay the horizon to guide pitch and roll inputs.</p><p>The top of each PFD shows autopilot and flight control annunciations: AP (autopilot engaged), FD (flight director active), A/T (autothrottle), and mode annunciations (APP, ALT, VNAV, LNAV). Radio altitude readout appears at the bottom when below 2500 feet AGL.</p><p>The Navigation Display (ND) shows the aircraft position, selected navigation source (GPS, VOR, or IRS blended), waypoints, airways, and the active flight plan. In Map mode, the display is north-up; in Rose mode, it is aircraft-heading-up. Weather radar overlay, TCAS targets, and terrain warnings (if equipped) enhance situational awareness.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📡 PFD Layout','table'=>['headers'=>['Element','Location','Information'],'rows'=>[['Airspeed tape','Left side','Current IAS (white/green/yellow/red arcs)'],['Altitude tape','Right side','Altitude (selected, current, VSI integrated)'],['Attitude indicator','Center','Pitch and bank angle; flight director bars'],['Heading indicator','Bottom','Magnetic heading; selected heading bug'],['Radio altitude','Bottom','Height AGL; appears <2500 ft'],['AP annunciations','Top','AP, FD, A/T modes and status']]]],
        ['type'=>'green','head'=>'🗺️ ND Display Modes','table'=>['headers'=>['Mode','Orientation','Use'],'rows'=>[['Map (PLAN)','North-up','En route navigation; overview of flight plan'],['Rose (ARC)','Heading-up','Approach and terminal area; relative heading awareness'],['Expanded','Range dependent','20 nm, 10 nm, 5 nm, 2.5 nm ranges available']]]],
        ['type'=>'blue','head'=>'📋 ND Overlay Options','list'=>['Flight plan waypoints and active route','Weather radar mosaic (precipitation)','TCAS (Traffic Collision Avoidance System) targets','Terrain warnings (if equipped)','Navigation source indicator (GPS, VOR, IRS)']],
      ],
      'quiz'=>['q'=>'On the PFD, where is the airspeed indicator located?','options'=>['Bottom center','Top left','Left side','Right side of the attitude indicator'],'correct'=>2,'explanation'=>'The airspeed tape is located on the left side of the PFD, with color-coded airspeed arcs (white, green, yellow, red) indicating aircraft performance envelope.']
    ],
    [
      'badge'=>'Chapter 3','title'=>'EICAS & Crew Alerting','navTitle'=>'EICAS',
      'subtitle'=>'Alert priorities, crew response, and system monitoring',
      'time'=>'11 min','objective'=>'Understand EICAS alert priorities and how the crew responds to alerts.',
      'analogy'=>['label'=>'The Analogy — EICAS as Your Health Monitor','text'=>'EICAS monitors your engines and systems like a doctor monitoring your vital signs. Red = emergency room; Amber = urgent care; Cyan = routine checkup; White = FYI.'],
      'body'=>'<p>The Engine Indication and Crew Alerting System (EICAS) is the Q400\'s watchdog. It continuously monitors engine parameters (N1, N2, EGT, fuel flow), hydraulic pressures, electrical status, pressurization, anti-ice, and fire detection. When an abnormal condition is detected, EICAS generates an alert message with a priority level indicated by color.</p><p>A Level 3 WARNING (red) indicates a critical condition requiring immediate crew action. Examples include engine overheat, hydraulic failure, or fire detection. A red MASTER WARNING light flashes on both glareshields, and a continuous bell sounds. Crew must immediately acknowledge the warning by pressing the MASTER WARNING button, then execute the appropriate emergency checklist.</p><p>A Level 2 CAUTION (amber) indicates a condition that requires crew attention but does not demand immediate emergency action. Examples include low fuel, pressurization degradation, or system performance degradation. An amber MASTER CAUTION light flashes, and a single chime sounds. Crew acknowledges by pressing the MASTER CAUTION light.</p><p>Level 1 ADVISORY (cyan) and Level 0 STATUS (white) messages provide system information with no aural alert. Crew reviews these messages as workload permits.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Level 3 WARNING Examples','list'=>['Engine fire or overheat','Hydraulic pressure loss (below minimum)','Fuel system failure','Loss of both vacuum sources','Cabin pressurization loss','Electrical bus failure']],
        ['type'=>'amber','head'=>'⚠️ Level 2 CAUTION Examples','list'=>['Low fuel quantity','Engine parameter degradation (trending high EGT)','Pressurization degradation (cabin altitude exceeding limit)','Instrument air filter clogged','Landing gear will not extend','Anti-ice system malfunction']],
        ['type'=>'blue','head'=>'📋 EICAS Response Priority','table'=>['headers'=>['Alert Level','Aural','Light','Crew Action Timing'],'rows'=>[['Level 3 (WARNING)','Continuous bell','Red Master Warning (flashing)','Immediate — stop non-essential tasks'],['Level 2 (CAUTION)','Single chime','Amber Master Caution (flashing)','Urgent — respond within seconds/minutes'],['Level 1 (ADVISORY)','None','None','As workload permits — review after flight'],['Level 0 (STATUS)','None','None','Informational — filed only']]]],
      ],
      'quiz'=>['q'=>'You see a red flashing light on the glareshield and hear a continuous bell. What is happening?','options'=>['A Level 2 CAUTION alert','A Level 3 WARNING alert','An advisory message','An engine parameter trending high'],'correct'=>1,'explanation'=>'A red flashing light + continuous bell = Level 3 WARNING, the most urgent alert. Immediately press MASTER WARNING, identify the warning, and execute emergency checklist.']
    ],
    [
      'badge'=>'Chapter 4','title'=>'Recorders, Standby & Reversionary','navTitle'=>'Recorders & Standby',
      'subtitle'=>'Data recording, standby instruments, and reversionary displays',
      'time'=>'10 min','objective'=>'Understand flight data recording, standby instrument function, and display reversionary modes.',
      'analogy'=>['label'=>'The Analogy — Recorders & Standby','text'=>'The CVR and DFDR are like a black box on an airplane—they record everything for safety investigations. Standby instruments are your backup mechanical gauges, in case all electronics fail. Display reversionary is your safety net if a big screen goes dark.'],
      'body'=>'<p>The Cockpit Voice Recorder (CVR) continuously records the last 2 hours of flight deck audio: radio transmissions, crew conversation, aural alerts, and ambient flight deck noise. The CVR is required for all FAR 121 operations and provides the definitive record of crew actions and communications. The 2-hour loop means the oldest recording is overwritten as new data is added.</p><p>The Digital Flight Data Recorder (DFDR) records aircraft parameters at 1-second intervals (or faster for critical data). A minimum of 88 parameters are recorded, including airspeed, altitude, heading, attitude, engine N1/N2, fuel quantity, hydraulic pressure, electrical bus voltage, and control surface positions. The DFDR provides the technical record of aircraft performance during an incident.</p><p>The Q400 is equipped with standby instruments: an airspeed indicator, altimeter, and attitude indicator powered by an independent battery. These instruments provide a backup if the main electrical system fails. The standby ASI shows airspeed; the altimeter shows altitude; the attitude indicator shows pitch and bank. These are analog instruments that do not require electricity from the main buses.</p><p>Display reversionary allows the MFD to be reconfigured as a PFD if a primary display fails. This capability ensures continued flight with acceptable crew workload.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📹 CVR & DFDR','table'=>['headers'=>['Recorder','Duration','Records'],'rows'=>[['CVR (Cockpit Voice Recorder)','2 hours (loop)','Cockpit audio, radio, alerts, crew conversation'],['DFDR (Digital Flight Data Recorder)','25 hours minimum','88+ flight parameters at 1+ Hz']]]],
        ['type'=>'green','head'=>'🪂 Standby Instruments (Battery-Powered)','list'=>['Airspeed Indicator — independent pitot/static; shows IAS','Altimeter — independent static pressure; shows altitude','Attitude Indicator — independent gyro; shows pitch & bank','Battery — independent from main electrical system; ~30 min autonomy']],
        ['type'=>'blue','head'=>'🔄 Display Reversionary','list'=>['If left PFD fails, MFD can display PFD information on right side','If right PFD fails, MFD can display PFD information on left side','ND functions remain on MFD if needed','Crew workload increases but flight can continue safely']],
      ],
      'quiz'=>['q'=>'How long does the CVR record?','options'=>['30 minutes (latest only)','1 hour','2 hours (loop recording)','4 hours'],'correct'=>2,'explanation'=>'The CVR continuously records the last 2 hours of flight deck audio. As new data is recorded, the oldest data is overwritten.'],
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'PFD FAIL','steps'=>['1. Verify right PFD still operational','2. If left PFD fails: Configure right PFD to display all critical information','3. Switch MFD to reversionary mode to display PFD data (heading, altitude, airspeed)','4. Brief crew: MFD now shows PFD information; ND data on right PFD','5. Continue flight if both PFDs remain operational or if MFD reversionary is active','6. If both PFDs fail, refer to standby instruments and declare abnormal'],'why'=>'PFD failure requires immediate crew recognition and reconfiguration to prevent loss of flight guidance.'],
    ['type'=>'abnormal','title'=>'EICAS RED WARNING','eicasMsg'=>'[VARIOUS] WARNING','items'=>['1. MASTER WARNING light flashes red on both glareshields','2. Continuous bell alerts crew','3. Press MASTER WARNING button (top of glareshield light) to silence bell','4. Identify the warning message on MFD/EICAS','5. Execute appropriate emergency checklist immediately','6. Declare emergency to ATC if in-flight; advise of nature of emergency'],'why'=>'Immediate acknowledgment and crew action minimizes impact of critical system failure.'],
    ['type'=>'limit','title'=>'Indicating & Recording Limitations','items'=>['CVR & DFDR: Inoperative recorder may restrict aircraft to MEL limits; consult operator documentation','Standby Instruments: Battery-powered; ~30 minutes autonomy; cannot sustain prolonged electrical failure','PFD/MFD Displays: At least one PFD must be operative for dispatch; full reversionary capability required','EICAS: All alerts must be functional; inoperative EICAS may prevent dispatch']],
  ],
  'quiz' => [
    ['q'=>'The left PFD fails. What happens to the ND (Navigation Display)?','options'=>['ND also fails—you must divert','ND remains operational on the MFD','ND is blank; only the right PFD works','ND is transferred to the right PFD in reversionary mode'],'correct'=>1,'explanation'=>'The three displays are independent. If the left PFD fails, the ND (Navigation Display) remains operational on the MFD. The MFD can also be reconfigured to show PFD information if needed.'],
    ['q'=>'You see a red flashing light and hear a continuous bell. This is a:','options'=>['Level 1 Advisory','Level 2 Caution','Level 3 Warning','Status message'],'correct'=>2,'explanation'=>'Red light + continuous bell = Level 3 WARNING. This is the most urgent alert. Immediately acknowledge and execute emergency checklist.'],
    ['q'=>'What is the minimum duration the DFDR (Digital Flight Data Recorder) must record?','options'=>['1 hour','4 hours','8 hours','25 hours'],'correct'=>3,'explanation'=>'FAR 121 requires a minimum 25-hour DFDR capacity. This allows investigation of incidents that occur during multi-leg operations.'],
    ['q'=>'The standby attitude indicator is powered by:','options'=>['Main electrical bus','Backup electrical bus','Independent battery','Pneumatic pressure'],'correct'=>2,'explanation'=>'Standby instruments (ASI, ALT, ATT) are powered by an independent battery, ensuring they remain available if the main electrical system fails.'],
    ['q'=>'What is the maximum duration of CVR recording?','options'=>['30 minutes','1 hour','2 hours (loop)','4 hours'],'correct'=>2,'explanation'=>'The CVR records the last 2 hours continuously. New data overwrites the oldest data, ensuring a recent record is always available.'],
    ['q'=>'EICAS generates a Level 2 alert (amber CAUTION). What is the aural signal?','options'=>['Continuous bell','Single chime','Double chime','No aural signal'],'correct'=>1,'explanation'=>'Level 2 CAUTION = amber light + single chime. This requires crew attention but not immediate emergency action.'],
    ['q'=>'The right PFD fails. Where can you display PFD information?','options'=>['Only on the left PFD','On the ND in reversionary mode','On the MFD in reversionary mode','Impossible—you must divert'],'correct'=>2,'explanation'=>'The MFD can be reconfigured to display PFD information if either PFD fails. This is the reversionary capability of the EFIS system.'],
    ['q'=>'Which recorder captures crew conversation and radio transmissions?','options'=>['DFDR','CVR','Both equally','Neither—they are stored on the FMS'],'correct'=>1,'explanation'=>'The CVR (Cockpit Voice Recorder) captures all audio on the flight deck. The DFDR records flight parameters only.'],
  ]
]; }

// ── ATA35 OXYGEN
function ata35_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Why oxygen matters and what happens without it',
      'time'=>'9 min','objective'=>'After this chapter, understand the physiology of hypoxia and why supplemental oxygen is required above 10,000 feet.',
      'analogy'=>['label'=>'The Analogy — Altitude as a Climbing Mountain','text'=>'At sea level, the air has plenty of oxygen. As you climb a mountain, the air gets thinner: fewer oxygen molecules per breath. At 25,000 feet, you\'re breathing less than half the oxygen per breath compared to sea level. Oxygen system is your portable sea-level bottle.'],
      'body'=>'<p>The atmosphere contains 21% oxygen at all altitudes, but atmospheric pressure decreases exponentially with altitude. At 10,000 feet, atmospheric pressure is half that of sea level, meaning each breath delivers 50% of the oxygen. At 25,000 feet (the Q400 service ceiling), each breath delivers only 25% of the oxygen available at sea level.</p><p>Human physiology depends on a minimum partial pressure of oxygen in the blood. Without supplemental oxygen, the brain becomes hypoxic above 10,000 feet, leading to degradation of vision (first), judgment, and coordination. Loss of consciousness can occur within minutes at 35,000 feet without oxygen.</p><p>The Q400 is equipped with crew oxygen (high-pressure gaseous system) and passenger oxygen (chemical generators). Crew oxygen allows extended operation at the 25,000-foot service ceiling. Passenger oxygen provides 12–15 minutes of continuous flow sufficient for descent to a safe altitude if cabin pressure is lost.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📈 Oxygen Requirements by Altitude','table'=>['headers'=>['Altitude','Requirement','Notes'],'rows'=>[['0–10,000 ft','None','Sufficient cabin pressure; normal breathing adequate'],['10,000–13,000 ft','Supplemental O2 available','Crew should use; passengers on demand generators'],['13,000–25,000 ft','Continuous O2 required','Crew masks on 100%; passengers continuous flow'],['>25,000 ft','Pressurization + continuous O2','Q400 service ceiling = 25,000 ft']]]],
        ['type'=>'amber','head'=>'⭐ Hypoxia Progression','list'=>['0–2 minutes: Visual acuity degradation (night vision first)','2–5 minutes: Impaired judgment, delayed reaction time','5–10 minutes: Confusion, loss of coordination','>10 minutes: Loss of consciousness (varies with individual fitness)']],
      ],
      'quiz'=>['q'=>'At FL250 (25,000 feet), each breath delivers approximately what percentage of sea-level oxygen?','options'=>['75%','50%','25%','10%'],'correct'=>2,'explanation'=>'At 25,000 feet, atmospheric pressure is roughly 25% of sea-level pressure. Without supplemental oxygen, hypoxia occurs rapidly.']
    ],
    [
      'badge'=>'Chapter 2','title'=>'Crew Oxygen System','navTitle'=>'Crew O2',
      'subtitle'=>'High-pressure gaseous oxygen, regulator modes, and quick-don masks',
      'time'=>'13 min','objective'=>'Understand the crew oxygen system, regulator operation, and emergency procedures.',
      'analogy'=>['label'=>'The Analogy — Regulator as a Water Faucet','text'=>'The oxygen regulator is like a water faucet: NORMAL = diluted (tap water), 100% = pure (from a filter), EMERGENCY = high pressure (pressure washer). Each setting provides appropriate oxygen for the altitude.'],
      'body'=>'<p>The Q400 crew oxygen system consists of one high-pressure gaseous oxygen cylinder pressurized to approximately 1850 PSI when full. The system is divided into two independent masks: one for the captain, one for the first officer. Each pilot has an individual oxygen line from the cylinder through a regulator to their quick-don mask.</p><p>The oxygen regulator provides three operational modes: NORMAL (diluter-demand), 100% (continuous), and EMERGENCY (positive pressure). In NORMAL mode, the regulator mixes cabin air with oxygen, providing a diluted mixture appropriate for cruise altitudes. When the cabin altitude exceeds 8,000 feet or at higher altitudes, the pilot switches to 100% mode, providing pure oxygen. In EMERGENCY mode (selected manually or automatically if cabin altitude exceeds 35,000 feet equivalent), the regulator supplies 100% oxygen under positive pressure, forcing oxygen into the lungs even if the mask is not perfectly sealed.</p><p>The quick-don oxygen mask is a modern design that allows a pilot to don the mask in approximately 5 seconds. The harness automatically inflates around the head when pulled over the face, creating an airtight seal. The mask includes a boom microphone that automatically switches to the interphone when the mask is donned (see ATA23). The mask also includes integral smoke goggles that deploy if needed.</p><p>Minimum crew oxygen pressure is specified in the operator\'s Dispatch Manual. Typical minimums are 500–600 PSI, allowing at least 30 minutes of oxygen supply. Do not guess at the minimum; consult your operator\'s documentation.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'⚙️ Oxygen Regulator Modes','table'=>['headers'=>['Mode','Setting','Function','Use'],'rows'=>[['NORMAL','Diluter-demand','Mixes cabin air + O2; demands O2 on inhalation','Cruise below 13,000 ft'],['100%','Continuous flow','Pure O2 supplied continuously','Cruise FL130–FL250; all high-altitude'],['EMERGENCY','Positive pressure','100% O2 forced into lungs','Rapid decompression; emergency descent']]]],
        ['type'=>'green','head'=>'🎭 Quick-Don Oxygen Mask','table'=>['headers'=>['Feature','Description','Benefit'],'rows'=>[['Donning time','~5 seconds','Rapid oxygen access during emergency'],['Automatic sealing','Harness inflates on donning','Airtight seal without adjustment'],['Boom mic','Integrates into interphone','Uninterrupted communication on O2'],['Smoke goggles','Integral, deploy if needed','Protection if cabin smoke suspected']]]],
        ['type'=>'amber','head'=>'⭐ Key Numbers','list'=>['Cylinder pressure when full: ~1850 PSI','Typical minimum dispatch pressure: 500–600 PSI (check your operator\'s manual)','Mask donning time: 5 seconds','Oxygen supply duration: Varies; at FL250 with continuous demand, ~90 minutes at 1850 PSI']],
      ],
      'quiz'=>['q'=>'You are climbing through FL140 during a rapid ascent. What oxygen regulator mode should you select?','options'=>['NORMAL (diluter-demand)','100% (continuous flow)','EMERGENCY (positive pressure)','Depends on cabin pressure'],'correct'=>1,'explanation'=>'At FL140 (or any altitude above FL130), select 100% mode to provide continuous pure oxygen. NORMAL mode is adequate only below FL130 in a pressurized aircraft.']
    ],
    [
      'badge'=>'Chapter 3','title'=>'Passenger Oxygen System','navTitle'=>'Pax O2',
      'subtitle'=>'Chemical oxygen generators, mask deployment, and supply duration',
      'time'=>'10 min','objective'=>'Understand the passenger oxygen system and response to rapid decompression.',
      'analogy'=>['label'=>'The Analogy — Chemical Generator as a Glow Stick','text'=>'Passenger oxygen uses a chemical reaction (sodium chlorate candle) that generates oxygen when activated, just like a glow stick generates light when activated. Unlike crew oxygen (bottled), passenger oxygen burns a chemical to make oxygen on demand.'],
      'body'=>'<p>The Q400 passenger oxygen system consists of chemical oxygen generators installed above each passenger seat. Each generator is a single-use device containing a sodium chlorate candle that generates oxygen through an exothermic chemical reaction when activated. The reaction is triggered automatically when the cabin altitude exceeds 14,000 feet, or manually by a flight attendant pulling a yellow/red mask housing downward.</p><p>Once activated, the chemical generator provides continuous oxygen flow for approximately 12–15 minutes, sufficient for descent from the maximum cruise altitude (FL250) to 10,000 feet. At a 2,000-feet-per-minute descent rate, descent from FL250 to FL100 requires about 7 minutes—well within the 12–15 minute supply window.</p><p>Passengers are briefed at the beginning of each flight on mask donning procedures: pull the mask downward and toward you, place over nose and mouth, and breathe normally. The oxygen flow is continuous and does not require deep breathing to activate (unlike some older demand masks).</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🎭 Passenger Oxygen Mask Deployment','table'=>['headers'=>['Event','Activation','Deployment'],'rows'=>[['Automatic activation','Cabin altitude >14,000 ft','Masks drop automatically; flight attendants instruct passengers'],['Manual activation','Pilot or flight attendant pulls housing','Passenger pulls mask forward and places over nose/mouth']]]],
        ['type'=>'green','head'=>'⏱️ Passenger O2 Supply Duration','list'=>['Supply duration: 12–15 minutes continuous flow','Typical descent time (FL250 → FL100): 7–8 minutes at 2,000 fpm','Safety margin: 4–7 minutes remaining','Sufficient for controlled descent to safe altitude (10,000 ft)']],
        ['type'=>'amber','head'=>'⭐ Passenger O2 Key Facts','list'=>['Chemical generators: Single-use, sodium chlorate candle reaction','Coverage: One mask per seat (or group of seats depending on configuration)','Safety: If masks drop, instruct passengers to place over nose/mouth immediately','Post-flight: Used generators cannot be reactivated; must be replaced before next flight']],
      ],
      'quiz'=>['q'=>'At what cabin altitude do passenger oxygen masks deploy automatically?','options'=>['10,000 ft','12,000 ft','14,000 ft','16,000 ft'],'correct'=>2,'explanation'=>'Passenger oxygen masks deploy automatically when cabin altitude exceeds 14,000 feet. This provides a safety margin before hypoxia becomes imminent.']
    ],
    [
      'badge'=>'Chapter 4','title'=>'Abnormals & Limitations','navTitle'=>'Abnormals',
      'subtitle'=>'Crew O2 failures, rapid decompression, and dispatch requirements',
      'time'=>'9 min','objective'=>'Understand oxygen system failures and limitations.',
      'analogy'=>['label'=>'The Analogy — O2 Failure is Life-Threatening','text'=>'Loss of crew oxygen at altitude is like losing air in a submarine. You must descend immediately; there is no \"wait and fix\" option.'],
      'body'=>'<p>Crew oxygen system failure at altitude is a critical emergency. If the oxygen system fails above FL100, the affected pilot must don the quick-don mask immediately. If both oxygen supplies fail simultaneously, both pilots must descend to FL100 (or lower, depending on available oxygen) and declare emergency. At FL100, cabin pressure provides sufficient oxygen for limited time, but crew performance degrades rapidly.</p><p>Rapid decompression (explosive loss of cabin pressure) is rare but catastrophic. If cabin pressure is lost while the aircraft is above FL140, passenger masks deploy automatically, and the flight crew immediately selects EMERGENCY oxygen and begins a steep descent to FL100. The first officer handles the emergency descent while the captain manages system failures and communication.</p><p>Minimum crew oxygen pressure is specified in the operator\'s Dispatch Manual and MEL. Consult your manual before every flight. A typical minimum is 500–600 PSI, but this varies. Do not dispatch with inoperative crew oxygen above FL100.</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚨 Crew O2 Failure at Altitude','items'=>['If O2 sys fail above FL100: Don mask immediately, select EMERGENCY mode','Initiate descent to FL100 at maximum safe rate','Declare emergency to ATC; advise nature of emergency','If both O2 supplies fail: Dual descent required; both pilots on masks; declare emergency immediately']],
        ['type'=>'red','head'=>'🚨 Rapid Decompression Response','items'=>['1. Don quick-don oxygen mask immediately (5 seconds)','2. Select EMERGENCY oxygen (automatic or manual)','3. Begin descent to FL100 at maximum safe rate (nose-down attitude ~20°)','4. Notify ATC: "Rapid decompression; descending to [altitude]"','5. First officer manages descent; captain diagnoses cabin pressure source','6. Continue descent below FL100 for aircraft pressurization check']],
        ['type'=>'amber','head'=>'⭐ Dispatch Limitations','list'=>['Crew oxygen minimum pressure: Per operator MEL (typically 500–600 PSI minimum)','Oxygen system inoperative: Aircraft unserviceable above FL100','Passenger oxygen inoperative: Check with operator MEL; may restrict to FL250 or specific alternates','Quick-don mask availability: Both crew masks must be operational and accessible']],
      ],
      'quiz'=>['q'=>'Both crew oxygen regulators fail at FL200. What is your immediate action?','options'=>['Continue to destination at FL200','Descend to FL100 and declare emergency','Try resetting the oxygen system circuit breakers','Declare emergency and descend to FL100 immediately'],'correct'=>3,'explanation'=>'Loss of both crew oxygen supplies at altitude is life-threatening. Declare emergency immediately and begin descent to FL100 at maximum safe rate.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'CREW O2 DONNING','steps'=>['1. Reach up and pull quick-don mask harness down and forward','2. Place mask over nose and mouth (harness will inflate automatically)','3. Ensure mask is sealed; adjust harness if needed','4. Select oxygen regulator mode: NORMAL (below FL130) or 100% (FL130+)','5. Test mask by covering regulator intake and inhaling—you should feel suction','6. Verify boom microphone is seated in mask; audio should switch from headset to mask mic'],'why'=>'Rapid, correct donning ensures uninterrupted oxygen supply and communication during hypoxic emergency.'],
    ['type'=>'abnormal','title'=>'RAPID DECOMPRESSION','eicasMsg'=>'CABIN ALT (automatic or manual alert)','items'=>['1. Don quick-don oxygen mask immediately; select EMERGENCY','2. Flight control: Begin descent to FL100 at maximum safe rate (nose-down ~20°)','3. Flight deck crew: Don masks; advise each other of mask status','4. Cabin crew: Instruct passengers to place oxygen masks over nose/mouth; sit down','5. ATC: Declare emergency; advise "Rapid decompression; descending FL[current]00 to FL100"','6. After descent below FL100: Check cabin pressure source; advise ATC of situation; proceed to nearest suitable airport'],'why'=>'Rapid decompression requires immediate oxygen supply and steep descent to safe altitude before hypoxia incapacitates crew.'],
    ['type'=>'limit','title'=>'Oxygen System Limitations','items'=>['Crew oxygen minimum dispatch pressure: Per operator MEL (consult Dispatch Manual)','Crew oxygen system inoperative: Aircraft unserviceable above FL100','Passenger oxygen inoperative: Consult operator MEL; may restrict altitude or require specific alternates','Quick-don masks: Both masks must be accessible and functional before dispatch','Portable oxygen: Several bottles available in cabin for crew first aid; check availability pre-flight']],
  ],
  'quiz' => [
    ['q'=>'At FL250 with crew oxygen system inoperative, what is your required action?','options'=>['Continue to destination; use passenger masks as backup','Descend to FL100 immediately; declare emergency','Descend to FL100; no emergency declaration needed','Level off at FL200 and assess system'],'correct'=>1,'explanation'=>'Crew oxygen inoperative above FL100 is life-threatening. You must descend to FL100 immediately and declare emergency to ATC.'],
    ['q'=>'Passenger oxygen masks deploy automatically at what cabin altitude?','options'=>['10,000 ft','12,000 ft','14,000 ft','16,000 ft'],'correct'=>2,'explanation'=>'Automatic deployment occurs at 14,000 feet cabin altitude. This provides adequate time for crew to respond before passenger hypoxia becomes critical.'],
    ['q'=>'How long does a passenger oxygen generator supply oxygen?','options'=>['5 minutes','8 minutes','12–15 minutes','20 minutes'],'correct'=>2,'explanation'=>'Chemical generators provide 12–15 minutes of continuous oxygen, sufficient for descent from FL250 to FL100 (approximately 7–8 minutes at 2000 fpm).'],
    ['q'=>'What regulator mode should you select at FL180?','options'=>['NORMAL (diluter-demand)','100% (continuous)','EMERGENCY (positive pressure)','Depends on cabin altitude'],'correct'=>1,'explanation'=>'At FL180 (or any altitude above FL130), select 100% mode to provide pure oxygen. NORMAL mode is sufficient only below FL130 in a pressurized aircraft.'],
    ['q'=>'Quick-don oxygen mask donning time is approximately:','options'=>['2 seconds','5 seconds','10 seconds','15 seconds'],'correct'=>1,'explanation'=>'The quick-don mask is designed for rapid donning—approximately 5 seconds. This speed is critical for emergency response.'],
    ['q'=>'What is the typical minimum crew oxygen pressure for dispatch?','options'=>['200 PSI','400 PSI','500–600 PSI (per operator MEL)','1000 PSI'],'correct'=>2,'explanation'=>'Minimum crew oxygen pressure varies by operator but is typically 500–600 PSI. Always consult your operator\'s Dispatch Manual and MEL before flight.'],
    ['q'=>'During rapid decompression at FL200, what oxygen mode should you select?','options'=>['NORMAL','100%','EMERGENCY','Depends on descent rate'],'correct'=>2,'explanation'=>'During rapid decompression, immediately select EMERGENCY mode to provide positive-pressure 100% oxygen, then begin steep descent to FL100.'],
    ['q'=>'You are climbing through FL100 during pressurization failure. Cabin altitude is rising. What should you happen?','options'=>['Continue climb; cabin pressure may stabilize','Level off at current altitude','Descend immediately; select oxygen as needed','Declare emergency and climb to FL250'],'correct'=>2,'explanation'=>'If cabin pressure fails during climb, descend immediately to maintain safe crew oxygen supply and cabin environment. Level off at FL100 if oxygen is available, or lower if oxygen fails.'],
  ]
]; }

// ── ATA33 LIGHTING
function ata33_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'Aircraft lighting systems and operational requirements',
      'time'=>'8 min','objective'=>'After this chapter, understand the purpose and operation of exterior and interior lighting systems.',
      'analogy'=>['label'=>'The Analogy — Aircraft Lighting as Road Signs','text'=>'Navigation lights tell other aircraft where you\'re flying (red left, green right, white tail—same as ships). Anti-collision lights make you visible (like hazard flashers). Landing lights illuminate the runway (like high beams). Emergency lights guide exit routes if power is lost.'],
      'body'=>'<p>Aircraft lighting systems are critical for safety, visibility, and regulatory compliance. Exterior lighting includes navigation lights (always on in operation), anti-collision lights (beacon + strobes), landing lights, taxi lights, and wing inspection lights. Interior lighting includes flight deck instruments, cabin lighting, and emergency evacuation lighting.</p><p>The Q400 is equipped with emergency lighting powered by an independent battery, separate from the main electrical system. Emergency lights must be armed before every flight and automatically activate if main electrical power is lost, ensuring visibility of exit routes during evacuation.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'💡 Lighting Systems Overview','list'=>['Navigation lights (Red/Green/White) — Always on during flight','Anti-collision lights (Beacon + Strobes) — On during engine start/flight','Landing & Taxi lights — On during taxi, takeoff, approach, <10,000 ft','Wing inspection lights — On at night/icing to check for ice accretion','Emergency lighting — Battery-powered; independent of main electrical system']],
        ['type'=>'amber','head'=>'⭐ Key Rules','list'=>['Beacon: ON before engine start, OFF after last engine shutdown','Strobes: ON when entering runway; OFF after landing','Landing lights: ON below 10,000 ft','Emergency lights: ARM before every flight (safety checklist)']],
      ],
      'quiz'=>['q'=>'At what altitude should landing lights be illuminated?','options'=>['Below 15,000 feet','Below 10,000 feet','Below 5,000 feet','At any altitude during daylight approach'],'correct'=>1,'explanation'=>'Landing lights should be ON below 10,000 feet AGL to improve runway visibility during approach and landing.']
    ],
    [
      'badge'=>'Chapter 2','title'=>'Exterior Lighting','navTitle'=>'Exterior',
      'subtitle'=>'Navigation lights, anti-collision, landing, taxi, and inspection lights',
      'time'=>'12 min','objective'=>'Understand the purpose, operation, and regulatory requirements of exterior lighting.',
      'analogy'=>['label'=>'The Analogy — Exterior Lights as Visibility Tags','text'=>'Navigation lights (red/green/white) tell other pilots your aircraft\'s orientation (like a ship\'s running lights). Anti-collision lights make you a moving target (like a lighthouse). Landing lights illuminate the ground (like a car headlight).'],
      'body'=>'<p>Navigation lights provide the first visual clue of aircraft orientation to other pilots. The red light is on the left (port) wing, the green light on the right (starboard) wing, and the white light on the tail. These are always ON when the aircraft is in operation (engines running or in flight). Navigation light intensity and coverage angles are specified by regulation.</p><p>The anti-collision system consists of a red beacon (usually mounted top and bottom of fuselage) that flashes at a regulated rate. White strobes on the wingtips provide a bright, attention-grabbing flash. The beacon and strobes operate together as the anti-collision system, improving visibility to other aircraft in visual flight conditions.</p><p>Landing lights (typically installed on the nose gear or bottom of fuselage) illuminate the runway during approach and landing. Two landing lights provide redundancy and wider coverage. Landing lights should be ON below 10,000 feet AGL and for all takeoff and landing operations.</p><p>The taxi light (nose gear mounted) illuminates the ground directly ahead during taxi and low-speed movement. The taxi light is typically ON during pushback, taxi to runway, and post-landing taxi to gate.</p><p>Wing inspection lights (mounted on fuselage near each wing leading edge) illuminate the wing surface to allow visual inspection for ice accretion during icing conditions at night. These lights are ON whenever icing conditions exist and visibility requires external reference (night operations or IMC).</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'🔴🟢⚪ Navigation Lights','table'=>['headers'=>['Light','Location','Color','Function'],'rows'=>[['Left nav','Left wing tip','Red','Shows left (port) side of aircraft'],['Right nav','Right wing tip','Green','Shows right (starboard) side of aircraft'],['Tail nav','Tail fuselage','White','Shows rear of aircraft; assists in identification']]]],
        ['type'=>'green','head'=>'🚨 Anti-Collision Lights','table'=>['headers'=>['Component','Location','Pattern','Function'],'rows'=>[['Beacon','Top & bottom fuselage','Steady flash (~1 Hz)','Identifies aircraft type and orientation in darkness'],['Strobes','Wingtips','Bright white flash','High-intensity visibility to other aircraft']]]],
        ['type'=>'blue','head'=>'💡 Landing & Taxi Lights','list'=>['Landing lights (×2) — Illuminate runway during approach/landing; OFF <FL100 in cruise','Taxi light — Illuminates ground ahead during taxi; ON during pushback','Wing inspection lights — Illuminate wing leading edge; ON at night in icing']],
        ['type'=>'amber','head'=>'⭐ Lighting Operational Rules','list'=>['Beacon: ON before engine start; OFF after last engine shutdown','Strobes: ON when entering the runway; OFF after landing','Navigation lights: Always ON during flight','Landing lights: ON below 10,000 ft (descent/approach); OFF in cruise above 10,000 ft','Taxi light: ON during ground movement; OFF after takeoff','Inspection lights: ON at night in visible moisture/icing clouds']],
      ],
      'quiz'=>['q'=>'When should the landing lights be turned on during descent?','options'=>['At FL100','Below 10,000 ft','10 minutes before landing','When ATC clears you to land'],'correct'=>1,'explanation'=>'Landing lights should be ON below 10,000 feet AGL during descent and approach. They remain ON until after landing and taxi to the gate is complete.']
    ],
    [
      'badge'=>'Chapter 3','title'=>'Interior & Emergency Lighting','navTitle'=>'Interior & Emergency',
      'subtitle'=>'Flight deck, cabin, and battery-powered emergency lighting',
      'time'=>'11 min','objective'=>'Understand interior lighting and emergency lighting requirements.',
      'analogy'=>['label'=>'The Analogy — Emergency Lights as Backup Plan','text'=>'Emergency lighting is your backup if the main electrical system fails. Battery-powered lights guide exit routes so passengers can evacuate safely in the dark.'],
      'body'=>'<p>Flight deck interior lighting includes instrument panel lights (dimmable), map lights (adjustable intensity), and overhead flood lights. These lights are powered by main electrical buses and provide sufficient illumination for night operation. The instrument panel lighting is dimmable to reduce glare at night and is typically set to a consistent level across both pilot stations.</p><p>Cabin lighting includes passenger compartment overhead lights (controlled from flight deck or flight attendant panels), emergency illumination markers, and floor-level escape path lighting. Cabin lighting is typically set to a moderate level during cruise and reduced (night mode) during cruise segments to allow passenger rest.</p><p>Emergency lighting is powered by an independent battery, separate from the main electrical system. Emergency lights illuminate exit doors, escape routes, and floor-level strips guiding passengers to exits. The emergency lighting system must be ARMED (switched to ARM position) before every flight, typically as part of the pre-flight safety checklist. When armed, if main electrical power is lost, the emergency battery automatically activates and lights all emergency signs and floor-level lighting.</p><p>Exit signs are permanently illuminated (separate battery) and do not require arming. These are always visible to guide passenger orientation.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'💡 Flight Deck Lighting','list'=>['Instrument panel lights — Dimmable; set to match intensity across both stations','Overhead flood lights — Provide general illumination during night ops','Map lights — Adjustable intensity; typically red at night to preserve night vision']],
        ['type'=>'green','head'=>'🚪 Emergency Lighting System','table'=>['headers'=>['Component','Power Source','Activation','Purpose'],'rows'=>[['Emergency battery','Independent battery','ARM before flight; automatic on power loss','Powers all emergency lighting'],['Exit signs','Separate battery','Always live','Guides passenger orientation'],['Floor-level escape lights','Emergency battery','Automatic on power loss','Guides exit route during evacuation']]]],
        ['type'=>'amber','head'=>'⭐ Emergency Lighting Procedure','list'=>['ARM emergency lights before every flight (safety checklist item)','Verify all emergency signs are illuminated during pre-flight','If main electrical power is lost, emergency lighting automatically activates','Exit signs are always illuminated (independent battery)','Emergency battery autonomy: ~30 minutes (varies by aircraft config)']],
      ],
      'quiz'=>['q'=>'When should emergency lights be armed?','options'=>['5 minutes before flight','During engine start','Before every flight (safety checklist)','Only at night'],'correct'=>2,'explanation'=>'Emergency lights must be armed before every flight as a standard safety checklist item. This ensures the independent battery is ready to power evacuation lighting if main electrical power is lost.']
    ],
    [
      'badge'=>'Chapter 4','title'=>'SOPs & Limitations','navTitle'=>'SOPs',
      'subtitle'=>'Lighting operational procedures and dispatch requirements',
      'time'=>'9 min','objective'=>'Understand standard lighting procedures and system limitations.',
      'analogy'=>['label'=>'The Analogy — Lighting as a Checklist Dance','text'=>'Lighting has a rhythm: beacon ON before start, strobes ON when you taxi, landing lights ON when descending below 10,000 ft, emergency lights ARM before every flight. Following the rhythm keeps you compliant and safe.'],
      'body'=>'<p>Standard operating procedures for lighting are straightforward but critical. The beacon is the first light ON (before engine start) and the last light OFF (after engine shutdown). Strobes are ON while the aircraft is moving on the ground or in flight. Navigation lights are always ON during flight. Landing lights are ON below 10,000 feet and during all landing/takeoff phases.</p><p>Emergency lighting must be armed before every flight and checked during pre-flight. A quick visual scan ensures all exit signs and emergency markers are illuminated. If emergency lights cannot be armed, the aircraft may be unserviceable or restricted by MEL depending on operator policy.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📋 Lighting Sequence','table'=>['headers'=>['Phase','Lights Action'],'rows'=>[['Pre-flight','Emergency lights: ARM; verify illumination'],['Engine start','Beacon: ON'],['Taxi','Navigation lights: ON; strobes: ON; taxi light: ON'],['Takeoff','Landing lights: ON; strobes: ON'],['Climb','Strobes: ON; landing lights: OFF above 10,000 ft'],['Cruise','Navigation lights: ON; strobes: ON; beacon: OFF (some operators)'],['Descent','Below 10,000 ft: Landing lights: ON'],['Landing','Landing lights: ON; strobes: ON'],['Post-landing','Taxi light: ON; strobes: OFF after clear of runway'],['Shutdown','All lights OFF; beacon: OFF last']]]],
        ['type'=>'amber','head'=>'⭐ Key Limitations','list'=>['Emergency lights inoperative: Aircraft may be unserviceable or restricted by MEL','Landing lights inoperative: Consult MEL; may restrict night operations','Navigation lights inoperative: Consult MEL; may prevent flight','Anti-collision (beacon/strobes) inoperative: Consult MEL; usually requires day VFR only']],
      ],
      'quiz'=>['q'=>'You are climbing through FL100 after takeoff. Landing lights should be:','options'=>['ON (leave them on)','OFF (turn them off above 10,000 ft)','ON for another 5 minutes then OFF','Depends on outside visibility'],'correct'=>1,'explanation'=>'Landing lights should be OFF above 10,000 feet in cruise to reduce electrical load and heat generation. They are turned back ON when descending below 10,000 feet.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'EMERGENCY LIGHTING CHECKLIST','steps'=>['1. Before every flight: Switch emergency lights to ARM position','2. Visually verify all exit signs are illuminated','3. Verify floor-level escape path lighting is visible (if applicable)','4. If emergency lights cannot be armed, log defect and consult MEL','5. Emergency lights will automatically activate if main electrical power is lost','6. After landing, verify emergency lights function by cycling ARM/ON/OFF'],'why'=>'Emergency lighting is essential for safe passenger evacuation. Arming before every flight ensures battery is charged and system is functional.'],
    ['type'=>'abnormal','title'=>'EMERGENCY LIGHTS FAIL','eicasMsg'=>'EMERG LIGHTS FAIL or unable to arm','items'=>['1. Attempt to cycle emergency light switch (ARM to ON to OFF to ARM)','2. If lights still will not illuminate, check circuit breaker for emergency lighting','3. Log defect in maintenance log','4. Consult operator MEL for flight restrictions','5. Aircraft may be unserviceable depending on MEL; contact dispatch'],'why'=>'Emergency lighting failure may prevent flight dispatch or require operating restrictions (daylight VFR only, specific alternates, etc.).'],
    ['type'=>'limit','title'=>'Lighting System Limitations','items'=>['Emergency lights: Must be armed before every flight; independent battery autonomy ~30 minutes','Landing lights inoperative: Consult MEL; may restrict night operations or require specific alternates','Navigation lights inoperative: Consult MEL; may prevent IFR flight','Anti-collision lights inoperative: Consult MEL; typically restricted to day VFR only','Beacon ON before engine start: Required by FAA regulation']],
  ],
  'quiz' => [
    ['q'=>'At what altitude should landing lights be illuminated?','options'=>['Below 15,000 feet','Below 10,000 feet','Below 5,000 feet','Only during final approach'],'correct'=>1,'explanation'=>'Landing lights should be ON below 10,000 feet AGL. They improve runway visibility during descent, approach, and landing. They are typically OFF in cruise above 10,000 feet.'],
    ['q'=>'When should the beacon light be turned on?','options'=>['When entering the runway','5 minutes before engine start','Before engine start (pre-flight)','After takeoff clearance'],'correct'=>2,'explanation'=>'The beacon light must be ON before engine start and should be the last light turned OFF after engine shutdown.'],
    ['q'=>'Anti-collision lights consist of:','options'=>['Only the red beacon','Only the white strobes','Red beacon and white strobes together','Navigation lights plus strobe'],'correct'=>2,'explanation'=>'The anti-collision system includes both the red beacon (usually mounted top and bottom fuselage) and white strobes (on wingtips). Both operate together.'],
    ['q'=>'When should emergency lights be armed?','options'=>['5 minutes before takeoff','During engine start','Before every flight (standard safety checklist)','Only during night flights'],'correct'=>2,'explanation'=>'Emergency lights must be armed before every flight, both day and night, as a standard safety checklist item. This ensures the independent battery is ready.'],
    ['q'=>'What is the primary function of wing inspection lights?','options'=>['Improve landing light coverage','Illuminate the runway during approach','Allow visual inspection of wing leading edge for ice','Improve exterior aircraft visibility to other aircraft'],'correct'=>2,'explanation'=>'Wing inspection lights illuminate the wing leading edge to allow visual ice accretion checks during icing conditions, especially at night.'],
    ['q'=>'Navigation lights should be on:','options'=>['Only during night flights','During taxi and flight (engines running)','Only during takeoff and landing','Only in IMC'],'correct'=>1,'explanation'=>'Navigation lights (red, green, white) should be ON whenever the aircraft is in operation (engines running or in flight), both day and night.'],
    ['q'=>'After reaching cruise altitude above 10,000 feet, landing lights should be:','options'=>['Remain ON for entire flight','Turned OFF to reduce electrical load','Turned OFF only at night','Depend on outside visibility'],'correct'=>1,'explanation'=>'Landing lights should be turned OFF above 10,000 feet in cruise. They are turned back ON when descending below 10,000 feet.'],
    ['q'=>'Emergency lighting is powered by:','options'=>['Main electrical bus','Battery bus #1','Independent battery (separate from main electrical system)','Hydraulic backup power'],'correct'=>2,'explanation'=>'Emergency lighting has an independent battery separate from the main electrical system. This ensures emergency lights remain operational even if main power is completely lost.'],
  ]
]; }

// ── FMS FLIGHT MANAGEMENT SYSTEM
function fms_content() { return [
  'chapters' => [
    [
      'badge'=>'Introduction','title'=>'The Big Picture','navTitle'=>'Big Picture',
      'subtitle'=>'The FMS as the aircraft\'s brain for navigation and performance',
      'time'=>'8 min','objective'=>'After this chapter, understand the FMS role in navigation, performance, and automation.',
      'analogy'=>['label'=>'The Analogy — FMS as a Pilot\'s Co-Pilot','text'=>'The FMS is like a co-pilot who never sleeps: it plans the route, calculates the speeds, manages the climb/descent profile, and checks the navigation. Pilot tells the FMS the destination; FMS handles the details.'],
      'body'=>'<p>The Flight Management System (FMS) is the central navigational and performance computer on the Q400. It integrates route planning, navigation source selection, performance calculations, and vertical flight profile management. The FMS uses a combination of GPS, Inertial Reference System (IRS), and VOR/DME navigation to determine aircraft position and provide lateral and vertical navigation guidance to the autopilot.</p><p>The FMS database includes a navigation database (updated every 28 days per AIRAC—Aeronautical Information Regulation and Control) and a performance database containing aircraft weight/balance, fuel, and speed data. The navigation database is mandatory for RNP (Required Navigation Performance) and RNAV procedures; an expired database forces a return to conventional navigation (VOR, ILS).</p><p>The FMS consists of two Control Display Units (CDUs) — one for each pilot — and one or more FMS computers. The CDUs allow pilots to enter the route, verify performance calculations, and monitor navigation progress. If the FMS fails, the aircraft reverts to raw instrument data and conventional navigation, significantly increasing crew workload.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'⚙️ FMS Core Functions','list'=>['Route entry and flight plan management','V-speed calculation (V1, VR, V2) based on weight, temperature, runway','Navigation source blending (GPS + IRS + VOR/DME)','Lateral navigation (LNAV) guidance to autopilot','Vertical navigation (VNAV) climb/descent profile management']],
        ['type'=>'amber','head'=>'⭐ FMS Database Requirements','list'=>['Navigation database: AIRAC 28-day cycle; must be current for RNP/RNAV','Performance database: Aircraft weight, balance, fuel capacity, thrust tables','Expired database = cannot use RNP/RNAV procedures; revert to conventional nav']],
      ],
      'quiz'=>['q'=>'What navigation database update cycle is required for RNP procedures?','options'=>['52-week (annual)','28-day (AIRAC)','Every 7 days','No update required for RNP'],'correct'=>1,'explanation'=>'RNP procedures require an AIRAC (Aeronautical Information Regulation and Control) navigation database updated every 28 days. Expired databases prevent RNP certification.']
    ],
    [
      'badge'=>'Chapter 2','title'=>'Route & Navigation Programming','navTitle'=>'Route & Navigation',
      'subtitle'=>'FMS flight plan entry, SID/STAR selection, and navigation source',
      'time'=>'13 min','objective'=>'Understand how to enter a flight plan into the FMS and verify navigation accuracy.',
      'analogy'=>['label'=>'The Analogy — FMS Route as a GPS Route','text'=>'You enter departure, SID, waypoints, airways, STAR, and destination into the FMS, just like you enter an address into a car GPS. The FMS then calculates the best route and guides you there.'],
      'body'=>'<p>The FMS flight plan is entered via the Control Display Unit (CDU), typically on the INIT (Initialization) page. The flight plan includes: departure airport and runway, SID (Standard Instrument Departure), en route waypoints and airways, STAR (Standard Arrival Route), approach type, and destination airport. Each element is entered using the CDU keyboard or scratchpad.</p><p>The FMS calculates a great-circle route from departure to destination, automatically sequencing waypoints and altitude restrictions encoded in SIDs and STARs. The active waypoint is highlighted; upon reaching each waypoint, the FMS automatically sequences to the next waypoint.</p><p>Navigation source selection is automatic blending of GPS, IRS, and ground-based navaids (VOR/DME). The FMS continuously monitors position accuracy and alerts the crew if accuracy degrades below acceptable limits for the current procedure. If GPS is lost, the FMS seamlessly transitions to IRS/VOR-DME blending. If all navigation sources are lost, the FMS displays raw position data and flight crews must hand-fly to a nearby VOR or use ILS.</p><p>The FMS verifies that the aircraft position matches the loaded flight plan. If position error exceeds limits, an alert is annunciated, and the crew must address the discrepancy before continuing RNP operations.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'📋 FMS Flight Plan Structure','table'=>['headers'=>['Element','Entry','Example'],'rows'=>[['Departure','Airport ID + runway','KORD RWY28L'],['SID','Departure procedure name','KORD KEYES5 SID'],['En route','Waypoints/airways','J70 to MOJOS waypoint to J100'],['Altitude restrictions','FL/altitude at each waypoint','FL250 at MOJOS; FL350 enroute'],['STAR','Arrival procedure','LAX SADDE2 STAR'],['Approach','Approach type','ILS 24L'],['Destination','Airport ID','KLAX']]]],
        ['type'=>'green','head'=>'🌍 Navigation Sources','table'=>['headers'=>['Source','Coverage','Accuracy'],'rows'=>[['GPS','Global (when available)','±10–100 m, depending on mode'],['IRS (Inertial)','No external reference','Drifts over time (~1–2 nm/hour)'],['VOR/DME','Ground-based; continental','±0.25 nm from station']]]],
        ['type'=>'blue','head'=>'📡 FMS Navigation Source Blending','list'=>['GPS primary when available (high accuracy)','IRS blended for continuity and redundancy','VOR/DME used if GPS degraded','FMS alerts crew if position accuracy unacceptable for procedure']],
      ],
      'quiz'=>['q'=>'You are departing KJFK with an expired FMS navigation database. What procedures can you legally fly?','options'=>['RNP approaches','RNAV SIDs and STARs','Conventional VOR and ILS approaches','Any approach type'],'correct'=>2,'explanation'=>'An expired FMS database prevents RNP and RNAV procedures. You must revert to conventional navigation (VOR, ILS, DME). RNP and RNAV require a current AIRAC database.']
    ],
    [
      'badge'=>'Chapter 3','title'=>'Performance & V-Speeds','navTitle'=>'Performance',
      'subtitle'=>'FMS performance calculations, V-speed computation, and flight planning',
      'time'=>'12 min','objective'=>'Understand how the FMS calculates V-speeds and manages weight/balance for takeoff.',
      'analogy'=>['label'=>'The Analogy — V-Speeds as Breakpoints','text'=>'V1 is the decision speed (commit to takeoff or abort), VR is the rotation speed (pull back on yoke), V2 is the climb speed (minimum safe climb speed after engine failure). The FMS calculates these based on weight, temperature, and runway.'],
      'body'=>'<p>The FMS Performance page allows entry of aircraft weight, fuel quantity, and outside air temperature. The FMS then calculates three critical takeoff speeds: V1 (decision speed), VR (rotation speed), and V2 (minimum climb speed after engine failure). These speeds are displayed on the speed tape on the primary flight display and help the crew manage the takeoff.</p><p>V1 is the maximum speed at which the flight crew can reject a takeoff and safely stop on the remaining runway. If engine failure occurs before V1, the crew must abort. After V1, the takeoff must continue even with an engine failure (assuming sufficient runway remains).</p><p>VR is the rotation speed — the speed at which the captain rotates the control column to lift the nose and begin takeoff climb. VR is typically 5–10 knots above V1.</p><p>V2 is the minimum safe climb speed after losing an engine. If an engine fails after V1, the aircraft must be able to climb at V2 speed; failure to achieve V2 climb indicates an unsafe condition.</p><p>The FMS also calculates optimum cruise altitude, fuel burn, and arrival fuel prediction. These calculations help flight planning and contingency analysis.</p>',
      'cards'=>[
        ['type'=>'blue','head'=>'✈️ V-Speed Definitions','table'=>['headers'=>['Speed','Definition','Action'],'rows'=>[['V1','Decision speed','If engine fails before V1, abort; if after V1, continue'],['VR','Rotation speed','Pitch up to begin climb (pilot action)'],['V2','Minimum safe climb speed','Minimum speed after engine failure; climb capability']],'steps'=>['Takeoff: V1 → Engine failure → VR → Climb at V2']]],
        ['type'=>'green','head'=>'📊 FMS Performance Inputs','list'=>['Aircraft weight (ZFW—zero-fuel weight + fuel quantity)','Outside air temperature (OAT)','Runway length and elevation','Wind direction and speed','Runway surface type (dry, wet, contaminated)']],
        ['type'=>'amber','head'=>'⭐ V-Speed Calculation Factors','list'=>['Weight: Heavier aircraft = higher V-speeds','Temperature: Hot day = higher V-speeds (lower air density)','Runway: Shorter runway = lower V-speeds (less distance to stop)','Wind: Headwind = lower V-speeds; tailwind = higher V-speeds']],
      ],
      'quiz'=>['q'=>'V1 (decision speed) on a takeoff run has what significance?','options'=>['The rotation speed (pitch up)','The minimum safe climb speed','The maximum speed to reject takeoff','The speed to level off after climb'],'correct'=>2,'explanation'=>'V1 is the decision speed—the maximum speed at which you can safely reject and stop on the remaining runway. Above V1, you must continue takeoff even with engine failure.']
    ],
    [
      'badge'=>'Chapter 4','title'=>'FMS Failures & Abnormals','navTitle'=>'Failures',
      'subtitle'=>'FMS failures, database expiry, and reversion to conventional navigation',
      'time'=>'10 min','objective'=>'Understand FMS failure procedures and limitations.',
      'analogy'=>['label'=>'The Analogy — FMS Loss as Losing GPS','text'=>'If your car\'s GPS fails, you revert to paper maps and reading road signs. If the FMS fails, you revert to raw instruments (ILS, VOR) and hand-flying. Workload increases, but you can continue safely.'],
      'body'=>'<p>Complete FMS failure (loss of both CDUs or FMS computer) requires immediate crew action. The flight must revert to raw instrument data: ILS for approach, VOR/DME for en route, GPS standalone if available. The autopilot continues to function but requires manual input from raw data rather than automated navigation guidance. Crew workload increases significantly; a second crew or rest management is critical for long flights without FMS.</p><p>Partial FMS failure (one CDU inoperative, but FMS computer operational) allows continued use of the operational CDU. Both pilots can view FMS data on the remaining display. Navigation continues normally. If the FMS computer fails but CDUs remain powered, navigation displays raw data only.</p><p>Database expiry prevents use of RNP and RNAV procedures. The FMS will alert the crew that the database is expired and prohibit selection of RNP approaches or RNAV SIDs/STARs. The aircraft must revert to conventional navigation. Plan alternates with conventional approaches available (ILS, VOR, NDB).</p>',
      'cards'=>[
        ['type'=>'red','head'=>'🚨 FMS Failure Response','items'=>['Complete FMS failure: Declare abnormal; switch to raw instrument navigation','Revert to ILS for approach, VOR/DME for en route, GPS as backup','Autopilot continues to function but requires manual input','Crew workload increases significantly']],
        ['type'=>'amber','head'=>'⚠️ FMS Database Expiry','items'=>['Expired database: Cannot use RNP/RNAV procedures','Revert to conventional navigation (VOR, ILS, NDB)','Alert from FMS: "NAV DB EXPIRED"','Plan alternates with conventional approaches available (no RNAV SIDs/STARs)']],
        ['type'=>'blue','head'=>'📋 Partial FMS Failures','list'=>['One CDU inoperative: Operate with single CDU; cross-check data regularly','FMS computer partial failure: Limited functionality; verify all calculations manually','Autothrottle inoperative: Manual throttle control required; still use FMS for navigation']],
      ],
      'quiz'=>['q'=>'The FMS alerts "NAV DB EXPIRED." What procedures are now prohibited?','options'=>['ILS approaches','VOR approaches','RNP and RNAV procedures','Emergency approaches only'],'correct'=>2,'explanation'=>'An expired FMS navigation database prevents use of RNP and RNAV procedures. Standard ILS, VOR, and NDB approaches are still available but RNP and RNAV are prohibited.']
    ],
  ],
  'qrh' => [
    ['type'=>'memory','title'=>'FMS INITIALIZATION','steps'=>['1. Power on FMS CDUs (both left and right)','2. Enter departure airport and runway on INIT page','3. Enter destination airport','4. Verify aircraft weight and fuel quantity on PERF page','5. Calculate V1, VR, V2 based on conditions (enter OAT, runway elevation)','6. Load SID (departure procedure) and STAR (arrival procedure) from database','7. Verify flight plan on FPLN page: waypoints, altitudes, airways','8. Cross-check position: FMS position should match known position (within 5–10 nm)','9. Verify navigation database: Check AIRAC effective date; confirm current (not expired)'],'why'=>'Proper FMS initialization ensures accurate navigation, performance calculations, and flight planning before departure.'],
    ['type'=>'abnormal','title'=>'FMS FAILURE','eicasMsg'=>'FMS FAIL (if available)','items'=>['1. Verify both CDUs powered; if inoperative, use single CDU or raw data','2. If complete FMS loss: Switch to raw navigation (VOR/DME, ILS, GPS)','3. Inform ATC: "Request conventional routing; unable to comply with RNAV procedures"','4. Load conventional approach (ILS or VOR); divert to airport with conventional approach available','5. Verify alternate airport has non-RNAV approaches (VOR, ILS, NDB)','6. Crew workload: Increase cross-checks and manual calculations'],'why'=>'FMS failure requires immediate reversion to raw navigation and crew awareness of significantly increased workload.'],
    ['type'=>'limit','title'=>'FMS System Limitations','items'=>['Navigation database: Must be current AIRAC (28-day cycle) for RNP/RNAV procedures','FMS failure: Reverts to raw instruments; significant workload increase','Database expiry: RNP and RNAV procedures prohibited; use conventional nav only','Performance calculation: Requires accurate weight and temperature inputs; verify pre-flight','Autopilot: FMS guidance lost; manual input required; autopilot may revert to basic modes']],
  ],
  'quiz' => [
    ['q'=>'When must the FMS navigation database be updated for RNP procedures?','options'=>['Every 52 weeks','Every 28 days (AIRAC cycle)','As needed; no specific requirement','Before every flight if it exists'],'correct'=>1,'explanation'=>'RNP (Required Navigation Performance) procedures require a current AIRAC navigation database updated every 28 days. Expired databases prohibit RNP and RNAV procedures.'],
    ['q'=>'What are the three critical V-speeds calculated by the FMS?','options'=>['V1, Vne, Vs1','V1, VR, V2','VS, Vne, Vyse','Vy, Vx, Vno'],'correct'=>1,'explanation'=>'V1 = decision speed, VR = rotation speed, V2 = minimum climb speed (with engine failure). These are the three critical takeoff speeds calculated by the FMS.'],
    ['q'=>'The FMS displays "NAV DB EXPIRED." What approach types can you legally fly?','options'=>['RNP only','RNAV SIDs only','VOR and ILS approaches only','Any approach type'],'correct'=>2,'explanation'=>'An expired database prevents RNP and RNAV procedures but allows conventional VOR, ILS, and NDB approaches.'],
    ['q'=>'If the FMS completely fails in flight, what is the primary reversion mode?','options'=>['Autopilot takes over all navigation','Revert to raw instruments and hand-fly','Aircraft automatically descends to safe altitude','FMS resets automatically after 5 minutes'],'correct'=>1,'explanation'=>'Complete FMS failure requires reversion to raw instrument navigation (ILS, VOR, DME, GPS), and the flight crew must hand-fly with manual autopilot inputs.'],
    ['q'=>'What input does the FMS require to calculate V1, VR, and V2?','options'=>['Only departure airport','Weight, temperature, runway length/elevation, wind','Fuel quantity and aircraft type only','Destination airport only'],'correct'=>1,'explanation'=>'V-speed calculation requires aircraft weight, outside air temperature, runway length and elevation, and wind direction/speed. All these factors affect the calculated speeds.'],
    ['q'=>'The FMS shows your position 15 nm off the loaded flight plan route. What should you do?','options'=>['Continue; FMS will correct','Immediately declare emergency','Check position against external references (VOR, GPS); may indicate navigation error or flight plan error','Reduce altitude to improve navigation accuracy'],'correct'=>2,'explanation'=>'A 15 nm position deviation may indicate a navigation source error or incorrect flight plan entry. Cross-check against VOR and GPS; correct the error before continuing RNP operations.'],
    ['q'=>'During FMS initialization, what does AIRAC represent?','options'=>['Automatic Instrument Recalibration And Calibration','Aircraft Integrated Reporting And Communication','Aeronautical Information Regulation And Control','Airborne Intelligent Routing And Computation'],'correct'=>2,'explanation'=>'AIRAC = Aeronautical Information Regulation And Control. It defines the 28-day navigation database update cycle required for RNP and RNAV procedures.'],
    ['q'=>'What is V1 on a takeoff roll?','options'=>['The speed to start rotation (pull back yoke)','The decision speed—max speed to abort and stop safely','The climb speed after takeoff','The maximum altitude before descent'],'correct'=>1,'explanation'=>'V1 is the decision speed: the maximum speed at which you can reject the takeoff and safely stop on the remaining runway. Above V1, you must continue takeoff.'],
  ]
]; }

// ── CW - CAUTION & WARNING SYSTEM

function cw_content() {
  return [
    'chapters' => [
      [
        'badge' => 'Introduction',
        'title' => 'Alert Philosophy',
        'navTitle' => 'Big Picture',
        'subtitle' => 'Crew Alerting and Safety Priorities',
        'time' => '8 min',
        'objective' => 'Understand the caution and warning system design philosophy and alert priorities.',
        'analogy' => [
          'label' => 'The Analogy — Emergency Services Dispatch',
          'text' => 'When you call 911, they ask: Is it life-threatening (send helicopter), serious (send ambulance), or minor (send standard response)? The caution/warning system does the same: red warning = emergency (immediate action), amber caution = urgent (prompt action), cyan advisory = awareness only. Pilots respond to the most critical alert first.'
        ],
        'body' => '<p>The Caution and Warning (C&W) system is designed to alert the crew to aircraft system anomalies requiring crew attention. The system uses a prioritization scheme based on severity and urgency. Master WARNING (red flashing light + continuous bell) indicates Level 3 alerts—most critical conditions such as engine fire, terrain warning, or configuration failure. Master CAUTION (amber flashing light + single chime) indicates Level 2 alerts—system degradation or anomalies requiring prompt action. Level 1 (cyan) and Level 0 (white) are advisory and informational only with no aural alert. Alert inhibition logic suppresses certain cautions during takeoff (80 knots to 400 feet) to prevent crew distraction during critical flight phases. The crew response priority is universal: Fire > GPWS > Warning > Caution > Advisory. This ensures the most critical threats are addressed first. Understanding alert priorities and proper response procedures is essential for safe operations.</p>',
        'cards' => [
          [
            'type' => 'red',
            'head' => '🔴 Alert Severity Levels',
            'table' => [
              'headers' => ['Level', 'Color', 'Audio', 'Examples', 'Action'],
              'rows' => [
                ['3 - WARNING', 'RED', 'Bell', 'Engine fire, config fail, GPWS', 'Immediate'],
                ['2 - CAUTION', 'AMBER', 'Chime', 'Hydraulic low, electrical fail', 'Prompt'],
                ['1 - ADVISORY', 'CYAN', 'None', 'System monitor, low fuel', 'Monitor'],
                ['0 - STATUS', 'WHITE', 'None', 'Information only', 'Acknowledge']
              ]
            ]
          ],
          [
            'type' => 'blue',
            'head' => '📋 Crew Response Priority',
            'list' => [
              '1. Fire alerts: Highest priority—immediate life safety threat',
              '2. GPWS (Ground Proximity Warning): Terrain collision threat',
              '3. Master WARNING alerts: System failure or configuration error',
              '4. Master CAUTION alerts: System degradation or anomaly',
              '5. Advisory alerts: Monitor situation; non-urgent awareness',
              '6. Status messages: Information only; acknowledge and continue'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the crew response priority when multiple alerts are active?',
          'options' => [
            'Always respond to the first alert that appeared',
            'Respond based on severity priority: Fire > GPWS > Warning > Caution > Advisory',
            'Respond based on alphabetical order of the alert message',
            'Respond based on the pilot\'s personal judgment'
          ],
          'correct' => 1,
          'explanation' => 'Crew response priority is fixed: Fire > GPWS > Warning > Caution > Advisory. This ensures the most critical threats (fire, terrain) are addressed first, regardless of which alert appears first on the display.'
        ]
      ],
      [
        'badge' => 'Chapter 2',
        'title' => 'Warning & Caution Lights',
        'navTitle' => 'Warning & Caution',
        'subtitle' => 'Master Warning and Caution Light Operation',
        'time' => '10 min',
        'objective' => 'Understand Master Warning and Caution light operation and response procedures.',
        'analogy' => [
          'label' => 'The Analogy — Traffic Light System',
          'text' => 'Red light = stop now (Master Warning). Yellow light = slow down and prepare to stop (Master Caution). Green light = proceed safely (no alert). Both pilots see the same light so both know an alert is active. The light is your first cue to check EICAS.'
        ],
        'body' => '<p>The Master WARNING light is a red flashing light visible on both the captain\'s and first officer\'s glare shields, ensuring both pilots are immediately alerted. When a Level 3 alert is triggered, the Master WARNING light flashes and a continuous bell sounds. This is the most urgent alert; it demands immediate crew attention. Examples include engine fire, configuration warning (takeoff with flaps not set correctly), GPWS alerts, and certain system failures. The Master CAUTION light is an amber flashing light on both glare shields with a single chime. It indicates a Level 2 alert requiring prompt crew attention. Examples include hydraulic pressure low, electrical system failure, or fuel system imbalance. When either light activates, the crew must immediately check the EICAS display to read the CAS (Crew Alerting System) message. The CAS message describes the specific alert and may reference a QRH procedure. Some alerts are "sticky"—they remain displayed even if the underlying condition temporarily clears (e.g., fire warnings). The crew must read the full message, consult the QRH, and follow the prescribed procedure to resolve the alert.</p>',
        'cards' => [
          [
            'type' => 'red',
            'head' => '🔴 Master WARNING Response',
            'steps' => [
              '1. Red flashing light + continuous bell activates',
              '2. Both pilots immediately focus on EICAS display',
              '3. Read CAS message completely (may be multiple screens)',
              '4. Identify alert type (fire, GPWS, configuration, etc.)',
              '5. Open QRH to the applicable procedure',
              '6. Execute memory items if required (fire drill, etc.)',
              '7. Follow checklist steps',
              '8. Once resolved, acknowledge light (light stops flashing)'
            ]
          ],
          [
            'type' => 'blue',
            'head' => '📋 Master CAUTION Response',
            'steps' => [
              '1. Amber flashing light + single chime activates',
              '2. Both pilots check EICAS display',
              '3. Read CAS message (may be Level 2 or lower priorities)',
              '4. Assess urgency: some cautions allow crew to complete current task',
              '5. Open QRH to the applicable procedure',
              '6. Follow checklist steps (non-memory items typically)',
              '7. Monitor system status during resolution',
              '8. Acknowledge light once completed'
            ]
          ],
          [
            'type' => 'amber',
            'head' => '⚠️ Alert Characteristics',
            'list' => [
              'Master WARNING: Red, flashing, loud continuous bell (90+ dB)',
              'Master CAUTION: Amber, flashing, single chime (70 dB)',
              'CAS messages: Appear in priority order on EICAS display',
              'Sticky alerts: Fire warnings remain displayed until manually cleared',
              'Inhibited alerts: Some cautions inhibited during takeoff (80-400 ft)',
              'Both pilots alerted: Lights visible on both glare shields'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the difference between the Master WARNING and Master CAUTION lights?',
          'options' => [
            'Master WARNING is blue, Master CAUTION is red',
            'Master WARNING is red flashing + continuous bell (Level 3); Master CAUTION is amber flashing + chime (Level 2)',
            'Master WARNING is for engine failures; Master CAUTION is for electrical failures',
            'They are the same; the color depends on the time of day'
          ],
          'correct' => 1,
          'explanation' => 'Master WARNING (red, continuous bell) is Level 3—immediate action required. Master CAUTION (amber, single chime) is Level 2—prompt action required. This visual and aural distinction helps the crew prioritize their response.'
        ]
      ],
      [
        'badge' => 'Chapter 3',
        'title' => 'CAS Messages & Priorities',
        'navTitle' => 'CAS Messages',
        'subtitle' => 'Crew Alerting System Message Priority and Management',
        'time' => '11 min',
        'objective' => 'Master CAS message prioritization, display sequencing, and message content interpretation.',
        'analogy' => [
          'label' => 'The Analogy — Phone Call Waiting System',
          'text' => 'When you have multiple calls waiting, the phone system puts them in a queue—most important calls first. CAS messages do the same: highest priority alert displays first. Once you acknowledge and address it, the next priority message displays. This ensures the crew always addresses the most critical problem first.'
        ],
        'body' => '<p>CAS (Crew Alerting System) messages are text messages that appear on the EICAS display describing specific system alerts. Messages are generated by monitoring software that continuously checks aircraft parameters. CAS messages are prioritized by level and type. Level 3 (RED - WARNING) messages are highest priority and displayed first. Level 2 (AMBER - CAUTION) messages follow. Level 1 (CYAN - ADVISORY) messages are lower priority and may not cause an aural alert. Level 0 (WHITE - STATUS) messages are informational only. Within each level, messages are further prioritized by type (e.g., fire warnings are highest within Level 3). When multiple alerts exist, the highest-priority message displays, and the crew addresses it. Once the message is acknowledged (by pressing a button), the next-priority message appears. Some messages are "inert"—they don\'t clear until the underlying condition is resolved (e.g., ENGINE FIRE remains even if fire extinguishing is initiated, forcing crew to read the message repeatedly until fire is confirmed extinguished). Crew must read each message completely and understand what it means before consulting the QRH.</p>',
        'cards' => [
          [
            'type' => 'blue',
            'head' => '📋 CAS Message Structure',
            'table' => [
              'headers' => ['Message Type', 'Format', 'Example', 'Crew Action'],
              'rows' => [
                ['System Failure', '[System] [Condition]', 'ENGINE 1 OVERHEAT', 'QRH > procedure'],
                ['Configuration', 'CONFIG [detail]', 'CONFIG FLAPS', 'Correct configuration'],
                ['Fire/Overheat', '[System] FIRE/OVERHEAT', 'APU FIRE', 'Memory items + QRH'],
                ['Pressure Alert', '[System] PRESSURE [state]', 'HYD 1 PRESSURE LOW', 'Monitor + action']
              ]
            ]
          ],
          [
            'type' => 'green',
            'head' => '✓ CAS Message Management',
            'steps' => [
              '1. Master Warning/Caution activates (light + aural alert)',
              '2. Crew immediately focuses on EICAS display',
              '3. Read the highest-priority CAS message completely',
              '4. If message is fire or critical: execute memory items immediately',
              '5. Open QRH and find the applicable procedure',
              '6. Follow checklist steps to diagnose/resolve issue',
              '7. Press acknowledgment button to clear message from display',
              '8. Next-priority message displays (if any)',
              '9. Repeat process for all messages'
            ]
          ],
          [
            'type' => 'red',
            'head' => '🔴 Critical CAS Messages (Non-Memory)',
            'list' => [
              'ENGINE [n] FIRE: Demands immediate engine shutdown and fire extinguishing',
              'TERRAIN ALERT: GPWS terrain warning—immediate pull-up required',
              'CONFIG FLAPS: Takeoff configuration incorrect—abort takeoff or correct immediately',
              'CABIN PRESSURE: Rapid decompression detected—emergency descent',
              'ENGINE OVERHEAT: Engine temperature critical—monitor closely, prepare to shut down',
              'ELECTRICAL FAILURE: Loss of generator or electrical system—reduce electrical load'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'How are multiple CAS messages prioritized when several alerts are active simultaneously?',
          'options' => [
            'Messages displayed in order of when they were triggered',
            'Messages displayed alphabetically by system name',
            'Highest-priority message displayed first; after acknowledgment, next-priority displays',
            'All messages displayed simultaneously on multiple screens'
          ],
          'correct' => 2,
          'explanation' => 'CAS messages are prioritized by level (Level 3 highest) and type within level. The highest-priority message displays; once acknowledged, the next-priority message appears. This ensures the crew addresses the most critical problem first.'
        ]
      ],
      [
        'badge' => 'Chapter 4',
        'title' => 'Configuration Warnings & Special Alerts',
        'navTitle' => 'Special Alerts',
        'subtitle' => 'Configuration Warnings, GPWS, and Unique Alert Types',
        'time' => '10 min',
        'objective' => 'Understand configuration warning system, GPWS operation, and special alert handling.',
        'analogy' => [
          'label' => 'The Analogy — Pre-Flight Checklist Safety Lock',
          'text' => 'Before launching a rocket, engineers check that all systems are configured correctly. If the fuel valve is closed or hatch not sealed, the launch pad locks the system. The configuration warning does the same: if takeoff is attempted with flaps wrong, a horn blares. It\'s the system forcing you to fix the problem before continuing.'
        ],
        'body' => '<p>Configuration warnings are unique alerts that prevent unsafe takeoffs. The most common configuration warning is FLAP ERROR: if the aircraft is accelerating down the runway with flaps not set to 5° or 10° (for takeoff), a continuous horn sounds. This forces the pilots to abort the takeoff and correct the flap setting. Configuration warnings may also include landing gear position, speed brake extension, or other items that must be in a correct state for takeoff. GPWS (Ground Proximity Warning System) provides terrain and obstacle awareness. GPWS generates four types of alerts: Mode 1 (terrain closure), Mode 2 (terrain closure rate), Mode 3 (altitude loss), Mode 4 (unsafe terrain clearance), Mode 5 (glide slope deviation), Mode 6 (minimums), Mode 7 (terrain ahead). Modes 1-3 and 7 are warnings (red, immediate pull-up required). Modes 4-6 are cautions. All GPWS warnings require immediate action—pull up and climb away from terrain. These are not false alarms; crews must always respond to GPWS warnings. After pulling up and gaining altitude, the crew evaluates the situation. GPWS false alarms are rare; the system is designed to err on the side of safety (alerting when no terrain is actually a threat) rather than missing a real threat.</p>',
        'cards' => [
          [
            'type' => 'red',
            'head' => '🔴 Configuration Warning System',
            'steps' => [
              '1. Takeoff initiated: Acceleration down runway begins',
              '2. Configuration check: System monitors flap, gear, speed brake positions',
              '3. Condition incorrect: If flaps not at 5° or 10°, continuous horn activates',
              '4. Crew response: Immediately abort takeoff (pull throttles to idle, apply brakes)',
              '5. Exit runway: Once stopped, correct flap position',
              '6. Review checklist: Verify all takeoff configuration items',
              '7. Retry takeoff: Begin takeoff procedure again'
            ]
          ],
          [
            'type' => 'red',
            'head' => '🔴 GPWS Alert Types',
            'table' => [
              'headers' => ['Mode', 'Trigger', 'Alert Type', 'Crew Action'],
              'rows' => [
                ['Mode 1', 'Terrain ahead/below', 'WARNING (red)', 'Immediate pull-up'],
                ['Mode 2', 'Terrain closure rate high', 'WARNING (red)', 'Immediate pull-up'],
                ['Mode 3', 'Altitude loss after takeoff', 'WARNING (red)', 'Immediate pull-up'],
                ['Mode 4', 'Unsafe terrain clearance', 'CAUTION (amber)', 'Monitor/climb'],
                ['Mode 5', 'Glide slope deviation', 'CAUTION (amber)', 'Correct altitude'],
                ['Mode 6', 'Minimum altitude', 'CAUTION (amber)', 'Begin descent/land'],
                ['Mode 7', 'Terrain ahead (landing)', 'WARNING (red)', 'Go-around/climb']
              ]
            ]
          ],
          [
            'type' => 'green',
            'head' => '✓ GPWS Response Procedure',
            'steps' => [
              '1. GPWS warning activates: Terrain alert or closure detected',
              '2. Immediate action: Set pitch to climb (nose up) and apply full power',
              '3. Terrain avoidance: Gain altitude to avoid collision',
              '4. Retract landing gear: If landing gear down (approach/landing), raise gear',
              '5. Retract flaps: If flaps extended, reduce to climb setting',
              '6. Climb to safe altitude: Achieve positive terrain clearance',
              '7. Assess situation: Once safe, evaluate what triggered warning',
              '8. Plan recovery: Continue to safe airport or plan approach'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What happens if the crew attempts to take off with flaps not properly configured?',
          'options' => [
            'A warning message displays on the EICAS, but takeoff is allowed',
            'A continuous horn sounds, forcing the crew to abort takeoff',
            'The engine power automatically limits to prevent takeoff',
            'The aircraft automatically retracts the landing gear'
          ],
          'correct' => 1,
          'explanation' => 'The configuration warning system activates a continuous horn if takeoff is attempted with flaps in an incorrect position. This forces the crew to abort the takeoff and correct the flap setting before attempting takeoff again.'
        ]
      ]
    ],
    'qrh' => [
      [
        'type' => 'memory',
        'title' => 'Master WARNING - Fire Alert',
        'steps' => [
          '1. Red flashing light + continuous bell',
          '2. Identify fire location: Check EICAS CAS message (ENGINE FIRE, APU FIRE, etc.)',
          '3. Engine fire: Set engine fire switch (if equipped) and throttle to idle',
          '4. Engine fire: Deploy fire extinguisher per procedure',
          '5. Declare emergency to ATC: "Mayday, engine fire, descending to [nearest airport]"',
          '6. Land immediately: Divert to nearest suitable airport',
          '7. QRH: Open Fire Procedures checklist immediately'
        ],
        'why' => 'Fire is the most critical alert. Memory items for engine fire must be executed immediately—delay increases severity.'
      ],
      [
        'type' => 'abnormal',
        'title' => 'Master CAUTION - System Failure',
        'eicasMsg' => 'CAUTION light + amber CAS message (e.g., HYD PRESSURE LOW)',
        'items' => [
          'Read EICAS message to identify affected system',
          'Assess impact: Can flight continue or must divert?',
          'Open QRH to the system-specific abnormal procedure',
          'Follow checklist steps to diagnose/mitigate issue',
          'Monitor system status during flight',
          'Plan diversion if system cannot be restored'
        ],
        'why' => 'Caution alerts indicate system degradation. Prompt action prevents escalation to warning-level severity.'
      ],
      [
        'type' => 'abnormal',
        'title' => 'GPWS Terrain Warning (Mode 1/2/3)',
        'eicasMsg' => 'TERRAIN ALERT or PULL UP - red alert',
        'items' => [
          'Immediate action: Set pitch to climb (nose up)',
          '2. Apply full engine power',
          '3. Retract landing gear (if down)',
          '4. Retract flaps (if extended)',
          '5. Climb to safe altitude (at least 1,000 ft AGL)',
          '6. Once safe: Assess situation and plan recovery'
        ],
        'why' => 'GPWS terrain warnings are never false alarms requiring crew response. Immediate pull-up is the only correct action.'
      ],
      [
        'type' => 'limit',
        'title' => 'Caution & Warning System Limits',
        'items' => [
          'Alert inhibition: Some cautions inhibited from 80 knots to 400 ft takeoff to reduce workload',
          'Configuration warning: Applies only during takeoff acceleration (continuous horn if flaps wrong)',
          'Master Warning persistence: Some warnings remain displayed until manually cleared (fire)',
          'Alert priority: Fire > GPWS > Warning > Caution > Advisory (always)',
          'Both pilots alerted: Master lights visible on both glare shields for crew awareness'
        ]
      ]
    ],
    'quiz' => [
      [
        'q' => 'What is the crew response priority when multiple alerts are active simultaneously?',
        'options' => [
          'Respond to alerts in the order they appear on the EICAS display',
          'Respond based on severity priority: Fire > GPWS > Warning > Caution > Advisory',
          'Respond to the alert that triggered the loudest aural signal',
          'Crew can choose the order based on their assessment'
        ],
        'correct' => 1,
        'explanation' => 'Alert response priority is fixed and universal: Fire > GPWS > Warning > Caution > Advisory. This ensures the most critical threats are addressed first, regardless of display order.'
      ],
      [
        'q' => 'What is the purpose of the configuration warning system?',
        'options' => [
          'To monitor engine performance during takeoff',
          'To prevent unsafe takeoffs by alerting if aircraft is improperly configured',
          'To calculate optimal takeoff performance',
          'To automatically correct flight control positions'
        ],
        'correct' => 1,
        'explanation' => 'The configuration warning system prevents unsafe takeoffs by sounding a continuous horn if critical items (flaps, landing gear, speed brakes) are not in the correct position for takeoff. This forces the crew to abort and correct before attempting takeoff.'
      ],
      [
        'q' => 'How should the crew respond to a GPWS (Ground Proximity Warning System) terrain alert?',
        'options' => [
          'Reduce power and descend to avoid terrain',
          'Immediately climb and apply full power to gain altitude and avoid terrain',
          'Contact ATC to request higher altitude',
          'Retract landing gear and continue current flight path'
        ],
        'correct' => 1,
        'explanation' => 'GPWS terrain warnings require immediate pull-up action: set pitch to climb, apply full power, retract landing gear and flaps, and climb to safe altitude. GPWS alerts are never false alarms.'
      ],
      [
        'q' => 'What is the difference in aural alert between Master WARNING and Master CAUTION?',
        'options' => [
          'Master WARNING is a chime, Master CAUTION is a bell',
          'Master WARNING is a continuous bell, Master CAUTION is a single chime',
          'Both produce the same continuous bell sound',
          'Master WARNING has no aural alert, only visual'
        ],
        'correct' => 1,
        'explanation' => 'Master WARNING produces a loud continuous bell (emergency-level urgency). Master CAUTION produces a single chime (urgent but less critical). This aural difference helps the crew immediately recognize alert severity.'
      ],
      [
        'q' => 'At what point during takeoff is the configuration warning activated if flaps are incorrect?',
        'options' => [
          'Before the engines are started',
          'Immediately when the aircraft begins moving on the runway',
          'When the aircraft reaches rotation speed',
          'After the aircraft has rotated and is airborne'
        ],
        'correct' => 1,
        'explanation' => 'The configuration warning activates during takeoff acceleration on the runway if flaps are not in the proper takeoff position (5° or 10°). A continuous horn sounds, forcing the crew to abort the takeoff.'
      ],
      [
        'q' => 'What should the crew do immediately when a GPWS terrain warning activates?',
        'options' => [
          'Check the navigation system to verify position',
          'Reduce airspeed to get a better look at terrain',
          'Immediately climb (nose up) and apply full engine power',
          'Radio ATC for navigation assistance'
        ],
        'correct' => 2,
        'explanation' => 'GPWS terrain warnings demand immediate action: set pitch to climb, apply full power, retract landing gear, reduce flaps. Gaining altitude to clear terrain is the only appropriate response.'
      ]
    ]
  ];
}

// ── QRH - QUICK REFERENCE HANDBOOK

function qrh_content() {
  return [
    'chapters' => [
      [
        'badge' => 'Introduction',
        'title' => 'QRH Philosophy',
        'navTitle' => 'Big Picture',
        'subtitle' => 'Purpose and Structure of Emergency Procedures',
        'time' => '8 min',
        'objective' => 'Understand the purpose of the QRH and why memory items exist.',
        'analogy' => [
          'label' => 'The Analogy — Cookbook vs. Head Chef',
          'text' => 'In a restaurant, the head chef has memorized key recipes (memory items). For complex dishes, they reference the cookbook (QRH checklist). Under stress (emergency), the chef executes memory items instinctively, then consults the cookbook for detailed procedures. Pilots do the same: critical fire/decompression items from memory, then follow the QRH checklist.'
        ],
        'body' => '<p>The Quick Reference Handbook (QRH) is the aircraft-specific emergency procedures manual. It is organized by system and emergency type. Memory items (procedures that must be executed from memory without reference) are emphasized and highlighted; these are the most critical steps that pilots memorize through type rating training. Non-memory checklists are followed step-by-step after memory items are complete. The QRH includes normal procedures, abnormal procedures, emergency procedures, system limitations, and performance data. Procedures are organized by priority: Memory items first, then abnormal checklist, then normal checklist. The principle is Aviate → Navigate → Communicate → then consult QRH. During an emergency, the flying pilot focuses on flying the aircraft while the non-flying pilot consults the QRH. This division of duties prevents the flying pilot from becoming distracted by checklist details. The QRH must be immediately accessible—either in an armrest pocket or seat back pocket. Before every flight, crew briefing includes QRH location and how to access procedures. Understanding QRH structure and how to use it efficiently under stress is essential for emergency management.</p>',
        'cards' => [
          [
            'type' => 'blue',
            'head' => '📘 QRH Document Structure',
            'list' => [
              'Section 1: Normal Procedures (checklists for normal operations)',
              'Section 2: Abnormal Procedures (system failures, non-critical emergencies)',
              'Section 3: Emergency Procedures (fire, decompression, critical failures)',
              'Section 4: System Limitations (performance limits, operating constraints)',
              'Section 5: Performance Data (V-speeds, weights, fuel calculations)',
              'Section 6: Index (cross-reference by system, EICAS message, malfunction)'
            ]
          ],
          [
            'type' => 'amber',
            'head' => '⭐ Procedure Types',
            'table' => [
              'headers' => ['Type', 'Format', 'Reference', 'Example'],
              'rows' => [
                ['Memory Items', 'Memorized, no checklist', 'From memory only', 'Engine fire immediate actions'],
                ['Non-Memory Checklist', 'Step-by-step reference', 'QRH open (READ & DO)', 'System checkout procedures'],
                ['Challenge-Response', 'Pilot calls, copilot responds', 'QRH or memory', 'Checklist items (operator dependent)'],
                ['Normal Checklist', 'Routine procedures', 'QRH reference', 'Preflight, after-takeoff checks']
              ]
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the purpose of memory items in the QRH?',
          'options' => [
            'To reduce the weight of the printed QRH manual',
            'To ensure critical immediate actions are executed without reference during emergencies',
            'To make the QRH easier for new pilots to understand',
            'To comply with international aviation standards'
          ],
          'correct' => 1,
          'explanation' => 'Memory items are the most critical procedures that must be executed from memory during emergencies. They are prioritized, emphasized in training, and memorized to ensure instant execution without consulting the QRH during critical situations.'
        ]
      ],
      [
        'badge' => 'Chapter 2',
        'title' => 'QRH Structure and Navigation',
        'navTitle' => 'QRH Structure',
        'subtitle' => 'Organization and How to Find Procedures',
        'time' => '10 min',
        'objective' => 'Master QRH organization and learn to navigate quickly under stress.',
        'analogy' => [
          'label' => 'The Analogy — Finding a Book in a Library',
          'text' => 'A library is organized by category, author, and title. The QRH is organized similarly: by system (electrical, hydraulic), by procedure type (normal, abnormal, emergency), and by index (cross-reference). If you know which system failed, you go to that system section. If you have an EICAS message, you use the message index. Learning the organization means you can find the right procedure under stress.'
        ],
        'body' => '<p>The QRH is organized hierarchically for quick access under stress. System-based organization groups procedures by aircraft system (engines, hydraulics, electrical, pressurization, etc.). Within each system, procedures are ordered: normal procedures first, then abnormal (degraded performance), then emergency (critical failure). Emergency procedures start with memory items in bold, followed by non-memory steps. A master index at the front allows cross-referencing: if an EICAS message appears (e.g., "HYD 1 PRESSURE LOW"), the crew looks it up in the message index, which directs them to the correct QRH page. The crew must practice navigating the QRH during proficiency training so that during a real emergency, finding the correct procedure is automatic. QRH tabs and bookmarks help. Some operators use electronic QRHs on tablets, which allows rapid searching by keyword. The principle is consistent: memory items executed immediately, then crew refers to QRH for detailed procedures. Speed is critical in emergency situations; crews train to locate and follow procedures quickly.</p>',
        'cards' => [
          [
            'type' => 'blue',
            'head' => '📖 QRH Navigation Methods',
            'table' => [
              'headers' => ['Method', 'Use Case', 'Speed', 'Accuracy'],
              'rows' => [
                ['System-based (tabs)', 'Known system failure', 'Fast', 'High (if you know system)'],
                ['Index by EICAS message', 'CAS message displays', 'Medium', 'Very high (direct reference)'],
                ['Limitation/Performance', 'Need aircraft limits', 'Slow', 'Direct'],
                ['Emergency section', 'Critical emergency', 'Fast (memory items clear)', 'High (memory prioritized)'],
                ['Table of Contents', 'Unfamiliar procedure', 'Slow', 'Reliable']
              ]
            ]
          ],
          [
            'type' => 'green',
            'head' => '✓ Finding a Procedure Under Stress',
            'steps' => [
              '1. Recognize the emergency: EICAS message, warning light, or crew recognition',
              '2. If EICAS message: Use message index to locate QRH page directly',
              '3. If known system: Use system tab/section to find procedure',
              '4. Execute memory items immediately (from memory, not from checklist)',
              '5. Once stable: Open QRH and find the non-memory checklist',
              '6. Read the first checklist item',
              '7. Crew executes item (challenge-response format)',
              '8. Move to next item; repeat until checklist complete'
            ]
          ],
          [
            'type' => 'amber',
            'head' => '⚠️ QRH Best Practices',
            'list' => [
              'Keep QRH immediately accessible (armrest, seat pocket, NOT cockpit overhead)',
              'Tab or bookmark critical emergency procedures (fire, decompression)',
              'Practice finding procedures during simulator training',
              'Brief crew on QRH location before every flight',
              'Use EICAS message index to cross-reference procedure',
              'Memory items are executed from memory; non-memory items are READ and DO',
              'Electronic QRH (tablet) allows rapid searching but requires battery'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'How should the crew find the correct QRH procedure if an EICAS message appears?',
          'options' => [
            'Browse the entire QRH until finding a relevant procedure',
            'Use the QRH message index to cross-reference the EICAS message directly',
            'Call the airline dispatcher for guidance',
            'Use the table of contents to search by system name'
          ],
          'correct' => 1,
          'explanation' => 'The QRH includes a message index that cross-references each EICAS message to the correct QRH page. This allows the crew to find the correct procedure quickly without browsing the entire manual.'
        ]
      ],
      [
        'badge' => 'Chapter 3',
        'title' => 'Memory Items',
        'navTitle' => 'Memory Items',
        'subtitle' => 'Critical Procedures Executed from Memory',
        'time' => '12 min',
        'objective' => 'Understand why memory items exist and which procedures are typical memory items.',
        'analogy' => [
          'label' => 'The Analogy — First Aid CPR',
          'text' => 'CPR is a memory item in first aid. When someone collapses, you don\'t pull out a book—you do chest compressions from memory. Similarly, engine fire immediate actions are memory items. You don\'t reference a checklist; you execute critical actions instinctively from your training and muscle memory.'
        ],
        'body' => '<p>Memory items are the most critical procedures that must be executed from memory without consulting the QRH. These are typically immediate actions for fire, decompression, control failure, or other critical emergencies where delay could be fatal. Memory items are emphasized in training, highlighted in the QRH, and practiced repeatedly in the simulator. Examples of typical memory items include: engine fire (set fire switch, deploy fire extinguisher), rapid decompression (don oxygen, declare emergency, descend), engine failure (trim, verify dead engine, manage remaining engine), loss of control (trim, reduce power, recover aircraft). The philosophy is that in the first few seconds of a critical emergency, there is no time to reference a checklist—actions must be instinctive. After memory items are complete and the emergency is stabilized, the crew consults the QRH for detailed follow-up procedures. Type rating training includes extensive memory item study; pilots are tested on memory items during checkrides. Competency in memory items is essential for safe emergency management. Airlines conduct recurrent training on memory items annually or bi-annually to maintain crew proficiency.</p>',
        'cards' => [
          [
            'type' => 'red',
            'head' => '🔴 Typical Memory Items',
            'list' => [
              'ENGINE FIRE: Set fire switch, deploy fire extinguisher, throttle idle, descent',
              'RAPID DECOMPRESSION: Don O2 mask (EMERGENCY), declare emergency, emergency descent',
              'ENGINE FAILURE (twin): Verify dead engine, trim, manage surviving engine, declare',
              'LOSS OF CONTROL: Trim, reduce power, recover attitude, stabilize aircraft',
              'HYDRAULIC FAILURE: Identify failed system, assess control capability, plan landing',
              'ELECTRICAL FIRE: Identify electrical bus, disconnect circuit, assess avionics'
            ]
          ],
          [
            'type' => 'blue',
            'head' => '📋 Memory Item Execution Steps',
            'steps' => [
              '1. Emergency occurs: Recognize critical condition (fire, decompression, etc.)',
              '2. Aviate first: Stabilize aircraft attitude (pitch, roll, speed)',
              '3. Navigate: Ensure aircraft is on safe heading/altitude',
              '4. Communicate: Notify ATC "Declaring emergency, [brief description]"',
              '5. Execute memory items: From memory, no checklist reference',
              '6. Stabilization: Aircraft should be stable and controlled after memory items',
              '7. Then consult QRH: Open QRH and find detailed non-memory checklist',
              '8. Continue checklist: Read and execute each non-memory item'
            ]
          ],
          [
            'type' => 'amber',
            'head' => '⚠️ Memory Item Training',
            'list' => [
              'Type rating: Memory items studied and tested during initial training',
              'Checkride: Memory items tested under proficiency check',
              'Recurrent training: Annual or biennial recurrent training includes memory item review',
              'Simulator: Practice memory items in realistic emergency scenarios',
              'Emphasis: Memory items are always highlighted in QRH (bold, capital letters)',
              'No substitution: Memory items are non-negotiable; executed as trained'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the correct order of priorities in an emergency (Aviate, Navigate, Communicate, Checklist)?',
          'options' => [
            'Communicate, Aviate, Navigate, Checklist',
            'Aviate (stabilize aircraft), Navigate, Communicate, then Checklist',
            'Checklist, Aviate, Navigate, Communicate',
            'Navigate, Communicate, Aviate, Checklist'
          ],
          'correct' => 1,
          'explanation' => 'The priority is Aviate (stabilize the aircraft attitude and speed), Navigate (ensure safe heading/altitude), Communicate (declare emergency to ATC), then execute memory items and Checklist. This order ensures the aircraft is stable before addressing complex procedures.'
        ]
      ],
      [
        'badge' => 'Chapter 4',
        'title' => 'Using QRH in Flight',
        'navTitle' => 'Using QRH',
        'subtitle' => 'Crew Coordination and Checklist Management',
        'time' => '9 min',
        'objective' => 'Master checklist reading and execution under normal and emergency conditions.',
        'analogy' => [
          'label' => 'The Analogy — Surgical Team Coordination',
          'text' => 'In surgery, one person reads steps aloud ("retractors ready"), another confirms ("retractors in place"). The surgical team uses this challenge-response to ensure no step is missed. Airline crews do the same with checklists: one pilot calls the item, the other responds and executes, then confirms complete. This system catches errors and keeps both pilots engaged.'
        ],
        'body' => '<p>During normal operations, checklists are read aloud and executed in a challenge-response format: the flying pilot (or flight attendant) calls out the checklist item, the other pilot confirms action is complete and correct. This ensures no steps are skipped and both pilots remain aware of system status. During emergencies, roles may shift: the non-flying pilot becomes the "checklist reader" and refers to the QRH while the flying pilot executes items or continues to fly the aircraft (depending on workload). If crew workload is very high (e.g., single-engine approach after engine failure), the non-flying pilot may need to handle flying while the other pilot consults the QRH. The key is clear communication: "Complete the before-landing checklist," "Checklist complete," or "Item not complete; stand by." Some items are conditional: "IF [condition], THEN [action]." The reader must assess whether the condition applies before execution. After a procedure is complete, both pilots verify that the aircraft state is appropriate for the next phase of flight. QRH procedures must be followed as written; pilots should not deviate or skip steps unless authorized by the airline or regulatory body. Following checklist discipline prevents errors and ensures consistent aircraft management.</p>',
        'cards' => [
          [
            'type' => 'green',
            'head' => '✓ Challenge-Response Checklist Format',
            'steps' => [
              '1. Reader calls item: "Trim set for takeoff"',
              '2. Pilot executes item: Sets trim to takeoff position',
              '3. Pilot confirms: "Trim set for takeoff; complete"',
              '4. Reader verifies: Glances at trim position to confirm',
              '5. Reader moves to next item: "Flaps set for takeoff"',
              '6. Repeat: Process continues for all items',
              '7. Final confirmation: "Checklist complete; ready for flight"',
              '8. Both pilots confirm: Acknowledge readiness'
            ]
          ],
          [
            'type' => 'blue',
            'head' => '📋 DO-List Format (Alternate)',
            'list' => [
              'Some airlines use DO-LIST format instead of challenge-response',
              'Pilot reads items aloud and executes them without separate confirmation',
              'Less formal but faster if workload is manageable',
              'Still requires deliberate item execution and awareness',
              'Some checklists require challenge-response; others allow do-list',
              'Crew must follow their airline training for preferred format'
            ]
          ],
          [
            'type' => 'amber',
            'head' => '⚠️ Common Checklist Errors',
            'list' => [
              'Skipping items: Crew familiarity leads to skipped steps (error)',
              'Mis-reading: Item misunderstood; wrong action executed',
              'Conditional steps: Reader forgets to assess IF condition before executing',
              'Distraction: Phone calls, ATC transmissions interrupt checklist flow',
              'Workload: High workload causes checklist to be abandoned (avoid)',
              'Recovery: If checklist is interrupted, restart from beginning of that section'
            ]
          ]
        ],
        'quiz' => [
          'q' => 'What is the correct procedure if a checklist is interrupted by an ATC call during execution?',
          'options' => [
            'Skip the remaining items and prepare for ATC clearance',
            'Resume checklist from where you left off',
            'Complete the ATC call, then restart the checklist from the beginning of that section',
            'Ask ATC to wait until the checklist is complete'
          ],
          'correct' => 2,
          'explanation' => 'If a checklist is interrupted, the crew should complete the interruption (ATC call, weather check), then restart the checklist from the beginning of that section. This prevents loss of place and missed items.'
        ]
      ]
    ],
    'qrh' => [
      [
        'type' => 'memory',
        'title' => 'Engine Fire - Immediate Actions',
        'steps' => [
          '1. Recognize: Engine fire detected (fire warning light, crew alert)',
          '2. Engine fire switch: Activate engine fire switch (if equipped)',
          '3. Fire extinguisher: Deploy fire extinguisher per procedure',
          '4. Throttle: Reduce to idle for affected engine',
          '5. Declare: "Mayday, engine fire, descending to [nearest airport]"',
          '6. Emergency descent: Manage descent for immediate landing',
          '7. Approach: Request emergency services at landing airport'
        ],
        'why' => 'Engine fire is the highest priority emergency. Memory items ensure immediate response; delay risks loss of aircraft.'
      ],
      [
        'type' => 'abnormal',
        'title' => 'Engine Failure During Climb',
        'eicasMsg' => 'ENGINE [n] FAILURE or similar CAS message',
        'items' => [
          'Flying pilot: Maintain aircraft attitude and safety altitude',
          'Non-flying pilot: Identify dead engine (verify on MFD/engine gauges)',
          'Trim: Set trim for single-engine flight (nose-heavy with one engine)',
          'Power: Manage surviving engine (set to climb power)',
          'Course: Turn to heading toward nearest airport',
          'Descent: May be necessary depending on climb performance'
        ],
        'why' => 'Single-engine performance is limited. Immediate recognition and trim adjustment prevent aircraft from becoming uncontrollable.'
      ],
      [
        'type' => 'limit',
        'title' => 'QRH Custody and Accessibility',
        'items' => [
          'Each pilot carries a personal QRH (or one QRH shared between both pilots)',
          'QRH must be accessible within 5-10 seconds (not in overhead, not locked away)',
          'Arm rest pocket, seat back pocket, or lap acceptable storage',
          'Electronic QRH on tablet acceptable if battery is charged',
          'Paper QRH is the backup if electronic QRH fails',
          'QRH must be current: updated pages per airline procedures'
        ]
      ]
    ],
    'quiz' => [
      [
        'q' => 'What is the primary purpose of memory items in the QRH?',
        'options' => [
          'To reduce the weight of the printed manual',
          'To allow pilots to execute critical immediate actions from memory without consulting the checklist',
          'To make the QRH easier to read during normal operations',
          'To comply with international standards'
        ],
        'correct' => 1,
        'explanation' => 'Memory items are critical procedures executed from memory during emergencies. They are emphasized in training to ensure instinctive execution without delay or reference to a checklist.'
      ],
      [
        'q' => 'What is the priority order for emergency response?',
        'options' => [
          'Communicate, Aviate, Navigate, Checklist',
          'Aviate (stabilize aircraft), Navigate, Communicate, then execute Checklist',
          'Checklist, Navigate, Communicate, Aviate',
          'Navigate, Communicate, Aviate, Checklist'
        ],
        'correct' => 1,
        'explanation' => 'The priority is Aviate (stabilize aircraft), Navigate (safe heading/altitude), Communicate (declare emergency), then execute checklists. Stabilizing the aircraft takes precedence over consulting procedures.'
      ],
      [
        'q' => 'How should the crew find the correct QRH procedure for an EICAS message?',
        'options' => [
          'Browse system sections until finding a matching procedure',
          'Call the airline dispatcher',
          'Use the QRH message index to cross-reference the EICAS message',
          'Use the table of contents and search by keyword'
        ],
        'correct' => 2,
        'explanation' => 'The QRH includes a message index that cross-references EICAS messages directly to the correct QRH page, allowing rapid location of the procedure.'
      ],
      [
        'q' => 'What format is typically used for QRH checklists during normal operations?',
        'options' => [
          'Silent reading by the flying pilot only',
          'Challenge-response: one pilot calls items, the other confirms',
          'Automated system that reads items aloud',
          'Checklist printed on the instrument panel'
        ],
        'correct' => 1,
        'explanation' => 'Challenge-response format requires one pilot to call checklist items and the other to confirm execution. This system ensures no steps are skipped and both pilots remain engaged.'
      ],
      [
        'q' => 'Where should the QRH be stored for immediate accessibility during flight?',
        'options' => [
          'In the cockpit overhead compartment',
          'In a locked case under the seat',
          'In an armrest pocket or seat back pocket (immediately accessible)',
          'In the galley area'
        ],
        'correct' => 2,
        'explanation' => 'The QRH must be immediately accessible (typically within 5-10 seconds) to allow rapid reference during emergencies. Armrest or seat back pockets are standard storage locations.'
      ],
      [
        'q' => 'What should the crew do if a QRH checklist is interrupted by an ATC transmission?',
        'options' => [
          'Skip remaining items and respond to ATC',
          'Ignore ATC until the checklist is complete',
          'Complete the ATC transmission, then restart the checklist from the beginning of that section',
          'Complete the checklist before responding to ATC'
        ],
        'correct' => 2,
        'explanation' => 'If a checklist is interrupted, the crew should handle the interruption (ATC call, priority task), then restart the checklist from the beginning of that section to prevent missed items.'
      ]
    ]
  ];
}


function include_system_stub($code, $name, $desc) {
  // Stub — returns a basic structure telling users content is being prepared
  return [
    'chapters' => [
      [
        'badge'=>'Overview','title'=>$name.' — System Overview','navTitle'=>'Overview',
        'subtitle'=>$desc,'time'=>'10 min',
        'objective'=>'Comprehensive chapter content for <strong>'.$name.'</strong> is being prepared. Use the Quick Reference and Memory Aids tabs while this chapter is developed.',
        'body'=>'<p>This system module is in development. The overview content from the lesson database is shown in the Quick Reference tab. Return here for the full CBT chapter experience which will include: Big Picture analogy, component deep-dives, normal operation walkthrough, abnormal procedures, QRH correlation, and a 10-question knowledge check.</p>
        <p>In the meantime, study using: the <strong>System Diagram</strong> tab (animated schematic), <strong>Memory Aids</strong> tab (mnemonics), and <strong>Quick Reference</strong> tab (key facts from the lesson).</p>',
        'cards'=>[
          ['type'=>'blue','head'=>'📋 System: '.$code.' — '.$name,'list'=>[
            'Use the <strong>System Diagram</strong> tab for the animated system schematic',
            'Use the <strong>Memory Aids</strong> tab for mnemonics and quick-recall techniques',
            'Use the <strong>Quick Reference</strong> tab for key facts from the lesson database',
            'Use the <strong>Flashcards</strong> section for spaced-repetition practice on this system',
            'Full CBT chapter content coming soon — check back after each update'
          ]],
        ],
      ]
    ],
    'qrh' => [],
    'quiz' => []
  ];
}

function generic_content($system) {
  return include_system_stub($system['ata_code']??'', $system['name']??'System', $system['description']??'');
}
?>
