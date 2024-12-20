<?php

$routes = [
    '/' => 'index.php',
    '/admin' => 'admin_dashboard.php',
    '/user' => 'user_dashboard.php',
    '/reservation' => 'make_reservation.php',
];


$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = rtrim($request, '/'); // Normalize the path


if (array_key_exists($request, $routes)) {
    require __DIR__ . '/' . $routes[$request];
} else {
    http_response_code(404);
    require __DIR__ . '/views/404.php';
}