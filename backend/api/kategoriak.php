<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$id = (int)($_GET['id'] ?? 0);

// Validación del servidor
function irakurriIzena(array $data): string {
    $izena = trim($data['izena'] ?? '');
    if ($izena === '' || mb_strlen($izena) > 100) {
        json(['error' => 'Izena beharrezkoa da (gehienez 100 karaktere)'], 422);
    }
    return $izena;
}

switch ($method) {

    case 'GET':

        json($pdo->query('SELECT id, izena FROM kategoriak ORDER BY izena')->fetchAll());

    case 'POST':

        $izena = irakurriIzena($data);
        $pdo->prepare('INSERT INTO kategoriak (izena) VALUES (?)')->execute([$izena]);
        json(['id' => (int)$pdo->lastInsertId(), 'izena' => $izena], 201);

    case 'PUT':

        if ($id <= 0) json(['error' => 'ID baliogabea'], 422);
        $izena = irakurriIzena($data);
        $pdo->prepare('UPDATE kategoriak SET izena = ? WHERE id = ?')->execute([$izena, $id]);
        json(['ok' => true]);

    case 'DELETE':
        
        if ($id <= 0) json(['error' => 'ID baliogabea'], 422);
        try {
            $pdo->prepare('DELETE FROM kategoriak WHERE id = ?')->execute([$id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {   // clave foránea: tiene baliabideak asociados
                json(['error' => 'Ezin da ezabatu: baliabideak ditu'], 409);
            }
            throw $e;
        }
        json(['ok' => true]);

    default:
        json(['error' => 'Metodo baliogabea'], 405);
}