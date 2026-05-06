<div class="progress-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>My Progress</h1>
        <p class="subtitle">Track your learning journey through Q400 systems</p>
    </div>

    <?php
    // Phase 9 — brand-new account empty state. Show only when literally
    // nothing has been logged yet so seasoned learners never see this.
    $isBrandNew = (
        ((int)($stats['completed_systems'] ?? 0)) === 0 &&
        ((int)($stats['in_progress_systems'] ?? 0)) === 0 &&
        ((int)($stats['total_study_time_mins'] ?? 0)) === 0 &&
        empty($studyHistory)
    );
    ?>
    <?php if ($isBrandNew): ?>
        <div class="progress-blank-slate">
            <div class="blank-slate-icon"><i data-lucide="rocket"></i></div>
            <h2>Your progress will land here.</h2>
            <p>Open a system, study a few flashcards, take a quiz — this page lights up the moment you start.</p>
            <div class="blank-slate-actions">
                <a href="/systems" class="btn btn-primary"><i data-lucide="book-open"></i> Browse systems</a>
                <a href="/flashcards" class="btn btn-secondary"><i data-lucide="layers"></i> Try a flashcard deck</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Overall Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="percent"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Overall Progress</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['overall_percentage'] ?? 0); ?>%</p>
                <div class="stat-bar">
                    <div class="stat-fill" style="width: <?php echo htmlspecialchars($stats['overall_percentage'] ?? 0); ?>%"></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon streak">
                <i data-lucide="flame"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Study Streak</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['study_streak'] ?? 0); ?> <span class="stat-total">days</span></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="clock"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Study Time</h3>
                <p class="stat-value"><?php echo htmlspecialchars(floor(($stats['total_study_time_mins'] ?? 0) / 60)); ?> <span class="stat-total">hours</span></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon quiz">
                <i data-lucide="target"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Avg Quiz Score</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['average_quiz_score'] ?? 0); ?>%</p>
            </div>
        </div>
    </div>

    <!-- Systems Progress -->
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Systems Progress</h2>
            <span class="card-meta"><?php echo htmlspecialchars($stats['completed_systems'] ?? 0); ?> / <?php echo htmlspecialchars($stats['total_systems'] ?? 0); ?> completed</span>
        </div>
        <div class="card-body">
            <?php if (!empty($systemProgress)): ?>
                <div class="system-progress-list">
                    <?php foreach ($systemProgress as $system): ?>
                        <div class="system-progress-item">
                            <div class="system-progress-header">
                                <div class="system-progress-title">
                                    <div class="system-color-dot" style="background-color: <?php echo htmlspecialchars($system['color_hex'] ?? '#3B82F6'); ?>"></div>
                                    <span><?php echo htmlspecialchars($system['name']); ?></span>
                                </div>
                                <div class="system-progress-meta">
                                    <span class="progress-percentage"><?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?>%</span>
                                    <span class="progress-confidence">Confidence: <?php echo htmlspecialchars($system['confidence'] ?? 0); ?>%</span>
                                </div>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?>%; background-color: <?php echo htmlspecialchars($system['color_hex'] ?? '#3B82F6'); ?>"></div>
                            </div>
                            <div class="system-progress-footer">
                                <?php
                                  $ls = $system['last_studied'] ?? null;
                                  if (!$ls) { $lsLabel = 'Never'; }
                                  else {
                                      $diff = max(0, time() - strtotime((string)$ls));
                                      if ($diff < 60)        $lsLabel = 'Just now';
                                      elseif ($diff < 3600)  $lsLabel = floor($diff / 60) . ' min ago';
                                      elseif ($diff < 86400) $lsLabel = floor($diff / 3600) . ' hr ago';
                                      elseif ($diff < 604800)$lsLabel = floor($diff / 86400) . ' day' . (floor($diff/86400) === 1 ? '' : 's') . ' ago';
                                      else                   $lsLabel = date('M j, Y', strtotime((string)$ls));
                                  }
                                ?>
                                <span class="last-studied">Last studied: <?php echo htmlspecialchars($lsLabel); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="inbox"></i>
                    <p>Start studying to see your progress here.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Weak Topics -->
    <?php if (!empty($weakTopics)): ?>
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Areas for Improvement</h2>
        </div>
        <div class="card-body">
            <div class="weak-topics-grid">
                <?php foreach ($weakTopics as $topic): ?>
                    <div class="weak-topic-card">
                        <div class="topic-header">
                            <h4><?php echo htmlspecialchars($topic['system_name']); ?></h4>
                            <span class="strength-badge">
                                <i data-lucide="alert-triangle"></i>
                                <?php echo htmlspecialchars($topic['strength_score']); ?>%
                            </span>
                        </div>
                        <div class="strength-bar">
                            <div class="strength-fill" style="width: <?php echo htmlspecialchars($topic['strength_score']); ?>%"></div>
                        </div>
                        <p class="topic-hint">Consider reviewing this topic</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Study History -->
    <?php if (!empty($studyHistory)): ?>
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Study History</h2>
        </div>
        <div class="card-body">
            <div class="study-history-chart">
                <canvas id="studyHistoryChart" height="200"></canvas>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>
.progress-container {
    padding: 20px;
}

.system-progress-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.system-progress-item {
    padding: 16px;
    background: var(--color-dark-bg);
    border-radius: 8px;
    border-left: 4px solid;
}

.system-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    flex-wrap: wrap;
    gap: 16px;
}

.system-progress-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    color: var(--color-white-text);
}

.system-color-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
}

.system-progress-meta {
    display: flex;
    gap: 20px;
    font-size: 14px;
}

.progress-percentage {
    font-weight: 700;
    color: var(--color-white-text);
}

.progress-confidence {
    color: var(--color-gray-text);
}

.progress-bar {
    height: 8px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
}

.progress-fill {
    height: 100%;
    transition: width 0.3s ease;
}

.system-progress-footer {
    font-size: 12px;
    color: var(--color-muted-text);
    text-align: right;
}

.last-studied {
    display: inline-block;
}

.weak-topics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}

.weak-topic-card {
    padding: 16px;
    background: var(--color-dark-bg);
    border-radius: 8px;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.topic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    gap: 12px;
}

.topic-header h4 {
    margin: 0;
    color: var(--color-white-text);
    font-size: 14px;
    font-weight: 600;
}

.strength-badge {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    background: rgba(239, 68, 68, 0.15);
    border-radius: 4px;
    color: var(--color-danger);
    font-size: 12px;
    font-weight: 600;
}

.strength-badge i {
    width: 14px;
    height: 14px;
}

.strength-bar {
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
}

.strength-fill {
    height: 100%;
    background: var(--color-danger);
}

.topic-hint {
    margin: 0;
    font-size: 12px;
    color: var(--color-gray-text);
}

.study-history-chart {
    position: relative;
    height: 200px;
}

/* Phase 9 — brand-new account empty-state hero */
.progress-blank-slate {
    display: flex; flex-direction: column; align-items: center; gap: 14px;
    padding: 42px 24px; margin-bottom: 24px;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(59,130,246,0.10), rgba(56,189,248,0.05));
    border: 1px solid rgba(56,189,248,0.20);
    text-align: center;
}
.progress-blank-slate .blank-slate-icon {
    width: 56px; height: 56px; border-radius: 999px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(56,189,248,0.15); color: #38BDF8;
}
.progress-blank-slate .blank-slate-icon i { width: 28px; height: 28px; }
.progress-blank-slate h2 { margin: 0; font-size: 22px; font-weight: 700; color: var(--color-white-text); }
.progress-blank-slate p  { margin: 0; max-width: 480px; color: var(--color-gray-text); font-size: 14.5px; line-height: 1.55; }
.progress-blank-slate .blank-slate-actions {
    display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-top: 6px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studyHistory = <?php echo json_encode($studyHistory ?? []); ?>;

    const canvas = document.getElementById('studyHistoryChart');
    if (canvas && typeof Chart !== 'undefined' && studyHistory.length > 0) {
        const ctx = canvas.getContext('2d');

        const labels = studyHistory.map(item => item.date);
        const data = studyHistory.map(item => item.session_count);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Study Sessions',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>
