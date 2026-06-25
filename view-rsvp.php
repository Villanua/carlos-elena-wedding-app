<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'u966112943_carlos_elena');
define('DB_PASS', 'CarlosElena@1');
define('DB_NAME', 'u966112943_carlos_elena');
define('VIEW_PASSWORD', 'CarlosElena@1');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if ($password === VIEW_PASSWORD) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $pdo->query(
                'SELECT fecha, email, nombre, asistencia, alergias, detalles_alergias, autobus, mensaje
                 FROM rsvp ORDER BY fecha ASC'
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="confirmaciones-' . date('Y-m-d') . '.csv"');
            header('Cache-Control: no-cache, no-store, must-revalidate');

            $output = fopen('php://output', 'w');
            // UTF-8 BOM para que Excel abra correctamente los acentos
            fputs($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Fecha', 'Email', 'Nombre', 'Asistencia', 'Alergias', 'Detalles alergias', 'Autobús', 'Mensaje'], ';');

            foreach ($rows as $row) {
                fputcsv($output, array_values($row), ';');
            }

            fclose($output);
            exit;
        } catch (Exception $e) {
            $error = 'Error al conectar con la base de datos.';
        }
    } else {
        $error = 'Contraseña incorrecta.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Elena & Carlos — Confirmaciones</title>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f5f0e8;
      font-family: 'Lato', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
    }
    .card {
      background: white;
      border-radius: 8px;
      padding: 48px 40px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.08);
      max-width: 380px;
      width: 100%;
      text-align: center;
    }
    h1 {
      font-family: 'Great Vibes', cursive;
      font-size: 42px;
      color: #3d2b1f;
      margin-bottom: 8px;
    }
    p {
      font-size: 14px;
      color: #6b4c35;
      font-weight: 300;
      margin-bottom: 28px;
    }
    input[type="password"] {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid #d4b896;
      border-radius: 6px;
      font-size: 15px;
      font-family: 'Lato', sans-serif;
      box-sizing: border-box;
      margin-bottom: 16px;
      outline: none;
      transition: border-color 0.2s;
    }
    input[type="password"]:focus {
      border-color: #6b4c35;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #c47a5a;
      color: white;
      border: none;
      border-radius: 50px;
      font-family: 'Lato', sans-serif;
      font-size: 13px;
      letter-spacing: 2px;
      text-transform: uppercase;
      cursor: pointer;
      transition: background 0.2s;
    }
    button:hover { background: #a85c3a; }
    .error {
      color: #c47a5a;
      font-size: 13px;
      margin-top: 12px;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Elena & Carlos</h1>
    <p>Introduce la contraseña para descargar las confirmaciones</p>
    <form method="POST">
      <input type="password" name="password" placeholder="Contraseña" autofocus required>
      <button type="submit">Descargar Excel</button>
      <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
