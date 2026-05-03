<?php
declare(strict_types=1);
/** @var string $requestId */
/** @var ?string $debugMessage */
/** @var ?string $debugTrace */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Something went wrong — AviatorTutor</title>
  <meta name="theme-color" content="#0F172A">
  <meta name="robots" content="noindex">
  <style>
    :root { color-scheme: dark; }
    * { box-sizing: border-box; }
    body {
      margin: 0; min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 32px 16px;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Inter, system-ui, sans-serif;
      background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
      color: #F1F5F9;
    }
    .wrap { max-width: 540px; width: 100%; text-align: center; }
    .badge {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 6px 14px; border-radius: 999px;
      background: rgba(239,68,68,0.15); color: #FCA5A5;
      font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;
      margin-bottom: 20px;
    }
    h1 { margin: 0 0 12px; font-size: 28px; font-weight: 700; letter-spacing: -0.01em; }
    p  { margin: 0 0 20px; color: #94A3B8; font-size: 15px; line-height: 1.6; }
    .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    .btn {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 10px 18px; border-radius: 10px; border: 0;
      font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none;
      transition: transform .1s ease;
    }
    .btn:active { transform: translateY(1px); }
    .btn-primary { background: linear-gradient(135deg, #38BDF8, #0EA5E9); color: #0F172A; }
    .btn-ghost   { background: rgba(255,255,255,0.05); color: #E2E8F0; border: 1px solid rgba(255,255,255,0.1); }
    .meta {
      margin-top: 24px; padding-top: 16px;
      border-top: 1px solid rgba(255,255,255,0.06);
      font-size: 12px; color: #64748B;
    }
    .meta code { font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace; color: #94A3B8; }
    details { margin-top: 18px; text-align: left; }
    details summary { cursor: pointer; color: #94A3B8; font-size: 13px; }
    pre {
      margin: 8px 0 0; padding: 12px; max-height: 320px; overflow: auto;
      background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08);
      border-radius: 8px; font-size: 11.5px; color: #CBD5E1;
      white-space: pre-wrap; word-break: break-word;
    }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="badge">Something went wrong</div>
    <h1>We hit turbulence on this page.</h1>
    <p>The error has been logged. Try the page again — most of the time a refresh is enough.</p>
    <div class="actions">
      <button class="btn btn-primary" type="button" onclick="location.reload();">Refresh page</button>
      <a class="btn btn-ghost" href="/dashboard">Back to dashboard</a>
    </div>
    <div class="meta">
      Request ID: <code><?= $h($requestId) ?></code>
      <?php if (!empty($debugMessage)): ?>
        <details>
          <summary>Debug details (visible because debug mode is on)</summary>
          <pre><?= $h($debugMessage) ?>

<?= $h($debugTrace ?? '') ?></pre>
        </details>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
