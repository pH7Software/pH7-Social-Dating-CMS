<?php
/**
 * PHP built-in server router for pH7Builder
 * Routes all requests through index.php while serving static files directly
 */

$uri = $_SERVER['REQUEST_URI'];

// Strip query string for file checks
$path = parse_url($uri, PHP_URL_PATH);
$filePath = __DIR__ . $path;

// Serve static files directly if they exist
if ($path !== '/' && file_exists($filePath) && is_file($filePath)) {
    return false;
}

// If requesting the _install directory, serve its index.php directly
if (strpos($path, '/_install') === 0) {
    $installIndex = __DIR__ . '/_install/index.php';
    if (file_exists($installIndex)) {
        $_SERVER['SCRIPT_FILENAME'] = $installIndex;
        $_SERVER['SCRIPT_NAME'] = '/_install/index.php';
        $_SERVER['PHP_SELF'] = '/_install/index.php';
        chdir(__DIR__ . '/_install');
        require $installIndex;
        return;
    }
}

// For all other requests, route through index.php
require __DIR__ . '/index.php';
