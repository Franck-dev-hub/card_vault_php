<?php

// This file is used by PHP's built-in server to route all requests to index.php
// It's only used in development mode

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// If the request is for a file that exists (like CSS, JS, images), serve it directly
if ($requestPath !== '/' && file_exists(__DIR__ . $requestPath)) {
    return false; // Serve the requested resource as-is
}

// Otherwise, route everything to index.php
require_once __DIR__ . '/index.php';
