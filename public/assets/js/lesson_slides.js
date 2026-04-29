/**
 * AviatorTutor — Interactive Slide-Based Lesson Player
 *
 * Handles slide navigation, question-gate state machine, progress sync,
 * keyboard shortcuts, accessibility toggles, and resume-on-reload.
 */
(function (global) {
  'use strict';

  var cfg = null;
  var stack = null;          // .slide-stack element
  var cards = [];            // <article.slide-card> NodeList → array
  var currentIndex = 0;      // 0-based; cards[totalSlides] is the completion card
  var totalSlides = 0;       // not counting the completion card
  var lsKey = null;          // localStorage key for resume position
  var marked = false;        // becomes true once we POST /complete

  function $(sel, root) { return (root || document).querySelector(sel); }
  function $$(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

  function init(config) {
    cfg = config || {};
    stack = $('#slide-stack');
    if (!stack) return;

    cards = $$('.slide-card', stack);
    totalSlides = cfg.totalSlides || 0;

    if (cards.length === 0) return;

    lsKey = 'av_slide_progress_lesson_' + cfg.lessonId;

    bindUI();
    restoreToggles();

    // Resume from last known slide (only into a real slide, not the completion card).
    var saved = parseInt(localStorage.getItem(lsKey) || '0', 10);
    if (isNaN(saved) || saved < 0 || saved >= cards.length - 1) saved = 0;
    showSlide(saved);
  }

  function bindUI() {
    var prev = $('#slide-prev-btn');
    var next = $('#slide-next-btn');
    var jump = $('#slide-jump');
    var larger  = $('#slide-larger-text');
    var smaller = $('#slide-smaller-text');
    var contrast = $('#slide-contrast');
    var focusBtn = $('#slide-focus-mode');
    var markBtn = $('#slide-mark-complete-btn');

    if (prev)  prev.addEventListener('click', goPrev);
    if (next)  next.addEventListener('click', goNext);
    if (jump)  jump.addEventListener('change', function () { showSlide(parseInt(this.value, 10) || 0); });
    if (larger)   larger.addEventListener('click',   function () { adjustTextSize(+1); });
    if (smaller)  smaller.addEventListener('click',  function () { adjustTextSize(-1); });
    if (contrast) contrast.addEventListener('click', function () { toggleHighContrast(); });
    if (focusBtn) focusBtn.addEventListener('click', function () { toggleFocusMode(); });
    if (markBtn) markBtn.addEventListener('click', markComplete);

    // Wire each gate
    cards.forEach(function (card) {
      if (card.getAttribute('data-has-gate') !== '1') return;
      var submit = $('.slide-gate-submit', card);
      if (submit) submit.addEventListener('click', function (e) {
        e.preventDefault();
        handleGateSubmit(card);
      });
    });

    // Keyboard nav
    document.addEventListener('keydown', function (e) {
      if (e.target && /^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName)) return;
      if (e.key === 'ArrowLeft')  { e.preventDefault(); goPrev(); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); goNext(); }
      else if (e.key === 'Escape' && document.body.classList.contains('slide-focus-mode')) {
        e.preventDefault();
        toggleFocusMode();
      }
      // Number keys 1–4 pick the matching MCQ option on the active slide.
      else if (/^[1-4]$/.test(e.key)) {
        var active = $('.slide-card.is-active');
        if (!active || active.getAttribute('data-has-gate') !== '1') return;
        var idx = parseInt(e.key, 10) - 1;
        var radios = $$('.slide-gate-option input[type="radio"]', active);
        if (radios[idx] && !radios[idx].disabled) {
          radios[idx].checked = true;
          e.preventDefault();
        }
      }
    });
  }

  function showSlide(index) {
    if (index < 0) index = 0;
    if (index > cards.length - 1) index = cards.length - 1;

    cards.forEach(function (c, i) { c.classList.toggle('is-active', i === index); });

    currentIndex = index;
    updateProgress();
    updateNav();

    // Persist (only for real slides; clear when on completion card)
    if (index < totalSlides) {
      localStorage.setItem(lsKey, String(index));
    }

    // Sync the jump dropdown
    var jump = $('#slide-jump');
    if (jump && index < totalSlides) jump.value = String(index);

    // Scroll the active card into view from the top
    var active = cards[index];
    if (active && typeof active.scrollIntoView === 'function') {
      try { active.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (e) { /* noop */ }
    }
  }

  function goPrev() {
    if (currentIndex > 0) showSlide(currentIndex - 1);
  }

  function goNext() {
    var card = cards[currentIndex];
    if (card && card.getAttribute('data-has-gate') === '1' && !isGatePassed(card)) {
      flashGate(card);
      return;
    }
    if (currentIndex < cards.length - 1) showSlide(currentIndex + 1);
  }

  function isGatePassed(card) {
    var gate = $('.slide-gate', card);
    if (!gate) return true;
    return gate.getAttribute('data-gate-state') === 'correct';
  }

  function flashGate(card) {
    var gate = $('.slide-gate', card);
    if (!gate) return;
    gate.classList.remove('flash');
    // Reflow then add for restart
    void gate.offsetWidth;
    gate.classList.add('flash');
    var hint = $('#slide-nav-hint');
    if (hint) {
      hint.textContent = 'Answer the question to continue';
      hint.classList.add('show');
      setTimeout(function () { hint.classList.remove('show'); }, 2200);
    }
  }

  function handleGateSubmit(card) {
    var slideId = parseInt(card.getAttribute('data-slide-id'), 10);
    var correctIndex = parseInt(card.getAttribute('data-correct-index'), 10);
    var radios = $$('input[type="radio"]', card);
    var picked = null;
    radios.forEach(function (r) { if (r.checked) picked = parseInt(r.value, 10); });

    if (picked === null || isNaN(picked)) {
      flashGate(card);
      var fb = $('.slide-gate-feedback', card);
      var inner = fb && $('.slide-gate-feedback-inner', fb);
      if (fb && inner) {
        inner.textContent = 'Pick an option first.';
        fb.hidden = false;
        fb.classList.remove('correct');
        fb.classList.add('incorrect');
      }
      return;
    }

    var isCorrect = picked === correctIndex;
    var gate = $('.slide-gate', card);
    var fb = $('.slide-gate-feedback', card);
    var inner = fb && $('.slide-gate-feedback-inner', fb);

    if (gate) {
      gate.classList.remove('correct', 'incorrect');
      gate.classList.add(isCorrect ? 'correct' : 'incorrect');
      gate.setAttribute('data-gate-state', isCorrect ? 'correct' : 'unanswered');
    }

    if (fb && inner) {
      inner.textContent = isCorrect ? 'Correct!' : 'Not quite — review the explanation and try again.';
      fb.hidden = false;
      fb.classList.toggle('correct', isCorrect);
      fb.classList.toggle('incorrect', !isCorrect);
    }

    if (isCorrect) {
      // Lock the inputs and submit button
      radios.forEach(function (r) { r.disabled = true; });
      var submit = $('.slide-gate-submit', card);
      if (submit) {
        submit.disabled = true;
        submit.textContent = 'Locked in';
      }
      updateNav();
      // Auto-focus the Next button so keyboard users can advance with Enter
      var nextBtn = $('#slide-next-btn');
      if (nextBtn) try { nextBtn.focus(); } catch (e) { /* noop */ }
    } else {
      // Allow retry
      var submit2 = $('.slide-gate-submit', card);
      if (submit2) submit2.textContent = 'Try Again';
    }

    // Persist attempt (best-effort)
    postSlideAnswer(slideId, isCorrect);
  }

  function postSlideAnswer(slideId, isCorrect) {
    if (!cfg.slideAnswerUrl) return;
    try {
      fetch(cfg.slideAnswerUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
        body: 'slide_id=' + encodeURIComponent(slideId) + '&is_correct=' + (isCorrect ? '1' : '0')
      });
    } catch (e) { /* network errors should not block UX */ }
  }

  function updateProgress() {
    var fill = $('#slide-progress-fill');
    var cur = $('#slide-progress-current');
    var total = $('#slide-progress-total');
    var displayedIndex = Math.min(currentIndex + 1, Math.max(1, totalSlides));
    var pct = totalSlides > 0 ? Math.round((Math.min(currentIndex, totalSlides) / totalSlides) * 100) : 100;

    if (fill) fill.style.width = pct + '%';
    if (cur) cur.textContent = String(displayedIndex);
    if (total) total.textContent = String(Math.max(1, totalSlides));
  }

  function updateNav() {
    var prev = $('#slide-prev-btn');
    var next = $('#slide-next-btn');
    if (prev) prev.disabled = (currentIndex <= 0);
    if (next) {
      var atEnd = currentIndex >= cards.length - 1;
      next.disabled = atEnd;
      next.style.visibility = atEnd ? 'hidden' : '';
    }
  }

  function markComplete() {
    if (marked) return;
    var btn = $('#slide-mark-complete-btn');
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Marking complete…';
    }
    if (!cfg.lessonCompleteUrl) return;
    fetch(cfg.lessonCompleteUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    })
      .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
      .then(function () {
        marked = true;
        // Clear resume so next visit starts fresh
        try { localStorage.removeItem(lsKey); } catch (e) { /* noop */ }
        if (btn) btn.textContent = 'Completed ✓';
        // Redirect back after a brief celebration moment
        setTimeout(function () {
          if (cfg.systemUrl) window.location.href = cfg.systemUrl;
        }, 700);
      })
      .catch(function () {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Try again';
        }
      });
  }

  /* ---- Accessibility toggles ---- */

  // Three-step font size: -1 = smaller, 0 = default, +1 = larger.
  // Persisted as `av_slide_text_size`.
  function adjustTextSize(delta) {
    var current = 0;
    try { current = parseInt(localStorage.getItem('av_slide_text_size') || '0', 10); } catch (e) {}
    if (isNaN(current)) current = 0;
    var next = Math.max(-1, Math.min(2, current + delta));
    applyTextSize(next);
    try { localStorage.setItem('av_slide_text_size', String(next)); } catch (e) {}
  }

  function applyTextSize(size) {
    document.body.classList.remove('slide-text-smaller', 'slide-text-larger', 'slide-text-largest');
    if (size === -1) document.body.classList.add('slide-text-smaller');
    if (size === 1)  document.body.classList.add('slide-text-larger');
    if (size === 2)  document.body.classList.add('slide-text-largest');
    var lg = $('#slide-larger-text');
    var sm = $('#slide-smaller-text');
    if (lg) lg.setAttribute('aria-pressed', size > 0 ? 'true' : 'false');
    if (sm) sm.setAttribute('aria-pressed', size < 0 ? 'true' : 'false');
  }

  function toggleHighContrast() {
    var on = !document.body.classList.contains('slide-high-contrast');
    document.body.classList.toggle('slide-high-contrast', on);
    try { localStorage.setItem('av_slide_high_contrast', on ? '1' : '0'); } catch (e) {}
    var btn = $('#slide-contrast');
    if (btn) btn.setAttribute('aria-pressed', on ? 'true' : 'false');
  }

  // Hides the pilot sidebar so the slide takes the full viewport.
  // Press Esc (or click the toggle again) to exit.
  function toggleFocusMode() {
    var on = !document.body.classList.contains('slide-focus-mode');
    document.body.classList.toggle('slide-focus-mode', on);
    try { localStorage.setItem('av_slide_focus_mode', on ? '1' : '0'); } catch (e) {}
    var btn = $('#slide-focus-mode');
    if (btn) btn.setAttribute('aria-pressed', on ? 'true' : 'false');
  }

  function restoreToggles() {
    try {
      var size = parseInt(localStorage.getItem('av_slide_text_size') || '0', 10);
      if (!isNaN(size) && size !== 0) applyTextSize(size);
      if (localStorage.getItem('av_slide_high_contrast') === '1') {
        document.body.classList.add('slide-high-contrast');
        var c = $('#slide-contrast'); if (c) c.setAttribute('aria-pressed', 'true');
      }
      if (localStorage.getItem('av_slide_focus_mode') === '1') {
        document.body.classList.add('slide-focus-mode');
        var f = $('#slide-focus-mode'); if (f) f.setAttribute('aria-pressed', 'true');
      }
    } catch (e) {}
  }

  global.LessonSlides = { init: init };

})(window);
