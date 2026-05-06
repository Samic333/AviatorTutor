<div class="quiz-take-container">
    <!-- Quiz Header -->
    <div class="quiz-header">
        <div class="quiz-header-left">
            <a href="/quiz" class="btn-back">
                <i data-lucide="chevron-left"></i>
            </a>
            <div>
                <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
            </div>
        </div>
        <div class="quiz-timer-display">
            <i data-lucide="clock"></i>
            <span id="timerDisplay">00:00</span>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="quiz-progress">
        <div class="progress-bar-large">
            <div class="progress-fill-large" id="progressFill"></div>
        </div>
        <div class="progress-info">
            <span id="questionCounter">Question 1 of <?php echo count($questions); ?></span>
        </div>
    </div>

    <!-- Questions Container -->
    <form method="POST" action="/quiz/<?php echo htmlspecialchars($quiz['id']); ?>/submit" id="quizForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="attempt_id" value="<?php echo htmlspecialchars($attempt_id); ?>">
        <div id="answersJSON" name="answers_json" style="display: none;"></div>

        <div class="quiz-content">
            <!-- Question Display Area -->
            <div id="questionContainer" class="question-container">
                <!-- Populated by JavaScript -->
            </div>

            <!-- Navigation Buttons -->
            <div class="quiz-navigation">
                <button type="button" id="prevBtn" class="btn btn-secondary" style="display: none;">
                    <i data-lucide="chevron-left"></i> Previous
                </button>
                <div class="nav-spacer"></div>
                <button type="button" id="nextBtn" class="btn btn-primary">
                    Next <i data-lucide="chevron-right"></i>
                </button>
                <button type="submit" id="finishBtn" class="btn btn-success" style="display: none;">
                    <i data-lucide="check"></i> Finish Quiz
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.quiz-take-container {
    padding: 0;
    background: var(--color-dark-bg);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.quiz-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 40px;
    background: var(--color-slate-bg);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.quiz-header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: var(--color-white-text);
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-back:hover {
    background: rgba(59, 130, 246, 0.1);
    border-color: var(--color-blue-accent);
}

.btn-back i {
    width: 20px;
    height: 20px;
}

.quiz-header h1 {
    margin: 0;
    font-size: 24px;
    color: var(--color-white-text);
}

.quiz-timer-display {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 20px;
    font-weight: 600;
    color: var(--color-amber-warning);
}

.quiz-timer-display i {
    width: 24px;
    height: 24px;
}

.quiz-timer-display.warning {
    color: var(--color-danger);
}

.quiz-progress {
    padding: 20px 40px;
    background: var(--color-dark-bg);
}

.progress-bar-large {
    height: 6px;
    background: var(--color-slate-bg);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 12px;
}

.progress-fill-large {
    height: 100%;
    background: var(--color-blue-accent);
    transition: width 0.3s ease;
}

.progress-info {
    text-align: center;
    color: var(--color-gray-text);
    font-size: 14px;
}

.quiz-content {
    flex: 1;
    padding: 40px;
    max-width: 800px;
    margin: 0 auto;
    width: 100%;
}

.question-container {
    margin-bottom: 40px;
}

.question-text {
    font-size: 20px;
    font-weight: 600;
    color: var(--color-white-text);
    margin-bottom: 30px;
    line-height: 1.6;
}

.question-type-label {
    display: inline-block;
    padding: 4px 10px;
    background: var(--color-blue-accent);
    color: white;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 16px;
}

.options-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.option-item {
    display: flex;
    align-items: center;
    padding: 16px;
    background: var(--color-slate-bg);
    border: 2px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.option-item:hover {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.1);
}

.option-item input[type="radio"],
.option-item input[type="checkbox"] {
    margin-right: 12px;
    cursor: pointer;
}

.option-item.selected {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.15);
}

.option-label {
    flex: 1;
    color: var(--color-white-text);
    cursor: pointer;
    user-select: none;
}

.true-false-buttons {
    display: flex;
    gap: 16px;
}

.true-false-btn {
    flex: 1;
    padding: 16px;
    background: var(--color-slate-bg);
    border: 2px solid transparent;
    border-radius: 8px;
    color: var(--color-white-text);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 16px;
}

.true-false-btn:hover {
    border-color: var(--color-blue-accent);
}

.true-false-btn.selected {
    background: var(--color-blue-accent);
    border-color: var(--color-blue-accent);
}

.quiz-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding-top: 40px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-spacer {
    flex: 1;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = <?php echo json_encode($questions ?? []); ?>;
    const timeLimitMins = <?php echo intval($quiz['time_limit_mins'] ?? 30); ?>;

    let currentQuestion = 0;
    let answers = {};
    let timeRemaining = timeLimitMins * 60;

    const questionContainer = document.getElementById('questionContainer');
    const questionCounter = document.getElementById('questionCounter');
    const progressFill = document.getElementById('progressFill');
    const timerDisplay = document.getElementById('timerDisplay');
    const timerElement = document.querySelector('.quiz-timer-display');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const finishBtn = document.getElementById('finishBtn');
    const quizForm = document.getElementById('quizForm');

    // Start timer
    const timerInterval = setInterval(function() {
        timeRemaining--;

        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        timerDisplay.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        if (timeRemaining < 120) {
            timerElement.classList.add('warning');
        }

        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            submitQuiz();
        }
    }, 1000);

    function renderQuestion(index) {
        const question = questions[index];
        const isLastQuestion = index === questions.length - 1;

        let html = '<div class="question-type-label">' + escapeHtml(question.question_type) + '</div>';
        html += '<div class="question-text">' + escapeHtml(question.question_text) + '</div>';

        if (question.question_type === 'mcq') {
            const options = JSON.parse(question.options || '[]');
            html += '<div class="options-group">';
            options.forEach((option, idx) => {
                const optionValue = option.text || option;
                const isSelected = answers[question.id] === optionValue;
                html += '<label class="option-item ' + (isSelected ? 'selected' : '') + '">';
                html += '<input type="radio" name="question_' + question.id + '" value="' + escapeHtml(optionValue) + '" ' + (isSelected ? 'checked' : '') + ' data-question-id="' + question.id + '">';
                html += '<span class="option-label">' + escapeHtml(optionValue) + '</span>';
                html += '</label>';
            });
            html += '</div>';
        } else if (question.question_type === 'true_false') {
            const isTrue = answers[question.id] === 'true';
            const isFalse = answers[question.id] === 'false';
            html += '<div class="true-false-buttons">';
            html += '<button type="button" class="true-false-btn ' + (isTrue ? 'selected' : '') + '" data-answer="true" data-question-id="' + question.id + '">True</button>';
            html += '<button type="button" class="true-false-btn ' + (isFalse ? 'selected' : '') + '" data-answer="false" data-question-id="' + question.id + '">False</button>';
            html += '</div>';
        }

        questionContainer.innerHTML = html;

        // Re-attach event listeners
        document.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', function() {
                answers[this.dataset.questionId] = this.value;
                updateQuestionDisplay();
            });
        });

        document.querySelectorAll('.true-false-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const questionId = this.dataset.questionId;
                const answer = this.dataset.answer;
                answers[questionId] = answer;
                updateQuestionDisplay();
            });
        });

        // Update navigation
        currentQuestion = index;
        questionCounter.textContent = 'Question ' + (index + 1) + ' of ' + questions.length;
        progressFill.style.width = ((index + 1) / questions.length * 100) + '%';

        prevBtn.style.display = index > 0 ? 'inline-flex' : 'none';
        nextBtn.style.display = isLastQuestion ? 'none' : 'inline-flex';
        finishBtn.style.display = isLastQuestion ? 'inline-flex' : 'none';
    }

    function updateQuestionDisplay() {
        renderQuestion(currentQuestion);
    }

    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentQuestion > 0) {
            renderQuestion(currentQuestion - 1);
        }
    });

    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentQuestion < questions.length - 1) {
            renderQuestion(currentQuestion + 1);
        }
    });

    quizForm.addEventListener('submit', function(e) {
        e.preventDefault();
        submitQuiz();
    });

    function submitQuiz() {
        clearInterval(timerInterval);

        const answersArray = questions.map(q => ({
            question_id: q.id,
            answer: answers[q.id] || null
        }));

        const answersInput = document.createElement('input');
        answersInput.type = 'hidden';
        answersInput.name = 'answers_json';
        answersInput.value = JSON.stringify(answersArray);
        quizForm.appendChild(answersInput);

        quizForm.submit();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Render first question
    renderQuestion(0);
});
</script>

<?php
$sessionType     = 'quiz';
$sessionSystemId = (int) ($quiz['system_id'] ?? 0);
include __DIR__ . '/../partials/study-session-heartbeat.php';
?>
