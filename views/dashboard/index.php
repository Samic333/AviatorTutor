<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Dashboard</h1>
        <p class="subtitle">Your learning progress and recommendations</p>
    </div>

    <!-- Stats Row -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon systems">
                <i data-lucide="book"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Systems Studied</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['systems_studied'] ?? 0); ?> <span class="stat-total">/ 22</span></p>
                <div class="stat-bar">
                    <div class="stat-fill" style="width: <?php echo ($stats['systems_studied'] ?? 0) / 22 * 100; ?>%"></div>
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
                <p class="stat-subtitle">Keep it up!</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon flashcards">
                <i data-lucide="credit-card"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Flashcards Due</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['flashcards_due'] ?? 0); ?> <span class="stat-total">today</span></p>
                <a href="/flashcards" class="stat-link">Review now</a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon quiz">
                <i data-lucide="target"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Avg Quiz Score</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['average_quiz_score'] ?? 0); ?><span class="stat-total">%</span></p>
                <p class="stat-subtitle">Based on last 10 quizzes</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Left Column (2/3) -->
        <div class="dashboard-col-lg">
            <!-- Continue Studying -->
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Continue Studying</h2>
                    <a href="/systems" class="link-secondary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($inProgress)): ?>
                        <div class="system-list">
                            <?php foreach (array_slice($inProgress, 0, 3) as $system): ?>
                                <div class="system-item">
                                    <div class="system-info">
                                        <h4 class="system-name"><?php echo htmlspecialchars($system['name']); ?></h4>
                                        <p class="system-topic"><?php echo htmlspecialchars($system['current_topic'] ?? 'Overview'); ?></p>
                                    </div>
                                    <div class="system-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?>%"></div>
                                        </div>
                                        <span class="progress-text"><?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?>%</span>
                                    </div>
                                    <a href="/study/<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-sm btn-primary">
                                        Continue
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i data-lucide="inbox"></i>
                            <p>No systems in progress yet. Start by selecting a system.</p>
                            <a href="/systems" class="btn btn-primary">Browse Systems</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Quiz Performance Chart -->
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Quiz Performance by System</h2>
                </div>
                <div class="card-body">
                    <canvas id="quizPerformanceChart" height="300"></canvas>
                </div>
            </section>

            <!-- Recent Activity -->
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Activity</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivity)): ?>
                        <div class="activity-feed">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i data-lucide="<?php echo htmlspecialchars($activity['icon'] ?? 'check-circle'); ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text"><?php echo htmlspecialchars($activity['description']); ?></p>
                                        <p class="activity-time"><?php echo htmlspecialchars($activity['time_ago']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No recent activity yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Right Column (1/3) -->
        <div class="dashboard-col-sm">
            <!-- Due for Review -->
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Due for Review</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($dueForReview)): ?>
                        <div class="due-list">
                            <?php foreach ($dueForReview as $item): ?>
                                <div class="due-item">
                                    <div class="due-badge"><?php echo htmlspecialchars($item['type']); ?></div>
                                    <div class="due-info">
                                        <p class="due-title"><?php echo htmlspecialchars($item['title']); ?></p>
                                        <p class="due-subtitle"><?php echo htmlspecialchars($item['system_name']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/flashcards" class="btn btn-block btn-secondary mt-3">Start Review Session</a>
                    <?php else: ?>
                        <div class="empty-state-sm">
                            <i data-lucide="check"></i>
                            <p>All caught up!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Suggested Next Topic -->
            <?php if (!empty($suggestedTopic)): ?>
                <section class="card card-highlight">
                    <div class="card-header">
                        <h2 class="card-title">Suggested Next</h2>
                    </div>
                    <div class="card-body">
                        <div class="suggested-item">
                            <div class="suggested-icon">
                                <i data-lucide="lightbulb"></i>
                            </div>
                            <p class="suggested-reason"><?php echo htmlspecialchars($suggestedTopic['reason']); ?></p>
                            <p class="suggested-topic"><?php echo htmlspecialchars($suggestedTopic['topic_name']); ?></p>
                            <a href="/study/<?php echo htmlspecialchars($suggestedTopic['system_id']); ?>" class="btn btn-primary btn-block mt-3">
                                Start Now
                            </a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Study Streak Calendar -->
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Study Activity</h2>
                </div>
                <div class="card-body">
                    <div class="streak-calendar">
                        <?php if (!empty($streakData)): ?>
                            <?php foreach ($streakData as $day): ?>
                                <div class="streak-day" title="<?php echo htmlspecialchars($day['date']); ?>" style="background-color: rgba(52, 211, 153, <?php echo $day['intensity']; ?>)">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted small">Last 90 days of activity</p>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quiz Performance Chart
    const ctx = document.getElementById('quizPerformanceChart');
    if (ctx && typeof Chart !== 'undefined') {
        const quizData = <?php echo json_encode($quizData ?? []); ?>;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: quizData.labels || [],
                datasets: [{
                    label: 'Quiz Score %',
                    data: quizData.scores || [],
                    backgroundColor: 'rgba(52, 211, 153, 0.8)',
                    borderColor: 'rgba(52, 211, 153, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100
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
