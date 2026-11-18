<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$stats = [
    'ekipamenduMotak' => (int)$pdo->query('SELECT COUNT(*) FROM baliabideak')->fetchColumn(),
    'unitateak'       => (int)$pdo->query('SELECT COUNT(*) FROM gailuak')->fetchColumn(),
    'gelak'           => (int)$pdo->query('SELECT COUNT(*) FROM gelak')->fetchColumn(),
    // gailuak gela batean kokatuta daudenak (mugimendu irekia dutenak)
    'inbentariatuta'  => (int)$pdo->query('SELECT COUNT(DISTINCT gailuId) FROM mugimenduak WHERE amaieraData IS NULL')->fetchColumn(),
];

$azkenErosketak = $pdo->query(
    "SELECT DATE_FORMAT(g.erosketaData, '%Y/%m/%d') AS data,
            b.izena AS ekipamendua,
            COUNT(*) AS kopurua,
            (SELECT COUNT(*) FROM gailuak g2 WHERE g2.baliabideId = b.id) AS stockBerria
       FROM gailuak g
       JOIN baliabideak b ON b.id = g.baliabideId
      WHERE g.erosketaData IS NOT NULL
      GROUP BY g.erosketaData, b.id, b.izena
      ORDER BY g.erosketaData DESC, b.izena
      LIMIT 5"
)->fetchAll();

json(['stats' => $stats, 'azkenErosketak' => $azkenErosketak]);