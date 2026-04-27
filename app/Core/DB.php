<?php
/**
 * Database Connection Manager
 *
 * Singleton pattern PDO wrapper with query logging and error handling
 */

namespace App\Core;

use PDO;
use PDOException;

class DB
{
    /**
     * Singleton instance
     */
    private static ?self $instance = null;

    /**
     * PDO connection
     */
    private PDO $connection;

    /**
     * Database configuration
     */
    private array $config;

    /**
     * Query log for debugging
     */
    private array $queries = [];

    /**
     * Constructor - private to enforce singleton
     */
    private function __construct()
    {
        $this->config = require BASE_PATH . '/config/database.php';
        $this->connect();
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
    }

    /**
     * Get singleton instance
     *
     * @return self
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish database connection
     *
     * @throws PDOException
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s;charset=%s',
                $this->config['driver'],
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options'] ?? []
            );

            // Log successful connection
            $this->log("Database connected successfully");
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Get the PDO connection instance
     *
     * @return PDO
     */
    public function connection(): PDO
    {
        return $this->connection;
    }

    /**
     * Execute a prepared statement
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return \PDOStatement
     * @throws PDOException
     */
    public function prepare(string $sql, array $params = []): \PDOStatement
    {
        try {
            $statement = $this->connection->prepare($sql);
            $this->log($sql, $params);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            $this->handleError($e, $sql, $params);
        }
    }

    /**
     * Execute a query and fetch all results
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return array
     */
    public function query(string $sql, array $params = []): array
    {
        return $this->prepare($sql, $params)->fetchAll();
    }

    /**
     * Execute a query and fetch one result
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return mixed
     */
    public function queryOne(string $sql, array $params = []): mixed
    {
        return $this->prepare($sql, $params)->fetch();
    }

    /**
     * Execute an insert query and return last insert ID
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return string|false
     */
    public function insert(string $sql, array $params = []): string|false
    {
        try {
            $this->prepare($sql, $params);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError($e, $sql, $params);
            return false;
        }
    }

    /**
     * Execute an update or delete query and return affected rows
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return int
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            $statement = $this->prepare($sql, $params);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e, $sql, $params);
            return 0;
        }
    }

    /**
     * Begin a transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a transaction
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->connection->rollback();
    }

    /**
     * Log a query for debugging
     *
     * @param string $sql SQL query
     * @param array $params Query parameters
     */
    private function log(string $sql, array $params = []): void
    {
        if ($this->config['log_queries'] ?? false) {
            $this->queries[] = [
                'sql' => $sql,
                'params' => $params,
                'time' => microtime(true),
            ];
        }
    }

    /**
     * Get query log
     *
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Handle database errors
     *
     * @param PDOException $e The exception
     * @param string $sql The SQL query
     * @param array $params The query parameters
     * @throws PDOException
     */
    private function handleError(PDOException $e, string $sql = '', array $params = []): void
    {
        $config = require BASE_PATH . '/config/app.php';

        if ($config['debug'] ?? false) {
            throw new PDOException(
                "Database Error: {$e->getMessage()}\nSQL: {$sql}\nParams: " . json_encode($params),
                (int)$e->getCode(),
                $e
            );
        }

        // Log error to file in production
        error_log("Database Error: {$e->getMessage()} | SQL: {$sql}");
        throw $e;
    }
}
