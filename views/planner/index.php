<div class="planner-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Study Planner</h1>
        <p class="subtitle">Create study schedules and track your learning goals</p>
    </div>

    <!-- Create New Plan Section -->
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Create New Study Plan</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="/planner" class="plan-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <div class="form-group">
                    <label class="form-label">Plan Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Prepare for Type Rating Exam" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Exam Date</label>
                        <input type="date" name="exam_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Daily Study Minutes</label>
                        <input type="number" name="daily_minutes" class="form-control" min="15" max="480" value="60" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Systems to Prioritize</label>
                    <div class="checkbox-group">
                        <?php if (!empty($systems)): ?>
                            <?php foreach ($systems as $system): ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="system_ids[]" value="<?php echo htmlspecialchars($system['id']); ?>">
                                    <span><?php echo htmlspecialchars($system['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    <i data-lucide="plus"></i>
                    Create Plan
                </button>
            </form>
        </div>
    </section>

    <!-- Active Plans -->
    <?php if (!empty($plans)): ?>
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Active Plans</h2>
        </div>
        <div class="card-body">
            <div class="plans-list">
                <?php foreach ($plans as $plan): ?>
                    <div class="plan-item">
                        <div class="plan-header">
                            <h3><?php echo htmlspecialchars($plan['title']); ?></h3>
                            <span class="plan-status">
                                <?php
                                $daysLeft = floor((strtotime($plan['exam_date']) - time()) / 86400);
                                if ($daysLeft > 0) {
                                    echo htmlspecialchars($daysLeft) . ' days left';
                                } else if ($daysLeft === 0) {
                                    echo 'Today!';
                                } else {
                                    echo 'Exam passed';
                                }
                                ?>
                            </span>
                        </div>
                        <div class="plan-meta">
                            <span><i data-lucide="calendar"></i> <?php echo date('M j, Y', strtotime($plan['exam_date'])); ?></span>
                            <span><i data-lucide="target"></i> <?php echo htmlspecialchars($plan['daily_minutes']); ?> min/day</span>
                            <span><i data-lucide="layers"></i> <?php echo htmlspecialchars($plan['system_count'] ?? 0); ?> systems</span>
                        </div>
                        <div class="plan-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo htmlspecialchars($plan['progress_percentage'] ?? 0); ?>%"></div>
                            </div>
                            <span class="progress-label"><?php echo htmlspecialchars($plan['progress_percentage'] ?? 0); ?>% Complete</span>
                        </div>
                        <div class="plan-actions">
                            <a href="/planner/<?php echo htmlspecialchars($plan['id']); ?>" class="btn btn-sm btn-secondary">View Details</a>
                            <button class="btn btn-sm btn-danger" onclick="deletePlan(<?php echo htmlspecialchars($plan['id']); ?>)">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Upcoming Sessions -->
    <?php if (!empty($upcomingItems)): ?>
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Upcoming Study Sessions</h2>
        </div>
        <div class="card-body">
            <div class="upcoming-list">
                <?php foreach ($upcomingItems as $item): ?>
                    <div class="upcoming-item">
                        <div class="item-date">
                            <span class="date-label"><?php echo htmlspecialchars($item['scheduled_date']); ?></span>
                        </div>
                        <div class="item-info">
                            <h4><?php echo htmlspecialchars($item['system_name']); ?></h4>
                            <p class="item-duration">
                                <i data-lucide="clock"></i>
                                <?php echo htmlspecialchars($item['duration_mins']); ?> minutes
                            </p>
                        </div>
                        <div class="item-status">
                            <span class="status-badge <?php echo htmlspecialchars($item['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($item['status'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php else: ?>
    <section class="card">
        <div class="card-body">
            <div class="empty-state">
                <i data-lucide="calendar"></i>
                <p>No study plans yet. Create one to get started!</p>
                <p class="text-muted">Create a plan to organize your learning schedule and track progress toward your goals.</p>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>
.planner-container {
    padding: 20px;
}

.plan-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    color: var(--color-white-text);
    font-weight: 600;
    font-size: 14px;
}

.form-control {
    padding: 10px 14px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: var(--color-white-text);
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--color-blue-accent);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.checkbox-item:hover {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.05);
}

.checkbox-item input[type="checkbox"] {
    cursor: pointer;
}

.checkbox-item span {
    color: var(--color-white-text);
    font-size: 14px;
}

.plans-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.plan-item {
    padding: 20px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    transition: border-color 0.2s;
}

.plan-item:hover {
    border-color: var(--color-blue-accent);
}

.plan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    gap: 16px;
}

.plan-header h3 {
    margin: 0;
    color: var(--color-white-text);
}

.plan-status {
    display: inline-block;
    padding: 4px 12px;
    background: var(--color-blue-accent);
    color: white;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.plan-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 16px;
    font-size: 14px;
    color: var(--color-gray-text);
}

.plan-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.plan-meta i {
    width: 16px;
    height: 16px;
    opacity: 0.7;
}

.plan-progress {
    margin-bottom: 16px;
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
    background: var(--color-blue-accent);
    transition: width 0.3s ease;
}

.progress-label {
    display: block;
    font-size: 12px;
    color: var(--color-gray-text);
    text-align: right;
}

.plan-actions {
    display: flex;
    gap: 12px;
}

.upcoming-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.upcoming-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 16px;
    background: var(--color-dark-bg);
    border-radius: 8px;
    border-left: 4px solid var(--color-blue-accent);
}

.item-date {
    flex-shrink: 0;
    text-align: center;
}

.date-label {
    display: block;
    font-weight: 600;
    color: var(--color-white-text);
    font-size: 14px;
}

.item-info {
    flex: 1;
}

.item-info h4 {
    margin: 0 0 4px 0;
    color: var(--color-white-text);
}

.item-duration {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--color-gray-text);
    font-size: 12px;
}

.item-duration i {
    width: 14px;
    height: 14px;
}

.item-status {
    flex-shrink: 0;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.2);
    color: var(--color-amber-warning);
}

.status-badge.completed {
    background: rgba(16, 185, 129, 0.2);
    color: var(--color-success);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
}

.text-muted {
    color: var(--color-gray-text);
}
</style>

<script>
function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this study plan?')) {
        // Submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/planner/' + planId + '/delete';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
