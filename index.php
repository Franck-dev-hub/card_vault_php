<?php
// Translator
require_once "public/includes/translator.php";
Translator::init('fr_FR');

// Route
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// PHP files directory
$mainDir = "public/pages/common/main/";

// Switch routes
switch($uri) {
    case "/dashboard":
        try {
            include $mainDir . "dashboard.php";
        } catch (DOMException $e) {
            echo "Can't access dashboard file";
        }
        break;
    case "/stats":
        try {
            include $mainDir . "stats.php";
        } catch (DOMException $e) {
            echo "Can't access stats file";
        }
        break;
    case "/scan":
        try {
            include $mainDir . "scan.php";
        } catch (DOMException $e) {
            echo "Can't access scan file";
        }
        break;
    case "/vault":
        try {
            include $mainDir . "vault.php";
        } catch (DOMException $e) {
            echo "Can't access vault file";
        }
        break;
    case "/search":
        try {
            include $mainDir . "search.php";
        } catch (DOMException $e) {
            echo "Can't access search file";
        }
        break;
    default:
        http_response_code(404);
        echo '404 - Page not found';
}
