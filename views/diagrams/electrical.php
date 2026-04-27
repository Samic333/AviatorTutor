<div class="electrical-diagram-container">
    <!-- Header -->
    <div class="diagram-header">
        <h1>Q400 Electrical System</h1>
        <p class="subtitle">Interactive diagram showing power generation, distribution, and component status</p>
    </div>

    <!-- State Controls -->
    <div class="state-controls">
        <div class="control-group">
            <label>System State:</label>
            <div class="button-group">
                <button class="state-toggle active" data-state="normal">
                    <i data-lucide="power"></i> Normal Operation
                </button>
                <button class="state-toggle" data-state="gen1-fail">
                    <i data-lucide="alert-triangle"></i> Gen 1 Failure
                </button>
                <button class="state-toggle" data-state="gen2-fail">
                    <i data-lucide="alert-triangle"></i> Gen 2 Failure
                </button>
                <button class="state-toggle" data-state="both-fail">
                    <i data-lucide="alert-circle"></i> Both Generators Failed
                </button>
            </div>
        </div>
    </div>

    <!-- SVG Diagram -->
    <div class="svg-container">
        <svg viewBox="0 0 1000 700" xmlns="http://www.w3.org/2000/svg" class="electrical-diagram">
            <defs>
                <!-- Gradients -->
                <linearGradient id="activeGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#1E40AF;stop-opacity:1" />
                </linearGradient>
                <linearGradient id="inactiveGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" style="stop-color:#64748B;stop-opacity:0.5" />
                    <stop offset="100%" style="stop-color:#475569;stop-opacity:0.5" />
                </linearGradient>

                <!-- Markers for arrows -->
                <marker id="arrowActive" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                    <path d="M0,0 L0,6 L9,3 z" fill="#3B82F6" />
                </marker>
                <marker id="arrowInactive" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                    <path d="M0,0 L0,6 L9,3 z" fill="#64748B" />
                </marker>
            </defs>

            <!-- Title and Legend -->
            <g id="legend">
                <rect x="750" y="20" width="220" height="140" fill="#1E293B" stroke="rgba(255,255,255,0.1)" stroke-width="1" rx="6"/>
                <text x="760" y="45" font-size="14" font-weight="600" fill="#F8FAFC">Legend</text>

                <line x1="760" y1="55" x2="960" y2="55" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>

                <!-- Powered -->
                <circle cx="770" cy="70" r="4" fill="#3B82F6"/>
                <text x="785" y="75" font-size="12" fill="#94A3B8">Powered</text>

                <!-- De-energized -->
                <circle cx="770" cy="90" r="4" fill="#64748B"/>
                <text x="785" y="95" font-size="12" fill="#94A3B8">De-energized</text>

                <!-- Alert -->
                <circle cx="770" cy="110" r="4" fill="#EF4444"/>
                <text x="785" y="115" font-size="12" fill="#94A3B8">Failed</text>

                <!-- Source -->
                <rect x="760" y="125" width="8" height="8" fill="#10B981"/>
                <text x="785" y="132" font-size="12" fill="#94A3B8">Source</text>
            </g>

            <!-- Power Sources -->
            <!-- Generator 1 (Left) -->
            <g id="gen1-group" class="component-group active" data-state-class="gen1">
                <!-- Generator symbol -->
                <circle cx="100" cy="100" r="35" fill="url(#activeGradient)" stroke="#10B981" stroke-width="2" class="component" data-component="Gen 1"/>
                <text x="70" y="90" font-size="11" font-weight="600" fill="white">~</text>
                <text x="100" y="120" font-size="12" font-weight="600" fill="white" text-anchor="middle">Gen 1</text>
                <text x="100" y="135" font-size="10" fill="#94A3B8" text-anchor="middle">115VAC</text>

                <!-- Hotspot for interaction -->
                <circle cx="100" cy="100" r="38" fill="none" stroke="transparent" class="hotspot" data-label="Generator 1" data-description="Primary AC power source. 115/200V AC, 3-phase alternator."/>
            </g>

            <!-- Generator 2 (Right) -->
            <g id="gen2-group" class="component-group active" data-state-class="gen2">
                <!-- Generator symbol -->
                <circle cx="900" cy="100" r="35" fill="url(#activeGradient)" stroke="#10B981" stroke-width="2" class="component" data-component="Gen 2"/>
                <text x="870" y="90" font-size="11" font-weight="600" fill="white">~</text>
                <text x="900" y="120" font-size="12" font-weight="600" fill="white" text-anchor="middle">Gen 2</text>
                <text x="900" y="135" font-size="10" fill="#94A3B8" text-anchor="middle">115VAC</text>

                <!-- Hotspot -->
                <circle cx="900" cy="100" r="38" fill="none" stroke="transparent" class="hotspot" data-label="Generator 2" data-description="Backup AC power source. 115/200V AC, 3-phase alternator."/>
            </g>

            <!-- Battery (Bottom Left) -->
            <g id="battery-group" class="component-group active">
                <rect x="70" y="580" width="50" height="70" rx="4" fill="url(#activeGradient)" stroke="#10B981" stroke-width="2"/>
                <line x1="100" y1="580" x2="100" y2="570" stroke="#10B981" stroke-width="2"/>
                <line x1="95" y1="565" x2="105" y2="565" stroke="#10B981" stroke-width="2"/>
                <text x="100" y="625" font-size="12" font-weight="600" fill="white" text-anchor="middle">Battery</text>
                <text x="100" y="640" font-size="10" fill="#94A3B8" text-anchor="middle">28VDC</text>

                <!-- Hotspot -->
                <circle cx="95" cy="615" r="28" fill="none" stroke="transparent" class="hotspot" data-label="Main Battery" data-description="28VDC battery pack. Provides emergency backup power and engine start capability."/>
            </g>

            <!-- TRU (Transformer Rectifier Unit) -->
            <g id="tru-group" class="component-group active">
                <rect x="440" y="550" width="80" height="60" rx="4" fill="url(#activeGradient)" stroke="#10B981" stroke-width="2"/>
                <text x="480" y="575" font-size="11" font-weight="600" fill="white" text-anchor="middle">TRU</text>
                <text x="480" y="590" font-size="10" fill="#94A3B8" text-anchor="middle">Transformer</text>
                <text x="480" y="603" font-size="10" fill="#94A3B8" text-anchor="middle">Rectifier Unit</text>

                <!-- Hotspot -->
                <circle cx="480" cy="580" r="32" fill="none" stroke="transparent" class="hotspot" data-label="TRU (Transformer Rectifier Unit)" data-description="Converts AC power from generators to 28VDC. Two units provide redundancy."/>
            </g>

            <!-- Power Distribution -->
            <!-- AC Bus Bar -->
            <g id="ac-bus-group" class="component-group active">
                <line x1="200" y1="200" x2="800" y2="200" stroke="#3B82F6" stroke-width="3" class="active-bus"/>
                <circle cx="200" cy="200" r="5" fill="#10B981"/>
                <circle cx="500" cy="200" r="5" fill="#10B981"/>
                <circle cx="800" cy="200" r="5" fill="#10B981"/>
                <text x="500" y="225" font-size="13" font-weight="600" fill="#F8FAFC" text-anchor="middle">AC BUS (115/200V)</text>

                <!-- Hotspot -->
                <rect x="200" y="190" width="600" height="20" fill="none" stroke="transparent" class="hotspot" data-label="AC Bus" data-description="Main 3-phase AC power distribution. Fed by either or both generators."/>
            </g>

            <!-- DC Bus Bar -->
            <g id="dc-bus-group" class="component-group active">
                <line x1="200" y1="350" x2="800" y2="350" stroke="#3B82F6" stroke-width="3" class="active-bus"/>
                <circle cx="200" cy="350" r="5" fill="#10B981"/>
                <circle cx="500" cy="350" r="5" fill="#10B981"/>
                <circle cx="800" cy="350" r="5" fill="#10B981"/>
                <text x="500" y="375" font-size="13" font-weight="600" fill="#F8FAFC" text-anchor="middle">DC BUS (28V)</text>

                <!-- Hotspot -->
                <rect x="200" y="340" width="600" height="20" fill="none" stroke="transparent" class="hotspot" data-label="DC Bus" data-description="28VDC main power distribution. Fed by TRU or battery."/>
            </g>

            <!-- Essential Bus (DC) -->
            <g id="ess-bus-group" class="component-group active">
                <line x1="600" y1="450" x2="800" y2="450" stroke="#3B82F6" stroke-width="3" class="active-bus"/>
                <circle cx="600" cy="450" r="5" fill="#10B981"/>
                <circle cx="800" cy="450" r="5" fill="#10B981"/>
                <text x="700" y="475" font-size="13" font-weight="600" fill="#F8FAFC" text-anchor="middle">ESSENTIAL BUS (28V)</text>

                <!-- Hotspot -->
                <rect x="600" y="440" width="200" height="20" fill="none" stroke="transparent" class="hotspot" data-label="Essential Bus" data-description="Critical systems bus. Powers essential avionics and flight instruments."/>
            </g>

            <!-- Connection Lines -->
            <!-- Gen 1 to AC Bus -->
            <line x1="135" y1="100" x2="200" y2="200" stroke="#3B82F6" stroke-width="2" class="power-line active-line" marker-end="url(#arrowActive)"/>

            <!-- Gen 2 to AC Bus -->
            <line x1="865" y1="100" x2="800" y2="200" stroke="#3B82F6" stroke-width="2" class="power-line active-line" marker-end="url(#arrowActive)"/>

            <!-- AC Bus to TRU -->
            <line x1="500" y1="200" x2="480" y2="550" stroke="#3B82F6" stroke-width="2" class="power-line active-line" marker-end="url(#arrowActive)"/>

            <!-- TRU to DC Bus -->
            <line x1="480" y1="610" x2="500" y2="350" stroke="#3B82F6" stroke-width="2" class="power-line active-line" marker-end="url(#arrowActive)"/>

            <!-- Battery to DC Bus -->
            <line x1="120" y1="615" x2="200" y2="350" stroke="#3B82F6" stroke-width="2" class="power-line active-line" marker-end="url(#arrowActive)"/>

            <!-- DC Bus to Essential Bus -->
            <line x1="700" y1="350" x2="700" y2="450" stroke="#3B82F6" stroke-width="2" class="power-line active-line" marker-end="url(#arrowActive)"/>

            <!-- Status Labels -->
            <g id="status-indicators">
                <text x="100" y="160" font-size="11" font-weight="600" fill="#10B981" text-anchor="middle" class="status-label" id="gen1-status">Online</text>
                <text x="900" y="160" font-size="11" font-weight="600" fill="#10B981" text-anchor="middle" class="status-label" id="gen2-status">Online</text>
                <text x="95" y="660" font-size="11" font-weight="600" fill="#10B981" text-anchor="middle" class="status-label">Charged</text>
                <text x="480" y="625" font-size="11" font-weight="600" fill="#10B981" text-anchor="middle" class="status-label">Active</text>
            </g>

            <!-- Current Flow Animation (SVG arrows) -->
            <g id="flow-indicators" opacity="0.6">
                <!-- Flow indicator circles that move along paths -->
                <circle class="flow-marker" cx="150" cy="150" r="4" fill="#3B82F6" filter="url(#glow)"/>
            </g>
        </svg>
    </div>

    <!-- Component Info Panel -->
    <div class="info-panel">
        <h3>Component Details</h3>
        <div id="component-info" class="info-content">
            <p style="color: var(--color-muted-text);">Hover over a component to see details...</p>
        </div>
    </div>

    <!-- System Status Summary -->
    <div class="status-summary">
        <div class="status-card">
            <h4>Power Generation</h4>
            <div class="status-item">
                <span class="status-dot active" id="gen1-indicator"></span>
                <span>Generator 1: <span id="gen1-text" class="status-text">115V / 90A</span></span>
            </div>
            <div class="status-item">
                <span class="status-dot active" id="gen2-indicator"></span>
                <span>Generator 2: <span id="gen2-text" class="status-text">115V / 90A</span></span>
            </div>
        </div>

        <div class="status-card">
            <h4>Power Distribution</h4>
            <div class="status-item">
                <span class="status-dot active"></span>
                <span>AC Bus: <span class="status-text">Connected</span></span>
            </div>
            <div class="status-item">
                <span class="status-dot active"></span>
                <span>DC Bus: <span class="status-text">Connected</span></span>
            </div>
        </div>

        <div class="status-card">
            <h4>Backup Systems</h4>
            <div class="status-item">
                <span class="status-dot active"></span>
                <span>Battery: <span class="status-text">Fully Charged</span></span>
            </div>
            <div class="status-item">
                <span class="status-dot active"></span>
                <span>Essential Bus: <span class="status-text">Powered</span></span>
            </div>
        </div>
    </div>
</div>

<style>
.electrical-diagram-container {
    padding: 20px;
    background: var(--color-dark-bg);
    color: var(--color-white-text);
}

.diagram-header {
    margin-bottom: 30px;
}

.diagram-header h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
    color: var(--color-white-text);
}

.subtitle {
    margin: 0;
    color: var(--color-gray-text);
    font-size: 14px;
}

.state-controls {
    margin-bottom: 30px;
    padding: 16px;
    background: var(--color-slate-bg);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.control-group {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.control-group label {
    color: var(--color-white-text);
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
}

.button-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.state-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: var(--color-gray-text);
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s;
}

.state-toggle:hover {
    border-color: var(--color-blue-accent);
    color: var(--color-blue-accent);
}

.state-toggle.active {
    background: var(--color-blue-accent);
    color: white;
    border-color: var(--color-blue-accent);
}

.state-toggle i {
    width: 16px;
    height: 16px;
}

.svg-container {
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    overflow: auto;
}

.electrical-diagram {
    width: 100%;
    max-width: 100%;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
}

.component {
    cursor: pointer;
    transition: opacity 0.2s;
}

.component:hover {
    opacity: 0.8;
}

.hotspot {
    cursor: pointer;
}

.hotspot:hover {
    stroke: var(--color-blue-accent) !important;
    stroke-width: 2 !important;
}

.power-line {
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: stroke 0.3s;
}

.active-line {
    stroke: #3B82F6;
    filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.5));
}

.inactive-line {
    stroke: #64748B;
    opacity: 0.4;
}

.active-bus {
    filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.6));
}

.info-panel {
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-left: 3px solid var(--color-blue-accent);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.info-panel h3 {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--color-white-text);
}

.info-content {
    font-size: 14px;
    line-height: 1.6;
}

.status-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.status-card {
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 16px;
}

.status-card h4 {
    margin: 0 0 12px 0;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-white-text);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    font-size: 13px;
    color: var(--color-gray-text);
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
    transition: all 0.3s;
}

.status-dot.active {
    background: var(--color-success);
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
}

.status-dot.inactive {
    background: #64748B;
    box-shadow: none;
}

.status-dot.failed {
    background: var(--color-danger);
    box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
}

.status-text {
    color: var(--color-white-text);
    font-weight: 500;
}

@media (max-width: 768px) {
    .electrical-diagram-container {
        padding: 16px;
    }

    .diagram-header h1 {
        font-size: 22px;
    }

    .control-group {
        flex-direction: column;
        align-items: flex-start;
    }

    .button-group {
        width: 100%;
    }

    .state-toggle {
        flex: 1;
        justify-content: center;
    }

    .status-summary {
        grid-template-columns: 1fr;
    }
}

/* Animation for flow markers */
@keyframes flowAnimation {
    0% {
        cx: 150;
        cy: 150;
    }
    50% {
        cx: 400;
        cy: 250;
    }
    100% {
        cx: 650;
        cy: 350;
    }
}

.flow-marker {
    animation: flowAnimation 3s ease-in-out infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateToggles = document.querySelectorAll('.state-toggle');
    const diagram = document.querySelector('.electrical-diagram');
    const componentInfo = document.getElementById('component-info');

    // State configurations
    const stateConfig = {
        'normal': {
            gen1: { status: 'Online', color: '#10B981', lineOpacity: 1, busOpacity: 1 },
            gen2: { status: 'Online', color: '#10B981', lineOpacity: 1, busOpacity: 1 },
            battery: { status: 'Charged', color: '#10B981' },
            tru: { status: 'Active', color: '#10B981' },
            acBus: { status: 'Connected', color: '#3B82F6', opacity: 1 },
            dcBus: { status: 'Connected', color: '#3B82F6', opacity: 1 },
            essBus: { status: 'Powered', color: '#3B82F6', opacity: 1 },
        },
        'gen1-fail': {
            gen1: { status: 'Failed', color: '#EF4444', lineOpacity: 0.3 },
            gen2: { status: 'Online', color: '#10B981', lineOpacity: 1 },
            acBus: { status: 'Reduced Power', color: '#F59E0B', opacity: 0.7 },
        },
        'gen2-fail': {
            gen1: { status: 'Online', color: '#10B981', lineOpacity: 1 },
            gen2: { status: 'Failed', color: '#EF4444', lineOpacity: 0.3 },
            acBus: { status: 'Reduced Power', color: '#F59E0B', opacity: 0.7 },
        },
        'both-fail': {
            gen1: { status: 'Failed', color: '#EF4444', lineOpacity: 0.1 },
            gen2: { status: 'Failed', color: '#EF4444', lineOpacity: 0.1 },
            acBus: { status: 'No Power', color: '#64748B', opacity: 0.3 },
            battery: { status: 'Supplying Power', color: '#F59E0B' },
            tru: { status: 'Offline', color: '#64748B' },
        }
    };

    // State toggle click handlers
    stateToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const state = this.dataset.state;
            stateToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            updateDiagramState(state);
        });
    });

    // Update diagram based on state
    function updateDiagramState(state) {
        const config = stateConfig[state] || stateConfig['normal'];

        // Update Gen 1
        if (config.gen1) {
            updateComponent('gen1-group', config.gen1);
            document.getElementById('gen1-status').textContent = config.gen1.status;
            document.getElementById('gen1-indicator').className = `status-dot ${config.gen1.status === 'Failed' ? 'failed' : 'active'}`;
        }

        // Update Gen 2
        if (config.gen2) {
            updateComponent('gen2-group', config.gen2);
            document.getElementById('gen2-status').textContent = config.gen2.status;
            document.getElementById('gen2-indicator').className = `status-dot ${config.gen2.status === 'Failed' ? 'failed' : 'active'}`;
        }

        // Update Battery
        if (config.battery) {
            updateComponent('battery-group', config.battery);
        }

        // Update TRU
        if (config.tru) {
            updateComponent('tru-group', config.tru);
        }
    }

    function updateComponent(groupId, config) {
        const group = document.getElementById(groupId);
        if (!group) return;

        // Update stroke color
        const circles = group.querySelectorAll('circle[class="component"]');
        const rects = group.querySelectorAll('rect[class*="component"], rect:not(.legend)');
        const lines = group.querySelectorAll('line[class*="power-line"]');

        circles.forEach(el => {
            if (config.color === '#EF4444') {
                el.setAttribute('stroke', config.color);
            }
        });

        rects.forEach(el => {
            if (el.classList.contains('component') || !el.classList.length) {
                if (config.color === '#EF4444') {
                    el.setAttribute('stroke', config.color);
                }
            }
        });
    }

    // Hotspot hover handlers
    const hotspots = diagram.querySelectorAll('.hotspot');
    hotspots.forEach(hotspot => {
        hotspot.addEventListener('mouseenter', function() {
            const label = this.getAttribute('data-label');
            const description = this.getAttribute('data-description');
            componentInfo.innerHTML = `
                <h4 style="color: var(--color-white-text); margin: 0 0 8px 0; font-size: 14px;">${label}</h4>
                <p style="color: var(--color-gray-text); margin: 0; line-height: 1.6; font-size: 13px;">${description}</p>
            `;
        });
    });

    // Component click handlers
    const components = diagram.querySelectorAll('.component');
    components.forEach(component => {
        component.addEventListener('click', function() {
            const label = this.getAttribute('data-component');
            componentInfo.innerHTML = `<p style="color: var(--color-muted-text);">Selected: <strong style="color: var(--color-white-text);">${label}</strong></p>`;
        });
    });
});
</script>
