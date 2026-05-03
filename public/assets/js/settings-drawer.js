/* AviatorTutor — Phase 3 settings drawer
 *
 * Applies and persists reading preferences. The drawer is opened by the
 * topbar settings cog; on form change, settings are applied immediately
 * (instant feedback) and a debounced POST to /api/settings/update saves
 * them server-side. localStorage mirrors the choice for FOUC-free first
 * paint on the next page load.
 *
 * The same body[data-*] attributes are read by themes.css to apply theme,
 * font family, line spacing and reading width. Font size class is added
 * to the body (study-font-{size}) so existing per-page CSS keeps working.
 */
(function () {
    'use strict';

    var LS_KEY = 'aviatortutor_settings';
    var DEFAULTS = {
        theme: 'dark',
        font_size: 'm',
        font_family: 'system',
        line_spacing: 'normal',
        reading_width: 'medium',
        reduced_motion: 0,
        audio_accent: 'us'
    };

    function readLocal() {
        try {
            var raw = localStorage.getItem(LS_KEY);
            if (!raw) return null;
            var parsed = JSON.parse(raw);
            return (parsed && typeof parsed === 'object') ? parsed : null;
        } catch (e) { return null; }
    }
    function writeLocal(s) {
        try { localStorage.setItem(LS_KEY, JSON.stringify(s)); } catch (e) {}
    }

    function applySettings(s) {
        if (!s) return;
        var body = document.body;
        if (s.theme)         body.setAttribute('data-theme', s.theme);
        if (s.font_family)   body.setAttribute('data-font-family', s.font_family);
        if (s.line_spacing)  body.setAttribute('data-line-spacing', s.line_spacing);
        if (s.reading_width) body.setAttribute('data-reading-width', s.reading_width);
        body.classList.toggle('reduce-motion', !!s.reduced_motion);

        // Font size: drive both the slide-text-* class (slide player) and the
        // study-font-* class (detail/notes) so the same setting hits both UIs.
        var sizes = ['xs','s','m','l','xl'];
        sizes.forEach(function (sz) {
            body.classList.remove('slide-text-' + sz, 'study-font-' + sz);
        });
        var size = sizes.indexOf(s.font_size) >= 0 ? s.font_size : 'm';
        body.classList.add('slide-text-' + size, 'study-font-' + size);

        // Sync any open form's radios to reflect current state.
        var form = document.getElementById('settings-form');
        if (form) {
            ['theme','font_size','font_family','line_spacing','reading_width','audio_accent'].forEach(function (name) {
                var radios = form.querySelectorAll('input[name="' + name + '"]');
                radios.forEach(function (r) { r.checked = (r.value === String(s[name])); });
            });
            var rm = form.querySelector('input[name="reduced_motion"]');
            if (rm) rm.checked = !!s.reduced_motion;
        }
    }

    // Apply ASAP on first load — runs before DOMContentLoaded if the script
    // tag is in <head> (no defer). We tolerate either case.
    var initial = readLocal();
    if (initial) applySettings(initial);
    document.addEventListener('DOMContentLoaded', function () {
        if (initial) applySettings(initial);
    });

    // ---- Drawer open/close ----
    document.addEventListener('click', function (e) {
        var t = e.target;
        if (!(t instanceof Element)) return;

        if (t.closest('[data-settings-toggle], #sc-settings-cog')) {
            e.preventDefault();
            var drawer = document.getElementById('settings-drawer');
            if (drawer && drawer.hasAttribute('hidden')) drawer.removeAttribute('hidden');
            document.body.classList.toggle('settings-open');
            return;
        }
        if (t.closest('[data-settings-close]')) {
            e.preventDefault();
            document.body.classList.remove('settings-open');
            return;
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.body.classList.contains('settings-open')) {
            document.body.classList.remove('settings-open');
        }
    });

    // ---- Save (debounced) ----
    var saveTimer = null;
    var lastStatus = null;
    function setStatus(msg, klass) {
        var el = document.getElementById('settings-status');
        if (!el) return;
        el.textContent = msg || '';
        el.className = 'sd-status' + (klass ? ' ' + klass : '');
        lastStatus = klass;
    }

    function readForm(form) {
        var fd = new FormData(form);
        var s = Object.assign({}, DEFAULTS);
        ['theme','font_size','font_family','line_spacing','reading_width','audio_accent'].forEach(function (k) {
            if (fd.get(k) != null) s[k] = String(fd.get(k));
        });
        s.reduced_motion = fd.get('reduced_motion') ? 1 : 0;
        return s;
    }

    function saveSettings(s, csrf) {
        var fd = new FormData();
        Object.keys(s).forEach(function (k) { fd.append(k, String(s[k])); });
        fd.append('csrf_token', csrf || '');
        return fetch('/api/settings/update', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        }).then(function (r) { return r.ok; }).catch(function () { return false; });
    }

    function trackChange(s) {
        try {
            var fd = new FormData();
            fd.append('event', 'settings_change');
            fd.append('props', JSON.stringify({
                theme: s.theme, font_size: s.font_size, font_family: s.font_family,
                line_spacing: s.line_spacing, reading_width: s.reading_width,
                reduced_motion: !!s.reduced_motion
            }));
            fetch('/api/track', { method: 'POST', body: fd, credentials: 'same-origin', keepalive: true });
        } catch (e) {}
    }

    document.addEventListener('change', function (e) {
        var form = document.getElementById('settings-form');
        if (!form || !form.contains(e.target)) return;

        var s = readForm(form);
        applySettings(s);
        writeLocal(s);
        setStatus('Saving…', null);

        clearTimeout(saveTimer);
        saveTimer = setTimeout(function () {
            saveSettings(s, form.getAttribute('data-csrf')).then(function (ok) {
                setStatus(ok ? 'Saved.' : 'Saved on this device only.', ok ? 'is-ok' : 'is-err');
                setTimeout(function () { if (lastStatus === 'is-ok') setStatus(''); }, 1800);
                trackChange(s);
            });
        }, 220);
    });

    // Expose for other modules (e.g. Phase 3 flashcards may want to read theme).
    window.AVSettings = {
        get: function () { return readLocal() || DEFAULTS; },
        apply: applySettings
    };
})();
