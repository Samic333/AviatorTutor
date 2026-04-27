/**
 * Flashcard Study Module
 *
 * Handles flashcard functionality including flipping, reviewing, and session tracking
 */

class FlashcardSession {
    constructor(containerId, systemId, csrfToken) {
        this.containerId = containerId;
        this.systemId = systemId;
        this.csrfToken = csrfToken;
        this.currentCardIndex = 0;
        this.cards = [];
        this.sessionStartTime = Date.now();
        this.reviewedCards = [];
        this.sessionId = null;

        this.init();
    }

    /**
     * Initialize flashcard session
     */
    init() {
        this.cards = this.loadCards();
        if (this.cards.length === 0) {
            this.showMessage('No flashcards available for this system.');
            return;
        }

        this.setupEventListeners();
        this.renderCard();
    }

    /**
     * Load flashcards from DOM
     */
    loadCards() {
        const container = document.getElementById(this.containerId);
        if (!container) return [];

        const cardElements = container.querySelectorAll('[data-flashcard]');
        const cards = [];

        cardElements.forEach((el, index) => {
            cards.push({
                id: parseInt(el.dataset.flashcard),
                front: el.dataset.front || el.querySelector('.card-front')?.textContent || '',
                back: el.dataset.back || el.querySelector('.card-back')?.textContent || '',
                element: el,
                index: index,
                reviewed: false,
                rating: null,
            });
        });

        return cards;
    }

    /**
     * Setup event listeners for card controls
     */
    setupEventListeners() {
        document.addEventListener('click', (e) => {
            const card = e.target.closest('[data-flashcard]');
            if (card) {
                this.flipCard(card);
            }
        });

        const prevBtn = document.querySelector('[data-action="prev-card"]');
        const nextBtn = document.querySelector('[data-action="next-card"]');

        if (prevBtn) prevBtn.addEventListener('click', () => this.previousCard());
        if (nextBtn) nextBtn.addEventListener('click', () => this.nextCard());

        // Rating buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('rating-btn')) {
                const rating = parseInt(e.target.dataset.rating);
                this.rateCard(rating);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                const card = document.querySelector('[data-flashcard].active');
                if (card) this.flipCard(card);
            }
            if (e.code === 'ArrowLeft') this.previousCard();
            if (e.code === 'ArrowRight') this.nextCard();
        });
    }

    /**
     * Render current card
     */
    renderCard() {
        if (this.cards.length === 0) return;

        const card = this.cards[this.currentCardIndex];
        const container = document.getElementById(this.containerId);

        if (!container) return;

        // Reset all cards as inactive
        container.querySelectorAll('[data-flashcard]').forEach(c => {
            c.classList.remove('active');
        });

        // Activate current card
        if (card.element) {
            card.element.classList.add('active');
        }

        this.updateProgress();
    }

    /**
     * Flip a flashcard
     */
    flipCard(cardElement) {
        cardElement.classList.toggle('flipped');

        // Add animation class
        cardElement.classList.add('flip-animation');
        setTimeout(() => {
            cardElement.classList.remove('flip-animation');
        }, 500);
    }

    /**
     * Go to previous card
     */
    previousCard() {
        if (this.currentCardIndex > 0) {
            this.currentCardIndex--;
            this.renderCard();
        }
    }

    /**
     * Go to next card
     */
    nextCard() {
        if (this.currentCardIndex < this.cards.length - 1) {
            this.currentCardIndex++;
            this.renderCard();
        }
    }

    /**
     * Rate current card and submit review
     */
    rateCard(rating) {
        const card = this.cards[this.currentCardIndex];

        if (!card) return;

        card.rating = rating;
        card.reviewed = true;
        this.reviewedCards.push({
            id: card.id,
            rating: rating,
        });

        // Submit review to server
        this.submitReview(card.id, rating);

        // Move to next card
        if (this.currentCardIndex < this.cards.length - 1) {
            setTimeout(() => this.nextCard(), 300);
        } else {
            this.showSessionComplete();
        }
    }

    /**
     * Submit flashcard review via AJAX
     */
    submitReview(flashcardId, rating) {
        const data = new FormData();
        data.append('flashcard_id', flashcardId);
        data.append('rating', rating);
        data.append('csrf_token', this.csrfToken);

        fetch('/api/flashcard/review', {
            method: 'POST',
            body: data,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Flashcard review recorded:', data.data);
                }
            })
            .catch(error => console.error('Error submitting review:', error));
    }

    /**
     * Update progress display
     */
    updateProgress() {
        const total = this.cards.length;
        const current = this.currentCardIndex + 1;
        const reviewedCount = this.reviewedCards.length;

        const progressEl = document.querySelector('[data-progress="status"]');
        if (progressEl) {
            progressEl.textContent = `Card ${current} of ${total} (${reviewedCount} reviewed)`;
        }

        const progressBar = document.querySelector('[data-progress="bar"]');
        if (progressBar) {
            const percentage = (current / total) * 100;
            progressBar.style.width = percentage + '%';
        }
    }

    /**
     * Show session complete message
     */
    showSessionComplete() {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Session Complete</h2>
                <p>You reviewed <strong>${this.reviewedCards.length}</strong> flashcards.</p>
                <div class="stats">
                    <p><strong>Time spent:</strong> ${this.getElapsedTime()}</p>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="location.reload()">Continue Studying</button>
                    <a href="/flashcards/${this.systemId}" class="btn btn-secondary">Back to System</a>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
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
        const container = document.getElementById(this.containerId);
        if (container) {
            container.innerHTML = `<div class="message">${message}</div>`;
        }
    }
}

/**
 * Initialize flashcard session on DOM ready
 */
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('[data-flashcard-session]');
    if (container) {
        const systemId = container.dataset.systemId;
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';

        window.flashcardSession = new FlashcardSession(
            container.id,
            systemId,
            csrfToken
        );
    }
});
