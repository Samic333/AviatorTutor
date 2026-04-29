<?php
/**
 * AviatorTutor public pricing tiers (marketing copy).
 *
 * Source of truth for the pricing cards shown on /pricing and the home
 * page pricing section. Keep tier copy here so both surfaces stay in sync
 * and future-tier launches only require editing one file.
 *
 * Stripe per-pack checkout is paused while the new tier model is in
 * waitlist mode — activation codes (issued from /admin/codes) remain the
 * paid path for early customers.
 */

declare(strict_types=1);

return [
    [
        'slug'         => 'free-preview',
        'badge'        => 'Free Preview',
        'name'         => 'Free Preview',
        'blurb'        => 'Sample lessons across aircraft systems, weather, CRM, and SOPs. Try the slide-based study experience and a handful of flashcards before you commit.',
        'price_label'  => 'Free',
        'price_suffix' => 'forever',
        'cta_label'    => 'Create free account',
        'cta_url'      => '/register',
        'highlight'    => false,
        'features'     => [
            'Sample slide-based lessons',
            '5 flashcards per topic',
            'Quick Revision preview',
            'Progress saved across devices',
        ],
    ],
    [
        'slug'         => 'student-pilot',
        'badge'        => 'Student / Pilot Learner',
        'name'         => 'Student / Pilot Learner',
        'blurb'        => 'Full access to one aircraft library or one aviation subject pack. Ideal for type-rating candidates, recurrent training, and aviation students.',
        'price_label'  => 'Coming soon',
        'price_suffix' => 'launch pricing TBA',
        'cta_label'    => 'Join the waitlist',
        'cta_url'      => '/contact?tier=student',
        'highlight'    => true,
        'features'     => [
            'One full aircraft systems library OR one subject pack',
            'All lessons, flashcards, quizzes, scenarios',
            'QRH & memory-item drills',
            'Quick Revision & weak-area review',
        ],
    ],
    [
        'slug'         => 'professional',
        'badge'        => 'Professional',
        'name'         => 'Professional',
        'blurb'        => 'Everything across the platform — every aircraft library, every aviation subject (weather, CRM, SMS, SOPs, QRH, cabin safety, emergency procedures), and airline interview prep.',
        'price_label'  => 'Coming soon',
        'price_suffix' => 'launch pricing TBA',
        'cta_label'    => 'Join the waitlist',
        'cta_url'      => '/contact?tier=professional',
        'highlight'    => false,
        'features'     => [
            'Every aircraft library',
            'Every aviation subject pack',
            'Airline interview prep (20+ carriers)',
            'Priority support',
        ],
    ],
    [
        'slug'         => 'instructor-org',
        'badge'        => 'Instructor / Organisation',
        'name'         => 'Instructor / Organisation',
        'blurb'        => 'Multi-seat licences for training organisations, airlines, and instructors. Admin dashboard, learner progress reports, and the ability to publish your own modules.',
        'price_label'  => 'Talk to us',
        'price_suffix' => 'volume & custom',
        'cta_label'    => 'Contact us',
        'cta_url'      => '/contact?tier=instructor',
        'highlight'    => false,
        'features'     => [
            'Multi-seat learner accounts',
            'Admin dashboard with progress reports',
            'Bulk activation codes',
            'Optional custom-content publishing',
        ],
    ],
];
