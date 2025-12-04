<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Dashboard - Olympus Bar</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="admin-container">

    <div class="admin-dashboard">

      <h1 class="admin-title">Dashboard Olympus Bar</h1>
      <p class="admin-subtitle">Panel de administración</p>

      <div class="admin-grid">

        <a href="editar_home.php" class="admin-card">
          <span class="admin-icon"></span>
          <div>
            <h3>Contenido del sitio</h3>
            <p>Editar título, banner y descripción</p>
          </div>
        </a>

        <a href="editar_config.php" class="admin-card">
          <span class="admin-icon"></span>
          <div>
            <h3>Datos de contacto</h3>
            <p>WhatsApp y nombre del dueño</p>
          </div>
        </a>

        <a href="editar_cocteles.php" class="admin-card">
          <span class="admin-icon"></span>
          <div>
            <h3>Cócteles</h3>
            <p>Gestionar precios e imágenes</p>
          </div>
        </a>

        <a href="editar_packs.php" class="admin-card">
          <span class="admin-icon"></span>
          <div>
            <h3>Packs</h3>
            <p>Combos y promociones</p>
          </div>
        </a>

        <a href="usuarios.php" class="admin-card">
          <span class="admin-icon"></span>
          <div>
            <h3>Usuarios</h3>
            <p>Administrar accesos al panel</p>
          </div>
        </a>

        <a href="logout.php" class="admin-card admin-logout">
          <span class="admin-icon"></span>
          <div>
            <h3>Cerrar sesión</h3>
            <p>Salir del panel de administración</p>
          </div>
        </a>

      </div>

    </div>

  </div>
</body>
</html>
