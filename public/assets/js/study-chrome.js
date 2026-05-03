/* AviatorTutor — Phase 2 study chrome
 *
 * Drives the new single-topbar layout:
 *   - Hamburger toggles the lesson drawer.
 *   - 3s of inactivity hides the topbar; any input restores it.
 *   - Keyboard shortcuts: → / Space next, ← prev, F fullscreen, T toggle drawer,
 *     Esc closes drawers, + / - font size (defers to whichever per-page font
 *     handler is loaded).
 *   - Edge tap zones invoke window.LessonSlides.goNext()/goPrev() when present.
 *   - Mobile swipe (pointer events) on .study-content advances slides.
 *
 * Auto-hide is suppressed when body.reduce-motion is set (read from
 * prefers-reduced-motion + the user's saved Phase-3 setting once that ships).
 */
(function () {
    'use strict';

    var IDLE_MS = 3000;
    var idleTimer = null;
    var body = document.body;

    function $(sel, root) { return (root || document).querySelector(sel); }
    function $$(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    function prefersReducedMotion() {
        try { return window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
        catch (e) { return false; }
    }
    if (prefersReducedMotion()) body.classList.add('reduce-motion');

    // ---- Auto-hide chrome -------------------------------------------------
    function hideChrome() {
        if (body.classList.contains('reduce-motion')) return;
        if (body.classList.contains('drawer-open'))  return;
        if (body.classList.contains('slide-picker-open')) return;
        body.classList.add('chrome-hidden');
    }
    function showChrome() {
        body.classList.remove('chrome-hidden');
        scheduleHide();
    }
    function scheduleHide() {
        if (idleTimer) clearTimeout(idleTimer);
        idleTimer = setTimeout(hideChrome, IDLE_MS);
    }

    // Reveal triggers — deliberately narrow so plain mouse motion during
    // reading doesn't keep chrome on screen forever:
    //   • mouse near the top edge (within 32px)
    //   • any tap, key, or click
    //   • the top reveal hover zone explicitly
    document.addEventListener('mousemove', function (e) {
        if (e.clientY <= 32) showChrome();
    }, { passive: true });
    ['mousedown', 'touchstart', 'keydown', 'click'].forEach(function (ev) {
        document.addEventListener(ev, showChrome, { passive: true });
    });
    var revealZone = $('.study-topbar-reveal');
    if (revealZone) revealZone.addEventListener('mouseenter', showChrome);

    // ---- Drawer ----------------------------------------------------------
    function toggleDrawer(force) {
        var open = (typeof force === 'boolean') ? force : !body.classList.contains('drawer-open');
        body.classList.toggle('drawer-open', open);
        if (open) showChrome();
    }

    document.addEventListener('click', function (e) {
        var t = e.target;
        if (!(t instanceof Element)) return;

        if (t.closest('[data-sc-drawer-toggle]')) {
            e.preventDefault();
            toggleDrawer();
            return;
        }
        if (t.closest('.study-drawer-backdrop')) {
            toggleDrawer(false);
            return;
        }
        if (t.closest('[data-sc-drawer-close]')) {
            e.preventDefault();
            toggleDrawer(false);
            return;
        }
    });

    // ---- Slide picker (tap progress bar) ---------------------------------
    var picker = $('.study-slide-picker');
    var progressEl = $('.study-progress');
    if (progressEl && picker) {
        progressEl.addEventListener('click', function (e) {
            e.stopPropagation();
            body.classList.toggle('slide-picker-open');
            showChrome();
        });
        document.addEventListener('click', function (e) {
            if (!body.classList.contains('slide-picker-open')) return;
            if (e.target.closest('.study-slide-picker') || e.target.closest('.study-progress')) return;
            body.classList.remove('slide-picker-open');
        });
    }

    // ---- Edge tap zones --------------------------------------------------
    function callSlideNav(dir) {
        var ls = window.LessonSlides;
        if (!ls) return false;
        if (dir > 0 && typeof ls.goNext === 'function') { ls.goNext(); return true; }
        if (dir < 0 && typeof ls.goPrev === 'function') { ls.goPrev(); return true; }
        return false;
    }
    $$('.study-edge-zone').forEach(function (zone) {
        zone.addEventListener('click', function () {
            callSlideNav(zone.classList.contains('study-edge-zone--next') ? 1 : -1);
        });
    });

    // ---- Mobile swipe gestures (plain pointer events) --------------------
    var swipeArea = $('.study-content[data-swipe="1"]');
    if (swipeArea) {
        var startX = 0, startY = 0, tracking = false;
        swipeArea.addEventListener('pointerdown', function (e) {
            if (e.pointerType === 'mouse') return;
            tracking = true; startX = e.clientX; startY = e.clientY;
        });
        swipeArea.addEventListener('pointerup', function (e) {
            if (!tracking) return;
            tracking = false;
            var dx = e.clientX - startX, dy = e.clientY - startY;
            // Horizontal swipe of >60px and not mostly vertical.
            if (Math.abs(dx) > 60 && Math.abs(dx) > Math.abs(dy) * 1.5) {
                callSlideNav(dx < 0 ? 1 : -1);
            }
        });
        swipeArea.addEventListener('pointercancel', function () { tracking = false; });
    }

    // ---- Keyboard shortcuts ----------------------------------------------
    document.addEventListener('keydown', function (e) {
        if (e.target && /^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName)) return;
        if (e.metaKey || e.ctrlKey || e.altKey) return;

        if (e.key === 'Escape') {
            if (body.classList.contains('slide-picker-open')) {
                body.classList.remove('slide-picker-open'); e.preventDefault(); return;
            }
            if (body.classList.contains('drawer-open')) {
                toggleDrawer(false); e.preventDefault(); return;
            }
            return;
        }
        // Don't double-handle slide nav: lesson_slides.js already binds
        // ArrowLeft / ArrowRight when the slide player is on the page. Other
        // study modes (detail, mind map, etc.) get their own arrow handling.

        // T → toggle drawer
        if (e.key === 't' || e.key === 'T') { e.preventDefault(); toggleDrawer(); return; }
        // F → fullscreen
        if (e.key === 'f' || e.key === 'F') {
            e.preventDefault();
            try {
                if (!document.fullscreenElement) document.documentElement.requestFullscreen();
                else document.exitFullscreen();
            } catch (err) { /* noop */ }
            return;
        }
    });

    // ---- Wire slide-picker tile clicks (delegated) ------------------------
    document.addEventListener('click', function (e) {
        var tile = e.target.closest('.study-slide-tile');
        if (!tile) return;
        var idx = parseInt(tile.getAttribute('data-slide-index') || '0', 10);
        var ls = window.LessonSlides;
        if (ls && typeof ls.goTo === 'function') { ls.goTo(idx); }
        body.classList.remove('slide-picker-open');
    });

    // ---- Analytics: fire on mode switch + drop-off ----
    function track(event, props) {
        try {
            var fd = new FormData();
            fd.append('event', event);
            fd.append('props', JSON.stringify(props || {}));
            fetch('/api/track', { method: 'POST', body: fd, credentials: 'same-origin', keepalive: true });
        } catch (e) { /* noop */ }
    }
    document.addEventListener('click', function (e) {
        var modeLink = e.target instanceof Element ? e.target.closest('.study-modes a') : null;
        if (modeLink) {
            var label = (modeLink.textContent || '').trim().toLowerCase();
            track('mode_open', { mode: label });
        }
    });
    window.addEventListener('beforeunload', function () {
        var card = document.querySelector('.slide-card.is-active');
        if (!card) return;
        var idx = parseInt(card.getAttribute('data-slide-index') || '0', 10);
        var lesson = document.querySelector('.slide-player');
        var lessonId = lesson ? parseInt(lesson.getAttribute('data-lesson-id') || '0', 10) : 0;
        // Drop-off: closing/reloading mid-deck before the completion card.
        var totalAttr = lesson ? parseInt(lesson.getAttribute('data-total-slides') || '0', 10) : 0;
        if (totalAttr > 0 && idx < totalAttr - 1) {
            track('slide_dropoff', { lesson_id: lessonId, slide_index: idx });
        }
    });

    // First idle clock starts on load.
    scheduleHide();
})();
