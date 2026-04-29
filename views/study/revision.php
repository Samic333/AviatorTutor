<?php
declare(strict_types=1);
/** @var array $system */
/** @var array $lessons */
/** @var array $flashcards */
/** @var array $revision_modes */

// Build Test Yourself questions from key_facts (max 2 per lesson, max 8 total)
$testQuestions = [];
foreach ($lessons as $lesson) {
    if (empty($lesson['key_facts'])) continue;
    $facts = json_decode($lesson['key_facts'], true) ?: [];
    foreach (array_slice($facts, 0, 2) as $fact) {
        $testQuestions[] = [
            'topic'  => $lesson['title'],
            'fact'   => $fact,
        ];
        if (count($testQuestions) >= 8) break 2;
    }
}

// Count totals for the top bar
$totalMustKnow  = 0;
$totalKeyFacts  = 0;
$totalExamTraps = 0;
foreach ($lessons as $l) {
    $totalKeyFacts  += count(json_decode($l['key_facts']  ?? '[]', true) ?: []);
    $totalMustKnow  += count(json_decode($l['must_know']  ?? '[]', true) ?: []);
    $totalExamTraps += count(json_decode($l['exam_traps'] ?? '[]', true) ?: []);
}
?>

<div class="plt-content" style="max-width:900px;">

  <!-- Header -->
  <div class="rev2-header">
    <div class="rev2-header__icon" style="background:<?= htmlspecialchars($system['color']) ?>20;border-color:<?= htmlspecialchars($system['color']) ?>44;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="<?= htmlspecialchars($system['color']) ?>" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
      </svg>
    </div>
    <div>
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--plt-text-muted);margin-bottom:4px;">
        Quick Revision · <?= htmlspecialchars($system['ata_code']) ?>
      </div>
      <h1 class="rev2-header__title"><?= htmlspecialchars($system['name']) ?></h1>
      <p class="rev2-header__sub"><?= htmlspecialchars($system['description'] ?? '') ?></p>
    </div>
    <div style="margin-left:auto;display:flex;gap:12px;align-items:flex-start;flex-shrink:0;flex-wrap:wrap;">
      <div style="text-align:center;padding:10px 16px;background:var(--plt-sky-soft);border:1px solid rgba(56,189,248,0.2);border-radius:var(--plt-radius);">
        <div style="font-size:1.4rem;font-weight:800;color:var(--plt-sky);font-family:var(--plt-font-head);"><?= $totalKeyFacts ?></div>
        <div style="font-size:10px;color:var(--plt-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Key Facts</div>
      </div>
      <div style="text-align:center;padding:10px 16px;background:var(--plt-warn-soft);border:1px solid rgba(251,191,36,0.2);border-radius:var(--plt-radius);">
        <div style="font-size:1.4rem;font-weight:800;color:var(--plt-warn);font-family:var(--plt-font-head);"><?= $totalMustKnow ?></div>
        <div style="font-size:10px;color:var(--plt-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Must Know</div>
      </div>
      <div style="text-align:center;padding:10px 16px;background:var(--plt-danger-soft);border:1px solid rgba(251,113,133,0.2);border-radius:var(--plt-radius);">
        <div style="font-size:1.4rem;font-weight:800;color:var(--plt-danger);font-family:var(--plt-font-head);"><?= $totalExamTraps ?></div>
        <div style="font-size:10px;color:var(--plt-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Exam Traps</div>
      </div>
    </div>
  </div>

  <!-- Top controls: Mode + Difficulty -->
  <div class="rev2-topbar" id="rev2-topbar">
    <span class="rev2-topbar__label">Duration</span>
    <?php foreach ($revision_modes as $mode): ?>
      <button class="rev2-mode-btn" data-duration="<?= (int)$mode['duration'] ?>" onclick="setRevMode(<?= (int)$mode['duration'] ?>, this)">
        <?= htmlspecialchars($mode['label']) ?>
      </button>
    <?php endforeach; ?>

    <div class="rev2-topbar__sep"></div>

    <span class="rev2-topbar__label">Level</span>
    <div class="diff-selector">
      <button class="diff-btn" data-diff="beginner"     onclick="setDiff('beginner',this)">Beginner</button>
      <button class="diff-btn active" data-diff="intermediate" onclick="setDiff('intermediate',this)">Intermediate</button>
      <button class="diff-btn" data-diff="advanced"     onclick="setDiff('advanced',this)">Advanced</button>
    </div>
  </div>

  <!-- ── KEY FACTS ── -->
  <section class="rev2-section rev-key-facts" id="rev-key-facts">
    <div class="rev2-section__head">
      <div class="rev2-section__icon" style="background:var(--plt-sky-soft);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--plt-sky)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      </div>
      <div>
        <h2 class="rev2-section__title">Key Facts</h2>
        <p class="rev2-section__sub">Essential points to remember for this system</p>
      </div>
    </div>
    <?php $hasFacts = false; ?>
    <?php foreach ($lessons as $lesson): ?>
      <?php $facts = json_decode($lesson['key_facts'] ?? '[]', true) ?: []; ?>
      <?php if (!empty($facts)): $hasFacts = true; ?>
        <div class="rev2-fact-card" style="margin-bottom:10px;">
          <div class="rev2-fact-topic"><?= htmlspecialchars($lesson['title']) ?></div>
          <ul class="rev2-fact-list">
            <?php foreach ($facts as $fact): ?>
              <li><?= htmlspecialchars($fact) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!$hasFacts): ?>
      <p style="color:var(--plt-text-muted);font-size:13px;">No key facts available for this system yet.</p>
    <?php endif; ?>
  </section>

  <!-- ── MUST KNOW ── -->
  <section class="rev2-section rev-must-know" id="rev-must-know">
    <div class="rev2-section__head">
      <div class="rev2-section__icon" style="background:var(--plt-warn-soft);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--plt-warn)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>
      </div>
      <div>
        <h2 class="rev2-section__title" style="color:var(--plt-warn);">Must Know</h2>
        <p class="rev2-section__sub">Critical information — these points appear in exams</p>
      </div>
    </div>
    <div class="rev2-must-list">
      <?php $hasMust = false; ?>
      <?php foreach ($lessons as $lesson): ?>
        <?php $mustKnow = json_decode($lesson['must_know'] ?? '[]', true) ?: []; ?>
        <?php if (!empty($mustKnow)): $hasMust = true; ?>
          <div class="rev2-must-item">
            <div class="rev2-must-topic"><?= htmlspecialchars($lesson['title']) ?></div>
            <ul class="rev2-must-list-inner">
              <?php foreach ($mustKnow as $point): ?>
                <li><?= htmlspecialchars($point) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php if (!$hasMust): ?>
        <p style="color:var(--plt-text-muted);font-size:13px;">No must-know items for this system yet.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ── EXAM TRAPS ── (hidden for Beginner/Intermediate, shown for Advanced; hidden in 3-min mode) -->
  <section class="rev2-section rev-exam-traps rev-advanced-only" id="rev-exam-traps">
    <div class="rev2-section__head">
      <div class="rev2-section__icon" style="background:var(--plt-danger-soft);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--plt-danger)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <div>
        <h2 class="rev2-section__title" style="color:var(--plt-danger);">Exam Traps</h2>
        <p class="rev2-section__sub">Common mistakes — don't get caught out</p>
      </div>
    </div>
    <div class="rev2-traps-list">
      <?php $hasTraps = false; ?>
      <?php foreach ($lessons as $lesson): ?>
        <?php $traps = json_decode($lesson['exam_traps'] ?? '[]', true) ?: []; ?>
        <?php if (!empty($traps)): $hasTraps = true; ?>
          <div class="rev2-trap-item">
            <div class="rev2-trap-topic"><?= htmlspecialchars($lesson['title']) ?></div>
            <ul class="rev2-trap-list-inner">
              <?php foreach ($traps as $trap): ?>
                <li><?= htmlspecialchars($trap) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php if (!$hasTraps): ?>
        <p style="color:var(--plt-text-muted);font-size:13px;">No exam traps listed for this system yet.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ── FLASHCARDS ── -->
  <?php if (!empty($flashcards)): ?>
    <section class="rev2-section" id="rev-flashcards">
      <div class="rev2-section__head">
        <div class="rev2-section__icon" style="background:rgba(139,92,246,0.12);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 4v16"/>
          </svg>
        </div>
        <div>
          <h2 class="rev2-section__title">Flashcard Review</h2>
          <p class="rev2-section__sub">Click a card to flip it — test your recall</p>
        </div>
      </div>
      <div class="rev2-cards-grid" id="rev-cards-grid">
        <?php foreach ($flashcards as $i => $card): ?>
          <div class="rev2-flashcard rev-fc-item"
               data-index="<?= $i ?>"
               onclick="this.classList.toggle('flipped')">
            <div class="rev2-flashcard__inner">
              <div class="rev2-flashcard__face rev2-flashcard__front">
                <span class="rev2-fc-badge <?= htmlspecialchars($card['difficulty']) ?>"><?= ucfirst($card['difficulty']) ?></span>
                <p class="rev2-fc-q"><?= htmlspecialchars($card['front']) ?></p>
                <p class="rev2-fc-hint">Tap to reveal answer</p>
              </div>
              <div class="rev2-flashcard__face rev2-flashcard__back">
                <p class="rev2-fc-a"><?= htmlspecialchars($card['back']) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- ── TEST YOURSELF ── (hidden in 3-min mode, shown in 5-min and 10-min) -->
  <?php if (!empty($testQuestions)): ?>
    <section class="rev2-section rev-test-yourself" id="rev-test-yourself">
      <div class="rev2-section__head">
        <div class="rev2-section__icon" style="background:var(--plt-success-soft);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--plt-success)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
          </svg>
        </div>
        <div>
          <h2 class="rev2-section__title">Test Yourself</h2>
          <p class="rev2-section__sub">Answer without looking — then reveal and self-grade</p>
        </div>
      </div>
      <div class="rev2-tq-list">
        <?php foreach ($testQuestions as $qi => $q): ?>
          <div class="rev2-tq-card" id="tq-<?= $qi ?>">
            <span class="rev2-tq-num">Question <?= $qi + 1 ?> · <?= htmlspecialchars($q['topic']) ?></span>
            <p class="rev2-tq-q">Can you explain: <?= htmlspecialchars($q['fact']) ?></p>
            <button class="rev2-tq-reveal-btn" onclick="revealTQ(<?= $qi ?>)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
              Reveal Answer
            </button>
            <div class="rev2-tq-answer" id="tqa-<?= $qi ?>">
              <strong>Answer:</strong> <?= htmlspecialchars($q['fact']) ?>
              <div class="rev2-tq-grade" id="tqg-<?= $qi ?>">
                <button class="rev2-tq-grade-btn tq-got-it" onclick="gradeQuestion(<?= $qi ?>, 'got-it')">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Got it
                </button>
                <button class="rev2-tq-grade-btn tq-review" onclick="gradeQuestion(<?= $qi ?>, 'review')">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.95"/></svg>
                  Review again
                </button>
              </div>
            </div>
            <div class="rev2-tq-done" id="tqd-<?= $qi ?>"></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- Action buttons -->
  <div class="rev2-actions">
    <a href="/study/<?= (int)$system['id'] ?>" class="plt-btn plt-btn--ghost">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Full Study
    </a>
    <a href="/flashcards?system=<?= (int)$system['id'] ?>" class="plt-btn plt-btn--primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 4v16"/></svg>
      Flashcard Session
    </a>
    <a href="/quiz?system=<?= (int)$system['id'] ?>" class="plt-btn plt-btn--primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      Take a Quiz
    </a>
  </div>

</div>

<script>
(function() {
  var currentMode = parseInt(localStorage.getItem('av_rev_mode') || '5', 10);
  var currentDiff = localStorage.getItem('av_difficulty') || 'intermediate';

  /* ── Difficulty ── */
  function applyDiff(diff) {
    currentDiff = diff;
    localStorage.setItem('av_difficulty', diff);

    // Exam traps section
    var trapsSection = document.getElementById('rev-exam-traps');
    if (trapsSection) {
      trapsSection.style.display = (diff === 'advanced') ? '' : 'none';
    }

    // Must know section - hide for beginner
    var mustSection = document.getElementById('rev-must-know');
    if (mustSection) {
      mustSection.style.display = (diff === 'beginner') ? 'none' : '';
    }

    // Key facts section - hide for nothing (always visible)
    var factsSection = document.getElementById('rev-key-facts');
    if (factsSection) {
      factsSection.style.display = '';
    }

    // Sync button states
    document.querySelectorAll('.diff-btn').forEach(function(btn) {
      btn.classList.toggle('active', btn.dataset.diff === diff);
    });
  }

  window.setDiff = function(diff, btn) {
    applyDiff(diff);
  };

  /* ── Mode (duration) ── */
  function applyMode(duration) {
    currentMode = duration;
    localStorage.setItem('av_rev_mode', duration.toString());

    var keyFacts   = document.getElementById('rev-key-facts');
    var mustKnow   = document.getElementById('rev-must-know');
    var examTraps  = document.getElementById('rev-exam-traps');
    var testSelf   = document.getElementById('rev-test-yourself');
    var fcGrid     = document.getElementById('rev-cards-grid');

    // 3-min: Must Know only, first 3 cards, no test yourself
    // 5-min: Must Know + Key Facts, first 5 cards, test yourself visible
    // 10-min: Everything, all cards, test yourself visible
    if (keyFacts)  keyFacts.style.display  = (duration >= 5)  ? '' : 'none';
    if (examTraps) examTraps.style.display = (duration >= 10 && currentDiff === 'advanced') ? '' : 'none';
    if (testSelf)  testSelf.style.display  = (duration >= 5)  ? '' : 'none';

    // Filter flashcard count
    if (fcGrid) {
      var cards = fcGrid.querySelectorAll('.rev-fc-item');
      var maxCards = duration === 3 ? 3 : duration === 5 ? 5 : cards.length;
      cards.forEach(function(card, i) {
        card.style.display = (i < maxCards) ? '' : 'none';
      });
    }

    // Sync mode button states
    document.querySelectorAll('.rev2-mode-btn').forEach(function(btn) {
      btn.classList.toggle('active', parseInt(btn.dataset.duration, 10) === duration);
    });
  }

  window.setRevMode = function(duration, btn) {
    applyMode(duration);
  };

  /* ── Test Yourself ── */
  window.revealTQ = function(idx) {
    var answerEl = document.getElementById('tqa-' + idx);
    var btn = document.querySelector('#tq-' + idx + ' .rev2-tq-reveal-btn');
    if (answerEl) answerEl.classList.add('visible');
    if (btn) btn.style.display = 'none';
  };

  window.gradeQuestion = function(idx, grade) {
    var gradeEl = document.getElementById('tqg-' + idx);
    var doneEl  = document.getElementById('tqd-' + idx);
    if (gradeEl) gradeEl.style.display = 'none';
    if (doneEl) {
      doneEl.className = 'rev2-tq-done ' + grade;
      doneEl.innerHTML = grade === 'got-it'
        ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Marked as known'
        : '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.95"/></svg> Flagged for review';
    }
  };

  /* ── Init on load ── */
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    applyDiff(currentDiff);
    applyMode(currentMode);
  });
})();
</script>
