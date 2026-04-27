<div class="search-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Search</h1>
        <p class="subtitle">Find systems, lessons, and flashcards</p>
    </div>

    <!-- Search Form -->
    <section class="card search-section">
        <div class="card-body">
            <form method="GET" action="/search" class="search-form">
                <div class="search-input-wrapper">
                    <i data-lucide="search"></i>
                    <input
                        type="text"
                        name="q"
                        class="search-input"
                        placeholder="Search systems, topics, flashcards..."
                        value="<?php echo htmlspecialchars($query ?? ''); ?>"
                        autofocus
                    >
                    <?php if (!empty($query)): ?>
                        <a href="/search" class="search-clear">
                            <i data-lucide="x"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <!-- Results Section -->
    <?php if (!empty($query)): ?>
        <?php if (!empty($results)): ?>
            <section class="card">
                <div class="card-body">
                    <div class="results-header">
                        <p class="result-count">
                            Found <?php echo htmlspecialchars(count($results)); ?> result<?php echo count($results) !== 1 ? 's' : ''; ?> for "<?php echo htmlspecialchars($query); ?>"
                        </p>
                    </div>

                    <!-- Group results by type -->
                    <div class="results-list">
                        <?php
                        $resultsByType = [];
                        foreach ($results as $result) {
                            $type = $result['type'] ?? 'other';
                            if (!isset($resultsByType[$type])) {
                                $resultsByType[$type] = [];
                            }
                            $resultsByType[$type][] = $result;
                        }

                        foreach ($resultsByType as $type => $typeResults):
                        ?>
                            <div class="results-group">
                                <h3 class="results-group-title">
                                    <?php
                                    $typeLabels = [
                                        'system' => 'Systems',
                                        'lesson' => 'Lessons',
                                        'flashcard' => 'Flashcards',
                                        'quiz' => 'Quizzes'
                                    ];
                                    echo htmlspecialchars($typeLabels[$type] ?? ucfirst($type) . 's');
                                    ?>
                                </h3>

                                <div class="results-items">
                                    <?php foreach ($typeResults as $result): ?>
                                        <a href="<?php echo htmlspecialchars($result['url']); ?>" class="result-item">
                                            <div class="result-header">
                                                <span class="result-type-badge badge">
                                                    <?php echo htmlspecialchars(ucfirst($result['type'])); ?>
                                                </span>
                                                <h4 class="result-title"><?php echo htmlspecialchars($result['title']); ?></h4>
                                            </div>
                                            <?php if (!empty($result['system_name'])): ?>
                                                <p class="result-system">
                                                    <i data-lucide="layers"></i>
                                                    <?php echo htmlspecialchars($result['system_name']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($result['excerpt'])): ?>
                                                <p class="result-excerpt">
                                                    <?php echo htmlspecialchars(substr($result['excerpt'], 0, 200)); ?>
                                                    <?php if (strlen($result['excerpt']) > 200): ?>...<?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
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
                        <i data-lucide="inbox"></i>
                        <p>No results found for "<?php echo htmlspecialchars($query); ?>"</p>
                        <p class="text-muted">Try different keywords or browse our systems library</p>
                        <a href="/systems" class="btn btn-primary">Browse All Systems</a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <!-- No query - show suggestions -->
        <section class="card">
            <div class="card-body">
                <div class="search-suggestions">
                    <h3>Quick Links</h3>
                    <div class="suggestion-grid">
                        <a href="/systems" class="suggestion-card">
                            <i data-lucide="layers"></i>
                            <span>All Systems</span>
                        </a>
                        <a href="/flashcards" class="suggestion-card">
                            <i data-lucide="credit-card"></i>
                            <span>Flashcards</span>
                        </a>
                        <a href="/quiz" class="suggestion-card">
                            <i data-lucide="check-square"></i>
                            <span>Quiz & Tests</span>
                        </a>
                        <a href="/progress" class="suggestion-card">
                            <i data-lucide="bar-chart-2"></i>
                            <span>My Progress</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<style>
.search-container {
    padding: 20px;
    max-width: 900px;
    margin: 0 auto;
}

.search-section {
    margin-bottom: 30px;
}

.search-form {
    width: 100%;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
}

.search-input-wrapper i {
    position: absolute;
    left: 16px;
    width: 20px;
    height: 20px;
    color: var(--color-gray-text);
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 14px 16px 14px 48px;
    background: var(--color-dark-bg);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: var(--color-white-text);
    font-size: 16px;
    transition: border-color 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: var(--color-blue-accent);
}

.search-input::placeholder {
    color: var(--color-muted-text);
}

.search-clear {
    position: absolute;
    right: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    color: var(--color-gray-text);
    cursor: pointer;
    transition: color 0.2s;
    text-decoration: none;
}

.search-clear:hover {
    color: var(--color-white-text);
}

.search-clear i {
    width: 18px;
    height: 18px;
}

.results-header {
    margin-bottom: 24px;
}

.result-count {
    margin: 0;
    color: var(--color-gray-text);
    font-size: 14px;
}

.results-list {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.results-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.results-group-title {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--color-white-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.results-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.result-item {
    display: block;
    padding: 16px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
}

.result-item:hover {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.05);
    transform: translateX(4px);
}

.result-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.result-type-badge {
    flex-shrink: 0;
    padding: 4px 10px;
    background: var(--color-blue-accent);
    color: white;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.result-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--color-white-text);
}

.result-system {
    margin: 6px 0;
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--color-gray-text);
    font-size: 12px;
}

.result-system i {
    width: 14px;
    height: 14px;
}

.result-excerpt {
    margin: 8px 0 0 0;
    color: var(--color-gray-text);
    font-size: 13px;
    line-height: 1.5;
}

.search-suggestions {
    padding: 20px 0;
}

.search-suggestions h3 {
    margin: 0 0 20px 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--color-white-text);
}

.suggestion-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
}

.suggestion-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 20px;
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: var(--color-white-text);
    text-decoration: none;
    transition: all 0.2s;
}

.suggestion-card:hover {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.1);
    transform: translateY(-2px);
}

.suggestion-card i {
    width: 32px;
    height: 32px;
    opacity: 0.8;
}

.suggestion-card span {
    font-weight: 500;
    text-align: center;
    font-size: 14px;
}

.text-muted {
    color: var(--color-muted-text);
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
</style>
