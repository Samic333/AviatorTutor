<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Q400 Study App</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .error-content {
            text-align: center;
            max-width: 500px;
        }

        .error-icon {
            font-size: 120px;
            color: #3B82F6;
            margin-bottom: 30px;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }

        .error-icon i {
            width: 120px;
            height: 120px;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .error-code {
            font-size: 96px;
            font-weight: 700;
            color: #F8FAFC;
            margin: 20px 0;
            letter-spacing: 4px;
            text-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
        }

        .error-title {
            font-size: 32px;
            font-weight: 600;
            color: #F8FAFC;
            margin: 20px 0;
        }

        .error-message {
            color: #94A3B8;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .error-decoration {
            margin: 30px 0;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #3B82F6;
            opacity: 0.5;
        }

        .dot:nth-child(2) {
            opacity: 1;
        }

        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #3B82F6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563EB;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: #1E293B;
            color: #94A3B8;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
            border-color: #3B82F6;
        }

        .btn i {
            width: 18px;
            height: 18px;
        }

        .quick-links {
            margin-top: 50px;
            padding-top: 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .quick-links-title {
            color: #64748B;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
        }

        .quick-link {
            padding: 12px;
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            color: #94A3B8;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s;
        }

        .quick-link:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: #3B82F6;
            color: #3B82F6;
        }

        @media (max-width: 600px) {
            .error-code {
                font-size: 64px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-message {
                font-size: 14px;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">
                <i data-lucide="compass"></i>
            </div>

            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>

            <p class="error-message">
                We couldn't find the page you're looking for. It may have been moved, deleted, or the URL might be incorrect.
            </p>

            <div class="error-decoration">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>

            <div class="error-actions">
                <a href="/dashboard" class="btn btn-primary">
                    <i data-lucide="home"></i>
                    Go to Dashboard
                </a>
                <button class="btn btn-secondary" onclick="history.back()">
                    <i data-lucide="arrow-left"></i>
                    Go Back
                </button>
            </div>

            <div class="quick-links">
                <div class="quick-links-title">Quick Navigation</div>
                <div class="quick-links-grid">
                    <a href="/systems" class="quick-link">Systems</a>
                    <a href="/flashcards" class="quick-link">Flashcards</a>
                    <a href="/quiz" class="quick-link">Quizzes</a>
                    <a href="/progress" class="quick-link">Progress</a>
                    <a href="/search" class="quick-link">Search</a>
                    <a href="/planner" class="quick-link">Planner</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
