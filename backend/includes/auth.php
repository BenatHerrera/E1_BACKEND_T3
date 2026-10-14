<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/datubasea.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

function json($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function requireLogin(): void {
    if (empty($_SESSION['user'])) {
        json(['error' => 'Saioa hasi behar da'], 401);
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['user']['rol'] !== 'admin') {
        json(['error' => 'Baimenik ez'], 403);
    }
}
