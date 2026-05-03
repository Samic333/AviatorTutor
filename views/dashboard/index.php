<?php
declare(strict_types=1);
/** @var array $user */
/** @var array $stats */
/** @var array $inProgress */
/** @var array $recentActivity */
/** @var array $dueForReview */
/** @var ?array $suggestedTopic */
/** @var array $quizData */
/** @var array $streakData */
/** @var ?array $currentAircraft */
/** @var array $studyableAircraft */
/** @var bool $isFreshAccount */
/** @var string $csrf_token */

$systemsTotal     = 22;
$systemsStudied   = (int)($stats['systems_studied'] ?? 0);
$systemsCompleted = (int)($stats['systems_completed'] ?? 0);
$flashcardsDue    = (int)($stats['flashcards_due'] ?? 0);
$avgQuizScore     = (int)($stats['average_quiz_score'] ?? 0);
$studyStreak      = (int)($stats['study_streak'] ?? 0);
$systemsPct       = $systemsTotal > 0 ? min(100, (int)round($systemsStudied / $systemsTotal * 100)) : 0;
$completedPct     = $systemsTotal > 0 ? min(100, (int)round($systemsCompleted / $systemsTotal * 100)) : 0;
?>

<!-- Page Header -->
<div class="plt-page-header">
  <div>
    <h1 class="plt-page-header__title">Welcome back, <?= htmlspecialchars(explode(' ', (string)($user['name'] ?? 'Pilot'))[0]) ?></h1>
    <p class="plt-page-header__sub">Your flight deck for systems study, flashcards, and quizzes.</p>
  </div>
  <?php if (!empty($currentAircraft) && !empty($studyableAircraft) && empty($isFreshAccount)): ?>
    <form method="post" action="/aircraft/<?= htmlspecialchars($currentAircraft['slug']) ?>/study" style="margin:0;">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
      <div class="plt-glass-card" style="padding:10px 14px;display:flex;align-items:center;gap:12px;">
        <span style="font-size:11px;color:var(--plt-text-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Studying</span>
        <select onchange="this.form.action='/aircraft/'+this.value+'/study';this.form.submit();" name="_slug"
                style="background:transparent;border:0;color:var(--plt-text);font-weight:600;font-size:14px;cursor:pointer;outline:none;">
          <?php foreach ($studyableAircraft as $a): ?>
            <option value="<?= htmlspecialchars($a['slug']) ?>" <?= (int)$a['id'] === (int)$currentAircraft['id'] ? 'selected' : '' ?>
                    style="background:#1E293B;color:var(--plt-text);">
              <?= htmlspecialchars($a['short_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php if (!empty($isFreshAccount)): ?>
  <!-- Welcome zero-state -->
  <div class="plt-glass-card" style="padding:36px;margin-bottom:24px;background:linear-gradient(135deg, rgba(56,189,248,0.08), rgba(14,165,233,0.04));border-color:rgba(56,189,248,0.2);">
    <span style="display:inline-block;padding:4px 12px;background:rgba(56,189,248,0.15);color:var(--plt-sky);border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">Welcome aboard</span>
    <h2 style="margin:14px 0 10px;font-size:1.5rem;font-family:var(--plt-font-head);">Pick where to start</h2>
    <p style="margin:0 0 20px;color:var(--plt-text-muted);max-width:60ch;line-height:1.6;">
      Your first complete library &mdash; Q400 &mdash; is live with 22 ATA-organised systems, flashcards, quizzes, QRH drills, and progress tracking. New aircraft modules and aviation subject packs (weather, SOPs, CRM, SMS, cabin safety, emergency procedures) launch regularly &mdash; <a href="/aircraft" style="color:var(--plt-sky);">browse the catalog</a>.
    </p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a href="/aircraft/q400" class="plt-btn plt-btn--primary">Start with Q400 &rarr;</a>
      <a href="/aircraft" class="plt-btn plt-btn--ghost">See full catalog</a>
    </div>
  </div>
<?php endif; ?>

<?php
// Phase 4 v2: when dashboard_v2 is on, wrap stats + promo in a 2-column
// row. The promo panel surfaces a discoverable next-step ("Add 777").
$__cfg = [];
try { $__cfg = require BASE_PATH . '/config/app.php'; } catch (\Throwable $e) {}
$__dashV2 = !empty($__cfg['features']['dashboard_v2'] ?? false);
if ($__dashV2):
?>
<script>document.body.classList.add('dashboard-v2');</script>
<div class="plt-dashboard-top">
<?php endif; ?>

<!-- Stats Grid: 4 progress-ring cards -->
<div class="plt-stats-grid">

  <div class="plt-stat-card">
    <div class="plt-stat-card__head">
      <span class="plt-stat-card__label">Systems Studied</span>
      <div class="plt-ring" data-ring="<?= $systemsPct ?>" style="--ring-pct: <?= (int)$systemsPct ?>;" aria-hidden="true">
        <svg viewBox="0 0 70 70" width="70" height="70">
          <circle class="plt-ring__bg" cx="35" cy="35" r="30"/>
          <circle class="plt-ring__fill" cx="35" cy="35" r="30" transform="rotate(-90 35 35)"/>
        </svg>
        <span class="plt-ring__pct"><?= $systemsPct ?>%</span>
      </div>
    </div>
    <div class="plt-stat-card__value"><?= $systemsStudied ?> <span style="font-size:14px;color:var(--plt-text-muted);font-weight:500;">/ <?= $systemsTotal ?></span></div>
    <div class="plt-stat-card__sub">
      <span><?= $systemsCompleted ?> completed</span>
      <a href="/systems" style="color:var(--plt-sky);">View all →</a>
    </div>
  </div>

  <div class="plt-stat-card">
    <div class="plt-stat-card__head">
      <span class="plt-stat-card__label">Study Streak</span>
      <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#F59E0B,#EF4444);display:flex;align-items:center;justify-content:center;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="white" aria-hidden="true">
          <path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/>
        </svg>
      </div>
    </div>
    <div class="plt-stat-card__value"><?= $studyStreak ?> <span style="font-size:14px;color:var(--plt-text-muted);font-weight:500;">days</span></div>
    <div class="plt-stat-card__sub">
      <span><?= $studyStreak > 0 ? 'Keep it up!' : 'Start your streak today' ?></span>
    </div>
  </div>

  <div class="plt-stat-card">
    <div class="plt-stat-card__head">
      <span class="plt-stat-card__label">Flashcards Due</span>
      <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--plt-sky-2),var(--plt-sky));display:flex;align-items:center;justify-content:center;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 4v16"/>
        </svg>
      </div>
    </div>
    <div class="plt-stat-card__value"><?= $flashcardsDue ?> <span style="font-size:14px;color:var(--plt-text-muted);font-weight:500;">today</span></div>
    <div class="plt-stat-card__sub">
      <a href="/flashcards" style="color:var(--plt-sky);font-weight:600;"><?= $flashcardsDue > 0 ? 'Review now →' : 'Browse decks →' ?></a>
    </div>
  </div>

  <div class="plt-stat-card">
    <div class="plt-stat-card__head">
      <span class="plt-stat-card__label">Avg Quiz Score</span>
      <div class="plt-ring" data-ring="<?= $avgQuizScore ?>" style="--ring-pct: <?= (int)$avgQuizScore ?>;" aria-hidden="true">
        <svg viewBox="0 0 70 70" width="70" height="70">
          <circle class="plt-ring__bg" cx="35" cy="35" r="30"/>
          <circle class="plt-ring__fill" cx="35" cy="35" r="30" transform="rotate(-90 35 35)"
                  style="stroke:<?= $avgQuizScore >= 70 ? 'var(--plt-success)' : ($avgQuizScore >= 50 ? 'var(--plt-warning)' : 'var(--plt-danger)') ?>;"/>
        </svg>
        <span class="plt-ring__pct"><?= $avgQuizScore ?>%</span>
      </div>
    </div>
    <div class="plt-stat-card__value"><?= $avgQuizScore ?>%</div>
    <div class="plt-stat-card__sub">
      <span>Last 10 quizzes</span>
      <a href="/quiz" style="color:var(--plt-sky);">Take quiz →</a>
    </div>
  </div>

</div>

<?php if ($__dashV2): ?>
  <!-- Phase 4: promo panel filling the right side of the KPI row. Treat
       like a house ad slot — eventually monetisable, for now surfaces the
       most discoverable next subject the learner doesn't own yet. -->
  <aside class="plt-promo" aria-label="Discover">
    <span class="plt-promo__eyebrow">Coming next</span>
    <span class="plt-promo__title">Add the B777 to your library</span>
    <span class="plt-promo__body">Same depth as the Q400 pack — request access and we'll send you a quote.</span>
    <a class="plt-promo__cta" href="/my-subjects#add-subject">Request access →</a>
  </aside>
</div><!-- end .plt-dashboard-top -->
<?php endif; ?>

<!-- Main grid: 2/3 + 1/3 -->
<div class="plt-grid-2-1">

  <!-- LEFT COLUMN -->
  <div style="display:flex;flex-direction:column;gap:20px;">

    <!-- Continue Studying -->
    <section class="plt-glass-card" style="padding:24px;">
      <div class="plt-section-header">
        <h2 class="plt-section-header__title">Continue Studying</h2>
        <a href="/systems" class="plt-section-header__link">View all systems →</a>
      </div>

      <?php if (!empty($inProgress)): ?>
        <div class="plt-system-list">
          <?php foreach (array_slice($inProgress, 0, 3) as $system): ?>
            <a href="/study/<?= (int)$system['id'] ?>" class="plt-system-item">
              <div class="plt-system-item__icon" style="background:<?= htmlspecialchars($system['color_hex'] ?? '#38BDF8') ?>20;color:<?= htmlspecialchars($system['color_hex'] ?? '#38BDF8') ?>;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              </div>
              <div class="plt-system-item__body">
                <h4 class="plt-system-item__name"><?= htmlspecialchars($system['name']) ?></h4>
                <div class="plt-progress-bar">
                  <div class="plt-progress-bar__fill" style="width: <?= (int)($system['completion_percentage'] ?? 0) ?>%;"></div>
                </div>
                <span class="plt-system-item__pct"><?= (int)($system['completion_percentage'] ?? 0) ?>% complete · <?= (int)($system['completed_lessons'] ?? 0) ?>/<?= (int)($system['total_lessons'] ?? 0) ?> lessons</span>
              </div>
              <span class="plt-system-item__cta">Continue →</span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="text-align:center;padding:32px 16px;">
          <div style="width:56px;height:56px;border-radius:14px;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--plt-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
          </div>
          <p style="margin:0 0 14px;color:var(--plt-text-muted);">No systems in progress yet.</p>
          <a href="/systems" class="plt-btn plt-btn--primary plt-btn--sm">Browse Systems</a>
        </div>
      <?php endif; ?>
    </section>

    <!-- Quiz Performance Chart -->
    <section class="plt-glass-card" style="padding:24px;">
      <div class="plt-section-header">
        <h2 class="plt-section-header__title">Quiz Performance by System</h2>
      </div>
      <div style="height:280px;">
        <canvas id="quizPerformanceChart"></canvas>
      </div>
    </section>

    <!-- Recent Activity -->
    <section class="plt-glass-card" style="padding:24px;">
      <div class="plt-section-header">
        <h2 class="plt-section-header__title">Recent Activity</h2>
      </div>
      <?php if (!empty($recentActivity)): ?>
        <div class="plt-activity-feed">
          <?php foreach ($recentActivity as $activity): ?>
            <div class="plt-activity-item">
              <div class="plt-activity-item__icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <div class="plt-activity-item__body">
                <p class="plt-activity-item__text"><?= $activity['description'] ?></p>
                <p class="plt-activity-item__time"><?= htmlspecialchars($activity['time_ago']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="margin:0;color:var(--plt-text-muted);font-size:13.5px;">No recent activity yet. Open a system to start a study session.</p>
      <?php endif; ?>
    </section>

  </div>

  <!-- RIGHT COLUMN -->
  <div style="display:flex;flex-direction:column;gap:20px;">

    <!-- Suggested Next -->
    <?php if (!empty($suggestedTopic)): ?>
      <section class="plt-glass-card" style="padding:22px;background:linear-gradient(135deg, rgba(201,168,76,0.08), rgba(255,255,255,0.02));border-color:rgba(201,168,76,0.25);">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
          <div style="width:32px;height:32px;border-radius:8px;background:rgba(201,168,76,0.15);color:var(--plt-gold);display:flex;align-items:center;justify-content:center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg>
          </div>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--plt-gold);">Suggested next</span>
        </div>
        <h3 style="margin:0 0 6px;font-family:var(--plt-font-head);font-size:16px;font-weight:700;color:var(--plt-text);"><?= htmlspecialchars($suggestedTopic['topic_name']) ?></h3>
        <p style="margin:0 0 14px;font-size:13px;color:var(--plt-text-muted);"><?= htmlspecialchars($suggestedTopic['reason']) ?></p>
        <a href="/study/<?= (int)$suggestedTopic['system_id'] ?>" class="plt-btn plt-btn--primary plt-btn--sm" style="width:100%;justify-content:center;">Start lesson →</a>
      </section>
    <?php endif; ?>

    <!-- Due for Review -->
    <section class="plt-glass-card" style="padding:22px;">
      <div class="plt-section-header">
        <h2 class="plt-section-header__title">Due for Review</h2>
      </div>
      <?php if (!empty($dueForReview)): ?>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <?php foreach ($dueForReview as $item): ?>
            <div style="display:flex;gap:10px;align-items:flex-start;padding:10px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid var(--plt-glass-border);">
              <span style="display:inline-block;padding:2px 8px;border-radius:6px;background:rgba(56,189,248,0.12);color:var(--plt-sky);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;flex-shrink:0;"><?= htmlspecialchars($item['type']) ?></span>
              <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:13px;font-weight:500;color:var(--plt-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($item['title']) ?></p>
                <p style="margin:2px 0 0;font-size:11.5px;color:var(--plt-text-muted);"><?= htmlspecialchars($item['system_name']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <a href="/flashcards" class="plt-btn plt-btn--primary plt-btn--sm" style="width:100%;margin-top:14px;justify-content:center;">Start review session →</a>
      <?php else: ?>
        <div style="text-align:center;padding:20px 8px;">
          <div style="width:42px;height:42px;border-radius:10px;background:rgba(34,197,94,0.12);color:var(--plt-success);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <p style="margin:0;font-size:13px;color:var(--plt-text-muted);">All caught up!</p>
        </div>
      <?php endif; ?>
    </section>

    <!-- Streak Calendar -->
    <section class="plt-glass-card" style="padding:22px;">
      <div class="plt-section-header">
        <h2 class="plt-section-header__title">Study Activity</h2>
      </div>
      <?php if (!empty($streakData)): ?>
        <div style="display:grid;grid-template-columns:repeat(15, 1fr);gap:3px;">
          <?php foreach ($streakData as $day): ?>
            <div title="<?= htmlspecialchars($day['date']) ?>"
                 style="aspect-ratio:1;border-radius:3px;background:<?= $day['intensity'] > 0 ? 'rgba(56,189,248,' . ($day['intensity'] * 0.85 + 0.15) . ')' : 'rgba(255,255,255,0.04)' ?>;border:1px solid <?= $day['intensity'] > 0 ? 'rgba(56,189,248,0.4)' : 'rgba(255,255,255,0.06)' ?>;"></div>
          <?php endforeach; ?>
        </div>
        <p style="margin:12px 0 0;font-size:11.5px;color:var(--plt-text-muted);">Last 90 days · Hover for date</p>
      <?php else: ?>
        <p style="margin:0;color:var(--plt-text-muted);font-size:13px;">No activity to show yet.</p>
      <?php endif; ?>
    </section>

  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('quizPerformanceChart');
  if (ctx && typeof Chart !== 'undefined') {
    var quizData = <?= json_encode($quizData ?? ['labels' => [], 'scores' => []]) ?>;
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: quizData.labels || [],
        datasets: [{
          label: 'Score %',
          data: quizData.scores || [],
          backgroundColor: 'rgba(56, 189, 248, 0.7)',
          borderColor: 'rgba(56, 189, 248, 1)',
          borderWidth: 1.5,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        scales: {
          x: { beginAtZero: true, max: 100, ticks: { color: 'rgba(226,232,240,0.6)' }, grid: { color: 'rgba(255,255,255,0.05)' } },
          y: { ticks: { color: 'rgba(226,232,240,0.85)' }, grid: { display: false } }
        },
        plugins: { legend: { display: false } }
      }
    });
  }
});
</script>
