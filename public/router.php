<?php
// Router script for PHP built-in server to forward non-existing files to index.php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$requested = __DIR__ . $uri;

// If the requested resource is a file that exists in the public directory, serve it directly
if ($uri !== '/' && file_exists($requested) && is_file($requested)) {
    return false; // let the built-in server serve the file
}

// Otherwise, route to front controller
require_once __DIR__ . '/index.php';
