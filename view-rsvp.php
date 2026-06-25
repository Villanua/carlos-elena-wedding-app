<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'u966112943_carlos_elena');
define('DB_PASS', 'CarlosElena@1');
define('DB_NAME', 'u966112943_carlos_elena');
define('VIEW_PASSWORD', 'CarlosElena@1');

session_start();
$error = '';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view-rsvp.php');
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === VIEW_PASSWORD) {
        $_SESSION['rsvp_auth'] = true;
    } else {
        $error = 'Contraseña incorrecta.';
    }
}

// Handle CSV download
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download']) && isset($_SESSION['rsvp_auth'])) {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $rows = $pdo->query('SELECT fecha, email, nombre, asistencia, alergias, detalles_alergias, autobus, mensaje FROM rsvp ORDER BY fecha ASC')->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="confirmaciones-' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-store');
        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Fecha', 'Email', 'Nombre', 'Asistencia', 'Alergias', 'Detalles alergias', 'Autobús', 'Mensaje'], ';');
        foreach ($rows as $row) fputcsv($out, array_values($row), ';');
        fclose($out);
        exit;
    } catch (Exception $e) {
        $error = 'Error al generar el archivo.';
    }
}

// Fetch data if logged in
$rows = [];
$stats = ['total' => 0, 'si' => 0, 'no' => 0, 'bus' => 0];
if (isset($_SESSION['rsvp_auth'])) {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $rows = $pdo->query('SELECT fecha, email, nombre, asistencia, alergias, detalles_alergias, autobus, mensaje FROM rsvp ORDER BY fecha DESC')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $stats['total']++;
            if (strtolower($r['asistencia']) === 'sí' || strtolower($r['asistencia']) === 'si') $stats['si']++;
            else $stats['no']++;
            if (stripos($r['autobus'], 'sí') !== false || stripos($r['autobus'], 'si') !== false) $stats['bus']++;
        }
    } catch (Exception $e) {
        $error = 'Error al conectar con la base de datos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Elena & Carlos — Confirmaciones</title>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { background:#f5f0e8; font-family:'Lato',sans-serif; min-height:100vh; }

    /* Login */
    .login-wrap { display:flex; align-items:center; justify-content:center; min-height:100vh; }
    .card { background:white; border-radius:8px; padding:48px 40px; box-shadow:0 8px 32px rgba(0,0,0,0.08); max-width:380px; width:100%; text-align:center; }
    .card h1 { font-family:'Great Vibes',cursive; font-size:42px; color:#3d2b1f; margin-bottom:8px; }
    .card p { font-size:14px; color:#6b4c35; font-weight:300; margin-bottom:28px; }
    input[type="password"] { width:100%; padding:12px 16px; border:1.5px solid #d4b896; border-radius:6px; font-size:15px; font-family:'Lato',sans-serif; margin-bottom:16px; outline:none; }
    input[type="password"]:focus { border-color:#6b4c35; }

    /* Dashboard */
    .dashboard { max-width:1100px; margin:0 auto; padding:40px 20px; }
    .header { display:flex; align-items:center; justify-content:space-between; margin-bottom:32px; flex-wrap:wrap; gap:16px; }
    .header h1 { font-family:'Great Vibes',cursive; font-size:48px; color:#3d2b1f; }
    .header-actions { display:flex; gap:12px; align-items:center; }

    .stats { display:flex; gap:16px; margin-bottom:32px; flex-wrap:wrap; }
    .stat { background:white; border-radius:8px; padding:20px 28px; text-align:center; box-shadow:0 2px 12px rgba(0,0,0,0.06); flex:1; min-width:120px; }
    .stat-num { font-size:36px; font-weight:600; color:#c47a5a; }
    .stat-label { font-size:12px; letter-spacing:1px; text-transform:uppercase; color:#6b4c35; font-weight:300; margin-top:4px; }

    table { width:100%; background:white; border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border-collapse:collapse; overflow:hidden; }
    th { background:#3d2b1f; color:white; padding:12px 16px; text-align:left; font-size:12px; letter-spacing:1px; text-transform:uppercase; font-weight:400; }
    td { padding:12px 16px; border-bottom:1px solid #f0e8dc; font-size:14px; color:#3d2b1f; font-weight:300; vertical-align:top; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#fdf8f2; }

    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:400; }
    .badge-si { background:#d4edda; color:#2d6a4f; }
    .badge-no { background:#f8d7da; color:#842029; }

    btn, .btn { display:inline-block; padding:10px 24px; border-radius:50px; font-family:'Lato',sans-serif; font-size:12px; letter-spacing:2px; text-transform:uppercase; cursor:pointer; border:none; text-decoration:none; transition:all 0.2s; }
    .btn-primary { background:#c47a5a; color:white; }
    .btn-primary:hover { background:#a85c3a; }
    .btn-outline { background:transparent; border:1.5px solid #6b4c35; color:#6b4c35; }
    .btn-outline:hover { background:#6b4c35; color:white; }
    .error { color:#c47a5a; font-size:13px; margin-top:12px; }
    .empty { text-align:center; padding:60px; color:#6b4c35; font-weight:300; }
  </style>
</head>
<body>

<?php if (!isset($_SESSION['rsvp_auth'])): ?>
  <div class="login-wrap">
    <div class="card">
      <h1>Elena & Carlos</h1>
      <p>Introduce la contraseña para ver las confirmaciones</p>
      <form method="POST">
        <input type="password" name="password" placeholder="Contraseña" autofocus required>
        <button type="submit" class="btn btn-primary" style="width:100%;">Acceder</button>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
      </form>
    </div>
  </div>

<?php else: ?>
  <div class="dashboard">
    <div class="header">
      <h1>Elena & Carlos</h1>
      <div class="header-actions">
        <form method="POST" style="display:inline;">
          <button type="submit" name="download" value="1" class="btn btn-outline">Descargar Excel</button>
        </form>
        <a href="?logout" class="btn btn-outline">Cerrar sesión</a>
      </div>
    </div>

    <div class="stats">
      <div class="stat"><div class="stat-num"><?= $stats['total'] ?></div><div class="stat-label">Total</div></div>
      <div class="stat"><div class="stat-num"><?= $stats['si'] ?></div><div class="stat-label">Asisten</div></div>
      <div class="stat"><div class="stat-num"><?= $stats['no'] ?></div><div class="stat-label">No asisten</div></div>
      <div class="stat"><div class="stat-num"><?= $stats['bus'] ?></div><div class="stat-label">Necesitan bus</div></div>
    </div>

    <?php if (empty($rows)): ?>
      <div class="empty">Todavía no hay confirmaciones.</div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Asistencia</th>
          <th>Alergias</th>
          <th>Detalles</th>
          <th>Autobús</th>
          <th>Mensaje</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars(substr($r['fecha'], 0, 16)) ?></td>
          <td><?= htmlspecialchars($r['nombre']) ?></td>
          <td><?= htmlspecialchars($r['email']) ?></td>
          <td><span class="badge <?= (stripos($r['asistencia'],'s') === 0) ? 'badge-si' : 'badge-no' ?>"><?= htmlspecialchars($r['asistencia']) ?></span></td>
          <td><?= htmlspecialchars($r['alergias']) ?></td>
          <td><?= htmlspecialchars($r['detalles_alergias']) ?></td>
          <td><?= htmlspecialchars($r['autobus']) ?></td>
          <td><?= htmlspecialchars($r['mensaje']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
<?php endif; ?>

</body>
</html>

