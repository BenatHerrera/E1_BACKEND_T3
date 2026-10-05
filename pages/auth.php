<?php
session_start();
require_once __DIR__ . '/datubasea.php';

function requireLogin() {
    if (empty($_SESSION['user'])) {
        header('Location: /logina.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['user']['rola'] !== 'admin') {
        http_response_code(403);
        exit('No tienes permiso');
    }
}