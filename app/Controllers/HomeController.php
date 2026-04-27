<?php
/**
 * Home Controller
 *
 * The public marketing homepage at "/". Logged-in users with an active
 * subscription are sent to /dashboard; logged-in users without a subscription
 * still see the marketing copy (with "Redeem code" CTA in the header).
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class HomeController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        if ($this->isAuthenticated()) {
            $userId = (int) $this->user()['id'];
            if (\App\Services\SubscriptionService::hasActive($userId)) {
                $this->redirect('/dashboard');
                return;
            }
        }

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    'name' => 'AviatorTutor',
                    'url'  => 'https://aviatortutor.com',
                ],
                [
                    '@type' => 'Organization',
                    'name' => 'AviatorTutor',
                    'url'  => 'https://aviatortutor.com',
                    'logo' => 'https://aviatortutor.com/assets/og/aviatortutor-1200x630.png',
                ],
            ],
        ];

        $response->html($this->view('marketing/home', [
            'title'       => 'AviatorTutor — Master aircraft systems, aviation interviews, and pilot knowledge',
            'description' => 'Self-study aviation platform for pilots and aviation learners. Aircraft systems, interview prep, quizzes, and a structured aviation question bank — for $10 a month.',
            'jsonLd'      => $jsonLd,
        ], 'marketing'));
    }
}
