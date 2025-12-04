<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$path = __DIR__ . '/../data/packs.json';
$packs = [];

if (file_exists($path)) {
    $packs = json_decode(file_get_contents($path), true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids          = $_POST['id'] ?? [];
    $names        = $_POST['name'] ?? [];
    $prices       = $_POST['price'] ?? [];
    $descs        = $_POST['description'] ?? [];
    $images_text  = $_POST['images'] ?? []; // texto (URLs / rutas por línea)
    $delete_imgs_all = $_POST['delete_img'] ?? []; // delete_img[packIndex][] = 'url/ruta'

    // archivos subidos (uno opcional por pack)
    $img_files = $_FILES['image_file'] ?? null;

    $new = [];
    $count = count($names);

    for ($i = 0; $i < $count; $i++) {
        $name = trim($names[$i] ?? '');
        if ($name === '') {
            // si no hay nombre, no guardamos ese pack (sirve como "borrar pack")
            continue;
        }

        // ID
        $id = trim($ids[$i] ?? '');
        if ($id === '') {
            $id = 'p' . ($i + 1);
        }

        // Procesar texto → array de imágenes
        $urls_raw = $images_text[$i] ?? '';
        $urls = array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $urls_raw))
        );

        // ¿Qué imágenes están marcadas para eliminar en este pack?
        $to_delete = $delete_imgs_all[$i] ?? []; // puede ser array de strings
        if (!empty($to_delete)) {
            // normalizamos
            $to_delete = array_map('trim', (array)$to_delete);

            // recorremos las que están marcadas para borrar
            foreach ($to_delete as $imgDel) {
                if ($imgDel === '') continue;

                // borrar del array de urls
                $urls = array_values(array_filter($urls, function($u) use ($imgDel) {
                    return $u !== $imgDel;
                }));

                // si la imagen es local (ruta interna), intentamos borrar el archivo
                if (strpos($imgDel, 'assets/img/packs/') === 0) {
                    $abs = __DIR__ . '/../' . $imgDel;
                    if (is_file($abs)) {
                        @unlink($abs);
                    }
                }
            }
        }

        // Si se subió un archivo para este pack, lo guardamos y lo añadimos al array
        if ($img_files && isset($img_files['name'][$i]) && $img_files['error'][$i] === UPLOAD_ERR_OK) {
            $tmp_name  = $img_files['tmp_name'][$i];
            $orig_name = $img_files['name'][$i];

            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            if ($ext === '') {
                $ext = 'jpg';
            }

            // Nombre de archivo seguro
            $safe_id  = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $id);
            $filename = $safe_id . '_' . time() . '_' . $i . '.' . $ext;

            $rel_path = 'assets/img/packs/' . $filename;       // para JSON
            $abs_path = __DIR__ . '/../' . $rel_path;          // en disco

            // Asegurar carpeta
            $dir = dirname($abs_path);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            if (move_uploaded_file($tmp_name, $abs_path)) {
                // añadimos la ruta local a la lista de imágenes
                $urls[] = $rel_path;
            }
        }

        $new[] = [
            'id'          => $id,
            'name'        => $name,
            'price'       => floatval($prices[$i] ?? 0),
            'description' => trim($descs[$i] ?? ''),
            'images'      => $urls
        ];
    }

    $packs = $new;
    file_put_contents(
        $path,
        json_encode($packs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
    $msg = "Packs guardados correctamente";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gestionar packs - Olympus Bar</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="admin-container">
    <h1>Gestionar packs</h1>
    <?php if (!empty($msg)): ?><p class="notice"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

    <!-- IMPORTANTE: enctype para subir archivos -->
    <form method="post" enctype="multipart/form-data">
      <?php foreach ($packs as $index => $p): ?>
      <fieldset class="admin-pack">
        <legend>Pack <?= htmlspecialchars($p['id']) ?></legend>

        <label>ID</label>
        <input type="text" name="id[]" value="<?= htmlspecialchars($p['id']) ?>">

        <label>Nombre</label>
        <input type="text" name="name[]" value="<?= htmlspecialchars($p['name']) ?>">

        <label>Precio (S/)</label>
        <input type="number" step="0.01" name="price[]" value="<?= htmlspecialchars($p['price']) ?>">

        <label>Descripción corta</label>
        <input type="text" name="description[]" value="<?= htmlspecialchars($p['description'] ?? '') ?>">

        <label>Imágenes (una URL o ruta por línea)</label>
        <textarea name="images[]" rows="4"><?=
          htmlspecialchars(implode("\n", $p['images'] ?? []))
        ?></textarea>

        <?php if (!empty($p['images'])): ?>
          <div style="margin-top:8px;">
            <strong>Marcar imágenes para borrar:</strong><br>
            <?php foreach ($p['images'] as $img): ?>
              <label style="display:block; font-size:0.85rem; margin-top:4px;">
                <input type="checkbox"
                       name="delete_img[<?= $index ?>][]"
                       value="<?= htmlspecialchars($img) ?>">
                Eliminar: <?= htmlspecialchars(basename($img)) ?>
                <span style="opacity:0.7;">(<?= htmlspecialchars($img) ?>)</span>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <label style="margin-top:8px;">Agregar imagen desde archivo (se suma a la lista)</label>
        <input type="file" name="image_file[]">
      </fieldset>
      <?php endforeach; ?>

      <!-- Nuevo pack -->
      <fieldset class="admin-pack">
        <legend>Nuevo pack</legend>

        <label>ID</label>
        <input type="text" name="id[]" placeholder="p?">

        <label>Nombre</label>
        <input type="text" name="name[]" placeholder="Nombre del pack">

        <label>Precio (S/)</label>
        <input type="number" step="0.01" name="price[]" placeholder="0.00">

        <label>Descripción corta</label>
        <input type="text" name="description[]" placeholder="Ej: 4 cócteles variados">

        <label>Imágenes (una URL o ruta por línea)</label>
        <textarea name="images[]" rows="4" placeholder="https://... o assets/img/packs/..."></textarea>

        <label>Agregar imagen desde archivo</label>
        <input type="file" name="image_file[]">
      </fieldset>

      <button type="submit">Guardar todo</button>
    </form>

    <p>Para eliminar un pack completo, deja vacío el campo <strong>Nombre</strong>.</p>
    <p>Para eliminar solo una imagen del pack, marca la casilla correspondiente y guarda.</p>
    <p><a href="dashboard.php">Volver al panel</a></p>
  </div>
</body>
</html>
