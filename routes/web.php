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

// Subscription routes
$router->get('/redeem',  'SubscriptionController@showRedeem');
$router->post('/redeem', 'SubscriptionController@redeem');
$router->get('/account', 'SubscriptionController@account');

// Systems Routes
$router->get('/systems', 'SystemsController@index');
$router->get('/systems/{id}', 'SystemsController@show');

// Study Routes
$router->get('/study/{id}', 'StudyController@detail');
$router->get('/study/{id}/revision', 'StudyController@revision');

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
$router->post('/admin/users/update', 'AdminController@usersUpdate');
$router->get('/admin/leads', 'AdminController@leads');
$router->get('/admin/aircrafts', 'AdminController@aircrafts');
$router->post('/admin/aircrafts/update', 'AdminController@aircraftsUpdate');
$router->get('/admin/content', 'AdminController@content');
$router->post('/admin/content/create', 'AdminController@createContent');
$router->get('/admin/import', 'AdminController@import');
$router->post('/admin/import/process', 'AdminController@processImport');
$router->get('/admin/flashcards', 'AdminController@flashcards');
$router->post('/admin/flashcards/create', 'AdminController@createFlashcard');
$router->get('/admin/quizzes', 'AdminController@quizzes');
$router->post('/admin/quizzes/create', 'AdminController@createQuiz');

// ============================================================================
// API ROUTES
// ============================================================================

$router->post('/api/progress/update', 'ApiController@updateProgress');
$router->post('/api/flashcard/review', 'ApiController@flashcardReview');
$router->get('/api/search', 'ApiController@search');
$router->post('/api/lessons/{id}/complete', 'ApiController@lessonComplete');
$router->post('/api/notes/save', 'ApiController@saveNotes');
$router->post('/api/systems/{id}/complete', 'ApiController@systemComplete');
