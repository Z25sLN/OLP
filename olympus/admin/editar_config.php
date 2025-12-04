<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$path = __DIR__ . '/../data/config.json';
$data = [
  'wa_number' => '',
  'owner' => ''
];

if (file_exists($path)) {
    $data = json_decode(file_get_contents($path), true) ?: $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['wa_number'] = $_POST['wa_number'] ?? '';
    $data['owner']     = $_POST['owner'] ?? '';
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $msg = "Guardado correctamente";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar configuración - Olympus Bar</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="admin-container">
    <h1>Editar configuración</h1>
    <?php if (!empty($msg)): ?><p class="notice"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <form method="post" class="admin-form">
      <label>WhatsApp (solo números, con código de país)</label>
      <input type="text" name="wa_number" value="<?= htmlspecialchars($data['wa_number']) ?>">

      <label>Nombre del dueño (para el mensaje de WhatsApp)</label>
      <input type="text" name="owner" value="<?= htmlspecialchars($data['owner']) ?>">

      <button type="submit">Guardar</button>
    </form>

    <p><a href="dashboard.php">Volver al panel</a></p>
  </div>
</body>
</html>
