<div class="quiz-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Quiz & Tests</h1>
        <p class="subtitle">Test your knowledge of Q400 systems</p>
    </div>

    <!-- Top filter row: keyword search + aircraft / subject / system dropdowns.
         Phase 8 follow-up (bug 27): replace the bare type-chip filter with a
         richer picker so learners on a multi-aircraft / multi-subject roster
         don't have to scroll a 50-quiz list. -->
    <div class="quiz-picker">
        <input type="search" class="quiz-picker__search" id="quizSearch"
               placeholder="Search quizzes…" autocomplete="off"
               aria-label="Search quizzes by title, description, or system">
        <?php if (!empty($aircrafts)): ?>
        <select class="quiz-picker__select" id="quizAircraft" aria-label="Filter by aircraft">
            <option value="">All aircraft</option>
            <?php foreach ($aircrafts as $aId => $aName): ?>
                <option value="<?= (int)$aId ?>"><?= htmlspecialchars($aName) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <?php if (!empty($subjects)): ?>
        <select class="quiz-picker__select" id="quizSubject" aria-label="Filter by subject">
            <option value="">All subjects</option>
            <?php foreach ($subjects as $sId => $sName): ?>
                <option value="<?= (int)$sId ?>"><?= htmlspecialchars($sName) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <?php if (!empty($systems)): ?>
        <select class="quiz-picker__select" id="quizSystem" aria-label="Filter by system">
            <option value="">All systems</option>
            <?php foreach ($systems as $sId => $sName): ?>
                <option value="<?= (int)$sId ?>"><?= htmlspecialchars($sName) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </div>

    <!-- Quiz type chips kept as a secondary filter -->
    <div class="quiz-filters">
        <button class="filter-btn active" data-filter="all">All types</button>
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
                        <div class="quiz-card"
                             data-type="<?php echo htmlspecialchars($quiz['quiz_type']); ?>"
                             data-aircraft="<?php echo (int)($quiz['aircraft_id'] ?? 0); ?>"
                             data-subject="<?php echo (int)($quiz['subject_id'] ?? 0); ?>"
                             data-system="<?php echo (int)($quiz['system_id'] ?? 0); ?>"
                             data-search="<?php echo htmlspecialchars(strtolower(($quiz['title'] ?? '') . ' ' . ($quiz['description'] ?? '') . ' ' . ($quiz['system_name'] ?? '') . ' ' . ($quiz['subject_name'] ?? '') . ' ' . ($quiz['aircraft_name'] ?? ''))); ?>">
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
                                    <span class="stat-value"><?php echo $quiz['avg_score'] !== null ? round((float)$quiz['avg_score']) . '%' : '<span style="font-size:18px;color:#94A3B8;">—</span>'; ?></span>
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

.quiz-picker {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
    align-items: stretch;
}
.quiz-picker__search {
    flex: 1 1 260px;
    min-width: 220px;
    padding: 10px 14px;
    background: var(--color-slate-bg);
    border: 1px solid rgba(148,163,184,0.18);
    border-radius: 8px;
    color: var(--color-white-text);
    font-size: 14px;
}
.quiz-picker__search:focus {
    outline: none;
    border-color: var(--color-blue-accent);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
}
.quiz-picker__select {
    flex: 0 1 auto;
    padding: 10px 14px;
    background: var(--color-slate-bg);
    border: 1px solid rgba(148,163,184,0.18);
    border-radius: 8px;
    color: var(--color-white-text);
    font-size: 14px;
    min-width: 160px;
    cursor: pointer;
}
.quiz-picker__select:focus {
    outline: none;
    border-color: var(--color-blue-accent);
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
    const grid = document.getElementById('quizzesGrid');
    const searchInput = document.getElementById('quizSearch');
    const aircraftSel = document.getElementById('quizAircraft');
    const subjectSel  = document.getElementById('quizSubject');
    const systemSel   = document.getElementById('quizSystem');

    let activeType = 'all';

    function applyFilters() {
        const term = (searchInput?.value || '').trim().toLowerCase();
        const aircraft = aircraftSel?.value || '';
        const subject  = subjectSel?.value || '';
        const system   = systemSel?.value || '';
        let visible = 0;

        quizCards.forEach(card => {
            const matchType = activeType === 'all' || card.dataset.type === activeType;
            const matchAircraft = !aircraft || card.dataset.aircraft === aircraft;
            const matchSubject  = !subject  || card.dataset.subject  === subject;
            const matchSystem   = !system   || card.dataset.system   === system;
            const matchSearch   = !term || (card.dataset.search || '').includes(term);

            const show = matchType && matchAircraft && matchSubject && matchSystem && matchSearch;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Empty state when nothing matches the active filters.
        let emptyMsg = document.getElementById('quizFilterEmpty');
        if (visible === 0 && quizCards.length > 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.id = 'quizFilterEmpty';
                emptyMsg.className = 'empty-state';
                emptyMsg.style.gridColumn = '1 / -1';
                emptyMsg.innerHTML = '<i data-lucide="search-x"></i><p>No quizzes match these filters.</p>';
                grid?.appendChild(emptyMsg);
            }
            emptyMsg.style.display = '';
        } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            activeType = this.dataset.filter;
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });

    [searchInput, aircraftSel, subjectSel, systemSel].forEach(el => {
        if (!el) return;
        el.addEventListener('input', applyFilters);
        el.addEventListener('change', applyFilters);
    });
});
</script>
