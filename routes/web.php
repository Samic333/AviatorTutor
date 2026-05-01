<?php
/**
 * Route Definitions
 *
 * Define all application routes here
 * Format: $router->get/post(path, 'Controller@method')
 */

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;

// Get router instance
global $router;

if (!isset($router)) {
    $router = new Router(new Request(), new Response());
}

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

// Public marketing homepage at /
$router->get('/', 'HomeController@index');

// Authenticated dashboard
$router->get('/dashboard', 'DashboardController@index');

// Public marketing pages
$router->get('/pricing',  'MarketingController@pricing');
$router->get('/about',    'MarketingController@about');
$router->get('/contact',  'MarketingController@contact');
$router->post('/contact', 'MarketingController@contactSend');
$router->get('/privacy',  'MarketingController@privacy');
$router->get('/terms',    'MarketingController@terms');
$router->get('/faq',      'MarketingController@faq');
$router->get('/coming-soon/{slug}',         'MarketingController@comingSoon');
$router->post('/coming-soon/{slug}/notify', 'MarketingController@comingSoonNotify');

// Aircraft catalog
$router->get('/aircraft',                'AircraftController@index');
$router->get('/aircraft/{slug}',         'AircraftController@show');
$router->post('/aircraft/{slug}/notify', 'AircraftController@notify');
$router->post('/aircraft/{slug}/study',  'AircraftController@startStudying');

// SEO assets
$router->get('/sitemap.xml', 'SeoController@sitemap');
$router->get('/robots.txt',  'SeoController@robots');

// Authentication Routes
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Email verification
$router->get('/verify-email',           'AuthController@verifyEmailPage');
$router->post('/verify-email/resend',   'AuthController@resendVerification');
$router->get('/verify-email/{token}',   'AuthController@verifyEmail');

// Password reset
$router->get('/forgot-password',         'AuthController@forgotForm');
$router->post('/forgot-password',        'AuthController@forgotSend');
$router->get('/reset-password/{token}',  'AuthController@resetForm');
$router->post('/reset-password/{token}', 'AuthController@resetPassword');

// Onboarding wizard
$router->get('/onboarding/aircraft',  'OnboardingController@aircraft');
$router->post('/onboarding/aircraft', 'OnboardingController@setAircraft');
$router->get('/onboarding/subjects',  'OnboardingController@subjects');
$router->post('/onboarding/subjects', 'OnboardingController@setSubjects');

// Subscription routes
$router->get('/redeem',  'SubscriptionController@showRedeem');
$router->post('/redeem', 'SubscriptionController@redeem');
$router->get('/account', 'SubscriptionController@account');

// Checkout (Stripe) — paused while we transition to the new tier model.
// Stripe per-pack checkout is disabled at the route layer; activation
// codes (/redeem) remain the paid path until tiered subscriptions launch.
// Re-enable by uncommenting the four /checkout routes below.
//
// $router->get('/checkout/success',  'CheckoutController@successPage');
// $router->get('/checkout/cancel',   'CheckoutController@cancel');
// $router->get('/checkout/{slug}',   'CheckoutController@show');
// $router->post('/checkout/{slug}',  'CheckoutController@create');
//
// Stripe webhook stays live so any in-flight subscriptions still reconcile.
$router->post('/stripe/webhook',   'CheckoutController@webhook');

// Send all /checkout/* visits to the new pricing page with a friendly notice.
$router->get('/checkout/{slug}',   'MarketingController@checkoutPaused');
$router->post('/checkout/{slug}',  'MarketingController@checkoutPaused');
$router->get('/checkout/success',  'MarketingController@checkoutPaused');
$router->get('/checkout/cancel',   'MarketingController@checkoutPaused');

// Profile routes
$router->get('/profile',           'ProfileController@show');
$router->post('/profile/update',   'ProfileController@update');
$router->post('/profile/avatar',   'ProfileController@avatar');
$router->get('/profile/password',  'ProfileController@passwordForm');
$router->post('/profile/password', 'ProfileController@passwordChange');

// Systems Routes
$router->get('/systems', 'SystemsController@index');
$router->get('/systems/{id}', 'SystemsController@show');

// Study Routes
$router->get('/study/{id}', 'StudyController@detail');
$router->get('/study/{id}/revision', 'StudyController@revision');
$router->get('/study/{id}/lesson/{lessonId}', 'StudyController@lesson');

// Flashcard Routes
$router->get('/flashcards', 'FlashcardController@index');
$router->get('/flashcards/{id}', 'FlashcardController@study');
$router->post('/flashcards/review', 'FlashcardController@review');

// Quiz Routes
$router->get('/quiz', 'QuizController@index');
$router->get('/quiz/{id}', 'QuizController@take');
$router->post('/quiz/{id}/submit', 'QuizController@submit');
$router->get('/quiz/{id}/result/{attempt_id}', 'QuizController@result');

// Progress Routes
$router->get('/progress', 'ProgressController@index');

// Planner Routes
$router->get('/planner', 'PlannerController@index');
$router->post('/planner', 'PlannerController@create');
$router->post('/planner/create', 'PlannerController@create');

// Search Routes
$router->get('/search', 'SearchController@index');

// Diagram Routes
$router->get('/diagram/{id}', 'DiagramController@show');
$router->get('/diagrams/{id}', 'DiagramController@show');

// ============================================================================
// ADMIN ROUTES
// ============================================================================

$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/codes', 'AdminController@codes');
$router->post('/admin/codes/generate', 'AdminController@codesGenerate');
$router->post('/admin/codes/revoke',   'AdminController@codesRevoke');
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/users/update',        'AdminController@usersUpdate');
$router->post('/admin/users/verify',        'AdminController@userVerify');
$router->post('/admin/users/resend-verify', 'AdminController@userResendVerify');
$router->get('/admin/leads', 'AdminController@leads');
$router->get('/admin/contacts',                'AdminController@contacts');
$router->get('/admin/contacts/{id}',           'AdminController@contactShow');
$router->post('/admin/contacts/{id}',          'AdminController@contactUpdate');
$router->post('/admin/contacts/{id}/reply',    'AdminController@contactReply');
$router->get('/admin/aircrafts', 'AdminController@aircrafts');
$router->post('/admin/aircrafts/update', 'AdminController@aircraftsUpdate');

// Systems CRUD
$router->get('/admin/systems',                 'AdminController@systemsList');
$router->get('/admin/systems/new',             'AdminController@systemNew');
$router->post('/admin/systems/create',         'AdminController@systemCreate');
$router->get('/admin/systems/{id}/edit',       'AdminController@systemEditForm');
$router->post('/admin/systems/{id}/update',    'AdminController@systemUpdate');
$router->post('/admin/systems/{id}/delete',    'AdminController@systemDelete');
$router->get('/admin/content', 'AdminController@content');
$router->post('/admin/content/create', 'AdminController@createContent');
$router->get('/admin/import', 'AdminController@import');
$router->post('/admin/import/process', 'AdminController@processImport');
$router->get('/admin/ai-test', 'AdminController@aiTest');
$router->post('/admin/ai-test/run', 'AdminController@aiTestRun');
$router->get('/admin/ai-jobs', 'AdminController@aiJobsList');
$router->get('/admin/ai-jobs/{id}', 'AdminController@aiJobShow');
$router->post('/admin/ai-jobs/{id}/publish', 'AdminController@aiJobPublish');
$router->post('/admin/ai-jobs/{id}/discard', 'AdminController@aiJobDiscard');
$router->get('/admin/flashcards',                  'AdminController@flashcards');
$router->get('/admin/flashcards/new',               'AdminController@flashcardNew');
$router->post('/admin/flashcards/create',           'AdminController@createFlashcard');
$router->get('/admin/flashcards/{id}/edit',         'AdminController@flashcardEdit');
$router->post('/admin/flashcards/{id}/update',      'AdminController@updateFlashcard');
$router->post('/admin/flashcards/{id}/delete',      'AdminController@deleteFlashcard');

$router->get('/admin/quizzes',                      'AdminController@quizzes');
$router->get('/admin/quizzes/new',                  'AdminController@quizNew');
$router->post('/admin/quizzes/create',              'AdminController@createQuiz');
$router->get('/admin/quizzes/{id}/edit',            'AdminController@quizEdit');
$router->post('/admin/quizzes/{id}/update',         'AdminController@updateQuiz');
$router->post('/admin/quizzes/{id}/delete',         'AdminController@deleteQuiz');
$router->get('/admin/slides', 'AdminController@slidesIndex');
$router->get('/admin/slides/lesson/{lessonId}', 'AdminController@slidesEdit');
$router->post('/admin/slides/lesson/{lessonId}/save', 'AdminController@slidesSave');
$router->post('/admin/slides/{slideId}/delete', 'AdminController@slideDelete');
$router->post('/admin/slides/{slideId}/move', 'AdminController@slideMove');
$router->get('/admin/subscriptions', 'AdminController@subscriptions');
$router->post('/admin/subscriptions/cancel', 'AdminController@subscriptionCancel');
$router->get('/admin/pricing', 'AdminController@pricing');
$router->post('/admin/pricing/update', 'AdminController@pricingUpdate');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings/update', 'AdminController@settingsUpdate');

// ============================================================================
// API ROUTES
// ============================================================================

$router->post('/api/progress/update', 'ApiController@updateProgress');
$router->post('/api/flashcard/review', 'ApiController@flashcardReview');
$router->get('/api/search', 'ApiController@search');
$router->post('/api/lessons/{id}/complete', 'ApiController@lessonComplete');
$router->post('/api/lessons/{id}/slide-answer', 'ApiController@slideAnswer');
$router->post('/api/notes/save', 'ApiController@saveNotes');
$router->post('/api/notes/delete', 'ApiController@deleteNote');
$router->post('/api/systems/{id}/complete', 'ApiController@systemComplete');
$router->post('/api/systems/{id}/unlock-next', 'ApiController@systemUnlockNext');
$router->post('/api/flashcards/{id}/grade', 'ApiController@flashcardGrade');
$router->post('/api/ai/ask', 'ApiController@aiAsk');

// Notes page
$router->get('/notes', 'NotesController@index');
