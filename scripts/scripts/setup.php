<?php
/**
 * Q400 Aircraft Systems Study App - Setup Wizard
 * 
 * Checks environment, creates database, runs migrations
 * Usage: php scripts/setup.php
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

// Base path
$basePath = dirname(__DIR__);

// Console output class
class Console
{
    const RESET = "\033[0m";
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const MAGENTA = "\033[35m";
    const CYAN = "\033[36m";
    const BOLD = "\033[1m";

    public static function success($message)
    {
        echo self::GREEN . "✓ " . $message . self::RESET . PHP_EOL;
    }

    public static function error($message)
    {
        echo self::RED . "✗ " . $message . self::RESET . PHP_EOL;
    }

    public static function warning($message)
    {
        echo self::YELLOW . "⚠ " . $message . self::RESET . PHP_EOL;
    }

    public static function info($message)
    {
        echo self::BLUE . "ℹ " . $message . self::RESET . PHP_EOL;
    }

    public static function heading($message)
    {
        echo "\n" . self::BOLD . self::MAGENTA . "╔════════════════════════════════════════╗" . self::RESET . PHP_EOL;
        echo self::BOLD . self::MAGENTA . "║ " . str_pad($message, 36) . " ║" . self::RESET . PHP_EOL;
        echo self::BOLD . self::MAGENTA . "╚════════════════════════════════════════╝" . self::RESET . PHP_EOL;
    }

    public static function section($title)
    {
        echo "\n" . self::BOLD . self::CYAN . ">>> " . $title . self::RESET . PHP_EOL;
    }

    public static function line()
    {
        echo PHP_EOL;
    }
}

// Setup class
class SetupWizard
{
    private $basePath;
    private $config;
    private $errors = [];
    private $warnings = [];

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->loadConfig();
    }

    private function loadConfig()
    {
        if (!file_exists($this->basePath . '/config/database.php')) {
            $this->errors[] = "Database configuration not found";
            return;
        }

        $this->config = require $this->basePath . '/config/database.php';
    }

    public function run()
    {
        Console::heading("Q400 Study App Setup Wizard");

        // Step 1: Check PHP version
        if (!$this->checkPHPVersion()) {
            return false;
        }

        // Step 2: Check extensions
        if (!$this->checkExtensions()) {
            return false;
        }

        // Step 3: Check configuration
        if (!$this->checkConfiguration()) {
            return false;
        }

        // Step 4: Test database connection
        if (!$this->testConnection()) {
            return false;
        }

        // Step 5: Create directories
        if (!$this->createDirectories()) {
            return false;
        }

        // Step 6: Run schema and seed
        if (!$this->runDatabase()) {
            return false;
        }

        // Step 7: Summary
        $this->showSummary();

        return true;
    }

    private function checkPHPVersion()
    {
        Console::section("Checking PHP Version");

        $version = phpversion();
        $required = '8.0.0';

        if (version_compare($version, $required, '>=')) {
            Console::success("PHP version: $version");
            return true;
        } else {
            Console::error("PHP version $required or higher required, found: $version");
            $this->errors[] = "PHP version too old";
            return false;
        }
    }

    private function checkExtensions()
    {
        Console::section("Checking PHP Extensions");

        $required = ['pdo', 'pdo_mysql', 'json'];
        $missing = [];

        foreach ($required as $ext) {
            if (extension_loaded($ext)) {
                Console::success("Extension loaded: $ext");
            } else {
                Console::error("Extension missing: $ext");
                $missing[] = $ext;
            }
        }

        if (!empty($missing)) {
            $this->errors[] = "Missing extensions: " . implode(', ', $missing);
            return false;
        }

        return true;
    }

    private function checkConfiguration()
    {
        Console::section("Checking Configuration");

        if (empty($this->config)) {
            Console::error("No database configuration found");
            $this->errors[] = "Database configuration missing";
            return false;
        }

        $required = ['host', 'username', 'database'];

        foreach ($required as $key) {
            if (isset($this->config[$key])) {
                $value = $this->config[$key];
                if ($key === 'username') {
                    Console::success("Configuration: $key = set");
                } else {
                    Console::success("Configuration: $key = $value");
                }
            } else {
                Console::warning("Configuration: $key = not set (using default)");
            }
        }

        return true;
    }

    private function testConnection()
    {
        Console::section("Testing Database Connection");

        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%d",
                $this->config['host'] ?? 'localhost',
                $this->config['port'] ?? 3306
            );

            $pdo = new PDO(
                $dsn,
                $this->config['username'] ?? 'root',
                $this->config['password'] ?? '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            Console::success("Database connection successful");
            Console::info("Host: " . ($this->config['host'] ?? 'localhost'));
            Console::info("Port: " . ($this->config['port'] ?? 3306));

            return true;
        } catch (PDOException $e) {
            Console::error("Connection failed: " . $e->getMessage());
            $this->errors[] = "Cannot connect to database";
            return false;
        }
    }

    private function createDirectories()
    {
        Console::section("Creating Required Directories");

        $dirs = [
            'storage/uploads',
            'storage/diagrams',
            'storage/logs',
            'database/seeds',
        ];

        foreach ($dirs as $dir) {
            $path = $this->basePath . '/' . $dir;

            if (is_dir($path)) {
                Console::info("Directory exists: $dir");
            } else {
                if (@mkdir($path, 0755, true)) {
                    Console::success("Directory created: $dir");
                } else {
                    Console::warning("Could not create directory: $dir");
                    $this->warnings[] = "Directory creation failed: $dir";
                }
            }
        }

        return true;
    }

    private function runDatabase()
    {
        Console::section("Setting Up Database");

        try {
            // Connect to MySQL
            $dsn = sprintf(
                "mysql:host=%s;port=%d;charset=utf8mb4",
                $this->config['host'] ?? 'localhost',
                $this->config['port'] ?? 3306
            );

            $pdo = new PDO(
                $dsn,
                $this->config['username'] ?? 'root',
                $this->config['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            // Drop existing database
            Console::info("Dropping existing database if present...");
            $pdo->exec("DROP DATABASE IF EXISTS `" . ($this->config['database'] ?? 'q400_study') . "`");

            // Create database
            Console::info("Creating database...");
            $pdo->exec("CREATE DATABASE `" . ($this->config['database'] ?? 'q400_study') . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            Console::success("Database created");

            // Select database
            $pdo->exec("USE `" . ($this->config['database'] ?? 'q400_study') . "`");

            // Load schema
            Console::info("Loading schema...");
            $schemaFile = $this->basePath . '/database/schema.sql';
            if (!file_exists($schemaFile)) {
                throw new Exception("Schema file not found: $schemaFile");
            }

            $sql = file_get_contents($schemaFile);
            $statements = preg_split('/;(?=\s*(--|\/\*|$))/m', $sql);

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            Console::success("Schema loaded");

            // Load seed data
            Console::info("Loading seed data...");
            $seedFile = $this->basePath . '/database/seed_data.sql';
            if (!file_exists($seedFile)) {
                Console::warning("Seed file not found, skipping initial data");
            } else {
                $sql = file_get_contents($seedFile);
                $statements = preg_split('/;(?=\s*(--|\/\*|$))/m', $sql);

                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        $pdo->exec($statement);
                    }
                }
                Console::success("Seed data loaded");
            }

            return true;
        } catch (Exception $e) {
            Console::error("Database setup failed: " . $e->getMessage());
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    private function showSummary()
    {
        Console::line();
        Console::heading("Setup Summary");

        if (empty($this->errors)) {
            Console::success("Setup completed successfully!");
            Console::info("Database: " . ($this->config['database'] ?? 'q400_study'));
            Console::info("Host: " . ($this->config['host'] ?? 'localhost'));

            Console::line();
            Console::info("Next steps:");
            Console::info("1. Configure your web server (Apache/Nginx)");
            Console::info("2. Set environment variables if needed");
            Console::info("3. Start your PHP development server or web server");
            Console::info("4. Access the application via your configured URL");
        } else {
            Console::error("Setup completed with errors:");
            foreach ($this->errors as $error) {
                Console::error("  - $error");
            }
        }

        if (!empty($this->warnings)) {
            Console::line();
            Console::warning("Warnings:");
            foreach ($this->warnings as $warning) {
                Console::warning("  - $warning");
            }
        }

        Console::line();
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }
}

// Execute setup
try {
    $wizard = new SetupWizard($basePath);

    if ($wizard->run()) {
        exit($wizard->hasErrors() ? 1 : 0);
    } else {
        exit(1);
    }
} catch (Exception $e) {
    Console::error("Fatal error: " . $e->getMessage());
    exit(1);
}
