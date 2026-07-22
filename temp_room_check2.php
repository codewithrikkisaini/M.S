<?php
$lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) $config[trim($parts[0])] = trim($parts[1]);
}
$pdo = new PDO('mysql:host=' . $config['DB_HOST'] . ';port=' . $config['DB_PORT'] . ';dbname=' . $config['DB_DATABASE'], $config['DB_USERNAME'], $config['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$rows = $pdo->query('SELECT r.id,r.room_number,r.room_type_id, rt.id AS rt_id FROM rooms r LEFT JOIN room_types rt ON r.room_type_id = rt.id WHERE rt.id IS NULL LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row){echo json_encode($row)."\n";}
