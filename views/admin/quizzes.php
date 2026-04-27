<div class="admin-quizzes-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Manage Quizzes</h1>
        <p class="subtitle">Create and manage quizzes for assessment and practice</p>
    </div>

    <!-- Add Quiz Form Section -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Create New Quiz</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="/admin/quizzes/create" class="quiz-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Quiz Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" required placeholder="Enter quiz title...">
                    </div>

                    <div class="form-group">
                        <label for="system_id">System <span class="required">*</span></label>
                        <select name="system_id" id="system_id" required>
                            <option value="">Select a system...</option>
                            <?php if (!empty($systems)): ?>
                                <?php foreach ($systems as $system): ?>
                                    <option value="<?php echo htmlspecialchars($system['id']); ?>">
                                        <?php echo htmlspecialchars($system['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quiz_type">Quiz Type <span class="required">*</span></label>
                        <select name="quiz_type" id="quiz_type" required>
                            <option value="">Select type...</option>
                            <option value="practice">Practice Quiz</option>
                            <option value="exam">Exam</option>
                            <option value="scenario">Scenario-Based</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="time_limit">Time Limit (minutes, optional)</label>
                        <input type="number" name="time_limit" id="time_limit" min="1" placeholder="Leave empty for no limit">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" placeholder="Brief description of the quiz..." rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="pass_score">Pass Score (%) <span class="required">*</span></label>
                        <input type="number" name="pass_score" id="pass_score" required min="0" max="100" value="70" placeholder="Default: 70">
                        <span class="form-help">Percentage needed to pass the quiz</span>
                    </div>

                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div style="display: flex; align-items: center; gap: 12px; padding-top: 2px;">
                            <input type="checkbox" name="is_published" id="is_published" value="1">
                            <label for="is_published" style="margin: 0; font-weight: 400;">Publish immediately</label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="plus"></i> Create Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quizzes Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Existing Quizzes</h2>
            <?php if (!empty($quizzes)): ?>
                <span class="card-badge"><?php echo count($quizzes); ?> quizzes</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>System</th>
                            <th>Type</th>
                            <th>Questions</th>
                            <th>Pass Score</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($quizzes)): ?>
                            <?php foreach ($quizzes as $quiz): ?>
                                <tr>
                                    <td class="title-cell">
                                        <span title="<?php echo htmlspecialchars($quiz['title']); ?>">
                                            <?php echo htmlspecialchars(substr($quiz['title'], 0, 40)); ?>
                                            <?php if (strlen($quiz['title']) > 40): ?>...<?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="system-cell">
                                        <span class="system-tag"><?php echo htmlspecialchars($quiz['system_name'] ?? 'Unknown'); ?></span>
                                    </td>
                                    <td class="type-cell">
                                        <span class="badge badge-type">
                                            <?php echo ucfirst(htmlspecialchars($quiz['quiz_type'] ?? 'practice')); ?>
                                        </span>
                                    </td>
                                    <td class="questions-cell">
                                        <?php echo htmlspecialchars($quiz['question_count'] ?? 0); ?> Q
                                    </td>
                                    <td class="pass-score-cell">
                                        <?php echo htmlspecialchars($quiz['pass_score'] ?? 70); ?>%
                                    </td>
                                    <td class="status-cell">
                                        <?php if (!empty($quiz['is_published'])): ?>
                                            <span class="badge badge-success">Published</span>
                                        <?php else: ?>
                                            <span class="badge badge-draft">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="date-cell">
                                        <?php
                                        if (!empty($quiz['created_at'])) {
                                            echo date('M d, Y', strtotime($quiz['created_at']));
                                        } else {
                                            echo '--';
                                        }
                                        ?>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="btn btn-xs btn-secondary" onclick="editQuiz(<?php echo htmlspecialchars($quiz['id']); ?>)">
                                            Edit
                                        </button>
                                        <button class="btn btn-xs btn-danger" onclick="deleteQuiz(<?php echo htmlspecialchars($quiz['id']); ?>)">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i data-lucide="inbox"></i>
                                    <p>No quizzes yet. Create one to get started!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.admin-quizzes-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
    color: var(--color-white-text);
}

.subtitle {
    margin: 0;
    color: var(--color-gray-text);
    font-size: 14px;
}

.card {
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.card-title {
    margin: 0;
    font-size: 18px;
    color: var(--color-white-text);
    font-weight: 600;
}

.card-badge {
    background: var(--color-blue-accent);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.card-body {
    padding: 20px;
}

.quiz-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-row.full {
    grid-template-columns: 1fr;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    color: var(--color-white-text);
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
}

.required {
    color: var(--color-danger);
}

.form-group input,
.form-group select,
.form-group textarea {
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    padding: 10px 12px;
    color: var(--color-white-text);
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: var(--color-muted-text);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--color-blue-accent);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    margin: 0;
}

.form-help {
    font-size: 12px;
    color: var(--color-muted-text);
    margin-top: 6px;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn i {
    width: 18px;
    height: 18px;
}

.btn-primary {
    background: var(--color-blue-accent);
    color: white;
}

.btn-primary:hover {
    background: #2563EB;
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--color-gray-text);
}

.btn-secondary:hover {
    background: var(--color-dark-bg);
    border-color: var(--color-blue-accent);
    color: var(--color-blue-accent);
}

.btn-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: var(--color-danger);
}

.btn-danger:hover {
    background: var(--color-danger);
    color: white;
}

.btn-xs {
    padding: 6px 12px;
    font-size: 12px;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: var(--color-dark-bg);
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.table th {
    padding: 12px;
    text-align: left;
    color: var(--color-gray-text);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table td {
    padding: 16px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    color: var(--color-white-text);
    font-size: 14px;
}

.table tbody tr:hover {
    background: rgba(59, 130, 246, 0.05);
}

.title-cell {
    max-width: 250px;
}

.system-cell {
    width: 120px;
}

.system-tag {
    display: inline-block;
    background: rgba(59, 130, 246, 0.2);
    color: var(--color-blue-accent);
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.type-cell {
    width: 100px;
}

.questions-cell {
    width: 80px;
    text-align: center;
}

.pass-score-cell {
    width: 80px;
    text-align: center;
    color: var(--color-muted-text);
}

.status-cell {
    width: 90px;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    width: fit-content;
}

.badge-type {
    background: rgba(59, 130, 246, 0.2);
    color: var(--color-blue-accent);
}

.badge-success {
    background: rgba(16, 185, 129, 0.2);
    color: var(--color-success);
}

.badge-draft {
    background: rgba(148, 163, 184, 0.2);
    color: var(--color-gray-text);
}

.date-cell {
    width: 100px;
    color: var(--color-muted-text);
    font-size: 13px;
}

.actions-cell {
    width: 160px;
    display: flex;
    gap: 8px;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: var(--color-muted-text);
}

.text-center i {
    width: 48px;
    height: 48px;
    margin: 0 auto 16px auto;
    opacity: 0.3;
}

@media (max-width: 1024px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .admin-quizzes-container {
        padding: 16px;
    }
}

@media (max-width: 768px) {
    .table {
        font-size: 12px;
    }

    .table th,
    .table td {
        padding: 10px 8px;
    }

    .title-cell,
    .system-cell {
        width: auto;
    }

    .actions-cell {
        width: auto;
        flex-wrap: wrap;
    }
}
</style>

<script>
function editQuiz(id) {
    // TODO: Implement edit quiz functionality
    alert('Edit quiz ' + id);
}

function deleteQuiz(id) {
    if (confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
        // TODO: Implement delete quiz functionality
        alert('Delete quiz ' + id);
    }
}
</script>
