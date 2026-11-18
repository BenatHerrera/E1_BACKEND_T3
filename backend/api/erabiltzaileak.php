<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$id = (int)($_GET['id'] ?? 0);

const ROLAK = ['admin', 'user'];

// Validación del servidor
function irakurriErabiltzailea(array $data, bool $pasahitzaDerrigorrezkoa): array {
    $izena = trim($data['izena'] ?? '');
    $erabiltzailea = trim($data['erabiltzailea'] ?? '');
    $pasahitza = (string)($data['pasahitza'] ?? '');
    $rola = trim($data['rola'] ?? '');

    if ($izena === '' || mb_strlen($izena) > 100) {
        json(['error' => 'Izena beharrezkoa da (gehienez 100 karaktere)'], 422);
    }
    if ($erabiltzailea === '' || mb_strlen($erabiltzailea) > 50) {
        json(['error' => 'Erabiltzailea beharrezkoa da (gehienez 50 karaktere)'], 422);
    }
    if ($pasahitzaDerrigorrezkoa && mb_strlen($pasahitza) < 6) {
        json(['error' => 'Pasahitzak gutxienez 6 karaktere izan behar ditu'], 422);
    }
    if ($pasahitza !== '' && mb_strlen($pasahitza) < 6) {
        json(['error' => 'Pasahitzak gutxienez 6 karaktere izan behar ditu'], 422);
    }
    if (!in_array($rola, ROLAK, true)) {
        json(['error' => 'Rola baliogabea'], 422);
    }
    return [$izena, $erabiltzailea, $pasahitza, $rola];
}

switch ($method) {
    case 'GET':
        json($pdo->query('SELECT id, izena, erabiltzailea, rola FROM erabiltzaileak ORDER BY izena')->fetchAll());

    case 'POST':
        [$izena, $erabiltzailea, $pasahitza, $rola] = irakurriErabiltzailea($data, true);
        $hash = password_hash($pasahitza, PASSWORD_DEFAULT);
        try {
            $pdo->prepare('INSERT INTO erabiltzaileak (izena, erabiltzailea, pasahitza, rola) VALUES (?, ?, ?, ?)')
                ->execute([$izena, $erabiltzailea, $hash, $rola]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                json(['error' => 'Erabiltzaile-izen hori jada badago'], 409);
            }
            throw $e;
        }
        json([
            'id' => (int)$pdo->lastInsertId(),
            'username' => $izena,
            'pasahitza' => $erabiltzailea,
            'rol' => $rola,
        ], 201);

    case 'PUT':
        if ($id <= 0) json(['error' => 'ID baliogabea'], 422);
        [$izena, $erabiltzailea, $pasahitza, $rola] = irakurriErabiltzailea($data, false);
        try {
            if ($pasahitza !== '') {
                $hash = password_hash($pasahitza, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE erabiltzaileak SET izena = ?, erabiltzailea = ?, pasahitza = ?, rola = ? WHERE id = ?')
                    ->execute([$izena, $erabiltzailea, $hash, $rola, $id]);
            } else {
                $pdo->prepare('UPDATE erabiltzaileak SET izena = ?, erabiltzailea = ?, rola = ? WHERE id = ?')
                    ->execute([$izena, $erabiltzailea, $rola, $id]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                json(['error' => 'Erabiltzaile-izen hori jada badago'], 409);
            }
            throw $e;
        }
        json(['ok' => true]);

    case 'DELETE':
        if ($id <= 0) json(['error' => 'ID baliogabea'], 422);
        if ((int)($_SESSION['user']['id'] ?? 0) === $id) {
            json(['error' => 'Ezin duzu zeure burua ezabatu'], 409);
        }
        try {
            $pdo->prepare('DELETE FROM erabiltzaileak WHERE id = ?')->execute([$id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                json(['error' => 'Ezin da ezabatu: erreferentziak ditu'], 409);
            }
            throw $e;
        }
        json(['ok' => true]);

    default:
        json(['error' => 'Metodo baliogabea'], 405);
}