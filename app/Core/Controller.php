<?php
/**
 * Base Controller
 *
 * Abstract base class for all controllers with common methods
 */

namespace App\Core;

abstract class Controller
{
    /**
     * Current request
     */
    protected Request $request;

    /**
     * Current response
     */
    protected Response $response;

    /**
     * View renderer
     */
    protected View $view;

    /**
     * Authentication manager
     */
    protected Auth $auth;

    /**
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request = null, Response $response = null)
    {
        $this->request  = $request  ?? new Request();
        $this->response = $response ?? new Response();
        $this->view     = new View();
        $this->auth     = new Auth();

        // Keep session role in sync with DB — prevents stale admin access after role change
        if (Auth::check()) {
            $fresh = DB::instance()->queryOne('SELECT role FROM users WHERE id = ?', [Auth::id()]);
            if ($fresh !== null) {
                Auth::update(['role' => $fresh['role']]);
            }
        }
    }

    /**
     * Render a view template with layout
     *
     * @param string $template Template name (e.g., 'dashboard/index')
     * @param array $data Data to pass to template
     * @param string $layout Layout name (default: 'app')
     * @return string Rendered HTML
     */
    protected function view(string $template, array $data = [], string $layout = 'app'): string
    {
        // Auto-inject current user and path into every view
        $data = array_merge([
            'currentUser' => Auth::user(),
            'currentPath' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
        ], $data);
        return $this->view->renderWithLayout($template, $layout, $data);
    }

    /**
     * Render without layout
     *
     * @param string $template Template name
     * @param array $data Data to pass
     * @return string
     */
    protected function render(string $template, array $data = []): string
    {
        return $this->view->render($template, $data);
    }

    /**
     * Send response with view
     *
     * @param string $template Template name
     * @param array $data Data to pass
     * @param string $layout Layout name
     * @return void
     */
    protected function renderView(string $template, array $data = [], string $layout = 'app'): void
    {
        $html = $this->view($template, $data, $layout);
        $this->response->html($html);
    }

    /**
     * Redirect to URL
     *
     * @param string $url Target URL
     * @param int $code HTTP status code
     * @return void
     */
    protected function redirect(string $url, int $code = 302): void
    {
        $this->response->redirect($url, $code);
    }

    /**
     * Redirect back to previous page
     *
     * @return void
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    /**
     * Send JSON response
     *
     * @param mixed $data Data to encode
     * @param int $code HTTP status code
     * @return void
     */
    protected function json(mixed $data, int $code = 200): void
    {
        $this->response->json($data, $code);
    }

    /**
     * Send success JSON response
     *
     * @param mixed $data Data to send
     * @param string $message Success message
     * @param int $code HTTP status code
     * @return void
     */
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200): void
    {
        $response = ['message' => $message, 'success' => true];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->response->json($response, $code);
    }

    /**
     * Send error JSON response
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @return void
     */
    protected function error(string $message, int $code = 400): void
    {
        $this->response->json(['message' => $message, 'error' => true], $code);
    }

    /**
     * Set response status code
     *
     * @param int $code HTTP status code
     * @return Response
     */
    protected function status(int $code): Response
    {
        return $this->response->status($code);
    }

    /**
     * Get request input
     *
     * @param string $key Input name
     * @param mixed $default Default value
     * @return mixed
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    /**
     * Get all request input
     *
     * @return array
     */
    protected function all(): array
    {
        return $this->request->all();
    }

    /**
     * Get query parameter
     *
     * @param string $key Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return $this->request->query($key, $default);
    }

    /**
     * Get route parameter
     *
     * @param string $key Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    protected function param(string $key, mixed $default = null): mixed
    {
        return $this->request->param($key, $default);
    }

    /**
     * Check if request has input
     *
     * @param string $key Input name
     * @return bool
     */
    protected function has(string $key): bool
    {
        return $this->request->has($key);
    }

    /**
     * Get uploaded file
     *
     * @param string $key File input name
     * @return array|null
     */
    protected function file(string $key): ?array
    {
        return $this->request->file($key);
    }

    /**
     * Check if file was uploaded
     *
     * @param string $key File input name
     * @return bool
     */
    protected function hasFile(string $key): bool
    {
        return $this->request->hasFile($key);
    }

    /**
     * Check CSRF token (for POST requests)
     *
     * @return bool
     */
    protected function checkCsrf(): bool
    {
        return CSRF::check($this->request);
    }

    /**
     * Get current authenticated user
     *
     * @return array|null
     */
    protected function user(): ?array
    {
        return Auth::user();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        return Auth::isAdmin();
    }

    /**
     * Require authentication
     *
     * @param string $redirectUrl Optional redirect URL after login
     * @return void
     */
    protected function requireAuth(?string $redirectUrl = null): void
    {
        Auth::guard($redirectUrl ?? $this->request->fullUrl());
    }

    /**
     * Require admin role
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        Auth::guardAdmin();
    }

    /**
     * Require an active subscription. If the user is not logged in we redirect
     * to /login; if logged in but unsubscribed we redirect to /redeem.
     * Stripe and activation-code subscriptions are both honoured.
     *
     * @return void
     */
    protected function requireActiveSubscription(): void
    {
        $this->requireAuth();
        $user   = $this->user();
        $userId = (int) ($user['id'] ?? 0);
        if ($userId === 0) {
            $this->redirect('/login');
            exit;
        }
        // Admins always have study access — they built the platform, no code needed.
        if (($user['role'] ?? '') === 'admin') {
            return;
        }
        if (!\App\Services\SubscriptionService::hasActive($userId)) {
            $_SESSION['flash_notice'] = 'A monthly subscription is required to access study content.';
            $this->redirect('/redeem');
            exit;
        }
    }

    /**
     * Require purchase / access to a specific subject. Admins always pass.
     * Falls through PurchaseService::hasAccess(), which honours the legacy
     * flat-rate subscription for the q400-pack subject.
     *
     * @param string $subjectSlug e.g. "q400-pack"
     */
    protected function requireSubjectAccess(string $subjectSlug): void
    {
        $this->requireAuth();
        $user   = $this->user();
        $userId = (int) ($user['id'] ?? 0);
        if ($userId === 0) {
            $this->redirect('/login');
            exit;
        }
        if (($user['role'] ?? '') === 'admin') {
            return;
        }
        if (!\App\Services\PurchaseService::hasAccess($userId, $subjectSlug)) {
            $_SESSION['flash_notice'] = 'Purchase required to access this content.';
            $this->redirect('/pricing');
            exit;
        }
    }

    /**
     * Pop a one-shot flash message off the session and return it.
     * Used by views to render a success/error banner once after a redirect.
     */
    protected function popFlash(string $key): ?string
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return is_string($value) ? $value : null;
    }

    /**
     * Render a styled 404 page using the pilot layout (or marketing if not
     * authenticated) with a heading, body, and a back-to-safety link.
     * Replaces the previous bare `<h1>X Not Found</h1>` HTML responses.
     */
    protected function renderNotFound(string $heading, string $body = '', string $backUrl = '/dashboard', string $backLabel = 'Back to dashboard'): void
    {
        $this->response->status(404);
        $layout = Auth::check() ? 'pilot' : 'marketing';
        $this->response->html($this->view('errors/not-found', [
            'title'     => $heading,
            'heading'   => $heading,
            'body'      => $body !== '' ? $body : 'The page or resource you were looking for is not available.',
            'backUrl'   => $backUrl,
            'backLabel' => $backLabel,
        ], $layout));
    }

    /**
     * Phase 3 fix — record a study session row.
     *
     * The dashboard's "Continue Studying", "Recent Activity", "Study Activity"
     * heatmap and the /progress total-time-studied widget all SELECT from
     * `study_sessions` — but until this helper landed, NO controller in the
     * app inserted a row into that table. As a result every learner saw
     * empty widgets regardless of how much they actually studied.
     *
     * Call this at the top of each "user is engaging with content" handler:
     *   - StudyController::lesson  → 'detail'
     *   - FlashcardController::study → 'flashcard'
     *   - QuizController::take     → 'quiz'
     *
     * Idempotency: we de-dupe on (user, system, type) for the same minute
     * so refreshing the page doesn't inflate the heatmap intensity. The
     * heatmap counts SESSIONS per day, so one row per minute per system is
     * a reasonable "active" signal without needing a beforeunload heartbeat.
     *
     * Failures are swallowed: this is an analytics path, not a feature
     * the user blocks on. A logged 500 here would punish the learner for
     * a logging bug.
     */
    protected function recordStudySession(int $userId, ?int $systemId, string $sessionType): void
    {
        if ($userId <= 0) return;
        $allowed = ['detail', 'revision', 'flashcard', 'quiz', 'diagram'];
        if (!in_array($sessionType, $allowed, true)) return;

        try {
            $db = \App\Core\DB::instance();

            // De-dupe: skip if a same-minute session already exists for this
            // (user, system, type) tuple. Prevents page-refresh spamming.
            $existing = $db->queryOne(
                'SELECT id FROM study_sessions
                  WHERE user_id = ?
                    AND ((? IS NULL AND system_id IS NULL) OR system_id = ?)
                    AND session_type = ?
                    AND started_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
                  ORDER BY started_at DESC
                  LIMIT 1',
                [$userId, $systemId, $systemId, $sessionType]
            );
            if ($existing) return;

            $db->insert(
                'INSERT INTO study_sessions (user_id, system_id, session_type, started_at)
                 VALUES (?, ?, ?, NOW())',
                [$userId, $systemId, $sessionType]
            );

            // Touch the user's streak. The User model already has the
            // dedup-by-day logic; calling it on every session insert is safe.
            try {
                if (method_exists(\App\Models\User::class, 'updateStreak')) {
                    (new \App\Models\User())->updateStreak($userId);
                }
            } catch (\Throwable $e) { /* streak failure must not break study */ }
        } catch (\Throwable $e) {
            // Analytics insert failed — swallow. Don't ever block content.
        }
    }
}
