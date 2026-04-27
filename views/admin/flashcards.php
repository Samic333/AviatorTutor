<div class="admin-flashcards-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Manage Flashcards</h1>
        <p class="subtitle">Create and manage flashcards for each system</p>
    </div>

    <!-- Add Flashcard Form Section -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add New Flashcard</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="/admin/flashcards/create" class="flashcard-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

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

                <div class="form-group">
                    <label for="front">Front (Question/Prompt) <span class="required">*</span></label>
                    <textarea name="front" id="front" required placeholder="Enter the question or prompt..." rows="3"></textarea>
                    <span class="form-help">This is what students will see first</span>
                </div>

                <div class="form-group">
                    <label for="back">Back (Answer) <span class="required">*</span></label>
                    <textarea name="back" id="back" required placeholder="Enter the answer..." rows="3"></textarea>
                    <span class="form-help">This is the correct answer or explanation</span>
                </div>

                <div class="form-group">
                    <label for="hint">Hint (Optional)</label>
                    <input type="text" name="hint" id="hint" placeholder="Optional hint to help students...">
                </div>

                <div class="form-group">
                    <label for="difficulty">Difficulty <span class="required">*</span></label>
                    <select name="difficulty" id="difficulty" required>
                        <option value="">Select difficulty...</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="plus"></i> Create Flashcard
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flashcards Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Existing Flashcards</h2>
            <?php if (!empty($flashcards)): ?>
                <span class="card-badge"><?php echo count($flashcards); ?> cards</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>System</th>
                            <th>Front (Question)</th>
                            <th>Difficulty</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($flashcards)): ?>
                            <?php foreach ($flashcards as $card): ?>
                                <tr>
                                    <td class="system-cell">
                                        <span class="system-tag"><?php echo htmlspecialchars($card['system_name'] ?? 'Unknown'); ?></span>
                                    </td>
                                    <td class="front-cell">
                                        <span title="<?php echo htmlspecialchars($card['front']); ?>">
                                            <?php echo htmlspecialchars(substr($card['front'], 0, 60)); ?>
                                            <?php if (strlen($card['front']) > 60): ?>...<?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="difficulty-cell">
                                        <?php $difficulty = $card['difficulty'] ?? 'medium'; ?>
                                        <span class="badge badge-difficulty badge-<?php echo htmlspecialchars($difficulty); ?>">
                                            <?php echo ucfirst(htmlspecialchars($difficulty)); ?>
                                        </span>
                                    </td>
                                    <td class="date-cell">
                                        <?php
                                        if (!empty($card['created_at'])) {
                                            echo date('M d, Y', strtotime($card['created_at']));
                                        } else {
                                            echo '--';
                                        }
                                        ?>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="btn btn-xs btn-secondary" onclick="editFlashcard(<?php echo htmlspecialchars($card['id']); ?>)">
                                            Edit
                                        </button>
                                        <button class="btn btn-xs btn-danger" onclick="deleteFlashcard(<?php echo htmlspecialchars($card['id']); ?>)">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i data-lucide="inbox"></i>
                                    <p>No flashcards yet. Create one to get started!</p>
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
.admin-flashcards-container {
    padding: 20px;
    max-width: 1200px;
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

.flashcard-form {
    display: grid;
    gap: 20px;
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

.front-cell {
    max-width: 400px;
    color: var(--color-gray-text);
}

.difficulty-cell {
    width: 100px;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-difficulty {
    width: fit-content;
}

.badge-easy {
    background: rgba(16, 185, 129, 0.2);
    color: var(--color-success);
}

.badge-medium {
    background: rgba(245, 158, 11, 0.2);
    color: var(--color-warning);
}

.badge-hard {
    background: rgba(239, 68, 68, 0.2);
    color: var(--color-danger);
}

.date-cell {
    width: 120px;
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

@media (max-width: 768px) {
    .admin-flashcards-container {
        padding: 16px;
    }

    .flashcard-form {
        gap: 16px;
    }

    .table {
        font-size: 12px;
    }

    .table th,
    .table td {
        padding: 10px 8px;
    }

    .actions-cell {
        width: auto;
        flex-wrap: wrap;
    }
}
</style>

<script>
function editFlashcard(id) {
    // TODO: Implement edit flashcard functionality
    alert('Edit flashcard ' + id);
}

function deleteFlashcard(id) {
    if (confirm('Are you sure you want to delete this flashcard?')) {
        // TODO: Implement delete flashcard functionality
        alert('Delete flashcard ' + id);
    }
}
</script>
