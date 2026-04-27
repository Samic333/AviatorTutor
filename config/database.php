<?php
/**
 * Database configuration.
 *
 * Production override: create config/database.local.php (gitignored) returning
 * an array with the same keys to override these defaults — typically
 * 'host', 'database', 'username', 'password'.
 */

$defaults = [
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'port'      => 3306,
    'database'  => 'aviatortutor',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'options' => [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ],
    'log_queries' => false,
];

$localPath = __DIR__ . '/database.local.php';
if (is_file($localPath)) {
    $local = require $localPath;
    if (is_array($local)) {
        // 'options' merges; everything else replaces
        if (isset($local['options']) && is_array($local['options'])) {
            $local['options'] = $local['options'] + $defaults['options'];
        }
        return array_replace($defaults, $local);
    }
}

return $defaults;
