<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Q400 Study App'); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h1 class="app-title">Q400 Study</h1>
                <button class="sidebar-toggle" aria-label="Toggle sidebar">
                    <i data-lucide="menu"></i>
                </button>
            </div>

            <div class="sidebar-content">
                <!-- Main Navigation -->
                <div class="nav-section">
                    <a href="/dashboard" class="nav-item <?php echo ($currentPath === '/dashboard') ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/systems" class="nav-item <?php echo ($currentPath === '/systems') ? 'active' : ''; ?>">
                        <i data-lucide="layers"></i>
                        <span>Systems Library</span>
                    </a>
                </div>

                <!-- Study Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Study</div>
                    <a href="/flashcards" class="nav-item <?php echo (strpos($currentPath, '/flashcards') === 0) ? 'active' : ''; ?>">
                        <i data-lucide="credit-card"></i>
                        <span>Flashcards</span>
                    </a>
                    <a href="/quiz" class="nav-item <?php echo (strpos($currentPath, '/quiz') === 0) ? 'active' : ''; ?>">
                        <i data-lucide="check-square"></i>
                        <span>Quiz & Tests</span>
                    </a>
                </div>

                <!-- Progress Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Progress</div>
                    <a href="/progress" class="nav-item <?php echo ($currentPath === '/progress') ? 'active' : ''; ?>">
                        <i data-lucide="bar-chart-2"></i>
                        <span>My Progress</span>
                    </a>
                    <a href="/planner" class="nav-item <?php echo ($currentPath === '/planner') ? 'active' : ''; ?>">
                        <i data-lucide="calendar"></i>
                        <span>Study Planner</span>
                    </a>
                </div>

                <!-- Reference Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Reference</div>
                    <a href="/search" class="nav-item <?php echo ($currentPath === '/search') ? 'active' : ''; ?>">
                        <i data-lucide="search"></i>
                        <span>Search</span>
                    </a>
                </div>

                <!-- Admin Section (conditional) -->
                <?php if (!empty($currentUser) && $currentUser['role'] === 'admin'): ?>
                <div class="nav-section">
                    <div class="nav-section-title">Admin</div>
                    <a href="/admin/dashboard" class="nav-item <?php echo ($currentPath === '/admin/dashboard') ? 'active' : ''; ?>">
                        <i data-lucide="settings"></i>
                        <span>Content Manager</span>
                    </a>
                    <a href="/admin/import" class="nav-item <?php echo ($currentPath === '/admin/import') ? 'active' : ''; ?>">
                        <i data-lucide="upload"></i>
                        <span>Import Tool</span>
                    </a>
                    <a href="/admin/quizzes" class="nav-item <?php echo (strpos($currentPath, '/admin/quizzes') === 0) ? 'active' : ''; ?>">
                        <i data-lucide="plus-square"></i>
                        <span>Quiz Builder</span>
                    </a>
                    <a href="/admin/flashcards" class="nav-item <?php echo (strpos($currentPath, '/admin/flashcards') === 0) ? 'active' : ''; ?>">
                        <i data-lucide="credit-card"></i>
                        <span>Flashcards</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="topbar">
                <div class="breadcrumb-area">
                    <nav aria-label="Breadcrumb" class="breadcrumb">
                        <?php if (!empty($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                                <?php if ($index < count($breadcrumbs) - 1): ?>
                                    <a href="<?php echo htmlspecialchars($crumb['url']); ?>"><?php echo htmlspecialchars($crumb['label']); ?></a>
                                    <span class="breadcrumb-separator">/</span>
                                <?php else: ?>
                                    <span class="breadcrumb-current"><?php echo htmlspecialchars($crumb['label']); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="search-bar">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" placeholder="Search systems, flashcards..." class="search-input" id="globalSearch">
                </div>

                <div class="topbar-user">
                    <?php if (!empty($currentUser)): ?>
                        <div class="user-dropdown">
                            <button class="user-button" aria-label="User menu">
                                <img src="<?php echo htmlspecialchars($currentUser['avatar'] ?? '/assets/img/default-avatar.png'); ?>" alt="<?php echo htmlspecialchars($currentUser['name']); ?>" class="user-avatar">
                                <span class="user-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                                <i data-lucide="chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="/progress" class="dropdown-item">
                                    <i data-lucide="user"></i>
                                    <span>My Progress</span>
                                </a>
                                <a href="/planner" class="dropdown-item">
                                    <i data-lucide="calendar"></i>
                                    <span>Study Planner</span>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="/logout" class="dropdown-item text-danger">
                                    <i data-lucide="log-out"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Page Content -->
            <main class="content-area">
                <?php echo $content; ?>
            </main>

            <!-- Footer -->
            <footer class="app-footer">
                <p style="color:var(--color-muted-text);font-size:12px;text-align:center;">Q400 Study App &bull; Aviation Systems Trainer</p>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/diagram.js"></script>
    <script src="/assets/js/flashcard.js"></script>
    <script src="/assets/js/quiz.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
