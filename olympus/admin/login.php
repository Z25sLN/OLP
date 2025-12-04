<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    // LOGIN TEMPORAL (luego se conecta a BD)
    if ($user === 'admin' && $pass === '1234') {

        // Sesión unificada
        $_SESSION['admin'] = true;
        $_SESSION['user'] = [
            'id' => 1,
            'nombre' => 'Administrador',
            'rol' => 'admin'
        ];

        header('Location: dashboard.php');
        exit;
    }

    $error = "Usuario o contraseña incorrectos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login · Olympus Bar</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="login-body">

  <div class="login-box">

    <!-- LOGO -->
    <div class="login-logo">
      <img src="../assets/img/logo.png" alt="Olympus Bar">
    </div>

    <!-- TÍTULO -->
    <h1>Panel Olympus Bar</h1>
    <p class="login-subtitle">Acceso privado al panel de gestión</p>

    <!-- ERROR -->
    <?php if ($error): ?>
      <div class="error-msg">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <form method="post" class="login-form" autocomplete="off">
      <input
        type="text"
        name="user"
        placeholder="Usuario"
        required
        autofocus
      >

      <input
        type="password"
        name="pass"
        placeholder="Contraseña"
        required
      >

      <button type="submit">Entrar</button>
    </form>

  </div>

</body>
</html>
