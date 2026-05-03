/* AviatorTutor — Phase 3 mind map renderer
 *
 * Vanilla SVG, no framework. Reads a JSON tree off
 *   <div id="mind-map" data-tree="<json>">
 * and renders it as a horizontal layout (root on the left, leaves on the
 * right). Pan via drag, zoom via wheel/pinch, click-to-collapse on any
 * non-leaf node. Designed to be compact (~250 LOC) so the file ships
 * inside the existing static-asset budget.
 */
(function () {
    'use strict';

    var ROW_HEIGHT  = 28;
    var COL_WIDTH   = 220;
    var NODE_PAD_X  = 12;
    var NODE_PAD_Y  = 6;

    function $(sel, root) { return (root || document).querySelector(sel); }

    function layout(node, depth, cursor) {
        // Pre-order walk: assign x by depth, y by next available row.
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

    function render(container, tree) {
        // Reset layout state.
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

        var svg = svgEl('svg', {
            viewBox: '0 0 ' + width + ' ' + height,
            width: '100%', height: height,
            xmlns: 'http://www.w3.org/2000/svg',
            'aria-label': 'Mind map'
        });

        var g = svgEl('g', { id: 'mm-root', transform: 'translate(0,0)' });
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
            var maxChars = label.length;
            var nodeW = Math.min(COL_WIDTH - 16, Math.max(110, 8 * maxChars + NODE_PAD_X * 2));
            var nx = n._x;
            var ny = n._y;

            var color = '#38BDF8';
            if (n.kind === 'system')  color = 'var(--thm-accent, #38BDF8)';
            if (n.kind === 'lesson')  color = '#A78BFA';
            if (n.kind === 'bucket')  color = '#FBBF24';
            if (n.kind === 'leaf')    color = 'var(--thm-fg-muted, #94A3B8)';

            var group = svgEl('g', { transform: 'translate(' + nx + ',' + ny + ')', class: 'mm-node mm-' + n.kind, 'data-id': n.id });
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

            if (n.children && n.children.length) {
                group.style.cursor = 'pointer';
                group.addEventListener('click', function (e) {
                    e.stopPropagation();
                    n._collapsed = !n._collapsed;
                    redraw();
                });
            } else if (n.href) {
                group.style.cursor = 'pointer';
                group.addEventListener('click', function () { window.location.href = n.href; });
            }

            g.appendChild(group);
        });

        container.innerHTML = '';
        container.appendChild(svg);
        attachPanZoom(svg, g);
    }

    function attachPanZoom(svg, g) {
        var dragging = false, sx = 0, sy = 0;
        var tx = 0, ty = 0, scale = 1;

        function apply() {
            g.setAttribute('transform', 'translate(' + tx + ',' + ty + ') scale(' + scale + ')');
        }
        svg.addEventListener('pointerdown', function (e) {
            dragging = true; sx = e.clientX - tx; sy = e.clientY - ty;
            svg.setPointerCapture(e.pointerId);
        });
        svg.addEventListener('pointermove', function (e) {
            if (!dragging) return;
            tx = e.clientX - sx; ty = e.clientY - sy; apply();
        });
        svg.addEventListener('pointerup', function () { dragging = false; });
        svg.addEventListener('pointercancel', function () { dragging = false; });

        svg.addEventListener('wheel', function (e) {
            e.preventDefault();
            var delta = -Math.sign(e.deltaY) * 0.1;
            scale = Math.max(0.4, Math.min(2.5, scale + delta));
            apply();
        }, { passive: false });
    }

    var redraw;

    function init(containerSelector) {
        var container = $(containerSelector || '#mind-map');
        if (!container) return;
        var raw = container.getAttribute('data-tree');
        if (!raw) return;
        var tree;
        try { tree = JSON.parse(raw); } catch (e) { return; }
        redraw = function () { render(container, tree); };
        redraw();
    }

    window.MindMap = { init: init };
})();
