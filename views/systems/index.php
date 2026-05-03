<div class="systems-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Q400 Systems Library</h1>
        <p class="subtitle">Complete technical knowledge of all aircraft systems</p>
    </div>

    <?php if (!empty($systemPickerV2 ?? false)): ?>
      <!-- Phase 4 v2 picker: search + jump dropdown + tiles grouped by
           category. Falls through to the legacy filter-bar + grid when
           system_picker_v2 is off. -->
      <?php include __DIR__ . '/../partials/system-picker.php'; ?>
    <?php else: ?>
    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-group">
            <input 
                type="text" 
                class="search-input" 
                id="systemSearch" 
                placeholder="Search systems by name or ATA code..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
            >
        </div>

        <div class="filter-group">
            <select id="difficultyFilter" class="filter-select">
                <option value="">All Difficulties</option>
                <option value="basic" <?php echo ($_GET['difficulty'] ?? '') === 'basic' ? 'selected' : ''; ?>>Basic</option>
                <option value="intermediate" <?php echo ($_GET['difficulty'] ?? '') === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                <option value="advanced" <?php echo ($_GET['difficulty'] ?? '') === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
            </select>
        </div>

        <div class="filter-group">
            <button class="btn btn-secondary btn-sm" id="resetFilters">Reset Filters</button>
        </div>
    </div>

    <!-- Systems Grid -->
    <?php if (!empty($systems)): ?>
        <div class="systems-grid">
            <?php foreach ($systems as $system):
                $isLocked = !empty($system['locked']);
            ?>
                <div class="system-card<?php echo $isLocked ? ' system-card--locked' : ''; ?>"
                     <?php if ($isLocked): ?>style="opacity:0.55;"<?php endif; ?>>
                    <!-- Color Border -->
                    <div class="system-card-color" style="background-color: <?php echo htmlspecialchars($system['color'] ?? '#34d399'); ?>"></div>
                    <?php if ($isLocked): ?>
                      <div style="position:absolute; top:10px; right:10px; padding:3px 10px; border-radius:999px;
                                  background:rgba(0,0,0,0.6); color:#fbbf24; font-size:11px; font-weight:700;
                                  letter-spacing:0.5px; text-transform:uppercase; z-index:2;">
                        🔒 Locked
                      </div>
                    <?php endif; ?>

                    <!-- Card Content -->
                    <div class="system-card-body">
                        <!-- Icon and Name -->
                        <div class="system-card-header">
                            <div class="system-icon">
                                <i data-lucide="<?php echo htmlspecialchars($system['icon'] ?? 'zap'); ?>"></i>
                            </div>
                            <div class="system-title">
                                <h3 class="system-name"><?php echo htmlspecialchars($system['name']); ?></h3>
                                <span class="system-ata-badge"><?php echo htmlspecialchars($system['ata_code']); ?></span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="system-description">
                            <?php 
                                $desc = $system['description'] ?? '';
                                echo htmlspecialchars(strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc);
                            ?>
                        </p>

                        <!-- Progress Bar -->
                        <div class="system-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?>%"></div>
                            </div>
                            <span class="progress-text"><?php echo htmlspecialchars($system['completion_percentage'] ?? 0); ?>% Complete</span>
                        </div>

                        <!-- Difficulty Badge -->
                        <div class="system-meta">
                            <span class="difficulty-badge difficulty-<?php echo htmlspecialchars($system['difficulty'] ?? 'basic'); ?>">
                                <?php echo htmlspecialchars(ucfirst($system['difficulty'] ?? 'Basic')); ?>
                            </span>
                            <span class="topic-count">
                                <i data-lucide="layers"></i>
                                <?php echo htmlspecialchars($system['topic_count'] ?? 0); ?> Topics
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="system-actions">
                            <?php if ($isLocked): ?>
                                <span class="btn btn-secondary btn-sm" style="cursor:not-allowed; pointer-events:none;">
                                    <i data-lucide="lock"></i>
                                    Complete <?php echo htmlspecialchars($system['unlock_blocker_name'] ?? 'previous system'); ?> first
                                </span>
                            <?php else: ?>
                                <a href="/study/<?php echo htmlspecialchars($system['id']); ?>" class="btn btn-primary btn-sm">
                                    <i data-lucide="book-open"></i>
                                    Study
                                </a>
                                <a href="/study/<?php echo htmlspecialchars($system['id']); ?>/revision" class="btn btn-secondary btn-sm">
                                    <i data-lucide="zap"></i>
                                    Quick Revision
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">
                <i data-lucide="inbox"></i>
            </div>
            <h2>No Systems Found</h2>
            <p>Try adjusting your filters or search criteria</p>
        </div>
    <?php endif; ?>
    <?php endif; // systemPickerV2 fallthrough ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('systemSearch');
    const difficultyFilter = document.getElementById('difficultyFilter');
    const resetBtn = document.getElementById('resetFilters');

    if (searchInput || difficultyFilter) {
        function applyFilters() {
            const search = searchInput ? searchInput.value : '';
            const difficulty = difficultyFilter ? difficultyFilter.value : '';
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (difficulty) params.append('difficulty', difficulty);
            
            window.location.href = '/systems' + (params.toString() ? '?' + params.toString() : '');
        }

        if (searchInput) {
            searchInput.addEventListener('change', applyFilters);
        }
        if (difficultyFilter) {
            difficultyFilter.addEventListener('change', applyFilters);
        }
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            window.location.href = '/systems';
        });
    }

    lucide.createIcons();
});
</script>
