<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Q400 Study App</title>
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
                <p class="auth-subtitle">Create your account</p>
            </div>

            <!-- Error Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i data-lucide="alert-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Success Messages -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <i data-lucide="check-circle"></i>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" action="/register" class="auth-form">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                <input type="hidden" name="role" value="learner">

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        required 
                        autocomplete="name"
                        placeholder="John Doe"
                    >
                </div>

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
                            autocomplete="new-password"
                            placeholder="Minimum 8 characters"
                            minlength="8"
                        >
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility" onclick="togglePasswordVisibility('password')">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">Minimum 8 characters, include uppercase, lowercase, and numbers</small>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <div class="password-input-group">
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="form-control" 
                            required 
                            autocomplete="new-password"
                            placeholder="Re-enter your password"
                            minlength="8"
                        >
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility" onclick="togglePasswordVisibility('password_confirm')">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms Checkbox -->
                <div class="form-group checkbox-group">
                    <input 
                        type="checkbox" 
                        id="terms" 
                        name="accept_terms" 
                        class="form-checkbox"
                        value="1"
                        required
                    >
                    <label for="terms" class="checkbox-label">
                        I agree to the Terms of Service and Privacy Policy
                    </label>
                </div>

                <!-- Register Button -->
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="auth-divider">
                <span>Already have an account?</span>
            </div>

            <!-- Login Link -->
            <a href="/login" class="btn btn-secondary btn-lg btn-block">
                Sign In
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

        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const toggle = event.currentTarget;
            
            if (input.type === 'password') {
                input.type = 'text';
                toggle.classList.add('visible');
            } else {
                input.type = 'password';
                toggle.classList.remove('visible');
            }
        }
    </script>
</body>
</html>
