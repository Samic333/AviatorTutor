<div class="quiz-result-container">
    <!-- Result Header -->
    <div class="result-header">
        <div class="result-score-circle">
            <div class="score-inner">
                <span class="score-number"><?php echo htmlspecialchars($attempt['score']); ?></span>
                <span class="score-label">%</span>
            </div>
        </div>
        <div class="result-status">
            <h1 class="result-title <?php echo $passed ? 'passed' : 'failed'; ?>">
                <?php echo $passed ? 'Passed!' : 'Try Again'; ?>
            </h1>
            <p class="result-message">
                <?php
                if ($passed) {
                    echo 'Excellent work! You\'ve mastered this material.';
                } else {
                    echo 'You need ' . (intval($quiz['pass_score']) - intval($attempt['score'])) . ' more points to pass.';
                }
                ?>
            </p>
        </div>
    </div>

    <!-- Stats Section -->
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Quiz Statistics</h2>
        </div>
        <div class="card-body">
            <div class="stats-row">
                <div class="stat-col">
                    <span class="stat-label">Score</span>
                    <span class="stat-value"><?php echo htmlspecialchars($attempt['score']); ?>%</span>
                </div>
                <div class="stat-col">
                    <span class="stat-label">Pass Score</span>
                    <span class="stat-value"><?php echo htmlspecialchars($quiz['pass_score']); ?>%</span>
                </div>
                <div class="stat-col">
                    <span class="stat-label">Time Taken</span>
                    <span class="stat-value">
                        <?php
                        $totalSecs = intval($attempt['time_taken_secs']);
                        $mins = floor($totalSecs / 60);
                        $secs = $totalSecs % 60;
                        echo htmlspecialchars($mins) . 'm ' . htmlspecialchars($secs) . 's';
                        ?>
                    </span>
                </div>
                <div class="stat-col">
                    <span class="stat-label">Status</span>
                    <span class="stat-value status-badge">
                        <span class="badge <?php echo $passed ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo htmlspecialchars(ucfirst($attempt['status'])); ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Answer Breakdown -->
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Answer Review</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($answers)): ?>
                <div class="answers-breakdown">
                    <?php foreach ($answers as $index => $answer): ?>
                        <div class="answer-item <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
                            <div class="answer-header">
                                <div class="answer-number">
                                    <span class="question-num">Q<?php echo $index + 1; ?></span>
                                    <span class="result-icon">
                                        <?php if ($answer['is_correct']): ?>
                                            <i data-lucide="check-circle"></i>
                                        <?php else: ?>
                                            <i data-lucide="x-circle"></i>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="answer-question">
                                    <p class="question-text"><?php echo htmlspecialchars($answer['question_text']); ?></p>
                                </div>
                            </div>
                            <div class="answer-details">
                                <?php
                                    $rawUA = $answer['user_answer'] ?? null;
                                    $rawCA = $answer['correct_answer'] ?? null;
                                    $dispUA = ($rawUA !== null) ? (json_decode($rawUA, true) ?? $rawUA) : 'Not answered';
                                    $dispCA = ($rawCA !== null) ? (json_decode($rawCA, true) ?? $rawCA) : '';
                                    if (is_array($dispUA)) $dispUA = implode(', ', $dispUA);
                                    if (is_array($dispCA)) $dispCA = implode(', ', $dispCA);
                                ?>
                                <div class="answer-row">
                                    <span class="label">Your Answer:</span>
                                    <span class="value user-answer">
                                        <?php echo htmlspecialchars((string)($dispUA ?: 'Not answered')); ?>
                                    </span>
                                </div>
                                <?php if (!$answer['is_correct']): ?>
                                    <div class="answer-row">
                                        <span class="label">Correct Answer:</span>
                                        <span class="value correct-answer">
                                            <?php echo htmlspecialchars((string)$dispCA); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($answer['explanation'])): ?>
                                    <div class="answer-explanation">
                                        <span class="explanation-label">
                                            <i data-lucide="info"></i>
                                            Explanation
                                        </span>
                                        <p><?php echo htmlspecialchars($answer['explanation']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Action Buttons -->
    <div class="result-actions">
        <a href="/quiz/<?php echo htmlspecialchars($quiz['id']); ?>" class="btn btn-primary btn-lg">
            <i data-lucide="rotate-ccw"></i>
            Retake Quiz
        </a>
        <a href="/quiz" class="btn btn-secondary btn-lg">
            <i data-lucide="chevron-left"></i>
            Back to Quizzes
        </a>
        <a href="/systems/<?php echo htmlspecialchars($quiz['system_id'] ?? ''); ?>" class="btn btn-secondary btn-lg">
            <i data-lucide="book"></i>
            Study System
        </a>
    </div>
</div>

<style>
.quiz-result-container {
    padding: 40px 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.result-header {
    display: flex;
    align-items: center;
    gap: 40px;
    margin-bottom: 40px;
    padding: 40px;
    background: var(--color-slate-bg);
    border-radius: 12px;
}

.result-score-circle {
    flex-shrink: 0;
}

.score-inner {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    border: 4px solid var(--color-blue-accent);
    position: relative;
}

.quiz-result-container .result-status:has(.result-title.passed) ~ .result-score-circle .score-inner,
.result-score-circle .score-inner {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
}

.result-status:has(.result-title.failed) ~ .result-score-circle .score-inner {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(245, 158, 11, 0.1) 100%);
    border-color: var(--color-danger);
}

.score-number {
    font-size: 72px;
    color: var(--color-white-text);
    line-height: 1;
}

.score-label {
    font-size: 24px;
    color: var(--color-gray-text);
    margin-top: 8px;
}

.result-status {
    flex: 1;
}

.result-title {
    margin: 0;
    font-size: 40px;
    color: var(--color-white-text);
}

.result-title.passed {
    color: var(--color-success);
}

.result-title.failed {
    color: var(--color-danger);
}

.result-message {
    margin-top: 12px;
    color: var(--color-gray-text);
    font-size: 16px;
    line-height: 1.6;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.stat-col {
    padding: 16px;
    background: var(--color-dark-bg);
    border-radius: 8px;
    text-align: center;
}

.stat-label {
    display: block;
    color: var(--color-gray-text);
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: var(--color-white-text);
}

.status-badge {
    display: block;
}

.answers-breakdown {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.answer-item {
    padding: 20px;
    border-left: 4px solid;
    border-radius: 8px;
    background: var(--color-dark-bg);
}

.answer-item.correct {
    border-color: var(--color-success);
    background: rgba(16, 185, 129, 0.05);
}

.answer-item.incorrect {
    border-color: var(--color-danger);
    background: rgba(239, 68, 68, 0.05);
}

.answer-header {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    align-items: flex-start;
}

.answer-number {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.question-num {
    font-weight: 700;
    color: var(--color-blue-accent);
    font-size: 14px;
}

.result-icon {
    display: flex;
    align-items: center;
}

.result-icon i {
    width: 24px;
    height: 24px;
}

.answer-item.correct .result-icon i {
    color: var(--color-success);
}

.answer-item.incorrect .result-icon i {
    color: var(--color-danger);
}

.answer-question {
    flex: 1;
}

.question-text {
    margin: 0;
    color: var(--color-white-text);
    font-weight: 500;
    line-height: 1.5;
}

.answer-details {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.answer-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.answer-row .label {
    color: var(--color-gray-text);
    font-size: 14px;
    flex-shrink: 0;
}

.answer-row .value {
    color: var(--color-white-text);
    font-weight: 500;
    flex: 1;
    text-align: right;
}

.user-answer {
    color: var(--color-amber-warning);
}

.correct-answer {
    color: var(--color-success);
}

.answer-explanation {
    padding: 12px;
    background: rgba(59, 130, 246, 0.1);
    border-radius: 6px;
    border-left: 3px solid var(--color-blue-accent);
}

.explanation-label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--color-blue-accent);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.explanation-label i {
    width: 14px;
    height: 14px;
}

.answer-explanation p {
    margin: 0;
    color: var(--color-gray-text);
    font-size: 14px;
    line-height: 1.5;
}

.result-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
    margin-top: 40px;
    flex-wrap: wrap;
}

.result-actions .btn {
    min-width: 180px;
}
</style>
