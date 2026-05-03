<?php
/**
 * Post-deploy smoke tester.
 *
 * Hits a list of routes via cURL and asserts:
 *   • HTTP 200 OK (or an explicit allow-list of redirects)
 *   • response body does NOT contain "500 Internal Server Error" or our
 *     friendly-500 marker text.
 *
 * Usage:
 *   php tools/smoke.php                          # hits prod (https://aviatortutor.com)
 *   php tools/smoke.php --base=http://localhost  # local dev server
 *   php tools/smoke.php --cookie=name=value      # authenticated routes (paste your session cookie)
 *
 * Exits 0 on full pass, 1 on any failure. Designed to be safe to run in
 * a deploy hook or post-pull cron without polluting prod data.
 */
declare(strict_types=1);

$opts = ['base' => 'https://aviatortutor.com', 'cookie' => null];
foreach ($argv as $a) {
    if (str_starts_with((string)$a, '--base=')) $opts['base'] = rtrim(substr((string)$a, 7), '/');
    if (str_starts_with((string)$a, '--cookie=')) $opts['cookie'] = substr((string)$a, 9);
}

// Routes to check. Public ones run without a cookie; auth ones only run
// when --cookie is provided (otherwise they'd redirect to /login and
// noise up the report).
$publicRoutes = [
    ['/',                  [200], 'home'],
    ['/aircraft',          [200], 'public catalog'],
    ['/aircraft/q400',     [200], 'q400 detail'],
    ['/pricing',           [200], 'pricing'],
    ['/about',             [200], 'about'],
    ['/contact',           [200], 'contact'],
    ['/faq',               [200], 'faq'],
    ['/privacy',           [200], 'privacy'],
    ['/terms',             [200], 'terms'],
    ['/login',             [200], 'login form'],
    ['/register',          [200], 'register form'],
    ['/sitemap.xml',       [200], 'sitemap'],
    ['/robots.txt',        [200], 'robots'],
];

$authRoutes = [
    ['/dashboard',                  [200, 302], 'dashboard'],
    ['/my-subjects',                [200, 302], 'my subjects'],
    ['/systems',                    [200, 302], 'systems'],
    ['/study/1',                    [200, 302], 'system detail'],
    ['/study/1/lesson/1',           [200, 302], 'first slide deck — was 500 source'],
    ['/study/1/deep-notes',         [200, 302, 404], 'deep notes (flag-gated → 404 ok if off)'],
    ['/study/1/mnemonics',          [200, 302, 404], 'mnemonics (flag-gated)'],
    ['/study/1/mind-map',           [200, 302, 404], 'mind map (flag-gated)'],
    ['/flashcards/1',               [200, 302], 'flashcards'],
    ['/quiz',                       [200, 302], 'quiz list'],
    ['/progress',                   [200, 302], 'progress'],
    ['/profile',                    [200, 302], 'profile'],
    ['/account',                    [200, 302], 'account'],
];

$adminRoutes = [
    ['/admin',                      [200, 302], 'admin home'],
    ['/admin/subject-requests',     [200, 302], 'admin subject requests'],
    ['/admin/analytics',            [200, 302, 403], 'admin analytics (flag-gated)'],
];

$pass = 0;
$fail = 0;
$reports = [];

$check = static function(array $route, ?string $cookie) use (&$pass, &$fail, &$reports, $opts) {
    [$path, $okCodes, $label] = $route;
    $url = $opts['base'] . $path;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'AviatorTutor-smoke/1.0',
        CURLOPT_HEADER         => false,
    ]);
    if ($cookie) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $body = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    $codeOk = in_array($code, $okCodes, true);
    $bodyOk = stripos($body, '500 Internal Server Error') === false
           && stripos($body, 'We hit turbulence on this page') === false; // friendly-500 marker

    $ok = $codeOk && ($code >= 300 || $bodyOk);
    if ($ok) {
        $pass++;
        $reports[] = sprintf("  ✓ %-40s %d  %s", $path, $code, $label);
    } else {
        $fail++;
        $why = !$codeOk ? "HTTP {$code} (expected " . implode('|', $okCodes) . ")"
                        : "body contained error marker";
        if ($err) $why .= " · curl: {$err}";
        $reports[] = sprintf("  ✗ %-40s %d  %s — %s", $path, $code, $label, $why);
    }
};

echo "Smoke testing " . $opts['base'] . "\n\n";
echo "PUBLIC ROUTES\n";
foreach ($publicRoutes as $r) $check($r, null);

if ($opts['cookie']) {
    echo "\nAUTH ROUTES (with cookie)\n";
    foreach ($authRoutes as $r) $check($r, $opts['cookie']);
    echo "\nADMIN ROUTES (with cookie — assumed admin)\n";
    foreach ($adminRoutes as $r) $check($r, $opts['cookie']);
} else {
    echo "\n(no --cookie supplied; skipping auth + admin routes)\n";
}

echo "\n" . implode("\n", $reports) . "\n\n";
echo "Summary: {$pass} pass, {$fail} fail\n";
exit($fail === 0 ? 0 : 1);
