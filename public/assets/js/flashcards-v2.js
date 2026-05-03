/* AviatorTutor — Phase 3 flashcards v2
 *
 * Swipeable deck on top of the existing /flashcards/review endpoint.
 * Wrong cards (rating=again=1) are pushed back into the queue ~10 minutes
 * later by the backend's SM2 update; in this session we also resurface
 * them immediately so the learner gets another shot before moving on.
 */
(function () {
    'use strict';

    var deck = document.getElementById('fcv2-deck');
    if (!deck) return;

    var csrf = window.AVFlashcardsCSRF || '';
    var cards = Array.prototype.slice.call(deck.querySelectorAll('.fcv2-card'));
    var stats = { right: 0, wrong: 0, remaining: cards.length };
    var topCard = null;

    function $(s, root) { return (root || document).querySelector(s); }
    function setStats() {
        var c = $('#fcv2-correct'), w = $('#fcv2-wrong'), r = $('#fcv2-remaining');
        if (c) c.textContent = String(stats.right);
        if (w) w.textContent = String(stats.wrong);
        if (r) r.textContent = String(stats.remaining);
    }

    function pickTop() {
        topCard = cards.length ? cards[cards.length - 1] : null;
        cards.forEach(function (c, i) {
            c.style.zIndex = String(i);
            c.style.pointerEvents = (i === cards.length - 1) ? 'auto' : 'none';
            c.style.transform = (i === cards.length - 1)
                ? 'translateY(0) scale(1)'
                : 'translateY(' + ((cards.length - 1 - i) * 6) + 'px) scale(' + (1 - (cards.length - 1 - i) * 0.02) + ')';
        });
    }

    function flip(card) {
        var f = card.querySelector('.fcv2-front');
        var b = card.querySelector('.fcv2-back');
        if (!f || !b) return;
        f.hidden = !f.hidden;
        b.hidden = !b.hidden;
    }

    function gradeAndAdvance(card, correct) {
        if (!card) return;
        if (correct) stats.right++; else stats.wrong++;
        stats.remaining = Math.max(0, stats.remaining - 1);
        setStats();

        // POST to existing review endpoint. Backend already handles SM2.
        var fd = new FormData();
        fd.append('csrf_token', csrf);
        fd.append('card_id', card.getAttribute('data-card-id'));
        // Existing endpoint expects rating 1=again, 3=hard, 4=good, 5=easy.
        fd.append('rating', correct ? '4' : '1');
        fetch('/flashcards/review', { method: 'POST', body: fd, credentials: 'same-origin' })
            .catch(function () { /* silent — toast.js will surface on hard failure */ });

        card.classList.add(correct ? 'fcv2-leave-right' : 'fcv2-leave-left');
        var idx = cards.indexOf(card);
        setTimeout(function () {
            cards.splice(idx, 1);
            if (!correct) {
                // Resurface wrong cards near the front of the deck.
                card.classList.remove('fcv2-leave-left');
                card.style.transform = '';
                // Reset to front face for the second shot.
                var f = card.querySelector('.fcv2-front'); var b = card.querySelector('.fcv2-back');
                if (f && b) { f.hidden = false; b.hidden = true; }
                cards.unshift(card);
                stats.remaining++;
                setStats();
            } else {
                card.remove();
            }
            if (cards.length === 0) {
                deck.innerHTML = '<div style="margin:auto;text-align:center;padding:32px;font-size:15px;color:var(--thm-fg,#F1F5F9);"><strong>Deck cleared.</strong><br><span style="color:var(--thm-fg-muted,#94A3B8);">Come back when more cards are due.</span></div>';
                return;
            }
            pickTop();
        }, 220);
    }

    // ---- Click handlers ----
    deck.addEventListener('click', function (e) {
        var t = e.target;
        if (!(t instanceof Element)) return;
        if (t.matches('[data-fcv2-flip]')) {
            var c = t.closest('.fcv2-card');
            if (c) flip(c);
            return;
        }
        var grade = t.getAttribute('data-fcv2-grade');
        if (grade) {
            var card = t.closest('.fcv2-card');
            gradeAndAdvance(card, grade === 'right');
            return;
        }
    });

    // ---- Keyboard ----
    document.addEventListener('keydown', function (e) {
        if (e.target && /^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName)) return;
        if (!topCard) return;
        if (e.key === ' ') { e.preventDefault(); flip(topCard); }
        else if (e.key === 'ArrowRight') gradeAndAdvance(topCard, true);
        else if (e.key === 'ArrowLeft')  gradeAndAdvance(topCard, false);
    });

    // ---- Swipe (pointer events, no library) ----
    var startX = 0, startY = 0, dragging = false;
    deck.addEventListener('pointerdown', function (e) {
        if (!topCard) return;
        if (!topCard.contains(e.target)) return;
        dragging = true;
        startX = e.clientX; startY = e.clientY;
        topCard.style.transition = 'none';
    });
    deck.addEventListener('pointermove', function (e) {
        if (!dragging || !topCard) return;
        var dx = e.clientX - startX, dy = e.clientY - startY;
        topCard.style.transform = 'translate(' + dx + 'px, ' + dy + 'px) rotate(' + (dx * 0.05) + 'deg)';
    });
    function endDrag(e) {
        if (!dragging || !topCard) return;
        dragging = false;
        topCard.style.transition = '';
        var dx = (e.clientX || 0) - startX;
        if (Math.abs(dx) > 80) {
            gradeAndAdvance(topCard, dx > 0);
        } else {
            topCard.style.transform = '';
        }
    }
    deck.addEventListener('pointerup', endDrag);
    deck.addEventListener('pointercancel', endDrag);

    setStats();
    pickTop();
})();
