<div class="revision-container">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon" style="color: <?php echo htmlspecialchars($system['color']); ?>">
                <i data-lucide="<?php echo htmlspecialchars($system['icon']); ?>"></i>
            </div>
            <div class="header-text">
                <h1>Quick Revision - <?php echo htmlspecialchars($system['name']); ?></h1>
                <p class="subtitle">ATA <?php echo htmlspecialchars($system['ata_code']); ?></p>
            </div>
        </div>
    </div>

    <!-- Mode Selector -->
    <div class="revision-modes">
        <h2>Choose Review Duration</h2>
        <div class="modes-grid">
            <?php foreach ($revision_modes as $mode): ?>
                <button class="mode-card" onclick="selectMode(<?php echo $mode['duration']; ?>)">
                    <i data-lucide="clock"></i>
                    <span class="mode-label"><?php echo htmlspecialchars($mode['label']); ?></span>
                    <p class="mode-desc">Quick overview</p>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Key Facts Section -->
    <section class="revision-section">
        <div class="section-header">
            <h2>Key Facts</h2>
            <p>Essential points to remember</p>
        </div>
        <div class="facts-grid">
            <?php if (!empty($lessons)): ?>
                <?php foreach ($lessons as $lesson): ?>
                    <?php if (!empty($lesson['key_facts'])): ?>
                        <div class="fact-card">
                            <div class="fact-topic"><?php echo htmlspecialchars($lesson['title']); ?></div>
                            <ul class="fact-list">
                                <?php foreach ((array)json_decode($lesson['key_facts'], true) ?? [] as $fact): ?>
                                    <li><?php echo htmlspecialchars($fact); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No key facts available.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Must Know Section -->
    <section class="revision-section">
        <div class="section-header">
            <h2 style="color: #FBBF24;">Must Know</h2>
            <p>Critical information for exams</p>
        </div>
        <div class="must-know-list">
            <?php if (!empty($lessons)): ?>
                <?php foreach ($lessons as $lesson): ?>
                    <?php if (!empty($lesson['must_know'])): ?>
                        <div class="must-know-item">
                            <div class="item-title"><?php echo htmlspecialchars($lesson['title']); ?></div>
                            <ul class="item-list">
                                <?php foreach ((array)json_decode($lesson['must_know'], true) ?? [] as $point): ?>
                                    <li><?php echo htmlspecialchars($point); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No must-know points available.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Exam Traps Section -->
    <section class="revision-section">
        <div class="section-header">
            <h2 style="color: #FCA5A5;">Exam Traps</h2>
            <p>Common mistakes to avoid</p>
        </div>
        <div class="traps-list">
            <?php if (!empty($lessons)): ?>
                <?php foreach ($lessons as $lesson): ?>
                    <?php if (!empty($lesson['exam_traps'])): ?>
                        <div class="trap-item">
                            <div class="trap-title"><?php echo htmlspecialchars($lesson['title']); ?></div>
                            <ul class="trap-list">
                                <?php foreach ((array)json_decode($lesson['exam_traps'], true) ?? [] as $trap): ?>
                                    <li><?php echo htmlspecialchars($trap); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No exam traps available.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Flashcards Quick Review -->
    <?php if (!empty($flashcards)): ?>
        <section class="revision-section">
            <div class="section-header">
                <h2>Quick Flashcard Review</h2>
                <p>Spaced repetition learning</p>
            </div>
            <div class="flashcards-container">
                <?php foreach ($flashcards as $card): ?>
                    <div class="flashcard" onclick="this.classList.toggle('flipped')">
                        <div class="flashcard-inner">
                            <div class="flashcard-front">
                                <p><?php echo htmlspecialchars($card['front']); ?></p>
                                <small>Click to reveal</small>
                            </div>
                            <div class="flashcard-back">
                                <p><?php echo htmlspecialchars($card['back']); ?></p>
                            </div>
                        </div>
                        <div class="difficulty-badge difficulty-<?php echo htmlspecialchars($card['difficulty']); ?>">
                            <?php echo ucfirst($card['difficulty']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Study Tips -->
    <section class="revision-section revision-tips">
        <h2>Study Tips</h2>
        <div class="tips-grid">
            <div class="tip-card">
                <i data-lucide="repeat"></i>
                <h3>Spaced Repetition</h3>
                <p>Review content at increasing intervals to maximize retention.</p>
            </div>
            <div class="tip-card">
                <i data-lucide="highlighter"></i>
                <h3>Active Recall</h3>
                <p>Test yourself on the material to identify weak areas.</p>
            </div>
            <div class="tip-card">
                <i data-lucide="book-open"></i>
                <h3>Read Carefully</h3>
                <p>Pay attention to technical details and specific terminology.</p>
            </div>
            <div class="tip-card">
                <i data-lucide="checkmark"></i>
                <h3>Practice Exams</h3>
                <p>Take practice quizzes to assess your readiness.</p>
            </div>
        </div>
    </section>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="/systems/<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-secondary">
            <i data-lucide="book-open"></i>
            Full Study
        </a>
        <a href="/flashcards?system=<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-primary">
            <i data-lucide="credit-card"></i>
            Flashcards
        </a>
        <a href="/quiz?system=<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-primary">
            <i data-lucide="check-square"></i>
            Quiz
        </a>
    </div>
</div>

<style>
.revision-container {
    padding: 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
}

.header-content {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.header-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    background: rgba(59, 130, 246, 0.1);
    border-radius: 12px;
    flex-shrink: 0;
}

.header-text h1 {
    margin: 0 0 5px 0;
}

.header-text .subtitle {
    color: var(--color-gray-text);
    margin: 0;
    font-size: 12px;
}

.revision-modes {
    margin-bottom: 40px;
}

.revision-modes h2 {
    margin-bottom: 15px;
}

.modes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.mode-card {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
    border: 2px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    color: var(--color-white-text);
}

.mode-card:hover {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.1));
    border-color: rgba(59, 130, 246, 0.4);
    transform: translateY(-4px);
}

.mode-card i {
    width: 32px;
    height: 32px;
    color: var(--color-blue-light);
}

.mode-label {
    font-weight: 600;
    font-size: 15px;
}

.mode-desc {
    font-size: 12px;
    color: var(--color-gray-text);
    margin: 0;
}

.revision-section {
    margin-bottom: 40px;
}

.section-header {
    margin-bottom: 20px;
}

.section-header h2 {
    margin: 0 0 5px 0;
}

.section-header p {
    margin: 0;
    color: var(--color-gray-text);
    font-size: 13px;
}

.facts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
}

.fact-card {
    background: var(--color-slate-bg);
    border: 1px solid rgba(148, 163, 184, 0.1);
    border-left: 3px solid rgba(59, 130, 246, 0.5);
    border-radius: 8px;
    padding: 15px;
}

.fact-topic {
    font-weight: 600;
    color: var(--color-white-text);
    margin-bottom: 10px;
    font-size: 13px;
}

.fact-list {
    margin: 0;
    padding-left: 20px;
    font-size: 12px;
    color: var(--color-gray-text);
    line-height: 1.6;
}

.fact-list li {
    margin-bottom: 5px;
}

.must-know-list,
.traps-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.must-know-item,
.trap-item {
    background: var(--color-slate-bg);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-left: 3px solid rgba(245, 158, 11, 0.5);
    border-radius: 8px;
    padding: 15px;
}

.trap-item {
    border-color: rgba(239, 68, 68, 0.2);
    border-left-color: rgba(239, 68, 68, 0.5);
}

.item-title,
.trap-title {
    font-weight: 600;
    color: var(--color-white-text);
    margin-bottom: 10px;
    font-size: 13px;
}

.item-list,
.trap-list {
    margin: 0;
    padding-left: 20px;
    font-size: 12px;
    color: var(--color-gray-text);
    line-height: 1.6;
}

.item-list li,
.trap-list li {
    margin-bottom: 5px;
}

.flashcards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
}

.flashcard {
    height: 200px;
    cursor: pointer;
    perspective: 1000px;
    position: relative;
}

.flashcard-inner {
    width: 100%;
    height: 100%;
    transition: transform 0.6s;
    transform-style: preserve-3d;
    position: relative;
}

.flashcard.flipped .flashcard-inner {
    transform: rotateY(180deg);
}

.flashcard-front,
.flashcard-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid rgba(148, 163, 184, 0.1);
}

.flashcard-front {
    background: var(--color-slate-bg);
    color: var(--color-white-text);
}

.flashcard-back {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(10, 184, 181, 0.1));
    color: var(--color-white-text);
    transform: rotateY(180deg);
    border-color: rgba(59, 130, 246, 0.2);
}

.flashcard-front p,
.flashcard-back p {
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
}

.flashcard-front small {
    position: absolute;
    bottom: 10px;
    left: 10px;
    right: 10px;
    font-size: 10px;
    color: var(--color-gray-text);
}

.difficulty-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
}

.difficulty-easy {
    background: rgba(16, 185, 129, 0.2);
    color: #86EFAC;
}

.difficulty-medium {
    background: rgba(245, 158, 11, 0.2);
    color: #FBBF24;
}

.difficulty-hard {
    background: rgba(239, 68, 68, 0.2);
    color: #FCA5A5;
}

.revision-tips {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(10, 184, 181, 0.05));
    border: 1px solid rgba(148, 163, 184, 0.1);
    border-radius: 8px;
    padding: 25px;
    margin-top: 40px;
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.tip-card {
    text-align: center;
    padding: 15px;
}

.tip-card i {
    width: 32px;
    height: 32px;
    color: var(--color-blue-light);
    margin-bottom: 10px;
}

.tip-card h3 {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: var(--color-white-text);
}

.tip-card p {
    margin: 0;
    font-size: 12px;
    color: var(--color-gray-text);
    line-height: 1.5;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid rgba(148, 163, 184, 0.1);
}

.btn {
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: var(--color-white-text);
}

.btn-primary {
    background: var(--color-blue-accent);
}

.btn-primary:hover {
    background: #2563EB;
    transform: translateY(-2px);
}

.btn-secondary {
    background: transparent;
    border: 1px solid rgba(148, 163, 184, 0.2);
}

.btn-secondary:hover {
    background: rgba(148, 163, 184, 0.1);
}

.text-muted {
    color: var(--color-gray-text);
}

@media (max-width: 768px) {
    .modes-grid {
        grid-template-columns: 1fr;
    }

    .facts-grid {
        grid-template-columns: 1fr;
    }

    .flashcards-container {
        grid-template-columns: 1fr;
    }

    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});

function selectMode(duration) {
    // Timer mode could be implemented here
    console.log('Revision mode selected: ' + duration + ' minutes');
}
</script>
