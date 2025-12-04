<?php
$home_config = load_json('home') ?? [];
$title = $home_config['title'] ?? 'Olympus Bar — Pedidos & Eventos';
$description = $home_config['description'] ?? 'Olympus Bar - Delivery de cócteles y servicio de bar móvil.';

$config = load_json('config') ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($description) ?>" />

  <!-- CSS DEL FRONT (ruta directa para evitar fallos) -->
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Config PHP → JS -->
  <script>
    window.OLYMPUS_CONFIG = <?= json_encode($config, JSON_UNESCAPED_UNICODE) ?>;
  </script>
</head>
<body>

  <div class="wrap">

    <header>
      <div class="brand">
          <img src="assets/img/logo.png"
               style="
                  width:65px;
                  height:65px;
                  border-radius:12px;
                  object-fit:cover;
                  border:2px solid rgba(255,215,0,0.6);
                  box-shadow:0 0 12px rgba(255,215,0,0.45);
               "
               alt="Olympus Bar">
          <h1>Olympus Bar</h1>
      </div>

      <nav>
        <a href="#cocteles">Cócteles</a>
        <a href="#packs">Packs</a>
        <a href="#eventos">Eventos</a>
      </nav>

      <div class="controls">
        <button class="cart-btn" onclick="toggleCart()">🛒 Ver carrito</button>
      </div>
    </header>
