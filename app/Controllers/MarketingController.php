<?php
/**
 * Marketing Controller
 *
 * Static-ish public pages: pricing, about, contact, privacy, terms, FAQ,
 * coming-soon waitlist (with email capture).
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;

class MarketingController extends Controller
{
    public function pricing(Request $request, Response $response): void
    {
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name'  => 'AviatorTutor monthly subscription',
            'description' => 'Full access to AviatorTutor self-study platform including aircraft systems, flashcards, quizzes and progress tracking.',
            'offers' => [
                '@type'         => 'Offer',
                'price'         => '10.00',
                'priceCurrency' => 'USD',
                'url'           => 'https://aviatortutor.com/pricing',
                'availability'  => 'https://schema.org/InStock',
            ],
        ];

        $response->html($this->view('marketing/pricing', [
            'title'       => 'Pricing — AviatorTutor',
            'description' => 'One simple subscription: $10/month for full access to aircraft systems, interview prep, flashcards, quizzes and progress tracking.',
            'jsonLd'      => $jsonLd,
        ], 'marketing'));
    }

    public function about(Request $request, Response $response): void
    {
        $response->html($this->view('marketing/about', [
            'title'       => 'About AviatorTutor',
            'description' => 'AviatorTutor is a self-study platform built for pilots, student pilots, cabin crew, and aviation interview candidates.',
        ], 'marketing'));
    }

    public function contact(Request $request, Response $response): void
    {
        $response->html($this->view('marketing/contact', [
            'title'       => 'Contact AviatorTutor',
            'description' => 'Get in touch with the AviatorTutor team. We answer support requests within one business day.',
            'csrf_token'  => CSRF::generate(),
            'flashOk'     => $this->popFlash('flash_ok'),
            'flashError'  => $this->popFlash('flash_error'),
        ], 'marketing'));
    }

    public function contactSend(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please try again.';
            $this->redirect('/contact');
            return;
        }
        $name    = trim((string) $this->input('name', ''));
        $email   = trim((string) $this->input('email', ''));
        $message = trim((string) $this->input('message', ''));

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 10) {
            $_SESSION['flash_error'] = 'Please provide a name, valid email, and a message of at least 10 characters.';
            $this->redirect('/contact');
            return;
        }

        $appConfig = require BASE_PATH . '/config/app.php';
        $to        = (string) ($appConfig['contact_to'] ?? 'samickenya@gmail.com');
        $fromAddr  = (string) ($appConfig['mail_from']  ?? 'no-reply@aviatortutor.com');
        $fromName  = 'AviatorTutor Contact Form';

        // Filter against header injection — strip CR/LF from any value placed in headers.
        $clean = static fn(string $v): string => trim(preg_replace('/[\r\n]+/', ' ', $v) ?? '');
        $cleanName  = $clean($name);
        $cleanEmail = $clean($email);

        $subject = '[AviatorTutor] New contact from ' . $cleanName;
        $body    = "Name:    {$cleanName}\n"
                 . "Email:   {$cleanEmail}\n"
                 . "When:    " . date('c') . "\n"
                 . "IP:      " . ($_SERVER['REMOTE_ADDR'] ?? '?') . "\n"
                 . "Agent:   " . substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200) . "\n"
                 . "----------------------------------------\n"
                 . $message
                 . "\n";

        $headers = implode("\r\n", [
            'From: ' . $fromName . ' <' . $fromAddr . '>',
            'Reply-To: ' . $cleanName . ' <' . $cleanEmail . '>',
            'X-Mailer: AviatorTutor/1.0',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ]);

        // Envelope sender (-f) helps cPanel pass SPF since the domain matches the From host.
        $envelope = '-f' . $fromAddr;

        $sent = @mail($to, $subject, $body, $headers, $envelope);

        // Always log a record — both for audit and as a fallback if mail() fails silently.
        $logFile = BASE_PATH . '/storage/logs/contact.log';
        $line = sprintf(
            "[%s] sent=%s to=%s from=%s name=%s email=%s ip=%s\n%s\n----\n",
            date('c'),
            $sent ? 'OK' : 'FAIL',
            $to,
            $fromAddr,
            $cleanName,
            $cleanEmail,
            $_SERVER['REMOTE_ADDR'] ?? '?',
            $message
        );
        @file_put_contents($logFile, $line, FILE_APPEND);

        $_SESSION['flash_ok'] = $sent
            ? 'Thanks — your message is in. We reply within one business day.'
            : 'Thanks — your message was received and queued; if you don\'t hear back in 48h, email samickenya@gmail.com directly.';
        $this->redirect('/contact');
    }

    public function privacy(Request $request, Response $response): void
    {
        $response->html($this->view('marketing/privacy', [
            'title'       => 'Privacy Policy — AviatorTutor',
            'description' => 'How AviatorTutor collects, stores, and protects your study data.',
        ], 'marketing'));
    }

    public function terms(Request $request, Response $response): void
    {
        $response->html($this->view('marketing/terms', [
            'title'       => 'Terms of Service — AviatorTutor',
            'description' => 'The agreement between you and AviatorTutor for using the platform.',
        ], 'marketing'));
    }

    public function faq(Request $request, Response $response): void
    {
        $faqs = self::faqList();
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn($f) => [
                '@type' => 'Question',
                'name'  => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
            ], $faqs),
        ];
        $response->html($this->view('marketing/faq', [
            'title'       => 'FAQ — AviatorTutor',
            'description' => 'Common questions about AviatorTutor pricing, content, devices, and account management.',
            'faqs'        => $faqs,
            'jsonLd'      => $jsonLd,
        ], 'marketing'));
    }

    public function comingSoon(Request $request, Response $response): void
    {
        $slug = (string) $this->param('slug', '');
        $module = self::comingSoonLabel($slug);
        $response->html($this->view('marketing/coming-soon', [
            'title'        => $module . ' — Coming soon — AviatorTutor',
            'description'  => $module . ' study material is in production. Get notified when it lands.',
            'moduleSlug'   => $slug,
            'moduleLabel'  => $module,
            'csrf_token'   => CSRF::generate(),
            'flashOk'      => $this->popFlash('flash_ok'),
            'flashError'   => $this->popFlash('flash_error'),
        ], 'marketing'));
    }

    public function comingSoonNotify(Request $request, Response $response): void
    {
        if (!CSRF::check($request)) {
            $_SESSION['flash_error'] = 'Session expired. Please try again.';
            $this->redirect('/coming-soon/' . $this->param('slug'));
            return;
        }
        $email = trim((string) $this->input('email', ''));
        $slug  = (string) $this->param('slug', '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please enter a valid email.';
            $this->redirect('/coming-soon/' . $slug);
            return;
        }
        DB::instance()->insert(
            'INSERT INTO lead_signups (email, requested_module_slug, ip, user_agent)
             VALUES (?, ?, ?, ?)',
            [$email, $slug, $_SERVER['REMOTE_ADDR'] ?? null, substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 240)]
        );
        $_SESSION['flash_ok'] = 'You\'re on the list. We\'ll email you the moment ' . self::comingSoonLabel($slug) . ' goes live.';
        $this->redirect('/coming-soon/' . $slug);
    }

    /* ------------------------------------------------------------------ */

    private function popFlash(string $key): ?string
    {
        $v = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $v;
    }

    public static function faqList(): array
    {
        return [
            ['q' => 'What is AviatorTutor?',
             'a' => 'AviatorTutor is a self-study platform for aviation learners. Subscribers study aircraft systems, interview questions, flashcards and quizzes at their own pace.'],
            ['q' => 'How much does it cost?',
             'a' => '$10 per month for full access to every module currently live. No tiers, no upsells.'],
            ['q' => 'What can I study right now?',
             'a' => 'Q400 aircraft systems is the first module live, with chapter-by-chapter content, interactive diagrams, flashcards, and quizzes. More aircraft and topic packs are being added.'],
            ['q' => 'Is this only for pilots?',
             'a' => 'No — the platform also serves student pilots, cabin crew, and aviation interview candidates. The Q400 module is most useful to pilots transitioning to or revising the type, but the question banks and CRM/Human Factors content (coming) work for any aviation learner.'],
            ['q' => 'Can I study on mobile?',
             'a' => 'Yes — the entire platform is responsive. Study on a phone, tablet, or laptop. There are no native apps yet.'],
            ['q' => 'Do I need an instructor?',
             'a' => 'No — AviatorTutor is purely self-study. You move at your own pace and the system tracks what you have covered.'],
            ['q' => 'Are more aircraft modules coming?',
             'a' => 'Yes. Cessna Caravan / C208 and a Pilot Interview Questions bank are next on the roadmap. Click any "Coming soon" tile to be notified when each one lands.'],
            ['q' => 'How do I cancel or manage my subscription?',
             'a' => 'V1 subscriptions are activated via a code that grants 30 days. You don\'t need to "cancel" — the access simply ends after 30 days unless you redeem another code. Stripe-based recurring billing is coming next.'],
        ];
    }

    private static function comingSoonLabel(string $slug): string
    {
        return match ($slug) {
            'cessna-caravan'    => 'Cessna Caravan / C208',
            'pilot-interview'   => 'Pilot Interview Questions',
            'cabin-crew'        => 'Cabin Crew Safety',
            'emergency'         => 'Emergency Procedures',
            'crm'               => 'CRM / Human Factors',
            'general-aviation'  => 'General Aviation Knowledge',
            default             => 'This module',
        };
    }
}
