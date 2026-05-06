/* AviatorTutor — Phase 10 global topbar search.
 *
 * Hits /api/search?q=… and renders a grouped dropdown. Lazy: only fires
 * after 200ms of typing-quiet to keep the API quiet. Up/Down navigates
 * results; Enter opens; Esc closes; Cmd/Ctrl+K focuses from anywhere.
 */
(function () {
    'use strict';

    var input    = document.getElementById('plt-search-input');
    var dropdown = document.getElementById('plt-search-dropdown');
    if (!input || !dropdown) return;

    var GROUP_LABELS = {
        system:    'Systems',
        lesson:    'Lessons',
        flashcard: 'Flashcards',
        quiz:      'Quizzes',
        mnemonic:  'Mnemonics'
    };
    var GROUP_ORDER = ['system', 'lesson', 'flashcard', 'quiz', 'mnemonic'];

    var debounce = null;
    var lastReq  = 0;
    var activeIdx = -1;

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function highlight(text, q) {
        var t = escapeHtml(text);
        if (!q) return t;
        var safe = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return t.replace(new RegExp('(' + safe + ')', 'ig'), '<mark>$1</mark>');
    }

    function close() {
        dropdown.hidden = true;
        dropdown.innerHTML = '';
        activeIdx = -1;
    }

    function render(results, q) {
        if (!results.length) {
            dropdown.innerHTML = '<p class="plt-search__empty">No matches for “' + escapeHtml(q) + '”.</p>';
            dropdown.hidden = false;
            activeIdx = -1;
            return;
        }
        var grouped = {};
        results.forEach(function (r) {
            var k = r.type || 'lesson';
            if (!grouped[k]) grouped[k] = [];
            grouped[k].push(r);
        });
        var html = '';
        GROUP_ORDER.forEach(function (key) {
            if (!grouped[key]) return;
            html += '<div class="plt-search__group">' + escapeHtml(GROUP_LABELS[key] || key) + '</div>';
            grouped[key].forEach(function (r) {
                var meta = r.system_name ? escapeHtml(r.system_name) : '';
                if (r.excerpt) {
                    meta += (meta ? ' · ' : '') + highlight(r.excerpt, q);
                }
                html += '<a href="' + escapeHtml(r.url || '#') + '" class="plt-search__hit" role="option">'
                      + '<div class="plt-search__hit-title">' + highlight(r.title || '(untitled)', q) + '</div>'
                      + (meta ? '<div class="plt-search__hit-meta">' + meta + '</div>' : '')
                      + '</a>';
            });
        });
        dropdown.innerHTML = html;
        dropdown.hidden = false;
        activeIdx = -1;
    }

    function fetchSearch(q) {
        var reqId = ++lastReq;
        fetch('/api/search?q=' + encodeURIComponent(q), { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (reqId !== lastReq) return; // a newer request superseded us
                render(Array.isArray(data.results) ? data.results : [], q);
            })
            .catch(function () { /* silent */ });
    }

    function onInput() {
        clearTimeout(debounce);
        var q = (input.value || '').trim();
        if (q.length < 2) { close(); return; }
        debounce = setTimeout(function () { fetchSearch(q); }, 200);
    }

    function hits() {
        return Array.prototype.slice.call(dropdown.querySelectorAll('.plt-search__hit'));
    }
    function setActive(i) {
        var h = hits();
        if (!h.length) return;
        activeIdx = (i + h.length) % h.length;
        h.forEach(function (el, idx) { el.classList.toggle('is-active', idx === activeIdx); });
        var act = h[activeIdx];
        if (act) act.scrollIntoView({ block: 'nearest' });
    }

    input.addEventListener('input', onInput);
    input.addEventListener('focus', function () {
        if ((input.value || '').trim().length >= 2 && dropdown.innerHTML !== '') {
            dropdown.hidden = false;
        }
    });

    input.addEventListener('keydown', function (e) {
        if (dropdown.hidden) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); setActive(activeIdx + 1); }
        else if (e.key === 'ArrowUp')   { e.preventDefault(); setActive(activeIdx - 1); }
        else if (e.key === 'Enter') {
            var h = hits();
            if (activeIdx >= 0 && h[activeIdx]) {
                e.preventDefault();
                window.location.href = h[activeIdx].href;
            }
        }
        else if (e.key === 'Escape') { close(); input.blur(); }
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== input) close();
    });

    // Cmd/Ctrl+K focuses the topbar search from anywhere.
    document.addEventListener('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K')) {
            if (e.target && /^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && e.target !== input) {
                // typing inside another input — let it through
                return;
            }
            e.preventDefault();
            try { input.focus(); input.select(); } catch (err) {}
        }
    });
})();
