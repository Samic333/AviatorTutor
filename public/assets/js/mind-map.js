/* AviatorTutor — Phase 12 mind map renderer
 *
 * Vanilla SVG, no framework. Reads a JSON tree off
 *   <div id="mind-map" data-tree="<json>" data-system-id="N">
 * and renders it as a horizontal layout (root left, leaves right). On
 * first paint we fit-to-viewport so the whole tree is visible regardless
 * of node count. Click any node to populate the detail panel; pan via
 * drag, zoom via wheel/pinch or the toolbar buttons. Mobile collapses
 * the panel into a bottom sheet.
 */
(function () {
    'use strict';

    var ROW_HEIGHT  = 28;
    var COL_WIDTH   = 220;
    var NODE_PAD_X  = 12;

    var KIND_COLORS = {
        system:  '#38BDF8',
        lesson:  '#A78BFA',
        bucket:  '#FBBF24',
        leaf:    '#94A3B8'
    };
    var KIND_LABELS = {
        system: 'System',
        lesson: 'Lesson',
        bucket: 'Section group',
        leaf:   'Concept'
    };

    function $(sel, root) { return (root || document).querySelector(sel); }

    function svgEl(name, attrs) {
        var el = document.createElementNS('http://www.w3.org/2000/svg', name);
        if (attrs) {
            Object.keys(attrs).forEach(function (k) {
                if (k === 'text') el.textContent = attrs.text;
                else el.setAttribute(k, attrs[k]);
            });
        }
        return el;
    }

    function layout(node, depth, cursor) {
        node._depth = depth;
        if (!node.children || node.children.length === 0 || node._collapsed) {
            node._x = depth * COL_WIDTH;
            node._y = cursor.y * ROW_HEIGHT;
            cursor.y += 1;
            return;
        }
        var firstChildY = cursor.y;
        node.children.forEach(function (c) { layout(c, depth + 1, cursor); });
        var lastChildY = cursor.y - 1;
        node._x = depth * COL_WIDTH;
        node._y = ((firstChildY + lastChildY) / 2) * ROW_HEIGHT;
    }

    function flatten(node, out) {
        out.push(node);
        if (!node.children || node._collapsed) return;
        node.children.forEach(function (c) { flatten(c, out); });
    }

    var state = {
        tree: null,
        container: null,
        canvas: null,
        panel: null,
        svg: null,
        g: null,
        scale: 1, tx: 0, ty: 0,
        contentW: 0, contentH: 0,
        systemId: 0
    };

    function applyTransform() {
        if (!state.g) return;
        state.g.setAttribute('transform',
            'translate(' + state.tx + ',' + state.ty + ') scale(' + state.scale + ')');
    }

    function fitToViewport() {
        if (!state.svg || !state.contentW || !state.contentH) return;
        var rect = state.svg.getBoundingClientRect();
        var pad = 24;
        var sx = (rect.width  - pad * 2) / state.contentW;
        var sy = (rect.height - pad * 2) / state.contentH;
        var s  = Math.max(0.4, Math.min(1.0, Math.min(sx, sy)));
        state.scale = s;
        state.tx = pad;
        state.ty = (rect.height - state.contentH * s) / 2;
        applyTransform();
    }

    function setActive(nodeId) {
        if (!state.g) return;
        Array.prototype.forEach.call(state.g.querySelectorAll('.mm-node.is-active'), function (el) {
            el.classList.remove('is-active');
        });
        if (!nodeId) return;
        var sel = state.g.querySelector('.mm-node[data-id="' + cssEscape(nodeId) + '"]');
        if (sel) sel.classList.add('is-active');
    }
    function cssEscape(s) {
        return String(s).replace(/[^a-zA-Z0-9_-]/g, '\\$&');
    }

    function showPanel(node) {
        if (!state.panel) return;
        var ph    = $('#mm-panel-placeholder', state.panel);
        var body  = $('#mm-panel-body', state.panel);
        var kind  = $('#mm-panel-kind', state.panel);
        var title = $('#mm-panel-title', state.panel);
        var det   = $('#mm-panel-detail', state.panel);
        var links = $('#mm-panel-links', state.panel);
        if (!body || !title) return;

        if (ph) ph.hidden = true;
        body.hidden = false;

        var kindKey = String(node.kind || 'leaf');
        kind.textContent = KIND_LABELS[kindKey] || kindKey;
        kind.style.background = (KIND_COLORS[kindKey] || '#38BDF8') + '22';
        kind.style.color      = KIND_COLORS[kindKey] || '#38BDF8';
        title.textContent = String(node.label || '');
        det.textContent   = String(node.detail || (node.kind === 'system' || node.kind === 'lesson' ? 'Open this branch to see its sub-concepts, or jump to the related study surface below.' : ''));

        // Cross-links — every node always sees the four study modes for the
        // system the map belongs to so you can jump from any concept to its
        // flashcards / quiz / mnemonics without back-tracking.
        links.innerHTML = '';
        var sid = state.systemId;
        function addLink(href, label, secondary) {
            var a = document.createElement('a');
            a.href = href;
            a.className = 'mm-panel__link' + (secondary ? ' mm-panel__link--secondary' : '');
            a.textContent = label;
            links.appendChild(a);
        }
        if (node.href) addLink(node.href, 'Open this →');
        if (sid) {
            addLink('/flashcards/' + sid,         'Flashcards', true);
            addLink('/quiz?system=' + sid,        'Quiz',       true);
            addLink('/study/' + sid + '/mnemonics', 'Mnemonics', true);
        }

        if (window.innerWidth <= 900) state.panel.classList.add('mm-panel--open');
    }

    function hidePanel() {
        if (!state.panel) return;
        state.panel.classList.remove('mm-panel--open');
    }

    function render() {
        var tree = state.tree;
        var cursor = { y: 0 };
        layout(tree, 0, cursor);

        var nodes = [];
        flatten(tree, nodes);

        var maxX = 0, maxY = 0;
        nodes.forEach(function (n) {
            if (n._x > maxX) maxX = n._x;
            if (n._y > maxY) maxY = n._y;
        });
        var width  = maxX + COL_WIDTH;
        var height = Math.max(maxY + ROW_HEIGHT * 2, 200);
        state.contentW = width;
        state.contentH = height;

        var svgRect = state.canvas.getBoundingClientRect();
        var svg = svgEl('svg', {
            viewBox: '0 0 ' + Math.max(svgRect.width, 200) + ' ' + Math.max(svgRect.height, 200),
            width: '100%', height: Math.max(svgRect.height, 200),
            xmlns: 'http://www.w3.org/2000/svg',
            'aria-label': 'Mind map'
        });
        state.svg = svg;
        var g = svgEl('g', { id: 'mm-root' });
        state.g = g;
        svg.appendChild(g);

        // Edges
        nodes.forEach(function (n) {
            if (!n.children || n._collapsed) return;
            n.children.forEach(function (c) {
                var x1 = n._x + COL_WIDTH - 8;
                var y1 = n._y + ROW_HEIGHT / 2;
                var x2 = c._x + 8;
                var y2 = c._y + ROW_HEIGHT / 2;
                var mx = (x1 + x2) / 2;
                var path = svgEl('path', {
                    d: 'M ' + x1 + ' ' + y1 + ' C ' + mx + ' ' + y1 + ', ' + mx + ' ' + y2 + ', ' + x2 + ' ' + y2,
                    stroke: 'var(--thm-fg-muted, #94A3B8)',
                    'stroke-width': 1, fill: 'none', opacity: 0.45
                });
                g.appendChild(path);
            });
        });

        // Nodes
        nodes.forEach(function (n) {
            var label = String(n.label || '').slice(0, 80);
            var nodeW = Math.min(COL_WIDTH - 16, Math.max(110, 8 * label.length + NODE_PAD_X * 2));
            var color = KIND_COLORS[n.kind] || '#38BDF8';

            var group = svgEl('g', {
                transform: 'translate(' + n._x + ',' + n._y + ')',
                class: 'mm-node mm-' + n.kind,
                'data-id': n.id
            });
            group.style.cursor = 'pointer';

            var rect = svgEl('rect', {
                x: 0, y: 0, width: nodeW, height: ROW_HEIGHT - 4, rx: 6,
                fill: 'var(--thm-card, rgba(255,255,255,0.04))',
                stroke: color, 'stroke-width': 1.5
            });
            var txt = svgEl('text', {
                x: NODE_PAD_X, y: ROW_HEIGHT / 2 - 2,
                fill: 'var(--thm-fg, #F1F5F9)',
                'font-size': 12, 'font-weight': n.kind === 'system' ? 700 : 600,
                'dominant-baseline': 'middle',
                text: label
            });
            group.appendChild(rect);
            group.appendChild(txt);

            // Chevron for parent nodes — clicking it toggles collapse.
            if (n.children && n.children.length) {
                var chev = svgEl('text', {
                    x: nodeW - 12, y: ROW_HEIGHT / 2 - 2,
                    fill: color, 'font-size': 13, 'font-weight': 700,
                    'dominant-baseline': 'middle', 'text-anchor': 'end',
                    'data-mm-toggle': '1',
                    text: n._collapsed ? '+' : '−'
                });
                chev.style.cursor = 'pointer';
                group.appendChild(chev);
            }

            group.addEventListener('click', function (e) {
                if (e.target && e.target.getAttribute('data-mm-toggle') === '1') {
                    e.stopPropagation();
                    n._collapsed = !n._collapsed;
                    render();
                    return;
                }
                e.stopPropagation();
                setActive(n.id);
                showPanel(n);
            });

            g.appendChild(group);
        });

        state.canvas.querySelector('#mind-map').innerHTML = '';
        state.canvas.querySelector('#mind-map').appendChild(svg);
        attachPanZoom(svg, g);
        // Defer fit until the SVG actually has dimensions.
        requestAnimationFrame(fitToViewport);
    }

    function attachPanZoom(svg, g) {
        var dragging = false, sx = 0, sy = 0, didMove = false;

        svg.addEventListener('pointerdown', function (e) {
            // Don't start a pan if the click started inside a node — the
            // node click handler should win.
            if (e.target && e.target.closest('.mm-node')) return;
            dragging = true; didMove = false;
            sx = e.clientX - state.tx; sy = e.clientY - state.ty;
            svg.classList.add('mm-dragging');
            try { svg.setPointerCapture(e.pointerId); } catch (err) {}
        });
        svg.addEventListener('pointermove', function (e) {
            if (!dragging) return;
            state.tx = e.clientX - sx; state.ty = e.clientY - sy;
            didMove = true;
            applyTransform();
        });
        function endPan() { dragging = false; svg.classList.remove('mm-dragging'); }
        svg.addEventListener('pointerup', endPan);
        svg.addEventListener('pointercancel', endPan);

        svg.addEventListener('wheel', function (e) {
            e.preventDefault();
            var delta = -Math.sign(e.deltaY) * 0.1;
            zoomAt(state.scale + delta, e.clientX, e.clientY);
        }, { passive: false });
    }

    function zoomAt(targetScale, clientX, clientY) {
        var clamped = Math.max(0.3, Math.min(2.5, targetScale));
        if (clamped === state.scale) return;
        if (typeof clientX === 'number') {
            var rect = state.svg.getBoundingClientRect();
            var cx = clientX - rect.left;
            var cy = clientY - rect.top;
            state.tx = cx - (cx - state.tx) * (clamped / state.scale);
            state.ty = cy - (cy - state.ty) * (clamped / state.scale);
        }
        state.scale = clamped;
        applyTransform();
    }

    function init(containerSelector) {
        state.container = $(containerSelector || '#mind-map');
        if (!state.container) return;
        var raw = state.container.getAttribute('data-tree');
        if (!raw) return;
        try { state.tree = JSON.parse(raw); } catch (e) { return; }
        state.systemId = parseInt(state.container.getAttribute('data-system-id') || '0', 10) || 0;
        state.canvas = state.container.closest('.mm-canvas') || state.container.parentNode;
        state.panel  = $('#mm-panel');

        render();

        // Toolbar
        var inBtn  = $('#mm-zoom-in');
        var outBtn = $('#mm-zoom-out');
        var fitBtn = $('#mm-fit');
        if (inBtn)  inBtn.addEventListener('click',  function () { zoomAt(state.scale + 0.2); });
        if (outBtn) outBtn.addEventListener('click', function () { zoomAt(state.scale - 0.2); });
        if (fitBtn) fitBtn.addEventListener('click', fitToViewport);

        // Mobile bottom-sheet close
        var closeBtn = $('#mm-panel-close');
        if (closeBtn) closeBtn.addEventListener('click', hidePanel);

        // Re-fit on viewport resize.
        var resizeT = null;
        window.addEventListener('resize', function () {
            clearTimeout(resizeT);
            resizeT = setTimeout(fitToViewport, 120);
        });
    }

    window.MindMap = { init: init };
})();
