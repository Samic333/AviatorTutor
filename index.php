<?php
/**
 * Root entry point — bridge to public/index.php.
 *
 * The real front controller lives at public/index.php. This file exists so
 * shared hosts (e.g. cPanel addon-domain setups) that point the document
 * root at the project root still serve the app correctly when mod_rewrite
 * is unavailable. With mod_rewrite, .htaccess rewrites first and this file
 * is rarely hit.
 */
require __DIR__ . '/public/index.php';
