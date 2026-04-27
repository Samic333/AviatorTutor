<div class="system-detail-container">
    <!-- Hero Header -->
    <div class="system-hero" style="border-left: 8px solid <?php echo htmlspecialchars($system['color'] ?? '#34d399'); ?>">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="ata-code"><?php echo htmlspecialchars($system['ata_code']); ?></span>
            </div>
            <h1 class="hero-title"><?php echo htmlspecialchars($system['name']); ?></h1>
            <p class="hero-description"><?php echo htmlspecialchars($system['description']); ?></p>
            <div class="hero-meta">
                <span class="meta-item">
                    <i data-lucide="book-open"></i>
                    <?php echo htmlspecialchars($system['topic_count'] ?? 0); ?> Topics
                </span>
                <span class="meta-item">
                    <i data-lucide="clock"></i>
                    ~<?php echo htmlspecialchars($system['estimated_hours'] ?? 5); ?> hours
                </span>
                <span class="meta-item">
                    <span class="difficulty-badge difficulty-<?php echo htmlspecialchars($system['difficulty'] ?? 'basic'); ?>">
                        <?php echo htmlspecialchars(ucfirst($system['difficulty'] ?? 'Basic')); ?>
                    </span>
                </span>
            </div>
        </div>
        <div class="hero-progress">
            <div class="progress-ring">
                <svg viewBox="0 0 100 100" class="ring-svg">
                    <circle cx="50" cy="50" r="45" class="ring-bg"></circle>
                    <circle 
                        cx="50" 
                        cy="50" 
                        r="45" 
                        class="ring-fill" 
                        style="stroke-dasharray: <?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?> 100"
                    ></circle>
                </svg>
                <div class="ring-text"><?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?>%</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="system-main">
        <!-- Sidebar with Topics -->
        <aside class="system-sidebar">
            <h2 class="sidebar-title">Topics</h2>
            <nav class="topic-nav">
                <?php if (!empty($system['topics'])): ?>
                    <?php foreach ($system['topics'] as $topic): ?>
                        <a 
                            href="#topic-<?php echo htmlspecialchars($topic['id']); ?>" 
                            class="topic-item <?php echo ($currentTopic === $topic['id']) ? 'active' : ''; ?>"
                        >
                            <div class="topic-indicator">
                                <?php if ($topic['completed']): ?>
                                    <i data-lucide="check-circle" class="icon-completed"></i>
                                <?php else: ?>
                                    <span class="topic-number"><?php echo htmlspecialchars($topic['order']); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="topic-name"><?php echo htmlspecialchars($topic['name']); ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Content Area -->
        <div class="system-content">
            <!-- Tab Navigation -->
            <div class="content-tabs">
                <button class="tab-button active" data-tab="overview">
                    <i data-lucide="book-open"></i>
                    Overview
                </button>
                <button class="tab-button" data-tab="components">
                    <i data-lucide="layers"></i>
                    Components
                </button>
                <button class="tab-button" data-tab="operation">
                    <i data-lucide="zap"></i>
                    Operation
                </button>
                <button class="tab-button" data-tab="abnormal">
                    <i data-lucide="alert-circle"></i>
                    Abnormal
                </button>
                <button class="tab-button" data-tab="flashcards">
                    <i data-lucide="credit-card"></i>
                    Flashcards
                </button>
                <button class="tab-button" data-tab="quiz">
                    <i data-lucide="check-square"></i>
                    Quiz
                </button>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane active" id="tab-overview">

                    <!-- START STUDYING CTA -->
                    <div class="content-section" style="background: linear-gradient(135deg,#1e3a5f,#1a2d4a); border:1px solid #3b82f6; border-radius:12px; padding:24px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
                        <div>
                            <h2 style="margin:0 0 6px; font-size:1.2rem; color:#fff;">Ready to study <?php echo htmlspecialchars($system['name']); ?>?</h2>
                            <p style="margin:0; color:#94a3b8; font-size:.9rem;"><?php echo count($lessons ?? []); ?> lesson<?php echo count($lessons ?? []) !== 1 ? 's' : ''; ?> &bull; Complete lessons in order for best results</p>
                        </div>
                        <a href="/study/<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-primary" style="white-space:nowrap; padding:12px 28px; font-size:1rem;">
                            <i data-lucide="play-circle"></i>
                            Start Studying
                        </a>
                    </div>

                    <!-- Lessons List -->
                    <?php if (!empty($lessons)): ?>
                        <div class="content-section">
                            <h3 style="margin-bottom:16px;">Lessons</h3>
                            <div style="display:flex; flex-direction:column; gap:10px;">
                                <?php foreach ($lessons as $idx => $lesson): ?>
                                    <?php $isComplete = ($lesson['status'] ?? '') === 'completed'; ?>
                                    <a href="/study/<?php echo htmlspecialchars($system['id']); ?>"
                                       style="display:flex; align-items:center; gap:14px; padding:14px 18px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; text-decoration:none; color:inherit; transition:background .2s;"
                                       onmouseover="this.style.background='rgba(59,130,246,0.12)'"
                                       onmouseout="this.style.background='rgba(255,255,255,0.04)'">
                                        <div style="width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:<?php echo $isComplete ? '#16a34a' : '#1e3a5f'; ?>; border:2px solid <?php echo $isComplete ? '#22c55e' : '#3b82f6'; ?>;">
                                            <?php if ($isComplete): ?>
                                                <i data-lucide="check" style="width:14px;height:14px;color:#22c55e;"></i>
                                            <?php else: ?>
                                                <span style="font-size:.8rem; color:#93c5fd; font-weight:600;"><?php echo $idx + 1; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="flex:1; min-width:0;">
                                            <div style="font-weight:600; font-size:.95rem; color:#f1f5f9;"><?php echo htmlspecialchars($lesson['title']); ?></div>
                                            <?php if (!empty($lesson['summary'])): ?>
                                                <div style="font-size:.8rem; color:#64748b; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($lesson['summary']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <span style="font-size:.75rem; color:<?php echo $isComplete ? '#22c55e' : '#64748b'; ?>; flex-shrink:0;">
                                            <?php echo $isComplete ? '✓ Done' : ucfirst($lesson['content_type'] ?? 'lesson'); ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($system['overview'])): ?>
                        <div class="content-section">
                            <h3>System Overview</h3>
                            <div class="content-text">
                                <?php echo nl2br(htmlspecialchars($system['overview'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Components Tab -->
                <div class="tab-pane" id="tab-components">
                    <?php if (!empty($system['components'])): ?>
                        <div class="components-grid">
                            <?php foreach ($system['components'] as $component): ?>
                                <div class="component-card">
                                    <h4><?php echo htmlspecialchars($component['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($component['description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No components listed yet.</p>
                    <?php endif; ?>
                </div>

                <!-- Operation Tab -->
                <div class="tab-pane" id="tab-operation">
                    <?php if (!empty($system['operation'])): ?>
                        <div class="content-text">
                            <?php echo nl2br(htmlspecialchars($system['operation'])); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Operation details coming soon.</p>
                    <?php endif; ?>
                </div>

                <!-- Abnormal Tab -->
                <div class="tab-pane" id="tab-abnormal">
                    <?php if (!empty($system['abnormal'])): ?>
                        <div class="content-text">
                            <?php echo nl2br(htmlspecialchars($system['abnormal'])); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Abnormal procedures coming soon.</p>
                    <?php endif; ?>
                </div>

                <!-- Flashcards Tab -->
                <div class="tab-pane" id="tab-flashcards">
                    <div class="action-section">
                        <h3>Flashcard Practice</h3>
                        <p><?php echo htmlspecialchars($flashcardCount ?? 0); ?> flashcards available for this system</p>
                        <a href="/flashcards/<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-primary">
                            Start Flashcard Session
                        </a>
                    </div>
                </div>

                <!-- Quiz Tab -->
                <div class="tab-pane" id="tab-quiz">
                    <div class="action-section">
                        <h3>Take a Quiz</h3>
                        <p>Test your knowledge of this system with a quiz</p>
                        <a href="/quiz?system=<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-primary">
                            Start Quiz
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="notes-section">
                <h3>My Notes</h3>
                <form method="POST" action="/api/notes/save">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                    <input type="hidden" name="system_id" value="<?php echo htmlspecialchars($system['id']); ?>">
                    <textarea
                        name="content"
                        class="form-control"
                        placeholder="Add your study notes here..."
                        rows="6"
                    ><?php echo htmlspecialchars($userNotes ?? ''); ?></textarea>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Save Notes</button>
                    </div>
                </form>
            </div>

            <!-- Related Systems -->
            <?php if (!empty($relatedSystems)): ?>
                <div class="related-section">
                    <h3>Related Systems</h3>
                    <div class="related-grid">
                        <?php foreach ($relatedSystems as $related): ?>
                            <a href="/systems/<?php echo htmlspecialchars($related['id']); ?>" class="related-card">
                                <span class="related-name"><?php echo htmlspecialchars($related['name']); ?></span>
                                <i data-lucide="arrow-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Bar -->
            <div class="action-bar">
                <button class="btn btn-success" onclick="markSystemComplete()">
                    <i data-lucide="check"></i>
                    Mark System Complete
                </button>
                <a href="/systems" class="btn btn-secondary">Back to Systems</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        });
    });

    // Topic navigation
    const topicItems = document.querySelectorAll('.topic-item');
    topicItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            topicItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    lucide.createIcons();
});

function markSystemComplete() {
    if (confirm('Mark this system as complete?')) {
        const systemId = '<?php echo htmlspecialchars($system['id']); ?>';
        fetch('/api/systems/' + systemId + '/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                alert('System marked as complete!');
                location.reload();
            }
        });
    }
}
</script>
