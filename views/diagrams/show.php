<div class="diagram-viewer-container">
    <!-- System Header -->
    <div class="diagram-header">
        <a href="/systems/<?php echo htmlspecialchars($system['id']); ?>" class="btn-back">
            <i data-lucide="chevron-left"></i>
            Back to System
        </a>
        <div class="diagram-title-section">
            <h1><?php echo htmlspecialchars($diagram['title']); ?></h1>
            <p class="diagram-subtitle">
                <span class="system-badge" style="background-color: <?php echo htmlspecialchars($system['color_hex'] ?? '#3B82F6'); ?>20">
                    <i data-lucide="layers"></i>
                    <?php echo htmlspecialchars($system['name']); ?>
                </span>
            </p>
        </div>
    </div>

    <?php if (!empty($diagram['description'])): ?>
        <div class="diagram-description">
            <p><?php echo htmlspecialchars($diagram['description']); ?></p>
        </div>
    <?php endif; ?>

    <!-- State Selector (if multiple states exist) -->
    <?php if (!empty($states) && count($states) > 1): ?>
        <div class="state-selector">
            <div class="state-buttons">
                <?php foreach ($states as $state): ?>
                    <button class="state-btn" data-state="<?php echo htmlspecialchars($state['id']); ?>">
                        <?php echo htmlspecialchars($state['label']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="diagram-viewer-main">
        <!-- Diagram Content -->
        <div class="diagram-viewer">
            <?php if (!empty($diagram['image_path'])): ?>
                <div class="diagram-canvas" id="diagramCanvas">
                    <img src="<?php echo htmlspecialchars($diagram['image_path']); ?>" alt="<?php echo htmlspecialchars($diagram['title']); ?>" id="diagramImage">

                    <!-- Hotspots Overlay -->
                    <div class="diagram-overlay"></div>

                    <!-- Hotspot Tooltip -->
                    <div class="hotspot-tooltip" id="hotspotTooltip" style="display: none;">
                        <div class="tooltip-content">
                            <h4 id="tooltipLabel"></h4>
                            <p id="tooltipDescription"></p>
                            <div class="tooltip-footer">
                                <span class="tooltip-type" id="tooltipType"></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-diagram-state">
                    <i data-lucide="image"></i>
                    <p>No diagram image available</p>
                    <p class="text-muted">Upload a diagram image to display the system layout</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Legend/Components Panel -->
        <?php if (!empty($hotspots)): ?>
            <aside class="diagram-legend">
                <h3>Components</h3>
                <div class="components-list">
                    <?php foreach ($hotspots as $hotspot): ?>
                        <div class="component-item" data-hotspot-id="<?php echo htmlspecialchars($hotspot['id']); ?>">
                            <div class="component-marker" style="background-color: <?php echo htmlspecialchars($hotspot['color_hex'] ?? '#3B82F6'); ?>"></div>
                            <div class="component-info">
                                <h5><?php echo htmlspecialchars($hotspot['label']); ?></h5>
                                <?php if (!empty($hotspot['description'])): ?>
                                    <p class="component-description"><?php echo htmlspecialchars(substr($hotspot['description'], 0, 60)); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        <?php endif; ?>
    </div>

    <!-- Hotspot Detail Panel -->
    <div class="hotspot-detail-panel" id="hotspot-detail" style="display: none; margin-top: 24px;">
        <!-- Populated by JavaScript -->
    </div>

    <!-- Instructions -->
    <div class="diagram-instructions">
        <i data-lucide="info"></i>
        <p>Hover over the colored dots on the diagram to learn about each component</p>
    </div>
</div>

<style>
.diagram-viewer-container {
    padding: 20px;
}

.diagram-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 30px;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: var(--color-white-text);
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    flex-shrink: 0;
    margin-top: 4px;
}

.btn-back:hover {
    background: rgba(59, 130, 246, 0.1);
    border-color: var(--color-blue-accent);
}

.btn-back i {
    width: 20px;
    height: 20px;
}

.diagram-title-section h1 {
    margin: 0;
    font-size: 32px;
    color: var(--color-white-text);
}

.diagram-subtitle {
    margin-top: 12px;
}

.system-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--color-blue-accent);
    color: white;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.system-badge i {
    width: 16px;
    height: 16px;
}

.diagram-description {
    margin-bottom: 24px;
    padding: 16px;
    background: var(--color-slate-bg);
    border-left: 3px solid var(--color-blue-accent);
    border-radius: 6px;
}

.diagram-description p {
    margin: 0;
    color: var(--color-gray-text);
    line-height: 1.6;
}

.state-selector {
    margin-bottom: 24px;
}

.state-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.state-btn {
    padding: 8px 16px;
    background: var(--color-slate-bg);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: var(--color-gray-text);
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.state-btn:hover {
    border-color: var(--color-blue-accent);
    color: var(--color-blue-accent);
}

.state-btn.active {
    background: var(--color-blue-accent);
    border-color: var(--color-blue-accent);
    color: white;
}

.diagram-viewer-main {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 24px;
    margin-bottom: 24px;
}

@media (max-width: 1200px) {
    .diagram-viewer-main {
        grid-template-columns: 1fr;
    }
}

.diagram-viewer {
    background: var(--color-slate-bg);
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.diagram-canvas {
    position: relative;
    width: 100%;
    max-height: 600px;
    overflow: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-dark-bg);
    min-height: 300px;
}

.diagram-canvas img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    display: block;
}

.diagram-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.hotspot-tooltip {
    position: absolute;
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 12px;
    min-width: 200px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
    z-index: 10;
}

.tooltip-content h4 {
    margin: 0 0 8px 0;
    color: var(--color-white-text);
    font-size: 14px;
}

.tooltip-content p {
    margin: 0 0 8px 0;
    color: var(--color-gray-text);
    font-size: 12px;
    line-height: 1.4;
}

.tooltip-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
}

.tooltip-type {
    display: inline-block;
    background: var(--color-blue-accent);
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    text-transform: uppercase;
}

.empty-diagram-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 400px;
    color: var(--color-gray-text);
}

.empty-diagram-state i {
    font-size: 64px;
    color: var(--color-slate-bg);
    margin-bottom: 16px;
    width: 64px;
    height: 64px;
}

.empty-diagram-state p {
    margin: 0;
}

.text-muted {
    color: var(--color-muted-text) !important;
}

.diagram-legend {
    background: var(--color-slate-bg);
    border-radius: 8px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    max-height: 600px;
    overflow-y: auto;
}

.diagram-legend h3 {
    margin: 0 0 16px 0;
    color: var(--color-white-text);
    font-size: 14px;
    text-transform: uppercase;
    font-weight: 600;
}

.components-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.component-item {
    display: flex;
    gap: 10px;
    padding: 10px;
    background: var(--color-dark-bg);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.component-item:hover {
    background: rgba(59, 130, 246, 0.1);
}

.component-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 4px;
}

.component-info {
    flex: 1;
    min-width: 0;
}

.component-info h5 {
    margin: 0;
    color: var(--color-white-text);
    font-size: 12px;
    font-weight: 600;
}

.component-description {
    margin: 4px 0 0 0;
    color: var(--color-gray-text);
    font-size: 11px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.hotspot-detail-panel {
    background: var(--color-slate-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 20px;
    border-left: 3px solid var(--color-blue-accent);
}

.diagram-instructions {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 6px;
    color: var(--color-blue-accent);
    font-size: 13px;
}

.diagram-instructions i {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

.diagram-instructions p {
    margin: 0;
}

@media (max-width: 768px) {
    .diagram-viewer-container {
        padding: 16px;
    }

    .diagram-header {
        flex-direction: column;
        gap: 16px;
    }

    .diagram-title-section h1 {
        font-size: 24px;
    }

    .diagram-canvas {
        max-height: 400px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hotspotsData = <?php echo json_encode($hotspots ?? []); ?>;
    const statesData = <?php echo json_encode($states ?? []); ?>;
    const container = document.getElementById('diagramCanvas');

    // Initialize diagram engine
    let engine = null;
    if (container && hotspotsData.length > 0) {
        engine = new DiagramEngine('diagramCanvas');
        engine.loadHotspots(hotspotsData);
        if (statesData.length > 0) {
            engine.loadStates(statesData);
        }
    }

    // Component item click
    const componentItems = document.querySelectorAll('.component-item');
    componentItems.forEach(item => {
        item.addEventListener('click', function() {
            const hotspotId = this.dataset.hotspoId;
            const hotspot = hotspotsData.find(h => h.id == hotspotId);
            if (hotspot && engine) {
                engine.selectHotspot(hotspot);
            }
        });
    });

    // State button clicks
    const stateButtons = document.querySelectorAll('.state-btn');
    stateButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            stateButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // TODO: Load state if needed
        });
    });

    // Set first state as active
    if (stateButtons.length > 0) {
        stateButtons[0].classList.add('active');
    }
});
</script>
