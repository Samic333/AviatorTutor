<div class="admin-dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Content Manager</h1>
        <p class="subtitle">Manage Q400 study content and track system usage</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Total Users</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['user_count'] ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="layers"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Systems</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['system_count'] ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="book-open"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Lessons</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['lesson_count'] ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="credit-card"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Flashcards</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['flashcard_count'] ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="check-square"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Quizzes</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['quiz_count'] ?? 0); ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i data-lucide="upload"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Imports</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['import_log_count'] ?? 0); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Quick Actions</h2>
        </div>
        <div class="card-body">
            <div class="actions-grid">
                <a href="/admin/import" class="action-card">
                    <div class="action-icon">
                        <i data-lucide="upload"></i>
                    </div>
                    <h3>Import Content</h3>
                    <p>Upload PDFs and images</p>
                </a>
                <a href="/admin/content?tab=flashcards" class="action-card">
                    <div class="action-icon">
                        <i data-lucide="credit-card"></i>
                    </div>
                    <h3>Manage Flashcards</h3>
                    <p>Edit or delete cards</p>
                </a>
                <a href="/admin/content?tab=quizzes" class="action-card">
                    <div class="action-icon">
                        <i data-lucide="check-square"></i>
                    </div>
                    <h3>Manage Quizzes</h3>
                    <p>Create or edit quizzes</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Recent Imports -->
    <?php if (!empty($recentImports)): ?>
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Imports</h2>
            <a href="/admin/import" class="link-secondary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Status</th>
                            <th>Records</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recentImports, 0, 10) as $import): ?>
                            <tr>
                                <td class="filename">
                                    <i data-lucide="file-text"></i>
                                    <?php echo htmlspecialchars($import['filename']); ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo htmlspecialchars($import['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($import['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($import['records_imported'] ?? 0); ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($import['created_at']))); ?></td>
                                <td>
                                    <a href="/admin/import/<?php echo htmlspecialchars($import['id']); ?>" class="link-small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Recent Users -->
    <?php if (!empty($users)): ?>
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Users</h2>
        </div>
        <div class="card-body">
            <div class="users-list">
                <?php foreach (array_slice($users, 0, 5) as $user): ?>
                    <div class="user-item">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['name'], 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <div class="user-joined">
                            <span><?php echo htmlspecialchars(date('M j, Y', strtotime($user['created_at']))); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>
.admin-dashboard-container {
    padding: 20px;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.action-card {
    padding: 20px;
    background: var(--color-dark-bg);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
    text-align: center;
}

.action-card:hover {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.05);
}

.action-icon {
    font-size: 32px;
    color: var(--color-blue-accent);
    margin-bottom: 12px;
    display: flex;
    justify-content: center;
}

.action-icon i {
    width: 32px;
    height: 32px;
}

.action-card h3 {
    margin: 12px 0 6px 0;
    font-size: 16px;
    color: var(--color-white-text);
}

.action-card p {
    margin: 0;
    color: var(--color-gray-text);
    font-size: 13px;
}

.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: var(--color-gray-text);
    font-size: 12px;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.admin-table td {
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--color-white-text);
}

.admin-table tr:hover {
    background: rgba(59, 130, 246, 0.05);
}

.filename {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filename i {
    width: 18px;
    height: 18px;
    opacity: 0.7;
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.success {
    background: rgba(16, 185, 129, 0.2);
    color: var(--color-success);
}

.status-badge.error {
    background: rgba(239, 68, 68, 0.2);
    color: var(--color-danger);
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.2);
    color: var(--color-amber-warning);
}

.link-small {
    color: var(--color-blue-accent);
    text-decoration: none;
    font-size: 12px;
    transition: opacity 0.2s;
}

.link-small:hover {
    opacity: 0.8;
}

.users-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.user-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--color-dark-bg);
    border-radius: 6px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--color-blue-accent);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.user-info {
    flex: 1;
}

.user-info h4 {
    margin: 0;
    color: var(--color-white-text);
    font-size: 14px;
}

.user-info p {
    margin: 4px 0 0 0;
    color: var(--color-gray-text);
    font-size: 12px;
}

.user-joined {
    flex-shrink: 0;
    color: var(--color-gray-text);
    font-size: 12px;
}

.link-secondary {
    color: var(--color-gray-text);
    text-decoration: none;
    font-size: 14px;
}

.link-secondary:hover {
    color: var(--color-blue-accent);
}
</style>
