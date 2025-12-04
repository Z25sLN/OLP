<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$path = __DIR__ . '/../data/home.json';
$data = [
  'title' => '',
  'description' => ''
];

if (file_exists($path)) {
    $data = json_decode(file_get_contents($path), true) ?: $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['title'] = $_POST['title'] ?? '';
    $data['description'] = $_POST['description'] ?? '';
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $msg = "Guardado correctamente";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar Home - Olympus Bar</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="admin-container">
    <h1>Editar datos del Home</h1>
    <?php if (!empty($msg)): ?><p class="notice"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <form method="post" class="admin-form">
      <label>Título de la página</label>
      <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>">

      <label>Descripción (meta description)</label>
      <textarea name="description" rows="3"><?= htmlspecialchars($data['description']) ?></textarea>

      <button type="submit">Guardar</button>
    </form>

    <p><a href="dashboard.php">Volver al panel</a></p>
  </div>
</body>
</html>
