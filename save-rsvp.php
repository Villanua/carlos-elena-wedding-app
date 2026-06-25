<?php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['result' => 'error']);
    exit;
}

define('DB_HOST', 'localhost');
define('DB_USER', 'u966112943_carlos_elena');
define('DB_PASS', 'CarlosElena@1');
define('DB_NAME', 'u966112943_carlos_elena');

function clean($value) {
    return trim($value ?? '');
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare(
        'INSERT INTO rsvp (fecha, email, nombre, asistencia, alergias, detalles_alergias, autobus, mensaje)
         VALUES (NOW(), :email, :nombre, :asistencia, :alergias, :detalles_alergias, :autobus, :mensaje)'
    );

    $stmt->execute([
        ':email'             => clean($_GET['emailAddress']     ?? ''),
        ':nombre'            => clean($_GET['nombre']           ?? ''),
        ':asistencia'        => clean($_GET['asistencia']       ?? ''),
        ':alergias'          => clean($_GET['alergias']         ?? ''),
        ':detalles_alergias' => clean($_GET['detallesAlergias'] ?? ''),
        ':autobus'           => clean($_GET['autobus']          ?? ''),
        ':mensaje'           => clean($_GET['mensaje']          ?? ''),
    ]);

    echo json_encode(['result' => 'ok']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['result' => 'error']);
}
exit;
