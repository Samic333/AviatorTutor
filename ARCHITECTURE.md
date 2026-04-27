# Q400 Study App - Architecture Documentation

## Overview

The Q400 Aircraft Systems Study App is built with a **clean MVC architecture** using a **front-controller pattern**. All requests are routed through a single entry point (`public/index.php`) and processed by the router.

## Directory Structure

```
q400-study/
├── public/                          # Web root
│   ├── index.php                   # Front controller (entry point)
│   └── .htaccess                   # URL rewriting rules
│
├── app/
│   ├── Core/                       # Framework core classes
│   │   ├── DB.php                 # PDO singleton & query builder
│   │   ├── Request.php            # HTTP request wrapper
│   │   ├── Response.php           # HTTP response helper
│   │   ├── Router.php             # URL routing engine
│   │   ├── Auth.php               # Authentication manager
│   │   ├── CSRF.php               # CSRF token protection
│   │   ├── View.php               # Template renderer
│   │   ├── Controller.php         # Base controller class
│   │   └── Model.php              # Base model class
│   │
│   ├── Controllers/               # Application controllers
│   │   ├── DashboardController.php
│   │   ├── AuthController.php
│   │   ├── SystemsController.php
│   │   ├── StudyController.php
│   │   ├── FlashcardController.php
│   │   ├── QuizController.php
│   │   ├── ProgressController.php
│   │   ├── PlannerController.php
│   │   ├── SearchController.php
│   │   ├── DiagramController.php
│   │   ├── AdminController.php
│   │   └── ApiController.php
│   │
│   └── Models/                   # Data models (future)
│       ├── User.php
│       ├── System.php
│       ├── Flashcard.php
│       └── Quiz.php
│
├── config/                       # Configuration files
│   ├── app.php                  # App settings
│   └── database.php             # Database config
│
├── routes/
│   └── web.php                  # Route definitions
│
├── views/                       # Template files
│   ├── layouts/
│   │   └── app.php              # Main layout
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── dashboard/
│   │   └── index.php
│   ├── systems/
│   │   ├── index.php
│   │   └── show.php
│   ├── study/
│   ├── flashcards/
│   ├── quiz/
│   ├── progress/
│   ├── planner/
│   ├── search/
│   ├── diagrams/
│   └── admin/
│
├── storage/
│   ├── logs/                    # Application logs
│   ├── uploads/                 # User uploads
│   └── pdfs/                    # Generated PDFs
│
├── .gitignore
├── .env.example
├── README.md
├── ARCHITECTURE.md              # This file
└── composer.json                # (future) PHP dependencies
```

## Request Flow

```
User Request
    ↓
public/index.php (Front Controller)
    ↓
    ├─→ Session initialization
    ├─→ Auto-loading setup
    └─→ Load core classes
            ↓
        routes/web.php (Define routes)
            ↓
        Router::dispatch()
            ↓
            ├─→ Match URL pattern
            ├─→ Extract route parameters
            └─→ Call matched controller@method
                    ↓
                Controller method executes
                    ├─→ Check authentication (if needed)
                    ├─→ Get request data
                    ├─→ Load/process data
                    ├─→ Render view or return JSON
                    └─→ Send response
                            ↓
                        HTTP Response
```

## Core Components

### 1. Front Controller (public/index.php)
- Single entry point for all requests
- Sets up environment (timezone, error handling)
- Initializes auto-loading
- Loads route definitions
- Dispatches request

### 2. Router (app/Core/Router.php)
- Maps URLs to controller methods
- Supports GET/POST routes
- Named parameter extraction: `/systems/{id}`
- Pattern matching with regex

**Example:**
```php
$router->get('/systems/{id}', 'SystemsController@show');
// Matches: /systems/123, /systems/abc
// Sets: request->param('id') = '123'
```

### 3. Request (app/Core/Request.php)
Wraps HTTP request data:
- `$request->method()` - GET, POST
- `$request->path()` - /systems/123
- `$request->input('key')` - POST data
- `$request->query('key')` - GET data
- `$request->param('id')` - Route param
- `$request->file('upload')` - Uploaded file

### 4. Response (app/Core/Response.php)
Builds HTTP responses:
```php
$response->status(200);
$response->header('X-Custom', 'value');
$response->json(['key' => 'value']);
$response->html($content);
$response->redirect('/path');
```

### 5. Database (app/Core/DB.php)
PDO singleton with query methods:
```php
$db = DB::instance();

// Queries
$rows = $db->query("SELECT * FROM users WHERE role = ?", ['admin']);
$row = $db->queryOne("SELECT * FROM users WHERE id = ?", [1]);

// Insert/Update/Delete
$id = $db->insert("INSERT INTO users (name) VALUES ?", ['John']);
$rows = $db->execute("UPDATE users SET name = ? WHERE id = ?", ['Jane', 1]);
$rows = $db->execute("DELETE FROM users WHERE id = ?", [1]);

// Transactions
$db->beginTransaction();
try {
    $db->execute(...);
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
}
```

### 6. Authentication (app/Core/Auth.php)
Session-based user auth:
```php
// Login
Auth::login($userId, ['name' => 'John', 'role' => 'admin']);

// Check
Auth::check();              // bool
Auth::user();               // array
Auth::id();                 // int
Auth::get('name');          // string
Auth::isAdmin();            // bool

// Guard
Auth::guard();              // Redirect if not logged in
Auth::guardAdmin();         // Redirect if not admin

// Logout
Auth::logout();
```

### 7. CSRF Protection (app/Core/CSRF.php)
Token-based CSRF defense:
```php
// In templates
<?php echo CSRF::field(); ?>
<!-- <input type="hidden" name="csrf_token" value="..."> -->

// In controller
if (!CSRF::check($request)) {
    return $response->error('Invalid token', 419);
}

// Regenerate (after login)
CSRF::regenerate();
```

### 8. View (app/Core/View.php)
Template rendering engine:
```php
// Simple render
$html = $view->render('dashboard/index', ['title' => 'Dashboard']);

// With layout
$html = $view->renderWithLayout(
    'dashboard/index',  // Template
    'app',              // Layout
    ['title' => 'Dashboard']
);

// Partial include
$view->partial('components/card', ['data' => $data]);

// Helpers
$view->escape($string);
$view->route('/systems', ['id' => 1]);
$view->url('/images/logo.png');
$view->csrfField();
```

### 9. Base Controller (app/Core/Controller.php)
Provides controller methods:
```php
class MyController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        // Authentication
        $this->requireAuth();
        $this->requireAdmin();

        // Get data
        $id = $this->param('id');
        $email = $this->input('email');
        $file = $this->file('upload');

        // Render
        $this->show('template', ['data' => $data]);
        $this->json(['success' => true]);
        $this->redirect('/path');
    }
}
```

### 10. Base Model (app/Core/Model.php)
ORM-like query builder:
```php
class System extends Model
{
    protected $table = 'systems';
    protected $fillable = ['name', 'description'];
}

// Usage
$system = System::find(1);
$all = System::all();
$active = System::where('status', '=', 'active');
$new = System::create(['name' => 'Test']);
System::update(1, ['name' => 'Updated']);
System::delete(1);

// Attributes
$system->name;
$system->getAttribute('name');
$system->toArray();
```

## Routing System

Routes are defined in `routes/web.php`:

```php
// Basic routes
$router->get('/', 'HomeController@index');
$router->post('/save', 'DataController@save');

// Route parameters
$router->get('/users/{id}', 'UserController@show');
// Access: $request->param('id')

// Nested parameters
$router->get('/systems/{id}/details/{section}', 'SystemController@detail');
// Access: $request->param('id'), $request->param('section')
```

**Pattern Matching:**
```
Route Pattern: /systems/{id}
Regex:        /^\/systems\/(?<id>[^/]+)$/

Matches:
- /systems/1      → id=1
- /systems/abc    → id=abc
- /systems/1/sub  → NO MATCH

Method Check:
- Only matches if request method equals route method
```

## Controller Pattern

All controllers extend the base `Controller` class:

```php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class ExampleController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        // Require authentication
        $this->requireAuth();

        // Get request data
        $id = $this->param('id');
        $name = $this->input('name');

        // Load data
        $data = ['id' => $id, 'name' => $name];

        // Render view with layout
        $this->show('example/index', $data);
    }

    public function store(Request $request, Response $response): void
    {
        $this->requireAuth();

        // Verify CSRF
        if (!CSRF::check($request)) {
            $this->error('Invalid token', 419);
            return;
        }

        // Process data
        $input = $this->all();

        // Return JSON
        $this->json(['success' => true, 'data' => $input]);
    }
}
```

## Model Pattern

Create models by extending the base `Model` class:

```php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'password_hash', 'role'];
    protected $hidden = ['password_hash'];
}

// Usage
$user = User::find(1);
$users = User::all();
$active = User::where('role', '=', 'admin');
$created = User::create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password_hash' => password_hash('secret', PASSWORD_ARGON2ID),
    'role' => 'user',
]);
User::update(1, ['role' => 'admin']);
User::delete(1);
```

## View Templates

Template structure:
```
views/
├── layouts/
│   └── app.php              # Main layout
├── auth/
│   ├── login.php
│   └── register.php
└── dashboard/
    └── index.php
```

**Layout (`layouts/app.php`):**
```php
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
</head>
<body>
    <header><!-- Navigation --></header>
    <main>
        <?php echo $content_for_layout; ?>
    </main>
    <footer><!-- Footer --></footer>
</body>
</html>
```

**Template (`dashboard/index.php`):**
```php
<h1><?php echo $title; ?></h1>
<p><?php echo $view->escape($description); ?></p>

<!-- Include partial -->
<?php $view->partial('components/card', ['data' => $data]); ?>

<!-- CSRF form -->
<form method="POST" action="/action">
    <?php echo $view->csrfField(); ?>
    <input type="text" name="title">
    <button type="submit">Save</button>
</form>
```

## Database Queries

**Using Query Builder:**
```php
use App\Core\DB;

$db = DB::instance();

// SELECT
$results = $db->query(
    "SELECT * FROM systems WHERE category = ? ORDER BY name",
    ['hydraulics']
);

// Single row
$system = $db->queryOne(
    "SELECT * FROM systems WHERE id = ?",
    [1]
);

// INSERT
$id = $db->insert(
    "INSERT INTO systems (name, category) VALUES (?, ?)",
    ['Hydraulics', 'main']
);

// UPDATE
$affected = $db->execute(
    "UPDATE systems SET name = ? WHERE id = ?",
    ['Updated Name', 1]
);

// DELETE
$affected = $db->execute(
    "DELETE FROM systems WHERE id = ?",
    [1]
);

// Transactions
$db->beginTransaction();
try {
    $db->execute(...);
    $db->execute(...);
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    throw $e;
}
```

## Configuration

### `config/app.php`
Application settings:
```php
return [
    'name' => 'Q400 Study',
    'debug' => true,
    'base_url' => 'http://localhost/q400-study/public',
    'session_name' => 'q400_study_session',
];
```

### `config/database.php`
Database connection:
```php
return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'q400_study',
    'username' => 'root',
    'password' => '',
];
```

## Security

### 1. SQL Injection Prevention
All queries use prepared statements:
```php
// SAFE - uses parameterized query
$result = $db->query("SELECT * FROM users WHERE id = ?", [$id]);

// UNSAFE - never do this
$result = $db->query("SELECT * FROM users WHERE id = $id");
```

### 2. CSRF Protection
All POST forms include CSRF token:
```php
// In template
<?php echo CSRF::field(); ?>

// In controller
if (!CSRF::check($request)) {
    return $response->error('Invalid token', 419);
}
```

### 3. XSS Prevention
Always escape output:
```php
<!-- SAFE -->
<?php echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8'); ?>
<?php echo $view->escape($user_input); ?>

<!-- UNSAFE -->
<?php echo $user_input; ?>
```

### 4. Authentication
Use the Auth class for protected routes:
```php
$this->requireAuth();      // Redirect if not logged in
$this->requireAdmin();     // Redirect if not admin
```

### 5. HTTPS
Use HTTPS in production by updating config/app.php:
```php
'base_url' => 'https://yourdomain.com/q400-study/public'
```

## Performance

1. **Database Queries:** Use prepared statements (automatic with DB class)
2. **Caching:** Query results can be cached (future implementation)
3. **Templates:** Output buffering for efficient rendering
4. **Route Matching:** Regex patterns efficiently match routes
5. **Static Methods:** Models use static methods for less memory overhead

## Testing

The architecture supports:
- Unit testing of models and controllers
- Integration testing of routes
- Database testing with transactions (for rollback)

Future: Create `tests/` directory with PHPUnit tests.

## Extending the App

### Add a New Controller
1. Create `app/Controllers/NameController.php`
2. Extend `Controller`
3. Add route to `routes/web.php`

### Add a New Model
1. Create `app/Models/Name.php`
2. Extend `Model`
3. Define `$table` property

### Add a New Route
Edit `routes/web.php`:
```php
$router->get('/path', 'Controller@method');
```

### Add a New View
Create view file in `views/` directory:
```php
views/section/template.php
```

Render with:
```php
$this->show('section/template', $data);
```

## Deployment

1. Set `debug => false` in config/app.php
2. Use HTTPS (update base_url)
3. Set strong CSRF encryption key
4. Configure proper file permissions
5. Set up MySQL backups
6. Use environment variables for sensitive data

## Future Enhancements

- [ ] Environment variable support (.env)
- [ ] Database migrations
- [ ] Caching layer
- [ ] Email support
- [ ] File upload handling
- [ ] API rate limiting
- [ ] Unit testing suite
- [ ] Middleware system
- [ ] Query builder improvements
- [ ] Admin panel components
