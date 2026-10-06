<?php

declare(strict_types=1);

$requestedFile = __DIR__ . parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

/*
 * Allow the web server to serve real files such as CSS, images, or JavaScript.
 */
if (is_file($requestedFile)) {
    return false;
}

/*
 * Send all other requests to the front controller.
 */
require __DIR__ . '/index.php';

