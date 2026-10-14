<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$id = (int)($_GET['id'] ?? 0);

// Validación del servidor
function irakurriGela(array $data): array {
    $izena = trim($data['izena'] ?? '');
    $taldea = trim($data['taldea'] ?? '');

    if ($izena === '' || mb_strlen($izena) > 100) {
        json(['error' => 'Izena beharrezkoa da (gehienez 100 karaktere)'], 422);
    }
    if (mb_strlen($taldea) > 100) {
        json(['error' => 'Taldea gehienez 100 karaktere'], 422);
    }
    return [$izena, $taldea === '' ? null : $taldea];
}

switch ($method) {
    case 'GET':
        json($pdo->query('SELECT id, izena, taldea FROM gelak ORDER BY izena')->fetchAll());

    case 'POST':
        [$izena, $taldea] = irakurriGela($data);
        $pdo->prepare('INSERT INTO gelak (izena, taldea) VALUES (?, ?)')->execute([$izena, $taldea]);
        json(['id' => (int)$pdo->lastInsertId(), 'izena' => $izena, 'taldea' => $taldea], 201);

    case 'PUT':
        if ($id <= 0) json(['error' => 'ID baliogabea'], 422);
        [$izena, $taldea] = irakurriGela($data);
        $pdo->prepare('UPDATE gelak SET izena = ?, taldea = ? WHERE id = ?')->execute([$izena, $taldea, $id]);
        json(['ok' => true]);

    case 'DELETE':
        if ($id <= 0) json(['error' => 'ID baliogabea'], 422);
        try {
            $pdo->prepare('DELETE FROM gelak WHERE id = ?')->execute([$id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {   // clave foránea: tiene mugimenduak asociados
                json(['error' => 'Ezin da ezabatu: mugimenduak ditu'], 409);
            }
            throw $e;
        }
        json(['ok' => true]);

    default:
        json(['error' => 'Metodo baliogabea'], 405);
}
