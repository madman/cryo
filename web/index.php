<?php

// PHP -S (built-in webserver) doesn't handle static assets without a `return false`
// For more information, see: http://silex.sensiolabs.org/doc/web_servers.html#php-5-4
if ('cli-server' === php_sapi_name()) {
    $filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);

    // If it is a file, just return false.
    if (is_file($filename)) {
        return false;
    }
}

require_once __DIR__ . '/../app/bootstrap.php';
$app->run();
