/**
 * Quiz Module
 *
 * Handles quiz functionality including timer, question navigation, answer tracking, and submission
 */

class QuizSession {
    constructor(quizId, csrfToken, timeLimitMins = null) {
        this.quizId = quizId;
        this.csrfToken = csrfToken;
        this.timeLimitMins = timeLimitMins;
        this.timeLimitSecs = timeLimitMins ? timeLimitMins * 60 : null;
        this.timeRemainingSecs = this.timeLimitSecs;
        this.sessionStartTime = Date.now();
        this.currentQuestionIndex = 0;
        this.questions = [];
        this.answers = {};
        this.timerInterval = null;
        this.answered = false;

        this.init();
    }

    /**
     * Initialize quiz session
     */
    init() {
        this.questions = this.loadQuestions();
        if (this.questions.length === 0) {
            this.showMessage('No questions available for this quiz.');
            return;
        }

        // Initialize answers object
        this.questions.forEach((q, index) => {
            this.answers[index] = null;
        });

        this.setupEventListeners();
        this.renderQuestion();

        if (this.timeLimitSecs) {
            this.startTimer();
        }
    }

    /**
     * Load questions from DOM
     */
    loadQuestions() {
        const questionsContainer = document.querySelector('[data-quiz-questions]');
        if (!questionsContainer) return [];

        const questionElements = questionsContainer.querySelectorAll('[data-question]');
        const questions = [];

        questionElements.forEach((el, index) => {
            const options = [];
            el.querySelectorAll('[data-option]').forEach(optionEl => {
                options.push({
                    id: optionEl.dataset.option,
                    text: optionEl.textContent,
                    element: optionEl,
                });
            });

            questions.push({
                id: parseInt(el.dataset.question),
                index: index,
                text: el.dataset.questionText || el.querySelector('.question-text')?.textContent || '',
                type: el.dataset.type || 'mcq',
                options: options,
                explanation: el.dataset.explanation || '',
                element: el,
                answered: false,
            });
        });

        return questions;
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Answer selection
        document.addEventListener('change', (e) => {
            if (e.target.name === 'answer') {
                this.selectAnswer(e.target.value);
            }
        });

        // Navigation buttons
        const prevBtn = document.querySelector('[data-action="prev-question"]');
        const nextBtn = document.querySelector('[data-action="next-question"]');
        const submitBtn = document.querySelector('[data-action="submit-quiz"]');

        if (prevBtn) prevBtn.addEventListener('click', () => this.previousQuestion());
        if (nextBtn) nextBtn.addEventListener('click', () => this.nextQuestion());
        if (submitBtn) submitBtn.addEventListener('click', () => this.submitQuiz());

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.code === 'ArrowLeft') this.previousQuestion();
            if (e.code === 'ArrowRight') this.nextQuestion();
        });
    }

    /**
     * Render current question
     */
    renderQuestion() {
        if (this.questions.length === 0) return;

        const question = this.questions[this.currentQuestionIndex];

        // Update active state
        document.querySelectorAll('[data-question]').forEach(el => {
            el.classList.remove('active');
        });
        if (question.element) {
            question.element.classList.add('active');
        }

        // Restore previous answer if exists
        this.restorePreviousAnswer();

        // Update progress
        this.updateProgress();
        this.updateNavigationButtons();
    }

    /**
     * Select answer for current question
     */
    selectAnswer(optionId) {
        this.answers[this.currentQuestionIndex] = optionId;
        this.questions[this.currentQuestionIndex].answered = true;

        // Visual feedback
        const answerRadios = document.querySelectorAll(`input[name="answer"][value="${optionId}"]`);
        answerRadios.forEach(radio => {
            radio.checked = true;
            radio.parentElement?.classList.add('selected');
        });

        this.updateProgress();
    }

    /**
     * Restore previous answer if exists
     */
    restorePreviousAnswer() {
        const answer = this.answers[this.currentQuestionIndex];
        if (answer) {
            const radio = document.querySelector(`input[name="answer"][value="${answer}"]`);
            if (radio) {
                radio.checked = true;
                radio.parentElement?.classList.add('selected');
            }
        }

        // Clear previous selections
        document.querySelectorAll(`input[name="answer"]`).forEach(radio => {
            if (radio.value !== answer) {
                radio.parentElement?.classList.remove('selected');
            }
        });
    }

    /**
     * Go to previous question
     */
    previousQuestion() {
        if (this.currentQuestionIndex > 0) {
            this.currentQuestionIndex--;
            this.renderQuestion();
        }
    }

    /**
     * Go to next question
     */
    nextQuestion() {
        if (this.currentQuestionIndex < this.questions.length - 1) {
            this.currentQuestionIndex++;
            this.renderQuestion();
        }
    }

    /**
     * Update navigation button states
     */
    updateNavigationButtons() {
        const prevBtn = document.querySelector('[data-action="prev-question"]');
        const nextBtn = document.querySelector('[data-action="next-question"]');

        if (prevBtn) {
            prevBtn.disabled = this.currentQuestionIndex === 0;
        }

        if (nextBtn) {
            nextBtn.disabled = this.currentQuestionIndex === this.questions.length - 1;
        }
    }

    /**
     * Update progress display
     */
    updateProgress() {
        const total = this.questions.length;
        const current = this.currentQuestionIndex + 1;
        const answered = Object.values(this.answers).filter(a => a !== null).length;

        // Question counter
        const counterEl = document.querySelector('[data-progress="counter"]');
        if (counterEl) {
            counterEl.textContent = `Question ${current} of ${total}`;
        }

        // Progress bar
        const progressBar = document.querySelector('[data-progress="bar"]');
        if (progressBar) {
            const percentage = (current / total) * 100;
            progressBar.style.width = percentage + '%';
        }

        // Answered counter
        const answeredEl = document.querySelector('[data-progress="answered"]');
        if (answeredEl) {
            answeredEl.textContent = `${answered}/${total} answered`;
        }

        // Update question markers
        this.updateQuestionMarkers();
    }

    /**
     * Update question marker colors based on answered status
     */
    updateQuestionMarkers() {
        const markers = document.querySelectorAll('[data-question-marker]');
        markers.forEach((marker, index) => {
            if (this.answers[index] !== null) {
                marker.classList.add('answered');
                marker.classList.remove('unanswered');
            } else {
                marker.classList.remove('answered');
                marker.classList.add('unanswered');
            }

            if (index === this.currentQuestionIndex) {
                marker.classList.add('active');
            } else {
                marker.classList.remove('active');
            }
        });
    }

    /**
     * Start countdown timer
     */
    startTimer() {
        this.timerInterval = setInterval(() => {
            this.timeRemainingSecs--;

            this.updateTimerDisplay();

            // Warning at 2 minutes
            if (this.timeRemainingSecs === 120) {
                this.showWarning('2 minutes remaining!');
            }

            // Auto-submit at time limit
            if (this.timeRemainingSecs <= 0) {
                this.clearTimer();
                this.showWarning('Time limit reached! Submitting quiz...');
                setTimeout(() => this.submitQuiz(), 2000);
            }
        }, 1000);
    }

    /**
     * Update timer display
     */
    updateTimerDisplay() {
        const timerEl = document.querySelector('[data-progress="timer"]');
        if (!timerEl) return;

        const minutes = Math.floor(this.timeRemainingSecs / 60);
        const seconds = this.timeRemainingSecs % 60;
        const timeStr = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        timerEl.textContent = timeStr;

        // Change color when time is low
        if (this.timeRemainingSecs <= 300) { // 5 minutes
            timerEl.classList.add('time-warning');
        }
        if (this.timeRemainingSecs <= 60) { // 1 minute
            timerEl.classList.add('time-critical');
        }
    }

    /**
     * Clear timer
     */
    clearTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    /**
     * Show warning message
     */
    showWarning(message) {
        const warningEl = document.createElement('div');
        warningEl.className = 'quiz-warning';
        warningEl.textContent = message;
        document.body.appendChild(warningEl);

        setTimeout(() => {
            warningEl.classList.add('fade-out');
            setTimeout(() => warningEl.remove(), 300);
        }, 3000);
    }

    /**
     * Submit quiz
     */
    submitQuiz() {
        // Check if all questions answered
        const unanswered = Object.values(this.answers).filter(a => a === null).length;
        if (unanswered > 0) {
            const confirm = window.confirm(`You have ${unanswered} unanswered question(s). Submit anyway?`);
            if (!confirm) return;
        }

        this.clearTimer();

        // Prepare submission data
        const formData = new FormData();
        formData.append('csrf_token', this.csrfToken);

        // Add each answer
        this.questions.forEach((question, index) => {
            formData.append(`answers[${question.id}]`, this.answers[index] || '');
        });

        // Submit to server
        const submitUrl = `/quiz/${this.quizId}/submit`;

        fetch(submitUrl, {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showResults(data.data);
                } else {
                    alert('Error submitting quiz: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error submitting quiz:', error);
                alert('Error submitting quiz. Please try again.');
            });
    }

    /**
     * Show quiz results
     */
    showResults(results) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';

        const scorePercentage = results.score || 0;
        const passed = scorePercentage >= 70;
        const statusClass = passed ? 'passed' : 'failed';

        modal.innerHTML = `
            <div class="modal-content quiz-results">
                <h2>Quiz Complete</h2>
                <div class="score-display ${statusClass}">
                    <div class="score-number">${scorePercentage}%</div>
                    <div class="score-label">${passed ? 'PASSED' : 'FAILED'}</div>
                </div>
                <div class="results-details">
                    <p><strong>Questions:</strong> ${results.total_questions || this.questions.length}</p>
                    <p><strong>Correct:</strong> ${results.correct_answers || 0}</p>
                    <p><strong>Time taken:</strong> ${this.getElapsedTime()}</p>
                </div>
                <div class="modal-actions">
                    <a href="/quiz" class="btn btn-primary">Back to Quizzes</a>
                    <button class="btn btn-secondary" onclick="location.reload()">Review Answers</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });

        // Disable further interaction
        document.querySelectorAll('input, button, select, textarea').forEach(el => {
            if (!el.closest('.modal-overlay')) {
                el.disabled = true;
            }
        });
    }

    /**
     * Get elapsed time in human readable format
     */
    getElapsedTime() {
        const elapsed = Math.round((Date.now() - this.sessionStartTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;

        if (minutes === 0) return `${seconds} seconds`;
        return `${minutes}m ${seconds}s`;
    }

    /**
     * Show message
     */
    showMessage(message) {
        const container = document.querySelector('[data-quiz-container]');
        if (container) {
            container.innerHTML = `<div class="message">${message}</div>`;
        }
    }
}

/**
 * Initialize quiz session on DOM ready
 */
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('[data-quiz-session]');
    if (container) {
        const quizId = container.dataset.quizId;
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
        const timeLimit = container.dataset.timeLimit ? parseInt(container.dataset.timeLimit) : null;

        window.quizSession = new QuizSession(
            quizId,
            csrfToken,
            timeLimit
        );
    }
});
