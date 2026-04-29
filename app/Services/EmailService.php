<?php
/**
 * Email Service
 *
 * Thin wrapper around PHP mail(). Falls back to logging the message to
 * storage/logs/mail.log if mail() returns false (or if MAIL_DISABLE is set).
 *
 * Usage:
 *   EmailService::send($to, $subject, $htmlBody);
 *   EmailService::sendVerification($user, $token);
 *   EmailService::sendPasswordReset($user, $token);
 */

declare(strict_types=1);

namespace App\Services;

class EmailService
{
    /** Send a raw HTML email. Returns true if mail() reports success. */
    public static function send(string $to, string $subject, string $htmlBody, array $opts = []): bool
    {
        $cfg       = require BASE_PATH . '/config/app.php';
        $fromAddr  = (string) ($opts['from']      ?? $cfg['mail_from']      ?? 'no-reply@aviatortutor.com');
        $fromName  = (string) ($opts['from_name'] ?? $cfg['mail_from_name'] ?? 'AviatorTutor');
        $replyTo   = (string) ($opts['reply_to']  ?? $fromAddr);

        $cleanSubject = str_replace(["\r", "\n"], '', $subject);
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";
        $headers .= "From: " . self::encodeName($fromName) . " <{$fromAddr}>\r\n";
        $headers .= "Reply-To: <{$replyTo}>\r\n";
        $headers .= "X-Mailer: AviatorTutor\r\n";

        $envelope = '-f ' . $fromAddr;
        $sent = false;
        try {
            $sent = @mail($to, $cleanSubject, $htmlBody, $headers, $envelope);
        } catch (\Throwable $e) {
            $sent = false;
        }

        // Log every attempt for audit + fallback inspection.
        $logDir  = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
        $logFile = $logDir . '/mail.log';
        $line = sprintf(
            "[%s] sent=%s to=%s subject=%s\n",
            date('c'),
            $sent ? 'OK' : 'FAIL',
            $to,
            $cleanSubject
        );
        @file_put_contents($logFile, $line, FILE_APPEND);

        // If sending failed, dump the body to a debug log so devs can recover the link.
        if (!$sent) {
            @file_put_contents($logDir . '/mail-failures.log', $line . $htmlBody . "\n----\n", FILE_APPEND);
        }

        return (bool) $sent;
    }

    /** Send verification email containing a one-shot link. */
    public static function sendVerification(array $user, string $rawToken): bool
    {
        $cfg     = require BASE_PATH . '/config/app.php';
        $baseUrl = rtrim((string) ($cfg['base_url'] ?? ''), '/');
        if ($baseUrl === '') {
            $baseUrl = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'aviatortutor.com');
        }

        $name = htmlspecialchars((string) ($user['name'] ?? 'pilot'));
        $link = $baseUrl . '/verify-email/' . urlencode($rawToken);

        $body = <<<HTML
<!doctype html>
<html><body style="margin:0;padding:0;background:#0F172A;font-family:Inter,Arial,sans-serif;color:#E2E8F0;">
  <div style="max-width:560px;margin:0 auto;padding:32px 24px;">
    <h1 style="margin:0 0 24px;font-size:22px;color:#38BDF8;">AviatorTutor</h1>
    <div style="background:#1E293B;padding:32px;border-radius:12px;border:1px solid rgba(255,255,255,0.06);">
      <h2 style="margin:0 0 12px;font-size:18px;">Welcome, {$name}</h2>
      <p style="margin:0 0 20px;line-height:1.6;color:#CBD5E1;">Please confirm your email address to activate your AviatorTutor account.</p>
      <p style="margin:0 0 24px;">
        <a href="{$link}" style="display:inline-block;padding:12px 24px;background:#38BDF8;color:#0F172A;text-decoration:none;font-weight:600;border-radius:8px;">Verify my email</a>
      </p>
      <p style="margin:0 0 8px;font-size:13px;color:#94A3B8;">If the button doesn't work, paste this link in your browser:</p>
      <p style="margin:0;font-size:12px;color:#64748B;word-break:break-all;">{$link}</p>
      <hr style="border:0;border-top:1px solid rgba(255,255,255,0.08);margin:24px 0;">
      <p style="margin:0;font-size:12px;color:#64748B;">This link expires in 48 hours. If you didn't create this account, you can safely ignore this email.</p>
    </div>
  </div>
</body></html>
HTML;

        return self::send((string) $user['email'], 'Verify your AviatorTutor email', $body);
    }

    /** Send password-reset email containing a one-shot link. */
    public static function sendPasswordReset(array $user, string $rawToken): bool
    {
        $cfg     = require BASE_PATH . '/config/app.php';
        $baseUrl = rtrim((string) ($cfg['base_url'] ?? ''), '/');
        if ($baseUrl === '') {
            $baseUrl = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'aviatortutor.com');
        }
        $ttl  = (int) ($cfg['password_reset_ttl_hours'] ?? 2);
        $name = htmlspecialchars((string) ($user['name'] ?? 'pilot'));
        $link = $baseUrl . '/reset-password/' . urlencode($rawToken);

        $body = <<<HTML
<!doctype html>
<html><body style="margin:0;padding:0;background:#0F172A;font-family:Inter,Arial,sans-serif;color:#E2E8F0;">
  <div style="max-width:560px;margin:0 auto;padding:32px 24px;">
    <h1 style="margin:0 0 24px;font-size:22px;color:#38BDF8;">AviatorTutor</h1>
    <div style="background:#1E293B;padding:32px;border-radius:12px;border:1px solid rgba(255,255,255,0.06);">
      <h2 style="margin:0 0 12px;font-size:18px;">Password reset, {$name}</h2>
      <p style="margin:0 0 20px;line-height:1.6;color:#CBD5E1;">We received a request to reset your AviatorTutor password. Click below to choose a new one.</p>
      <p style="margin:0 0 24px;">
        <a href="{$link}" style="display:inline-block;padding:12px 24px;background:#38BDF8;color:#0F172A;text-decoration:none;font-weight:600;border-radius:8px;">Reset password</a>
      </p>
      <p style="margin:0 0 8px;font-size:13px;color:#94A3B8;">If the button doesn't work, paste this link in your browser:</p>
      <p style="margin:0;font-size:12px;color:#64748B;word-break:break-all;">{$link}</p>
      <hr style="border:0;border-top:1px solid rgba(255,255,255,0.08);margin:24px 0;">
      <p style="margin:0;font-size:12px;color:#64748B;">This link expires in {$ttl} hours. If you didn't request this, ignore this email and your password stays unchanged.</p>
    </div>
  </div>
</body></html>
HTML;

        return self::send((string) $user['email'], 'Reset your AviatorTutor password', $body);
    }

    /** Generate a fresh random token (URL-safe). */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /** Hash a raw token for storage. */
    public static function hashToken(string $rawToken): string
    {
        return hash('sha256', $rawToken);
    }

    /** RFC 2047-style header name encoding for non-ASCII names. */
    private static function encodeName(string $name): string
    {
        return preg_match('/[^\x20-\x7e]/', $name)
            ? '=?UTF-8?B?' . base64_encode($name) . '?='
            : $name;
    }
}
