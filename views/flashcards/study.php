<div class="flashcard-study-container">
    <!-- System Header -->
    <div class="study-header">
        <a href="/flashcards" class="btn-back">
            <i data-lucide="chevron-left"></i>
            Back to Flashcards
        </a>
        <h1><?php echo htmlspecialchars($system['name']); ?></h1>
        <div class="study-meta">
            <span class="meta-badge"><?php echo htmlspecialchars($system['ata_code'] ?? ''); ?></span>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="progress-section">
        <div class="progress-info">
            <span class="progress-counter"><strong>Card <span id="currentCard">1</span> of <span id="totalCards"><?php echo count($flashcards); ?></span></strong></span>
        </div>
        <div class="progress-bar-large">
            <div class="progress-fill-large" id="progressFill"></div>
        </div>
    </div>

    <!-- Flashcard -->
    <div class="flashcard-wrapper" id="flashcardWrapper">
        <div class="flashcard" id="flashcard">
            <div class="flashcard-inner" id="flashcardInner">
                <!-- Front -->
                <div class="flashcard-front">
                    <div class="flashcard-label">Question</div>
                    <div class="flashcard-content" id="cardFront"></div>
                </div>
                <!-- Back -->
                <div class="flashcard-back">
                    <div class="flashcard-label">Answer</div>
                    <div class="flashcard-content" id="cardBack"></div>
                </div>
            </div>
        </div>
        <div class="flip-hint">
            <i data-lucide="rotate-ccw"></i>
            Click to flip
        </div>
    </div>

    <!-- Confidence Buttons (shown after flip) -->
    <div class="confidence-section" id="confidenceSection" style="display: none;">
        <p class="confidence-prompt">How confident are you with this card?</p>
        <div class="confidence-buttons">
            <button class="btn btn-danger btn-lg" data-rating="1" title="Mark as forgotten">
                <i data-lucide="x-circle"></i>
                Missed
            </button>
            <button class="btn btn-secondary btn-lg" data-rating="3" title="Mark as partially known">
                <i data-lucide="alert-triangle"></i>
                Almost
            </button>
            <button class="btn btn-success btn-lg" data-rating="5" title="Mark as mastered">
                <i data-lucide="check-circle"></i>
                Got it
            </button>
        </div>
    </div>

    <!-- Session Complete -->
    <div class="session-complete" id="sessionComplete" style="display: none;">
        <div class="complete-icon">
            <i data-lucide="award"></i>
        </div>
        <h2>Session Complete!</h2>
        <p class="complete-message">Great job reviewing these flashcards.</p>
        <div class="complete-stats">
            <div class="stat-item">
                <span class="stat-label">Cards Reviewed</span>
                <span class="stat-num" id="reviewedCount">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Average Confidence</span>
                <span class="stat-num" id="avgConfidence">0%</span>
            </div>
        </div>
        <div class="complete-actions">
            <a href="/flashcards" class="btn btn-secondary">Back to Flashcards</a>
            <button id="reviewAgainBtn" class="btn btn-primary">Review Again</button>
        </div>
    </div>
</div>

<style>
.flashcard-study-container {
    padding: 40px 20px;
    max-width: 900px;
    margin: 0 auto;
}

.study-header {
    margin-bottom: 40px;
    text-align: center;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--color-blue-accent);
    text-decoration: none;
    margin-bottom: 20px;
    font-size: 14px;
    transition: opacity 0.2s;
}

.btn-back:hover {
    opacity: 0.8;
}

.btn-back i {
    width: 18px;
    height: 18px;
}

.study-header h1 {
    margin: 0;
    font-size: 32px;
    color: var(--color-white-text);
}

.study-meta {
    margin-top: 12px;
}

.meta-badge {
    display: inline-block;
    background: var(--color-blue-accent);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.progress-section {
    margin-bottom: 40px;
}

.progress-info {
    margin-bottom: 12px;
    text-align: center;
    color: var(--color-gray-text);
    font-size: 14px;
}

.progress-counter {
    font-weight: 600;
}

.progress-bar-large {
    height: 8px;
    background: var(--color-slate-bg);
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill-large {
    height: 100%;
    background: var(--color-blue-accent);
    transition: width 0.3s ease;
    width: 0%;
}

.flashcard-wrapper {
    perspective: 1000px;
    margin-bottom: 40px;
    cursor: pointer;
}

.flashcard {
    position: relative;
    width: 100%;
    height: 400px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    transform-style: preserve-3d;
    transition: transform 0.6s;
}

.flashcard.flipped {
    transform: rotateY(180deg);
}

.flashcard-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
}

.flashcard-front,
.flashcard-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 40px;
    text-align: center;
    border-radius: 12px;
}

.flashcard-front {
    background: linear-gradient(135deg, var(--color-slate-bg) 0%, var(--color-slate-bg) 100%);
    border: 2px solid var(--color-blue-accent);
    color: var(--color-white-text);
}

.flashcard-back {
    background: linear-gradient(135deg, var(--color-blue-accent) 0%, #1E40AF 100%);
    color: white;
    transform: rotateY(180deg);
    border: 2px solid var(--color-blue-accent);
}

.flashcard-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    opacity: 0.8;
}

.flashcard-content {
    font-size: 24px;
    line-height: 1.6;
    font-weight: 500;
    max-height: 280px;
    overflow-y: auto;
}

.flip-hint {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: var(--color-gray-text);
    font-size: 14px;
    transition: opacity 0.3s;
    padding: 12px;
}

.flashcard-wrapper:hover .flip-hint {
    opacity: 0.6;
}

.flip-hint i {
    width: 16px;
    height: 16px;
}

.confidence-section {
    text-align: center;
    animation: slideUp 0.4s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.confidence-prompt {
    color: var(--color-gray-text);
    margin-bottom: 20px;
    font-size: 16px;
}

.confidence-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

.confidence-buttons .btn {
    min-width: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.session-complete {
    text-align: center;
    animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.complete-icon {
    font-size: 80px;
    color: var(--color-success);
    margin-bottom: 20px;
}

.complete-icon i {
    width: 80px;
    height: 80px;
}

.session-complete h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    color: var(--color-white-text);
}

.complete-message {
    color: var(--color-gray-text);
    margin-bottom: 30px;
}

.complete-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-bottom: 40px;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-label {
    color: var(--color-gray-text);
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.stat-num {
    font-size: 32px;
    font-weight: 700;
    color: var(--color-success);
}

.complete-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.complete-actions .btn {
    min-width: 160px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const flashcards = <?php echo json_encode($flashcards ?? []); ?>;
    const csrfToken = '<?php echo htmlspecialchars($csrf_token ?? ''); ?>';

    let currentIndex = 0;
    let ratings = {};
    let isFlipped = false;
    let sessionActive = true;

    const flashcardEl = document.getElementById('flashcard');
    const flashcardInner = document.getElementById('flashcardInner');
    const cardFront = document.getElementById('cardFront');
    const cardBack = document.getElementById('cardBack');
    const confidenceSection = document.getElementById('confidenceSection');
    const sessionComplete = document.getElementById('sessionComplete');
    const currentCardSpan = document.getElementById('currentCard');
    const totalCardsSpan = document.getElementById('totalCards');
    const progressFill = document.getElementById('progressFill');
    const flashcardWrapper = document.getElementById('flashcardWrapper');
    const flipHint = flashcardWrapper.querySelector('.flip-hint');

    function loadCard(index) {
        if (index >= flashcards.length) {
            endSession();
            return;
        }

        const card = flashcards[index];
        cardFront.textContent = card.front;
        cardBack.textContent = card.back;

        isFlipped = false;
        flashcardEl.classList.remove('flipped');
        confidenceSection.style.display = 'none';

        currentCardSpan.textContent = index + 1;
        updateProgress();
    }

    function updateProgress() {
        const percentage = ((currentIndex + 1) / flashcards.length) * 100;
        progressFill.style.width = percentage + '%';
    }

    function toggleFlip() {
        isFlipped = !isFlipped;
        flashcardEl.classList.toggle('flipped');

        if (isFlipped) {
            confidenceSection.style.display = 'block';
            flipHint.style.opacity = '0.3';
        } else {
            confidenceSection.style.display = 'none';
            flipHint.style.opacity = '1';
        }
    }

    function rateCard(rating) {
        if (!sessionActive) return;

        const card = flashcards[currentIndex];
        ratings[card.id] = rating;

        submitReview(card.id, rating);
    }

    function submitReview(cardId, rating) {
        const formData = new FormData();
        formData.append('card_id', cardId);
        formData.append('rating', rating);
        formData.append('csrf_token', csrfToken);

        fetch('/flashcards/review', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentIndex++;
                loadCard(currentIndex);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function endSession() {
        sessionActive = false;
        flashcardWrapper.style.display = 'none';
        confidenceSection.style.display = 'none';
        sessionComplete.style.display = 'block';

        const totalRated = Object.keys(ratings).length;
        const avgRating = totalRated > 0
            ? Math.round((Object.values(ratings).reduce((a, b) => a + b) / totalRated) * 100 / 5)
            : 0;

        document.getElementById('reviewedCount').textContent = totalRated;
        document.getElementById('avgConfidence').textContent = avgRating + '%';
    }

    // Event listeners
    flashcardWrapper.addEventListener('click', function(e) {
        if (!sessionActive || e.target.closest('button')) return;
        toggleFlip();
    });

    document.querySelectorAll('[data-rating]').forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            rateCard(rating);
        });
    });

    document.getElementById('reviewAgainBtn').addEventListener('click', function() {
        location.reload();
    });

    // Load first card
    loadCard(0);
});
</script>
