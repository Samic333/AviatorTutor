<?php
/**
 * Base Model Class
 *
 * Provides common database operations using PDO
 * Extend this class to create your own models
 */

namespace App\Core;

use PDOStatement;

abstract class Model
{
    /**
     * Table name - must be defined in child classes
     */
    protected string $table = '';

    /**
     * Primary key column
     */
    protected string $primaryKey = 'id';

    /**
     * Fillable attributes - columns that can be mass-assigned
     */
    protected array $fillable = [];

    /**
     * Hidden attributes - not included in toArray()
     */
    protected array $hidden = [];

    /**
     * Casts for type conversion
     */
    protected array $casts = [];

    /**
     * Model attributes
     */
    protected array $attributes = [];

    /**
     * Constructor
     *
     * @param array $attributes Initial attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the database instance
     *
     * @return DB
     */
    public static function db(): DB
    {
        return DB::instance();
    }

    /**
     * Find a record by primary key
     *
     * @param mixed $id Primary key value
     * @return static|null
     */
    public static function find(mixed $id): ?static
    {
        $instance = new static();
        $record = self::db()->queryOne(
            "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?",
            [$id]
        );

        return $record ? new static($record) : null;
    }

    /**
     * Get all records
     *
     * @return array Array of model instances
     */
    public static function all(): array
    {
        $instance = new static();
        $records = self::db()->query("SELECT * FROM {$instance->table}");

        return array_map(
            fn($record) => new static($record),
            $records
        );
    }

    /**
     * Query records with WHERE clause
     *
     * @param string $column Column name
     * @param string $operator Comparison operator (=, !=, >, <, >=, <=, LIKE)
     * @param mixed $value Value to compare
     * @return array Array of model instances
     */
    public static function where(string $column, string $operator, mixed $value): array
    {
        $instance = new static();
        $records = self::db()->query(
            "SELECT * FROM {$instance->table} WHERE {$column} {$operator} ?",
            [$value]
        );

        return array_map(
            fn($record) => new static($record),
            $records
        );
    }

    /**
     * Query a single record with WHERE clause
     *
     * @param string $column Column name
     * @param mixed $value Value to compare
     * @return static|null
     */
    public static function whereFirst(string $column, mixed $value): ?static
    {
        $instance = new static();
        $record = self::db()->queryOne(
            "SELECT * FROM {$instance->table} WHERE {$column} = ?",
            [$value]
        );

        return $record ? new static($record) : null;
    }

    /**
     * Create a new record
     *
     * @param array $data Data to insert
     * @return static|false New model instance or false on failure
     */
    public static function create(array $data): static|false
    {
        $instance = new static();

        // Filter to only fillable attributes
        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }

        if (empty($data)) {
            return false;
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $instance->table,
            implode(',', $columns),
            implode(',', $placeholders)
        );

        $id = self::db()->insert($sql, array_values($data));

        if ($id === false) {
            return false;
        }

        $data[$instance->primaryKey] = $id;
        return new static($data);
    }

    /**
     * Update a record by primary key
     *
     * @param mixed $id Primary key value
     * @param array $data Data to update
     * @return bool
     */
    public static function update(mixed $id, array $data): bool
    {
        $instance = new static();

        // Filter to only fillable attributes
        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }

        if (empty($data)) {
            return false;
        }

        $setClauses = array_map(fn($col) => "{$col} = ?", array_keys($data));

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = ?',
            $instance->table,
            implode(', ', $setClauses),
            $instance->primaryKey
        );

        $values = array_values($data);
        $values[] = $id;

        return self::db()->execute($sql, $values) > 0;
    }

    /**
     * Delete a record by primary key
     *
     * @param mixed $id Primary key value
     * @return bool
     */
    public static function delete(mixed $id): bool
    {
        $instance = new static();
        $sql = "DELETE FROM {$instance->table} WHERE {$instance->primaryKey} = ?";
        return self::db()->execute($sql, [$id]) > 0;
    }

    /**
     * Delete with WHERE clause
     *
     * @param string $column Column name
     * @param mixed $value Value to compare
     * @return int Number of affected rows
     */
    public static function deleteWhere(string $column, mixed $value): int
    {
        $instance = new static();
        $sql = "DELETE FROM {$instance->table} WHERE {$column} = ?";
        return self::db()->execute($sql, [$value]);
    }

    /**
     * Execute raw SQL query
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return mixed Query result
     */
    public static function query(string $sql, array $params = []): mixed
    {
        return self::db()->query($sql, $params);
    }

    /**
     * Count records
     *
     * @return int
     */
    public static function count(): int
    {
        $instance = new static();
        $result = self::db()->queryOne("SELECT COUNT(*) as count FROM {$instance->table}");
        return $result['count'] ?? 0;
    }

    /**
     * Check if record exists
     *
     * @param string $column Column name
     * @param mixed $value Value to compare
     * @return bool
     */
    public static function exists(string $column, mixed $value): bool
    {
        $instance = new static();
        $result = self::db()->queryOne(
            "SELECT 1 FROM {$instance->table} WHERE {$column} = ? LIMIT 1",
            [$value]
        );
        return $result !== false;
    }

    /**
     * Get an attribute value
     *
     * @param string $key Attribute name
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set an attribute value
     *
     * @param string $key Attribute name
     * @param mixed $value Attribute value
     * @return void
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Convert model to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = $this->attributes;

        // Remove hidden attributes
        foreach ($this->hidden as $key) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * Magic getter for attributes
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Magic setter for attributes
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Check if attribute exists
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * JSON serialize
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
