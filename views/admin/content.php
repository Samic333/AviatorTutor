<div class="admin-content-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Content Manager</h1>
        <p class="subtitle">Manage systems, lessons, flashcards, and quizzes</p>
    </div>

    <!-- Tabs -->
    <div class="content-tabs">
        <button class="tab-btn active" data-tab="systems">Systems</button>
        <button class="tab-btn" data-tab="lessons">Lessons</button>
        <button class="tab-btn" data-tab="flashcards">Flashcards</button>
        <button class="tab-btn" data-tab="quizzes">Quizzes</button>
    </div>

    <!-- Systems Tab -->
    <section class="tab-content active" id="systems-tab">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Systems</h2>
                <a href="/admin/content/systems/new" class="btn btn-sm btn-primary">
                    <i data-lucide="plus"></i> Add System
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>ATA Code</th>
                                <th>Description</th>
                                <th>Lessons</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($systems)): ?>
                                <?php foreach ($systems as $system): ?>
                                    <tr>
                                        <td class="name-cell">
                                            <div class="color-dot" style="background-color: <?php echo htmlspecialchars($system['color_hex'] ?? '#3B82F6'); ?>"></div>
                                            <span><?php echo htmlspecialchars($system['name']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($system['ata_code'] ?? '--'); ?></td>
                                        <td><?php echo htmlspecialchars(substr($system['description'] ?? '', 0, 50)); ?>...</td>
                                        <td><?php echo htmlspecialchars($system['lesson_count'] ?? 0); ?></td>
                                        <td class="actions-cell">
                                            <a href="/admin/content/systems/<?php echo htmlspecialchars($system['id']); ?>/edit" class="btn btn-xs btn-secondary">Edit</a>
                                            <button onclick="deleteItem('system', <?php echo htmlspecialchars($system['id']); ?>)" class="btn btn-xs btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No systems yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Lessons Tab -->
    <section class="tab-content" id="lessons-tab">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Lessons</h2>
                <a href="/admin/content/lessons/new" class="btn btn-sm btn-primary">
                    <i data-lucide="plus"></i> Add Lesson
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>System</th>
                                <th>Topics</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($lessons)): ?>
                                <?php foreach ($lessons as $lesson): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                                        <td><?php echo htmlspecialchars($lesson['system_name'] ?? '--'); ?></td>
                                        <td><?php echo htmlspecialchars($lesson['topic_count'] ?? 0); ?></td>
                                        <td>
                                            <span class="status-badge">
                                                <?php echo htmlspecialchars(ucfirst($lesson['status'] ?? 'draft')); ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="/admin/content/lessons/<?php echo htmlspecialchars($lesson['id']); ?>/edit" class="btn btn-xs btn-secondary">Edit</a>
                                            <button onclick="deleteItem('lesson', <?php echo htmlspecialchars($lesson['id']); ?>)" class="btn btn-xs btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No lessons yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Flashcards Tab -->
    <section class="tab-content" id="flashcards-tab">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Flashcards</h2>
                <a href="/admin/content/flashcards/new" class="btn btn-sm btn-primary">
                    <i data-lucide="plus"></i> Add Card
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Front</th>
                                <th>Back</th>
                                <th>System</th>
                                <th>Difficulty</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($flashcards)): ?>
                                <?php foreach (array_slice($flashcards, 0, 20) as $card): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(substr($card['front'] ?? '', 0, 40)); ?></td>
                                        <td><?php echo htmlspecialchars(substr($card['back'] ?? '', 0, 40)); ?></td>
                                        <td><?php echo htmlspecialchars($card['system_name'] ?? '--'); ?></td>
                                        <td>
                                            <span class="difficulty-badge <?php echo htmlspecialchars($card['difficulty'] ?? 'medium'); ?>">
                                                <?php echo htmlspecialchars(ucfirst($card['difficulty'] ?? 'medium')); ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="/admin/content/flashcards/<?php echo htmlspecialchars($card['id']); ?>/edit" class="btn btn-xs btn-secondary">Edit</a>
                                            <button onclick="deleteItem('flashcard', <?php echo htmlspecialchars($card['id']); ?>)" class="btn btn-xs btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No flashcards yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Quizzes Tab -->
    <section class="tab-content" id="quizzes-tab">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quizzes</h2>
                <a href="/admin/content/quizzes/new" class="btn btn-sm btn-primary">
                    <i data-lucide="plus"></i> Add Quiz
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>System</th>
                                <th>Type</th>
                                <th>Questions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($quizzes)): ?>
                                <?php foreach ($quizzes as $quiz): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['system_name'] ?? '--'); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($quiz['quiz_type'] ?? 'practice')); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['question_count'] ?? 0); ?></td>
                                        <td class="actions-cell">
                                            <a href="/admin/content/quizzes/<?php echo htmlspecialchars($quiz['id']); ?>/edit" class="btn btn-xs btn-secondary">Edit</a>
                                            <button onclick="deleteItem('quiz', <?php echo htmlspecialchars($quiz['id']); ?>)" class="btn btn-xs btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No quizzes yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.admin-content-container {
    padding: 20px;
}

.content-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.tab-btn {
    padding: 12px 20px;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--color-gray-text);
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.tab-btn:hover {
    color: var(--color-white-text);
}

.tab-btn.active {
    color: var(--color-blue-accent);
    border-bottom-color: var(--color-blue-accent);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.table-responsive {
    overflow-x: auto;
}

.content-table {
    width: 100%;
    border-collapse: collapse;
}

.content-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: var(--color-gray-text);
    font-size: 12px;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.content-table td {
    padding: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--color-white-text);
}

.content-table tr:hover {
    background: rgba(59, 130, 246, 0.05);
}

.name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    background: var(--color-blue-accent);
    color: white;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.difficulty-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.difficulty-badge.easy {
    background: rgba(16, 185, 129, 0.2);
    color: var(--color-success);
}

.difficulty-badge.medium {
    background: rgba(245, 158, 11, 0.2);
    color: var(--color-amber-warning);
}

.difficulty-badge.hard {
    background: rgba(239, 68, 68, 0.2);
    color: var(--color-danger);
}

.actions-cell {
    display: flex;
    gap: 8px;
}

.btn-xs {
    padding: 4px 10px;
    font-size: 11px;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: var(--color-gray-text);
}

.btn-secondary {
    background: var(--color-slate-bg);
    color: var(--color-white-text);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-secondary:hover {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.1);
}

.btn-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--color-danger);
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.btn-danger:hover {
    background: rgba(239, 68, 68, 0.2);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;

            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            this.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });
});

function deleteItem(type, id) {
    if (confirm('Are you sure you want to delete this ' + type + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/content/' + type + '/' + id + '/delete';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
