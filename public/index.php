<?php

// During debugging, ensure all PHP errors are visible in the browser.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$ds = DIRECTORY_SEPARATOR;

// Catch fatal errors on shutdown and display them (useful for blank pages).
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err !== null) {
        http_response_code(500);
        echo "<pre style=\"white-space:pre-wrap; font-family:monospace;\">";
        echo "Shutdown error:\n";
        echo htmlspecialchars(print_r($err, true));
        echo "</pre>";
    }
});

// Wrap bootstrap in try/catch to surface exceptions immediately.
try {
    require(__DIR__. $ds . '..' . $ds . 'app' . $ds . 'config' . $ds . 'bootstrap.php');
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>Unhandled Exception</h1>";
    echo "<pre style=\"white-space:pre-wrap; font-family:monospace;\">" . htmlspecialchars($e->__toString()) . "</pre>";
    exit(1);
}