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
$count = $pdo->query('SELECT COUNT(*) FROM rooms WHERE room_type_id IS NULL OR room_type_id = 0')->fetchColumn();
echo "null_count:$count\n";
$rows = $pdo->query('SELECT id,room_number,room_type_id FROM rooms WHERE room_type_id IS NULL OR room_type_id = 0 LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row){echo json_encode($row)."\n";}
