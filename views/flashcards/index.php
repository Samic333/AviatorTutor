<div class="flashcards-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Flashcards</h1>
        <p class="subtitle">Master Q400 systems with spaced repetition</p>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="credit-card"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Total Flashcards</h3>
                <p class="stat-value"><?php echo htmlspecialchars($totalFlashcards ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i data-lucide="alert-triangle"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Due Today</h3>
                <p class="stat-value"><?php echo htmlspecialchars($dueCount ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i data-lucide="check-circle"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Mastered</h3>
                <p class="stat-value"><?php echo htmlspecialchars($masteredCount ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="flame"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Study Streak</h3>
                <p class="stat-value"><?php echo htmlspecialchars($studyStreak ?? 0); ?> <span class="stat-total">days</span></p>
            </div>
        </div>
    </div>

    <!-- Systems Grid -->
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Study by System</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($systems)): ?>
                <div class="systems-grid">
                    <?php foreach ($systems as $system): ?>
                        <div class="system-card" style="border-left: 4px solid <?php echo htmlspecialchars($system['color_hex'] ?? '#3B82F6'); ?>">
                            <div class="system-card-header">
                                <div class="system-card-title">
                                    <h3><?php echo htmlspecialchars($system['name']); ?></h3>
                                    <?php if (!empty($system['icon'])): ?>
                                        <i data-lucide="<?php echo htmlspecialchars($system['icon']); ?>" class="system-icon"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="system-card-body">
                                <div class="card-stat-row">
                                    <span class="label">Flashcards:</span>
                                    <span class="value"><?php echo htmlspecialchars($system['flashcard_count'] ?? 0); ?></span>
                                </div>
                                <div class="card-stat-row">
                                    <span class="label">Due Today:</span>
                                    <span class="value" style="color: var(--color-amber-warning)">
                                        <?php echo htmlspecialchars($system['due_count'] ?? 0); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="system-card-footer">
                                <?php if ($system['flashcard_count'] > 0): ?>
                                    <a href="/flashcards/<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-primary btn-sm">
                                        Study Now
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">No flashcards yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="inbox"></i>
                    <p>No systems with flashcards yet.</p>
                    <a href="/systems" class="btn btn-primary">Browse Systems</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
.flashcards-container {
    padding: 20px;
}

.systems-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.system-card {
    background: var(--color-slate-bg);
    border-radius: 8px;
    border-left: 4px solid;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.system-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.system-card-header {
    padding: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.system-card-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.system-card-title h3 {
    margin: 0;
    font-size: 18px;
    color: var(--color-white-text);
}

.system-icon {
    width: 24px;
    height: 24px;
    opacity: 0.7;
}

.system-card-body {
    padding: 16px;
}

.card-stat-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 14px;
}

.card-stat-row:last-child {
    margin-bottom: 0;
}

.card-stat-row .label {
    color: var(--color-gray-text);
}

.card-stat-row .value {
    font-weight: 600;
    color: var(--color-white-text);
}

.system-card-footer {
    padding: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.text-muted {
    color: var(--color-muted-text);
    font-size: 14px;
}
</style>
