<div class="quiz-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Quiz & Tests</h1>
        <p class="subtitle">Test your knowledge of Q400 systems</p>
    </div>

    <!-- Filter Tabs -->
    <div class="quiz-filters">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="practice">Practice</button>
        <button class="filter-btn" data-filter="exam">Exam</button>
        <button class="filter-btn" data-filter="scenario">Scenario</button>
    </div>

    <!-- Quiz Grid -->
    <section class="card">
        <div class="card-body">
            <?php if (!empty($quizzes)): ?>
                <div class="quizzes-grid" id="quizzesGrid">
                    <?php foreach ($quizzes as $quiz): ?>
                        <div class="quiz-card" data-type="<?php echo htmlspecialchars($quiz['quiz_type']); ?>">
                            <div class="quiz-card-header">
                                <h3 class="quiz-title"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                                <div class="quiz-badges">
                                    <span class="badge badge-info"><?php echo htmlspecialchars(ucfirst($quiz['quiz_type'])); ?></span>
                                    <span class="badge badge-system"><?php echo htmlspecialchars($quiz['system_name'] ?? 'General'); ?></span>
                                </div>
                            </div>

                            <p class="quiz-description"><?php echo htmlspecialchars($quiz['description'] ?? ''); ?></p>

                            <div class="quiz-meta">
                                <div class="meta-item">
                                    <i data-lucide="help-circle"></i>
                                    <span><?php echo htmlspecialchars($quiz['question_count'] ?? 0); ?> Questions</span>
                                </div>
                                <div class="meta-item">
                                    <i data-lucide="clock"></i>
                                    <span><?php echo htmlspecialchars($quiz['time_limit_mins'] ?? '--'); ?> min</span>
                                </div>
                                <div class="meta-item">
                                    <i data-lucide="target"></i>
                                    <span>Pass: <?php echo htmlspecialchars($quiz['pass_score'] ?? 0); ?>%</span>
                                </div>
                            </div>

                            <div class="quiz-stats">
                                <div class="stat-box">
                                    <span class="stat-label">Attempts</span>
                                    <span class="stat-value"><?php echo htmlspecialchars($quiz['attempt_count'] ?? 0); ?></span>
                                </div>
                                <div class="stat-box">
                                    <span class="stat-label">Best Score</span>
                                    <span class="stat-value"><?php echo htmlspecialchars($quiz['avg_score'] ?? '--'); ?>%</span>
                                </div>
                            </div>

                            <a href="/quiz/<?php echo htmlspecialchars($quiz['id']); ?>" class="btn btn-primary btn-block">
                                Take Quiz
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="inbox"></i>
                    <p>No quizzes available yet.</p>
                    <a href="/systems" class="btn btn-primary">Browse Systems</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
.quiz-container {
    padding: 20px;
}

.quiz-filters {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 16px;
    background: var(--color-slate-bg);
    border: 1px solid transparent;
    border-radius: 6px;
    color: var(--color-gray-text);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    font-weight: 500;
}

.filter-btn:hover {
    border-color: var(--color-blue-accent);
    color: var(--color-blue-accent);
}

.filter-btn.active {
    background: var(--color-blue-accent);
    color: white;
    border-color: var(--color-blue-accent);
}

.quizzes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}

.quiz-card {
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    transition: all 0.3s;
}

.quiz-card:hover {
    border-color: var(--color-blue-accent);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}

.quiz-card-header {
    margin-bottom: 16px;
}

.quiz-title {
    margin: 0 0 12px 0;
    font-size: 18px;
    color: var(--color-white-text);
    font-weight: 600;
}

.quiz-badges {
    display: flex;
    gap: 8px;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-info {
    background: rgba(59, 130, 246, 0.2);
    color: var(--color-blue-accent);
}

.badge-system {
    background: rgba(245, 158, 11, 0.2);
    color: var(--color-amber-warning);
}

.quiz-description {
    color: var(--color-gray-text);
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 16px;
}

.quiz-meta {
    display: flex;
    gap: 16px;
    padding: 16px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 16px;
    font-size: 13px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--color-gray-text);
}

.meta-item i {
    width: 16px;
    height: 16px;
    opacity: 0.7;
}

.quiz-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}

.stat-box {
    background: rgba(255, 255, 255, 0.05);
    padding: 12px;
    border-radius: 6px;
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: var(--color-gray-text);
    margin-bottom: 4px;
}

.stat-value {
    display: block;
    font-size: 18px;
    font-weight: 700;
    color: var(--color-blue-accent);
}

.btn-block {
    width: 100%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const quizCards = document.querySelectorAll('.quiz-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterValue = this.dataset.filter;

            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            quizCards.forEach(card => {
                if (filterValue === 'all' || card.dataset.type === filterValue) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
