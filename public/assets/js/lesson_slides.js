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
      if (e.metaKey || e.ctrlKey || e.altKey) return;
      if (e.key === 'ArrowLeft')  { e.preventDefault(); goPrev(); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); goNext(); }
      // Font-size shortcuts: + (or =) grows, - (or _) shrinks. Mirrors the
      // detail-view shortcuts so the keyboard feels consistent across modes.
      else if (e.key === '+' || e.key === '=') { e.preventDefault(); adjustTextSize(1); }
      else if (e.key === '-' || e.key === '_') { e.preventDefault(); adjustTextSize(-1); }
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
    var radios = $$('input[type="radio"]', card);
    var picked = null;
    radios.forEach(function (r) { if (r.checked) picked = parseInt(r.value, 10); });

    var fb = $('.slide-gate-feedback', card);
    var inner = fb && $('.slide-gate-feedback-inner', fb);
    var explEl = fb && $('.slide-gate-explanation', fb);

    if (picked === null || isNaN(picked)) {
      flashGate(card);
      if (fb && inner) {
        inner.textContent = 'Pick an option first.';
        fb.hidden = false;
        fb.classList.remove('correct');
        fb.classList.add('incorrect');
      }
      return;
    }

    // Briefly disable the submit button while the API call is in flight
    // so a double-click doesn't burn an extra retry.
    var submit = $('.slide-gate-submit', card);
    if (submit) submit.disabled = true;

    postSlideAnswer(slideId, picked).then(function (res) {
      if (!res || res.ok !== true || !res.data) {
        // Network or server failure — let the user retry, no state change
        if (submit) submit.disabled = false;
        if (fb && inner) {
          inner.textContent = 'Couldn’t reach the server. Check your connection and try again.';
          fb.hidden = false;
          fb.classList.remove('correct');
          fb.classList.add('incorrect');
        }
        return;
      }

      var data = res.data;
      var gate = $('.slide-gate', card);
      var isCorrect = data.is_correct === true;
      var canProceed = data.can_proceed === true;
      var unlockedAfterFailure = data.unlocked_after_failure === true;

      if (gate) {
        gate.classList.remove('correct', 'incorrect');
        gate.classList.add(isCorrect ? 'correct' : 'incorrect');
        gate.setAttribute('data-gate-state', canProceed ? 'correct' : 'unanswered');
      }

      if (fb && inner) {
        if (isCorrect) {
          inner.textContent = 'Correct!';
        } else if (unlockedAfterFailure) {
          inner.textContent = 'Out of attempts — review the explanation below, then continue.';
        } else {
          var left = (typeof data.attempts_left === 'number') ? data.attempts_left : 0;
          inner.textContent = 'Not quite. ' + left + ' attempt' + (left === 1 ? '' : 's') + ' left.';
        }
        fb.hidden = false;
        fb.classList.toggle('correct', isCorrect);
        fb.classList.toggle('incorrect', !isCorrect);
      }

      // Reveal the explanation only when the server says the gate is settled.
      if (explEl) {
        if (canProceed && data.explanation) {
          explEl.textContent = data.explanation;
          explEl.hidden = false;
        } else {
          explEl.textContent = '';
          explEl.hidden = true;
        }
      }

      if (canProceed) {
        radios.forEach(function (r) { r.disabled = true; });
        if (submit) {
          submit.disabled = true;
          submit.textContent = isCorrect ? 'Locked in' : 'Continue';
        }
        updateNav();
        var nextBtn = $('#slide-next-btn');
        if (nextBtn) try { nextBtn.focus(); } catch (e) { /* noop */ }
      } else {
        // Wrong but still has retries
        if (submit) {
          submit.disabled = false;
          submit.textContent = 'Try Again';
        }
      }
    });
  }

  function postSlideAnswer(slideId, selectedIndex) {
    if (!cfg.slideAnswerUrl) return Promise.resolve({ ok: false });
    return fetch(cfg.slideAnswerUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
      body: 'slide_id=' + encodeURIComponent(slideId) +
            '&selected_index=' + encodeURIComponent(selectedIndex)
    })
      .then(function (r) {
        return r.json().then(function (j) { return { ok: r.ok, data: j }; }).catch(function () {
          return { ok: r.ok, data: null };
        });
      })
      .catch(function () { return { ok: false, data: null }; });
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
      .then(function (r) {
        // Try to parse the body either way so we can surface 403 gate-blocks
        return r.json()
          .then(function (j) { return { ok: r.ok, status: r.status, data: j }; })
          .catch(function () { return { ok: r.ok, status: r.status, data: null }; });
      })
      .then(function (res) {
        if (res.ok) {
          marked = true;
          try { localStorage.removeItem(lsKey); } catch (e) { /* noop */ }
          if (btn) btn.textContent = 'Completed ✓';
          setTimeout(function () {
            if (cfg.systemUrl) window.location.href = cfg.systemUrl;
          }, 700);
          return;
        }
        // 403 = unsettled gates; tell the user what to do.
        var msg = (res.data && res.data.error)
          ? res.data.error
          : 'Couldn’t mark this lesson complete. Try again.';
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Try again';
        }
        try { window.alert(msg); } catch (e) { /* noop */ }
      })
      .catch(function () {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Try again';
        }
      });
  }

  /* ---- Accessibility toggles ---- */

  // 5-step font size ladder: -2=xs, -1=s, 0=m (default), 1=l, 2=xl.
  // Persisted as integer in `av_slide_text_size`.
  // Was 3 steps with fixed-pixel CSS — now drives a rem-based class that
  // scales every descendant of .slide-player proportionally.
  var SLIDE_SIZE_CLASSES = ['slide-text-xs','slide-text-s','slide-text-m','slide-text-l','slide-text-xl'];

  function adjustTextSize(delta) {
    var current = 0;
    try { current = parseInt(localStorage.getItem('av_slide_text_size') || '0', 10); } catch (e) {}
    if (isNaN(current)) current = 0;
    var next = Math.max(-2, Math.min(2, current + delta));
    applyTextSize(next);
    try { localStorage.setItem('av_slide_text_size', String(next)); } catch (e) {}
  }

  function applyTextSize(size) {
    // Remove every legacy and current size class so we never end up with
    // two stacked size states from older sessions.
    var legacy = ['slide-text-smaller','slide-text-larger','slide-text-largest','slide-large-text'];
    SLIDE_SIZE_CLASSES.concat(legacy).forEach(function(c){ document.body.classList.remove(c); });
    var idx = Math.max(-2, Math.min(2, size | 0)) + 2;
    document.body.classList.add(SLIDE_SIZE_CLASSES[idx]);
    var lg = $('#slide-larger-text');
    var sm = $('#slide-smaller-text');
    if (lg) lg.setAttribute('aria-pressed', size > 0 ? 'true' : 'false');
    if (sm) sm.setAttribute('aria-pressed', size < 0 ? 'true' : 'false');
    if (lg) lg.disabled = (size >= 2);
    if (sm) sm.disabled = (size <= -2);
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
