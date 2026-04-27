/**
 * Q400 Interactive Diagram Engine
 * SVG overlay with hotspots, state toggling, flow animations
 */
class DiagramEngine {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.hotspots = [];
        this.states = {};
        this.currentState = 'normal';
        this.tooltip = null;
        this.init();
    }

    init() {
        if (!this.container) return;
        this.createTooltip();
        this.bindGlobalEvents();
    }

    createTooltip() {
        // Create floating tooltip element
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'diagram-tooltip';
        this.tooltip.style.cssText = `
            position: fixed; display: none; z-index: 1000;
            background: #1E293B; border: 1px solid rgba(148,163,184,0.2);
            border-radius: 8px; padding: 12px 16px;
            max-width: 280px; box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            pointer-events: none;
        `;
        document.body.appendChild(this.tooltip);
    }

    loadHotspots(hotspotsData) {
        // hotspotsData: array of {id, label, description, x_pct, y_pct, color_hex, hotspot_type}
        this.hotspots = hotspotsData;
        this.renderHotspots();
    }

    loadStates(statesData) {
        // statesData: array of {state_name, state_label, description, hotspot_overrides}
        statesData.forEach(state => {
            this.states[state.state_name] = state;
        });
    }

    renderHotspots() {
        const overlay = this.container.querySelector('.diagram-overlay');
        if (!overlay) return;

        overlay.innerHTML = '';

        this.hotspots.forEach(hotspot => {
            const pin = document.createElement('div');
            pin.className = `diagram-pin diagram-pin-${hotspot.hotspot_type || 'component'}`;
            pin.dataset.hotspotId = hotspot.id;
            pin.style.cssText = `
                position: absolute;
                left: ${hotspot.x_pct}%;
                top: ${hotspot.y_pct}%;
                transform: translate(-50%, -50%);
                width: 24px; height: 24px;
                border-radius: 50%;
                background: ${hotspot.color_hex || '#3B82F6'};
                border: 2px solid white;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 0 0 4px rgba(59,130,246,0.3);
                z-index: 10;
                display: flex; align-items: center; justify-content: center;
            `;

            // Inner dot
            const dot = document.createElement('div');
            dot.style.cssText = 'width: 8px; height: 8px; border-radius: 50%; background: white;';
            pin.appendChild(dot);

            // Label below pin
            if (hotspot.label) {
                const label = document.createElement('div');
                label.style.cssText = `
                    position: absolute; top: 100%; left: 50%;
                    transform: translateX(-50%); margin-top: 4px;
                    font-size: 10px; font-weight: 600; color: white;
                    white-space: nowrap; text-shadow: 0 1px 2px rgba(0,0,0,0.8);
                    pointer-events: none;
                `;
                label.textContent = hotspot.label;
                pin.appendChild(label);
            }

            // Events
            pin.addEventListener('mouseenter', (e) => this.showTooltip(e, hotspot));
            pin.addEventListener('mouseleave', () => this.hideTooltip());
            pin.addEventListener('mousemove', (e) => this.moveTooltip(e));
            pin.addEventListener('click', () => this.selectHotspot(hotspot));

            overlay.appendChild(pin);
        });
    }

    showTooltip(event, hotspot) {
        this.tooltip.innerHTML = `
            <div style="font-size:13px;font-weight:600;color:#F8FAFC;margin-bottom:6px;">${hotspot.label || ''}</div>
            <div style="font-size:12px;color:#94A3B8;line-height:1.5;">${hotspot.description || 'Component information'}</div>
            <div style="font-size:10px;color:#64748B;margin-top:6px;text-transform:uppercase;letter-spacing:0.05em;">${hotspot.hotspot_type || 'component'}</div>
        `;
        this.tooltip.style.display = 'block';
        this.moveTooltip(event);
    }

    hideTooltip() {
        this.tooltip.style.display = 'none';
    }

    moveTooltip(event) {
        const x = event.clientX + 16;
        const y = event.clientY - 8;
        const maxX = window.innerWidth - 300;
        const maxY = window.innerHeight - 200;
        this.tooltip.style.left = Math.min(x, maxX) + 'px';
        this.tooltip.style.top = Math.min(y, maxY) + 'px';
    }

    selectHotspot(hotspot) {
        // Highlight selected pin
        this.container.querySelectorAll('.diagram-pin').forEach(p => {
            p.style.boxShadow = '0 0 0 4px rgba(59,130,246,0.3)';
        });

        const selectedPin = this.container.querySelector(`[data-hotspot-id="${hotspot.id}"]`);
        if (selectedPin) {
            selectedPin.style.boxShadow = '0 0 0 6px rgba(59,130,246,0.6)';
        }

        // Show detail panel
        const detailPanel = document.getElementById('hotspot-detail');
        if (detailPanel) {
            detailPanel.innerHTML = `
                <h4 style="color:#F8FAFC;font-size:15px;margin-bottom:8px;">${hotspot.label}</h4>
                <p style="color:#94A3B8;font-size:13px;line-height:1.6;">${hotspot.description || 'No additional information available.'}</p>
            `;
        }
    }

    setState(stateName) {
        if (!this.states[stateName]) return;

        this.currentState = stateName;
        const state = this.states[stateName];

        // Apply hotspot overrides for this state
        if (state.hotspot_overrides) {
            const overrides = typeof state.hotspot_overrides === 'string'
                ? JSON.parse(state.hotspot_overrides)
                : state.hotspot_overrides;

            overrides.forEach(override => {
                const pin = this.container.querySelector(`[data-hotspot-id="${override.id}"]`);
                if (pin) {
                    if (override.color) pin.style.background = override.color;
                    if (override.hidden) pin.style.display = 'none';
                    if (!override.hidden) pin.style.display = 'flex';
                }
            });
        }

        // Update state indicator
        const stateLabel = document.getElementById('current-state-label');
        if (stateLabel) stateLabel.textContent = state.state_label || stateName;

        // Emit event
        this.container.dispatchEvent(new CustomEvent('stateChange', { detail: { state: stateName } }));
    }

    animateFlow(pathId, color = '#3B82F6', duration = 2000) {
        // Animate a flow path (SVG or CSS animation)
        const path = document.getElementById(pathId);
        if (!path) return;

        path.style.strokeDasharray = '10 5';
        path.style.animation = `flowAnimation ${duration}ms linear infinite`;
        path.style.stroke = color;
    }

    stopFlow(pathId) {
        const path = document.getElementById(pathId);
        if (!path) return;
        path.style.animation = '';
    }

    bindGlobalEvents() {
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.hideTooltip();
        });
    }

    destroy() {
        if (this.tooltip) this.tooltip.remove();
    }
}

// CSS for flow animation
const diagramStyle = document.createElement('style');
diagramStyle.textContent = `
    @keyframes flowAnimation {
        to { stroke-dashoffset: -15; }
    }
    .diagram-pin:hover {
        transform: translate(-50%, -50%) scale(1.2) !important;
    }
    .diagram-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        pointer-events: none;
    }
    .diagram-overlay .diagram-pin {
        pointer-events: all;
    }
    .diagram-container {
        position: relative;
        user-select: none;
    }
    .diagram-container img {
        width: 100%;
        height: auto;
        display: block;
    }
`;
document.head.appendChild(diagramStyle);

// Global instance registry
window.DiagramEngine = DiagramEngine;
window.diagramInstances = {};

// Auto-initialize diagrams on page load
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('[data-diagram]');
    containers.forEach(container => {
        const id = container.id;
        if (id) {
            window.diagramInstances[id] = new DiagramEngine(id);
        }
    });
});
