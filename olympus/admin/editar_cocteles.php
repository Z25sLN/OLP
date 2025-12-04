<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// SEGUIMOS USANDO JSON POR AHORA
$path = __DIR__ . '/../data/products.json';
$products = [];

if (file_exists($path)) {
    $products = json_decode(file_get_contents($path), true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids      = $_POST['id'] ?? [];
    $names    = $_POST['name'] ?? [];
    $prices   = $_POST['price'] ?? [];
    $imgs     = $_POST['img'] ?? [];          // texto (URL o ruta)
    $tags     = $_POST['tag'] ?? [];
    $delete_img = $_POST['delete_img'] ?? []; // índices de filas donde se quiere borrar la imagen

    // archivos subidos (puede venir vacío)
    $img_files = $_FILES['img_file'] ?? null;

    // normalizamos índices marcados para borrar
    $delete_img = array_map('strval', $delete_img);

    $new = [];
    $count = count($names);

    for ($i = 0; $i < $count; $i++) {
        $name = trim($names[$i] ?? '');
        if ($name === '') continue; // si no hay nombre, se ignora (borra la fila entera)

        // ID
        $id = trim($ids[$i] ?? '');
        if ($id === '') {
            $id = 'c' . ($i + 1);
        }

        // Empezamos con el valor de imagen que viene en el input de texto
        $img_value = trim($imgs[$i] ?? '');

        // ¿Se pidió borrar la imagen de esta fila?
        if (in_array((string)$i, $delete_img, true)) {
            // si es una ruta local tipo assets/img/cocteles/..., intentamos borrar el archivo
            $oldImg = $img_value;
            if ($oldImg !== '' && strpos($oldImg, 'assets/img/cocteles/') === 0) {
                $abs_old = __DIR__ . '/../' . $oldImg;
                if (is_file($abs_old)) {
                    @unlink($abs_old);
                }
            }
            // dejamos el valor de imagen vacío
            $img_value = '';
        } else {
            // SOLO si NO se marcó borrar imagen, revisamos si hay archivo subido
            if ($img_files && isset($img_files['name'][$i]) && $img_files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name  = $img_files['tmp_name'][$i];
                $orig_name = $img_files['name'][$i];

                // Sacar extensión
                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                if ($ext === '') {
                    $ext = 'jpg'; // por si acaso
                }

                // Nombre de archivo seguro: c1_1700000000_0.jpg
                $safe_id = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $id);
                $filename = $safe_id . '_' . time() . '_' . $i . '.' . $ext;

                // Ruta relativa (para guardar en JSON) y absoluta (para mover el archivo)
                $rel_path = 'assets/img/cocteles/' . $filename;
                $abs_path = __DIR__ . '/../' . $rel_path;

                // Asegurar carpeta
                $dir = dirname($abs_path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                if (move_uploaded_file($tmp_name, $abs_path)) {
                    // Si se subió bien, usamos esta ruta interna en lugar del texto
                    $img_value = $rel_path;
                }
            }
        }

        $new[] = [
            'id'   => $id,
            'name' => $name,
            'price'=> floatval($prices[$i] ?? 0),
            'img'  => $img_value,                         // puede ser URL, ruta local o vacío si se borró
            'tag'  => trim($tags[$i] ?? 'Bebida premium')
        ];
    }

    $products = $new;
    file_put_contents(
        $path,
        json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
    $msg = "Cócteles guardados correctamente";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gestionar cócteles - Olympus Bar</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="admin-container">
    <h1>Gestionar cócteles</h1>
    <?php if (!empty($msg)): ?><p class="notice"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <!-- OJO: enctype para permitir subida de archivos -->
    <form method="post" enctype="multipart/form-data">
      <table class="admin-table">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Precio (S/)</th>
          <th>Imagen (URL o archivo)</th>
          <th>Etiqueta / Tag</th>
          <th>Eliminar imagen</th>
        </tr>

        <?php foreach ($products as $index => $p): ?>
        <tr>
          <td><input type="text" name="id[]" value="<?= htmlspecialchars($p['id']) ?>"></td>
          <td><input type="text" name="name[]" value="<?= htmlspecialchars($p['name']) ?>"></td>
          <td><input type="number" step="0.01" name="price[]" value="<?= htmlspecialchars($p['price']) ?>"></td>
          <td>
            <!-- Texto: URL o ruta -->
            <input type="text"
                   name="img[]"
                   value="<?= htmlspecialchars($p['img']) ?>"
                   placeholder="URL o ruta interna (assets/img/...)">
            <!-- Archivo: si subes, reemplaza lo anterior -->
            <input type="file" name="img_file[]">
            <?php if (!empty($p['img'])): ?>
              <br><small>Actual: <?= htmlspecialchars($p['img']) ?></small>
            <?php endif; ?>
          </td>
          <td><input type="text" name="tag[]" value="<?= htmlspecialchars($p['tag'] ?? 'Bebida premium') ?>"></td>
          <td style="text-align:center;">
            <input type="checkbox" name="delete_img[]" value="<?= $index ?>">
          </td>
        </tr>
        <?php endforeach; ?>

        <!-- Filas vacías para nuevos cócteles -->
        <?php for ($i = 0; $i < 4; $i++): ?>
        <tr>
          <td><input type="text" name="id[]" placeholder="c?"></td>
          <td><input type="text" name="name[]" placeholder="Nombre"></td>
          <td><input type="number" step="0.01" name="price[]" placeholder="0.00"></td>
          <td>
            <input type="text" name="img[]" placeholder="URL o se genera al subir archivo">
            <input type="file" name="img_file[]">
          </td>
          <td><input type="text" name="tag[]" value="Bebida premium"></td>
          <td style="text-align:center;">—</td>
        </tr>
        <?php endfor; ?>
      </table>

      <button type="submit">Guardar todo</button>
    </form>

    <p>
      Para eliminar SOLO la imagen de un cóctel, marca la casilla
      <strong>Eliminar imagen</strong> de esa fila y guarda.
    </p>
    <p>Para eliminar un cóctel completo, deja vacío el campo <strong>Nombre</strong>.</p>
    <p><a href="dashboard.php">Volver al panel</a></p>
  </div>
</body>
</html>
