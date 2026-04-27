<?php
/**
 * Q400 Aircraft Systems Study Database Seeder
 * Seeds the database with schema and initial data
 * 
 * Usage: php scripts/seed_database.php
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set execution time limit
set_time_limit(300);

// Get base path
$basePath = dirname(__DIR__);

// Load configuration
$config = require $basePath . '/config/database.php';

// Color codes for output
class Console
{
    const RESET = "\033[0m";
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
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
        echo self::BOLD . self::BLUE . "\n=== " . $message . " ===" . self::RESET . PHP_EOL;
    }
}

// Database helper class
class Database
{
    private $connection;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        try {
            // Connect to MySQL server first (without specific database)
            $dsn = sprintf(
                "mysql:host=%s;port=%d;charset=%s",
                $this->config['host'],
                $this->config['port'],
                $this->config['charset']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            Console::success("Connected to MySQL server");
            return true;
        } catch (PDOException $e) {
            Console::error("Database connection failed: " . $e->getMessage());
            return false;
        }
    }

    public function selectDatabase()
    {
        try {
            $this->connection->exec("USE `" . $this->config['database'] . "`");
            Console::success("Selected database: " . $this->config['database']);
            return true;
        } catch (PDOException $e) {
            Console::error("Failed to select database: " . $e->getMessage());
            return false;
        }
    }

    public function executeSQLFile($filepath)
    {
        if (!file_exists($filepath)) {
            Console::error("SQL file not found: $filepath");
            return false;
        }

        try {
            $sql = file_get_contents($filepath);
            
            // Split by semicolons, handling multi-line statements
            $statements = preg_split('/;(?=\s*(--|\/\*|$))/m', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $this->connection->exec($statement);
                }
            }

            return true;
        } catch (PDOException $e) {
            Console::error("SQL execution error: " . $e->getMessage());
            return false;
        }
    }

    public function importJSON($filepath)
    {
        if (!file_exists($filepath)) {
            Console::warning("JSON file not found: $filepath");
            return 0;
        }

        try {
            $json = file_get_contents($filepath);
            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Console::error("Invalid JSON in $filepath: " . json_last_error_msg());
                return 0;
            }

            return $data;
        } catch (Exception $e) {
            Console::error("JSON import error: " . $e->getMessage());
            return 0;
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function execute($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            Console::error("Query execution failed: " . $e->getMessage());
            return false;
        }
    }

    public function query($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Console::error("Query failed: " . $e->getMessage());
            return [];
        }
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}

// Main seeder class
class DatabaseSeeder
{
    private $db;
    private $basePath;
    private $recordsImported = 0;

    public function __construct($db, $basePath)
    {
        $this->db = $db;
        $this->basePath = $basePath;
    }

    public function seed()
    {
        Console::heading("Q400 Aircraft Systems Study Database Seeder");

        // Step 1: Create database
        if (!$this->createDatabase()) {
            return false;
        }

        // Step 2: Load schema
        if (!$this->loadSchema()) {
            return false;
        }

        // Step 3: Load seed data
        if (!$this->loadSeedData()) {
            return false;
        }

        // Step 4: Import content from JSON files
        if (!$this->importContentFromJSON()) {
            return false;
        }

        Console::heading("Seeding Completed Successfully");
        Console::info("Total records imported: " . $this->recordsImported);

        return true;
    }

    private function createDatabase()
    {
        Console::heading("Creating Database");

        try {
            // Create database
            $dbName = $this->db->getConnection()->quote($this->db->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' 
                ? "`q400_study`" 
                : "\"q400_study\"");
            
            $this->db->getConnection()->exec("DROP DATABASE IF EXISTS q400_study");
            Console::info("Dropped existing database");
            
            $this->db->getConnection()->exec("CREATE DATABASE IF NOT EXISTS q400_study CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            Console::success("Database created: q400_study");
            
            return true;
        } catch (PDOException $e) {
            Console::error("Database creation failed: " . $e->getMessage());
            return false;
        }
    }

    private function loadSchema()
    {
        Console::heading("Loading Schema");

        $schemaFile = $this->basePath . '/database/schema.sql';
        
        // Select database first
        if (!$this->db->selectDatabase()) {
            return false;
        }

        if (!$this->db->executeSQLFile($schemaFile)) {
            Console::error("Failed to execute schema file");
            return false;
        }

        Console::success("Schema loaded successfully");
        return true;
    }

    private function loadSeedData()
    {
        Console::heading("Loading Seed Data");

        $seedFile = $this->basePath . '/database/seed_data.sql';

        if (!$this->db->executeSQLFile($seedFile)) {
            Console::error("Failed to execute seed data file");
            return false;
        }

        // Count inserted records
        $stats = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM users) as users,
                (SELECT COUNT(*) FROM systems) as systems,
                (SELECT COUNT(*) FROM subtopics) as subtopics,
                (SELECT COUNT(*) FROM lessons) as lessons,
                (SELECT COUNT(*) FROM flashcards) as flashcards,
                (SELECT COUNT(*) FROM quizzes) as quizzes,
                (SELECT COUNT(*) FROM quiz_questions) as quiz_questions
        ");

        if (!empty($stats)) {
            $stat = $stats[0];
            Console::success("Users: " . $stat['users']);
            Console::success("Systems: " . $stat['systems']);
            Console::success("Subtopics: " . $stat['subtopics']);
            Console::success("Lessons: " . $stat['lessons']);
            Console::success("Flashcards: " . $stat['flashcards']);
            Console::success("Quizzes: " . $stat['quizzes']);
            Console::success("Quiz Questions: " . $stat['quiz_questions']);

            $this->recordsImported = array_sum($stat);
        }

        return true;
    }

    private function importContentFromJSON()
    {
        Console::heading("Importing Content from JSON");

        $seedsDir = $this->basePath . '/database/seeds';

        if (!is_dir($seedsDir)) {
            Console::warning("Seeds directory not found, skipping JSON import");
            return true;
        }

        $indexFile = $seedsDir . '/systems_index.json';
        
        if (!file_exists($indexFile)) {
            Console::warning("Systems index not found, skipping JSON import");
            return true;
        }

        $index = $this->db->importJSON($indexFile);
        if (empty($index) || !isset($index['systems'])) {
            Console::warning("Invalid systems index format");
            return true;
        }

        $imported = 0;

        foreach ($index['systems'] as $systemData) {
            if (!isset($systemData['slug'])) {
                continue;
            }

            // Find matching system in database
            $systems = $this->db->query(
                "SELECT id FROM systems WHERE slug = ?",
                [$systemData['slug']]
            );

            if (empty($systems)) {
                Console::warning("System not found: " . $systemData['slug']);
                continue;
            }

            $systemId = $systems[0]['id'];

            // Import system content
            $contentFile = $seedsDir . '/' . $systemData['slug'] . '.json';
            if (!file_exists($contentFile)) {
                Console::info("Content file not found for system: " . $systemData['slug']);
                continue;
            }

            $content = $this->db->importJSON($contentFile);
            if (empty($content)) {
                continue;
            }

            // Create lesson from content
            try {
                $title = ($content['name'] ?? $systemData['name']) . ' - Overview';
                $body = $content['full_text'] ?? substr($content['description'] ?? '', 0, 5000);

                $this->db->execute(
                    "INSERT INTO lessons (system_id, title, slug, content_type, body, summary, sort_order, is_published)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $systemId,
                        $title,
                        str_replace(' ', '-', strtolower($systemData['slug'])) . '-overview',
                        'overview',
                        $body,
                        substr($body, 0, 500),
                        1,
                        1
                    ]
                );

                $lessonId = $this->db->lastInsertId();
                $imported++;

                // Create sections if available
                if (isset($content['sections']) && is_array($content['sections'])) {
                    foreach ($content['sections'] as $index => $section) {
                        $this->db->execute(
                            "INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order)
                             VALUES (?, ?, ?, ?, ?)",
                            [
                                $lessonId,
                                $section['title'] ?? 'Section ' . ($index + 1),
                                $section['content'] ?? '',
                                $section['type'] ?? 'overview',
                                $index + 1
                            ]
                        );
                    }
                }

                // Log import
                $this->db->execute(
                    "INSERT INTO content_import_logs (filename, import_type, status, records_imported)
                     VALUES (?, ?, ?, ?)",
                    [
                        basename($contentFile),
                        'json_content',
                        'completed',
                        1
                    ]
                );

                Console::info("Imported content for: " . $systemData['name']);
            } catch (Exception $e) {
                Console::error("Failed to import content for " . $systemData['slug'] . ": " . $e->getMessage());
            }
        }

        Console::success("JSON import completed: " . $imported . " system contents imported");
        $this->recordsImported += $imported;

        return true;
    }
}

// Main execution
try {
    $db = new Database($config);

    if (!$db->connect()) {
        exit(1);
    }

    $seeder = new DatabaseSeeder($db, $basePath);

    if ($seeder->seed()) {
        Console::success("\nDatabase seeding completed successfully!");
        exit(0);
    } else {
        Console::error("\nDatabase seeding failed!");
        exit(1);
    }
} catch (Exception $e) {
    Console::error("Fatal error: " . $e->getMessage());
    exit(1);
}
