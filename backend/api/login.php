<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json(['error' => 'Metodo baliogabea'], 405);
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$user = trim($data['user'] ?? '');
$pass = $data['password'] ?? '';

if ($user === '' || $pass === '' || strlen($user) > 50) {
    json(['error' => 'Datuak baliogabeak'], 422);
}

$stmt = $pdo->prepare('SELECT id, username, pasahitza, rol FROM erabiltzaileak WHERE username = ?');
$stmt->execute([$user]);
$row = $stmt->fetch();

if (!$row || !password_verify($pass, $row['pasahitza'])) {
    json(['error' => 'Erabiltzaile edo pasahitz okerra'], 401);
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'id' => $row['id'],
    'username' => $row['username'],
    'rol' => $row['rol'],
];

json(['ok' => true, 'user' => $_SESSION['user']]);
