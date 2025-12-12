<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch($uri) {
    case '/dashboard':
        include 'public/pages/index.php';
        break;
    default:
        http_response_code(404);
        echo '404 - Page not found';
}
