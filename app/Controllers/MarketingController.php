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
        // Pull catalog from subjects table (Phase 4). Fall back to legacy flat-rate if not migrated.
        $aircraftPacks    = [];
        $airlinePacks     = [];
        $subjectPacks     = [];
        $catalogAvailable = false;
        try {
            $aircraftPacks = \App\Services\PurchaseService::catalog('aircraft_pack');
            $airlinePacks  = \App\Services\PurchaseService::catalog('airline_interview');
            $subjectPacks  = \App\Services\PurchaseService::catalog('aviation_subject');
            $catalogAvailable = !empty($aircraftPacks) || !empty($airlinePacks) || !empty($subjectPacks);
        } catch (\Throwable $e) {
            $catalogAvailable = false;
        }

        $userId       = (int) ($this->user()['id'] ?? 0);
        $userPurchases = [];
        if ($userId > 0 && $catalogAvailable) {
            $rows = \App\Services\PurchaseService::userPurchases($userId);
            foreach ($rows as $r) {
                $userPurchases[(string) $r['slug']] = true;
            }
        }

        $selectedSlugs = array_merge(
            (array) ($_SESSION['onboarding_subject_slugs'] ?? []),
            $_SESSION['onboarding_aircraft_slug'] ?? '' ? [$_SESSION['onboarding_aircraft_slug']] : []
        );

        $jsonLd = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => 'AviatorTutor study packs',
            'description' => 'Per-subject study packs for aircraft systems, airline interview prep, and aviation subjects.',
            'offers'      => [
                '@type'         => 'AggregateOffer',
                'priceCurrency' => 'USD',
                'lowPrice'      => '14.00',
                'highPrice'     => '29.00',
                'url'           => 'https://aviatortutor.com/pricing',
                'availability'  => 'https://schema.org/InStock',
            ],
        ];

        $response->html($this->view('marketing/pricing', [
            'title'            => 'Pricing — AviatorTutor',
            'description'      => 'Per-subject access: aircraft packs $29, airline interview prep $19, aviation subject packs $14.',
            'jsonLd'           => $jsonLd,
            'aircraftPacks'    => $aircraftPacks,
            'airlinePacks'     => $airlinePacks,
            'subjectPacks'     => $subjectPacks,
            'userPurchases'    => $userPurchases,
            'selectedSlugs'    => array_flip(array_filter($selectedSlugs)),
            'catalogAvailable' => $catalogAvailable,
            'isAuthenticated'  => $this->isAuthenticated(),
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
        $subject = trim((string) $this->input('subject', ''));
        $message = trim((string) $this->input('message', ''));

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 10) {
            $_SESSION['flash_error'] = 'Please provide a name, valid email, and a message of at least 10 characters.';
            $this->redirect('/contact');
            return;
        }

        if ($subject === '') {
            $subject = 'General enquiry';
        }
        $subject = mb_substr($subject, 0, 200);

        // Persist to admin inbox (source of truth). Mail step below remains as a courtesy notifier.
        $authedUserId = $this->isAuthenticated() ? (int) ($this->user()['id'] ?? 0) : null;
        try {
            DB::instance()->insert(
                'INSERT INTO contact_messages (name, email, subject, message, status, ip, user_agent, user_id)
                 VALUES (?, ?, ?, ?, "new", ?, ?, ?)',
                [
                    $name,
                    $email,
                    $subject,
                    $message,
                    $_SERVER['REMOTE_ADDR']     ?? null,
                    substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 1000),
                    $authedUserId ?: null,
                ]
            );
        } catch (\Throwable $e) {
            // `subject` column may not exist yet (migration not applied) — retry without it.
            try {
                DB::instance()->insert(
                    'INSERT INTO contact_messages (name, email, message, status, ip, user_agent, user_id)
                     VALUES (?, ?, ?, "new", ?, ?, ?)',
                    [
                        $name,
                        $email,
                        '[' . $subject . '] ' . $message,
                        $_SERVER['REMOTE_ADDR']     ?? null,
                        substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 1000),
                        $authedUserId ?: null,
                    ]
                );
            } catch (\Throwable $e2) {
                // Table not migrated at all — fall through to mail+log only.
            }
        }

        $appConfig = require BASE_PATH . '/config/app.php';
        $to        = (string) ($appConfig['contact_to'] ?? 'samickenya@gmail.com');
        $fromAddr  = (string) ($appConfig['mail_from']  ?? 'no-reply@aviatortutor.com');
        $fromName  = 'AviatorTutor Contact Form';

        // Filter against header injection — strip CR/LF from any value placed in headers.
        $clean = static fn(string $v): string => trim(preg_replace('/[\r\n]+/', ' ', $v) ?? '');
        $cleanName  = $clean($name);
        $cleanEmail = $clean($email);

        $cleanSubject = $clean($subject);
        $mailSubject  = '[AviatorTutor] ' . $cleanSubject . ' — ' . $cleanName;
        $body    = "Name:    {$cleanName}\n"
                 . "Email:   {$cleanEmail}\n"
                 . "Subject: {$cleanSubject}\n"
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

        $sent = @mail($to, $mailSubject, $body, $headers, $envelope);

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

        // Message is in the admin inbox regardless of whether mail() succeeded.
        $_SESSION['flash_ok'] = 'Thanks — your message is in. We reply within one business day.';
        $this->redirect('/contact');
    }

    public function checkoutPaused(Request $request, Response $response): void
    {
        // Stripe per-pack checkout is disabled while the new tier model is
        // in waitlist mode. Drop visitors back on /pricing with a notice so
        // any old links or bookmarks don't dead-end.
        $_SESSION['flash_notice'] = 'Stripe per-pack checkout is paused while we launch new pricing tiers. Activation codes still work — and you can join any tier waitlist below.';
        $this->redirect('/pricing');
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

    // popFlash() lives on the base Controller class.

    public static function faqList(): array
    {
        return [
            ['q' => 'Who is AviatorTutor for?',
             'a' => 'Pilots (type-rating candidates, line captains, first officers), cabin crew, dispatchers, ground and safety teams, instructors, SMS trainers, aviation students, and operations professionals. If you study aviation seriously — for a checkride, an interview, recurrent training, or your day job — AviatorTutor is built for you.'],
            ['q' => 'Is AviatorTutor only for Q400?',
             'a' => 'No. AviatorTutor is a complete aviation learning platform. Q400 is the first fully-published aircraft library and serves as our reference module; aircraft systems libraries for additional types, aviation subject packs (weather, SOPs, QRH, CRM, SMS, cabin safety, emergency procedures), and airline interview prep are all part of the platform and ship regularly.'],
            ['q' => 'What subjects are covered?',
             'a' => 'Aircraft systems (chapter-by-chapter, ATA-organised), weather and meteorology, SOPs, QRH and memory items, CRM and human factors, cabin safety, SMS (ICAO Annex 19), emergency procedures, dangerous goods awareness, navigation (RNAV/RNP/PBN), communications and phraseology, air law, operations control, and airline interview prep for 20+ carriers.'],
            ['q' => 'Are lessons created by aviation professionals?',
             'a' => 'Yes. Every module is structured by working aviation professionals — line pilots, cabin crew, safety/SMS trainers, instructors, and operational experts — and reviewed by subject-matter specialists before publication. Lessons connect concepts to crew action, QRH references, scenarios, and memory triggers, not just textbook definitions.'],
            ['q' => 'How does the study method work?',
             'a' => 'Each topic is broken into short, interactive slide-based lessons (typically 8–15 slides). Each slide combines a concept, a visual, a memory hook, an operational-relevance note, and a check-understanding question gate. Wrong answers show explanations so you learn from the mistake. Quick Revision pulls out key facts, must-know items, and exam traps; SM-2 spaced-repetition flashcards surface what you’re about to forget; timed quizzes verify retention. Progress saves automatically across devices.'],
            ['q' => 'Can pilots, cabin crew, and aviation students all use it?',
             'a' => 'Yes. Aircraft systems modules are pilot-focused, but cabin safety, CRM, SMS, emergency procedures, and dangerous goods awareness are essential for cabin crew. Aviation students preparing for theory exams or interviews also use the platform. Each user picks their own learning path.'],
            ['q' => 'Will new subjects and courses be added?',
             'a' => 'Yes. New aircraft libraries and aviation subject packs are added regularly. Difficulty levels (Beginner / Intermediate / Advanced) are wired into every module so the same content works for first exposure and for advanced recurrent prep. Future plans include instructor-led and expert-created courses on the same platform.'],
            ['q' => 'How does account registration and email confirmation work?',
             'a' => 'Create a free account with your name, email, and a password. We send a confirmation email with a one-click verification link valid for 48 hours. Once confirmed, your account is active and you can study, save progress, and (when launched) join paid tiers. If the email doesn’t arrive, contact support and we can verify your account manually.'],
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
