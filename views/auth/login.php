<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Q400 Study App</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo/Header -->
            <div class="auth-header">
                <div class="auth-logo">
                    <i data-lucide="plane" class="logo-icon"></i>
                </div>
                <h1>Q400 Study App</h1>
                <p class="auth-subtitle">Welcome back</p>
            </div>

            <!-- Error Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i data-lucide="alert-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="/login" class="auth-form">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required 
                        autocomplete="email"
                        placeholder="you@example.com"
                    >
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            required 
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        >
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility" onclick="togglePasswordVisibility()">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-group checkbox-group">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember_me" 
                        class="form-checkbox"
                        value="1"
                    >
                    <label for="remember" class="checkbox-label">Remember me</label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="auth-divider">
                <span>Don't have an account?</span>
            </div>

            <!-- Register Link -->
            <a href="/register" class="btn btn-secondary btn-lg btn-block">
                Create Account
            </a>

            <!-- Footer -->
            <div class="auth-footer">
                <p class="text-muted">Q400 Aircraft Systems Study Application</p>
                <p class="text-muted small">For training and educational purposes</p>
            </div>
        </div>

        <!-- Background Design -->
        <div class="auth-bg-pattern"></div>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        lucide.createIcons();

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggle = event.currentTarget;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggle.classList.add('visible');
            } else {
                passwordInput.type = 'password';
                toggle.classList.remove('visible');
            }
        }
    </script>
</body>
</html>
