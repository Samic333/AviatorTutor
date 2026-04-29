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
        $message = trim((string) $this->input('message', ''));

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 10) {
            $_SESSION['flash_error'] = 'Please provide a name, valid email, and a message of at least 10 characters.';
            $this->redirect('/contact');
            return;
        }

        // Persist to admin inbox (source of truth). Mail step below remains as a courtesy notifier.
        $authedUserId = $this->isAuthenticated() ? (int) ($this->user()['id'] ?? 0) : null;
        try {
            DB::instance()->insert(
                'INSERT INTO contact_messages (name, email, message, status, ip, user_agent, user_id)
                 VALUES (?, ?, ?, "new", ?, ?, ?)',
                [
                    $name,
                    $email,
                    $message,
                    $_SERVER['REMOTE_ADDR']     ?? null,
                    substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 1000),
                    $authedUserId ?: null,
                ]
            );
        } catch (\Throwable $e) {
            // Table not migrated yet — fall through to mail+log only. No user-visible error.
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

        // Message is in the admin inbox regardless of whether mail() succeeded.
        $_SESSION['flash_ok'] = 'Thanks — your message is in. We reply within one business day.';
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

    // popFlash() lives on the base Controller class.

    public static function faqList(): array
    {
        return [
            ['q' => 'What is AviatorTutor?',
             'a' => 'A premium aviation learning platform built by aviation professionals — pilots, cabin crew, SMS trainers, and instructors — for the entire aviation community. Aircraft systems, airline interview prep, and aviation subject packs in one place.'],
            ['q' => 'Who is it for?',
             'a' => 'Pilots (type-rating candidates and line crews), cabin crew, dispatchers, ground and safety teams, instructors, SMS trainers, and aviation students. If you study aviation, you have a place here.'],
            ['q' => 'What subjects can I study?',
             'a' => 'Three pillars: aircraft systems libraries (multi-type), aviation subject packs (weather, performance, CRM, SMS, DGR, navigation, communications, ops control, human factors, air law), and airline interview prep for 20+ major carriers.'],
            ['q' => 'Is this only aircraft systems?',
             'a' => 'No. Aircraft systems is one pillar. Aviation subject packs and airline interview prep are equal pillars — most people start with whichever matches their immediate study goal.'],
            ['q' => 'How does pricing work?',
             'a' => 'Per-pack one-time purchase with lifetime access — no subscription, no recurring fees. Aircraft packs $29, airline interview prep $19, aviation subject packs $14. Activation codes from partners also work.'],
            ['q' => 'Who creates the content?',
             'a' => 'Working aviation professionals — line pilots, cabin crew, safety/SMS trainers, instructors, and operational aviation experts. Every pack is reviewed by subject-matter experts before publication.'],
            ['q' => "What's live today vs. coming soon?",
             'a' => 'The Q400 systems pack is fully live. B737, B787, A320, ATR-72, A350 packs and most airline interview / aviation subject packs are in production — you can join waitlists from each tile and we\'ll email you the day it goes live.'],
            ['q' => 'Do I need to register?',
             'a' => 'Yes — a free account is required to study or purchase any pack. Registration includes email confirmation. You can browse the catalog before signing up.'],
            ['q' => 'Can I study on mobile?',
             'a' => 'Yes — the entire platform is responsive on phone, tablet, and laptop with a mobile sidebar drawer. There are no native apps yet, but the web app is fully functional offline-friendly for most workflows.'],
            ['q' => 'Do you offer organisation or instructor licences?',
             'a' => 'Yes — schools, training organisations, and airlines can purchase volume bundles via shared activation codes. Use the contact form for a quote.'],
            ['q' => 'How do I get help?',
             'a' => 'Use the contact form on the Contact page — your message goes straight into our admin inbox and we reply within one business day.'],
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
