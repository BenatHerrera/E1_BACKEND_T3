<?php
// Ajusta estos datos a tu MySQL (XAMPP por defecto: root y contraseña vacía).
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=e1inbentarioa;charset=utf8mb4',
        'wesuser',
        '123456',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Datu-baseko errorea: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
