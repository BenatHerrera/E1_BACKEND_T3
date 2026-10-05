<?php
$pdo = new PDO(
    'mysql:host=localhost;dbname=e1inbentarioa;charset=utf8mb4',
    'wesuser',
    '1234',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);