/**
 * Q400 Aircraft Systems Study App - Main JavaScript
 * Professional aviation training application
 */

// =====================================================
// THEME MANAGEMENT
// =====================================================

class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'dark';
        this.applyTheme();
    }

    applyTheme() {
        const html = document.documentElement;
        html.setAttribute('data-theme', this.theme);
        localStorage.setItem('theme', this.theme);
    }

    toggle() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        this.applyTheme();
        this.notifyThemeChange();
    }

    notifyThemeChange() {
        window.dispatchEvent(new CustomEvent('themeChange', { detail: { theme: this.theme } }));
    }
}

const themeManager = new ThemeManager();

// =====================================================
// TOAST NOTIFICATIONS
// =====================================================

class ToastManager {
    constructor() {
        this.container = null;
        this.initContainer();
    }

    initContainer() {
        this.container = document.createElement('div');
        this.container.className = 'toast-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            pointer-events: none;
        `;
        document.body.appendChild(this.container);
    }

    show(message, type = 'info', duration = 4000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background-color: ${this.getBackgroundColor(type)};
            color: #F8FAFC;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
            pointer-events: auto;
            max-width: 400px;
            word-wrap: break-word;
        `;
        toast.textContent = message;
        this.container.appendChild(toast);

        if (duration > 0) {
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        return toast;
    }

    getBackgroundColor(type) {
        const colors = {
            success: '#10B981',
            error: '#EF4444',
            warning: '#F59E0B',
            info: '#3B82F6'
        };
        return colors[type] || colors.info;
    }
}

const toastManager = new ToastManager();

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// =====================================================
// MODAL / CONFIRM DIALOG
// =====================================================

class ConfirmDialog {
    static show(message, callback, options = {}) {
        const {
            title = 'Confirm',
            okText = 'Confirm',
            cancelText = 'Cancel'
        } = options;

        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9998;
        `;

        const dialog = document.createElement('div');
        dialog.style.cssText = `
            background-color: #1E293B;
            border-radius: 12px;
            padding: 32px;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(148, 163, 184, 0.1);
        `;

        const titleEl = document.createElement('h2');
        titleEl.style.cssText = 'color: #F8FAFC; font-size: 18px; font-weight: 700; margin-bottom: 12px;';
        titleEl.textContent = title;

        const messageEl = document.createElement('p');
        messageEl.style.cssText = 'color: #94A3B8; margin-bottom: 24px; line-height: 1.6;';
        messageEl.textContent = message;

        const buttonsContainer = document.createElement('div');
        buttonsContainer.style.cssText = 'display: flex; gap: 12px; justify-content: flex-end;';

        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn btn-secondary';
        cancelBtn.textContent = cancelText;
        cancelBtn.style.cssText = 'padding: 8px 16px; border: 1px solid #3B82F6; background: transparent; color: #3B82F6; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s ease;';
        cancelBtn.onmouseover = () => cancelBtn.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
        cancelBtn.onmouseout = () => cancelBtn.style.backgroundColor = 'transparent';
        cancelBtn.onclick = () => {
            overlay.remove();
        };

        const okBtn = document.createElement('button');
        okBtn.className = 'btn btn-primary';
        okBtn.textContent = okText;
        okBtn.style.cssText = 'padding: 8px 16px; background: #3B82F6; color: #F8FAFC; border-radius: 8px; cursor: pointer; font-weight: 600; border: none; transition: all 0.2s ease;';
        okBtn.onmouseover = () => okBtn.style.backgroundColor = '#2563EB';
        okBtn.onmouseout = () => okBtn.style.backgroundColor = '#3B82F6';
        okBtn.onclick = () => {
            callback();
            overlay.remove();
        };

        buttonsContainer.appendChild(cancelBtn);
        buttonsContainer.appendChild(okBtn);

        dialog.appendChild(titleEl);
        dialog.appendChild(messageEl);
        dialog.appendChild(buttonsContainer);
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);
    }
}

// =====================================================
// CSRF TOKEN MANAGEMENT
// =====================================================

class CsrfManager {
    static getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    static injectIntoForms() {
        const token = this.getToken();
        if (!token) return;

        document.querySelectorAll('form').forEach(form => {
            if (!form.querySelector('input[name="csrf_token"]')) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'csrf_token';
                input.value = token;
                form.insertBefore(input, form.firstChild);
            }
        });
    }

    static injectIntoHeaders() {
        const token = this.getToken();
        if (!token) return;

        // Fetch interceptor
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            const config = args[1] || {};
            if (!config.headers) config.headers = {};
            if (config.method && config.method.toUpperCase() !== 'GET') {
                config.headers['X-CSRF-Token'] = token;
            }
            return originalFetch.apply(this, [args[0], config]);
        };
    }
}

// =====================================================
// ALERT AUTO-HIDE
// =====================================================

function autoHideAlerts(timeout = 4000) {
    document.querySelectorAll('.alert').forEach(alert => {
        if (!alert.classList.contains('alert-no-auto-hide')) {
            setTimeout(() => {
                alert.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }, timeout);
        }
    });
}

// =====================================================
// SCROLL TO TOP BUTTON
// =====================================================

class ScrollToTop {
    constructor() {
        this.button = this.createButton();
        this.threshold = 300;
        this.init();
    }

    createButton() {
        const btn = document.createElement('button');
        btn.innerHTML = '↑';
        btn.className = 'scroll-to-top';
        btn.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #3B82F6;
            color: #F8FAFC;
            border: none;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 99;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        `;
        btn.onmouseover = () => btn.style.backgroundColor = '#2563EB';
        btn.onmouseout = () => btn.style.backgroundColor = '#3B82F6';
        btn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
        document.body.appendChild(btn);
        return btn;
    }

    init() {
        window.addEventListener('scroll', () => this.toggle());
    }

    toggle() {
        if (window.pageYOffset > this.threshold) {
            this.button.style.opacity = '1';
            this.button.style.visibility = 'visible';
        } else {
            this.button.style.opacity = '0';
            this.button.style.visibility = 'hidden';
        }
    }
}

const scrollToTop = new ScrollToTop();

// =====================================================
// STUDY TIMER
// =====================================================

class StudyTimer {
    constructor() {
        this.timeRemaining = 0;
        this.isRunning = false;
        this.intervalId = null;
        this.display = null;
    }

    start(seconds, displayElementId) {
        if (this.isRunning) this.stop();

        this.timeRemaining = seconds;
        this.display = document.getElementById(displayElementId);
        this.isRunning = true;

        this.updateDisplay();

        this.intervalId = setInterval(() => {
            this.timeRemaining--;
            this.updateDisplay();

            if (this.timeRemaining <= 0) {
                this.stop();
                this.onTimeUp();
            }
        }, 1000);
    }

    stop() {
        this.isRunning = false;
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }
    }

    updateDisplay() {
        if (this.display) {
            this.display.textContent = this.formatTime(this.timeRemaining);
        }
    }

    formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        if (hours > 0) {
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
        return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    static formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        if (hours > 0) {
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
        return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    onTimeUp() {
        toastManager.show('Time\'s up!', 'warning');
        window.dispatchEvent(new CustomEvent('timerComplete'));
    }
}

const studyTimer = new StudyTimer();

// =====================================================
// FLASHCARD MANAGEMENT
// =====================================================

class FlashcardUI {
    constructor(containerSelector) {
        this.container = document.querySelector(containerSelector);
        this.card = null;
        this.isFlipped = false;
    }

    flipCard() {
        if (!this.card) return;

        this.isFlipped = !this.isFlipped;
        this.card.classList.toggle('flipped', this.isFlipped);

        // Trigger animation
        this.card.style.animation = 'none';
        setTimeout(() => {
            this.card.style.animation = '';
        }, 10);
    }

    showAnswer() {
        this.flipCard();
    }

    hideAnswer() {
        if (this.isFlipped) {
            this.flipCard();
        }
    }

    setCard(cardData) {
        this.isFlipped = false;
        this.card = this.container.querySelector('.flashcard');
        if (this.card) {
            this.card.classList.remove('flipped');
        }
    }
}

// =====================================================
// QUIZ ENGINE UI
// =====================================================

class QuizUI {
    constructor(containerSelector) {
        this.container = document.querySelector(containerSelector);
        this.currentQuestion = 0;
        this.selectedOption = null;
    }

    selectOption(index) {
        this.selectedOption = index;
        const options = this.container.querySelectorAll('.option-button');
        options.forEach((opt, i) => {
            opt.classList.toggle('selected', i === index);
        });
    }

    disableOptions() {
        const options = this.container.querySelectorAll('.option-button');
        options.forEach(opt => opt.disabled = true);
    }

    enableOptions() {
        const options = this.container.querySelectorAll('.option-button');
        options.forEach(opt => opt.disabled = false);
    }

    showCorrect(index) {
        const option = this.container.querySelectorAll('.option-button')[index];
        if (option) option.classList.add('correct');
    }

    showWrong(index) {
        const option = this.container.querySelectorAll('.option-button')[index];
        if (option) option.classList.add('wrong');
    }

    updateProgress(current, total) {
        const progress = this.container.querySelector('.quiz-progress-bar');
        if (progress) {
            const percent = (current / total) * 100;
            progress.style.width = `${percent}%`;
        }
    }

    setTimer(seconds) {
        const timerBar = this.container.querySelector('.timer-bar');
        if (timerBar) {
            timerBar.style.width = '100%';
            const step = 100 / seconds;
            const interval = setInterval(() => {
                const currentWidth = parseFloat(timerBar.style.width);
                timerBar.style.width = (currentWidth - step) + '%';

                if (currentWidth <= 20) {
                    timerBar.classList.add('critical');
                } else if (currentWidth <= 50) {
                    timerBar.classList.add('warning');
                }

                if (currentWidth <= 0) {
                    clearInterval(interval);
                }
            }, 1000);
        }
    }
}

// =====================================================
// PROGRESS BAR ANIMATIONS
// =====================================================

class ProgressAnimator {
    static observeProgressBars() {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const fill = entry.target.querySelector('.strength-bar-fill');
                    if (fill && !fill.style.width) {
                        const targetWidth = fill.getAttribute('data-width') || '70%';
                        fill.style.width = targetWidth;
                    }
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.strength-bar').forEach(bar => {
            observer.observe(bar);
        });
    }

    static animateValue(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.round(current) + '%';
        }, 16);
    }
}

// =====================================================
// SIDEBAR NAVIGATION
// =====================================================

class SidebarNav {
    constructor() {
        this.init();
    }

    init() {
        this.highlightActiveLink();
    }

    highlightActiveLink() {
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-item').forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.includes(href)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    toggleMobileSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('open');
        }
    }

    closeMobileSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.remove('open');
        }
    }
}

const sidebarNav = new SidebarNav();

// =====================================================
// SEARCH DEBOUNCE
// =====================================================

class SearchDebounce {
    static create(inputSelector, callback, delay = 300) {
        const input = document.querySelector(inputSelector);
        if (!input) return;

        let timeoutId;
        input.addEventListener('input', (e) => {
            clearTimeout(timeoutId);
            const value = e.target.value;
            timeoutId = setTimeout(() => {
                callback(value);
            }, delay);
        });
    }
}

// =====================================================
// FORM VALIDATION
// =====================================================

class FormValidator {
    static validateRequired(input) {
        const isValid = input.value.trim().length > 0;
        this.setValidState(input, isValid);
        return isValid;
    }

    static validateEmail(input) {
        const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value);
        this.setValidState(input, isValid);
        return isValid;
    }

    static validateMinLength(input, length) {
        const isValid = input.value.length >= length;
        this.setValidState(input, isValid);
        return isValid;
    }

    static validatePassword(input) {
        const value = input.value;
        const isValid = value.length >= 8 && /[A-Z]/.test(value) && /[0-9]/.test(value);
        this.setValidState(input, isValid);
        return isValid;
    }

    static setValidState(input, isValid) {
        if (isValid) {
            input.classList.remove('form-control-error');
            const errorMsg = input.parentElement.querySelector('.form-error-text');
            if (errorMsg) errorMsg.remove();
        } else {
            input.classList.add('form-control-error');
        }
    }

    static showError(input, message) {
        input.classList.add('form-control-error');
        let errorMsg = input.parentElement.querySelector('.form-error-text');
        if (!errorMsg) {
            errorMsg = document.createElement('div');
            errorMsg.className = 'form-error-text';
            input.parentElement.appendChild(errorMsg);
        }
        errorMsg.textContent = message;
    }
}

// =====================================================
// AJAX FORM SUBMISSION
// =====================================================

class AjaxFormHandler {
    static setup(formSelector, successCallback, errorCallback) {
        const forms = document.querySelectorAll(formSelector);
        forms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.textContent : '';

                try {
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="loading-spinner"></span> Submitting...';
                    }

                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData,
                        headers: {
                            'X-CSRF-Token': CsrfManager.getToken() || ''
                        }
                    });

                    if (response.ok) {
                        const result = await response.json();
                        toastManager.show('Success!', 'success');
                        if (successCallback) successCallback(result);
                    } else {
                        const error = await response.json();
                        toastManager.show(error.message || 'An error occurred', 'error');
                        if (errorCallback) errorCallback(error);
                    }
                } catch (error) {
                    toastManager.show('Network error: ' + error.message, 'error');
                    if (errorCallback) errorCallback(error);
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                }
            });
        });
    }
}

// =====================================================
// CHART INITIALIZATION
// =====================================================

class ChartInitializer {
    static initCharts() {
        // Placeholder for Chart.js integration
        // Charts will be initialized when Chart.js is loaded
        window.dispatchEvent(new CustomEvent('chartsReady'));
    }

    static initProgressChart(elementId, data) {
        // Chart.js context-aware initialization
        const ctx = document.getElementById(elementId);
        if (!ctx || typeof Chart === 'undefined') return;

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94A3B8'
                        }
                    }
                }
            }
        });
    }

    static initLineChart(elementId, data) {
        const ctx = document.getElementById(elementId);
        if (!ctx || typeof Chart === 'undefined') return;

        new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#94A3B8'
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94A3B8'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94A3B8'
                        }
                    }
                }
            }
        });
    }
}

// =====================================================
// APP INITIALIZATION
// =====================================================

document.addEventListener('DOMContentLoaded', () => {
    // Inject CSRF tokens
    CsrfManager.injectIntoForms();
    CsrfManager.injectIntoHeaders();

    // Auto-hide alerts
    autoHideAlerts();

    // Progress animations
    ProgressAnimator.observeProgressBars();

    // Initialize charts
    ChartInitializer.initCharts();

    // Mobile sidebar toggle
    const hamburger = document.querySelector('.hamburger-btn');
    if (hamburger) {
        hamburger.addEventListener('click', () => sidebarNav.toggleMobileSidebar());
    }

    // Close sidebar when clicking nav items on mobile
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebarNav.closeMobileSidebar();
            }
        });
    });

    // Theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => themeManager.toggle());
    }

    // Close mobile sidebar on window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebarNav.closeMobileSidebar();
        }
    });

    // Global error handler
    window.addEventListener('error', (event) => {
        console.error('Global error:', event.error);
        toastManager.show('An error occurred. Please try again.', 'error');
    });

    // Unhandled promise rejection
    window.addEventListener('unhandledrejection', (event) => {
        console.error('Unhandled promise rejection:', event.reason);
        toastManager.show('An error occurred. Please try again.', 'error');
    });
});

// =====================================================
// EXPORTS
// =====================================================

window.app = {
    toastManager,
    themeManager,
    scrollToTop,
    studyTimer,
    sidebarNav,
    ConfirmDialog,
    FormValidator,
    SearchDebounce,
    AjaxFormHandler,
    ChartInitializer,
    ProgressAnimator,
    FlashcardUI,
    QuizUI
};
