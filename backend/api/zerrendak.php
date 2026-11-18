<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$mota = $_GET['mota'] ?? '';

switch ($mota) {
    case 'inbentarioa':
        $zutabeak = ['Etiketa', 'Ekipamendua', 'Kategoria', 'Egoera', 'Gela'];
        $files = $pdo->query(
            "SELECT g.etiketa, b.izena, k.izena AS kategoria, g.egoera, ge.izena AS gela
               FROM gailuak g
               JOIN baliabideak b ON b.id = g.baliabideId
               JOIN kategoriak k ON k.id = b.kategoriaId
          LEFT JOIN mugimenduak m ON m.gailuId = g.id AND m.amaieraData IS NULL
          LEFT JOIN gelak ge ON ge.id = m.gelaId
           ORDER BY g.etiketa"
        )->fetchAll(PDO::FETCH_NUM);
        break;

    case 'ekipamenduak':
        $zutabeak = ['Ekipamendua', 'Kategoria', 'Marka', 'Modeloa', 'Unitateak'];
        $files = $pdo->query(
            "SELECT b.izena, k.izena, b.marka, b.modeloa, COUNT(g.id)
               FROM baliabideak b
               JOIN kategoriak k ON k.id = b.kategoriaId
          LEFT JOIN gailuak g ON g.baliabideId = b.id
           GROUP BY b.id, b.izena, k.izena, b.marka, b.modeloa
           ORDER BY b.izena"
        )->fetchAll(PDO::FETCH_NUM);
        break;

    case 'erosketak':
        $zutabeak = ['Data', 'Ekipamendua', 'Kopurua', 'Stock berria'];
        $files = $pdo->query(
            "SELECT DATE_FORMAT(g.erosketaData, '%Y/%m/%d'), b.izena, COUNT(*),
                    (SELECT COUNT(*) FROM gailuak g2 WHERE g2.baliabideId = b.id)
               FROM gailuak g
               JOIN baliabideak b ON b.id = g.baliabideId
              WHERE g.erosketaData IS NOT NULL
           GROUP BY g.erosketaData, b.id, b.izena
           ORDER BY g.erosketaData DESC, b.izena"
        )->fetchAll(PDO::FETCH_NUM);
        break;

    default:
        json(['error' => 'Zerrenda baliogabea'], 422);
}

json(['zutabeak' => $zutabeak, 'errenkadak' => $files]);