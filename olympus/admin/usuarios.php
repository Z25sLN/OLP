<?php
session_start();
require_once __DIR__ . '/../inc/functions.php';

// Proteger: solo admin (MISMA LÓGICA QUE EL DASHBOARD)
if (
    empty($_SESSION['user']) ||
    $_SESSION['user']['rol'] !== 'admin'
) {
    header('Location: login.php');
    exit;
}

$mensaje = '';
$error = '';

// Cargar roles para el select
$roles_stmt = $pdo->query("SELECT id, nombre FROM roles ORDER BY id");
$roles = $roles_stmt->fetchAll();

// Eliminar usuario (opcional)
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int) $_GET['eliminar'];

    // evitar que un admin se borre a sí mismo
    if ($id_eliminar === (int) $_SESSION['user']['id']) {
        $error = 'No puedes eliminar tu propio usuario.';
    } else {
        $del = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $del->execute([':id' => $id_eliminar]);
        $mensaje = 'Usuario eliminado correctamente.';
    }
}

// Crear nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $id_rol   = (int) ($_POST['id_rol'] ?? 0);

    if ($nombre === '' || $email === '' || $password === '' || !$id_rol) {
        $error = 'Completa todos los campos.';
    } else {
        // Verificar si el email ya existe
        $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            $error = 'Ya existe un usuario con ese correo.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare(
                "INSERT INTO usuarios (nombre, email, password_hash, id_rol)
                 VALUES (:nombre, :email, :hash, :id_rol)"
            );
            $ins->execute([
                ':nombre' => $nombre,
                ':email'  => $email,
                ':hash'   => $hash,
                ':id_rol' => $id_rol
            ]);
            $mensaje = 'Usuario creado correctamente.';
        }
    }
}

// Listar usuarios
$users_stmt = $pdo->query(
  "SELECT u.id, u.nombre, u.email, u.creado_en, r.nombre AS rol
   FROM usuarios u
   JOIN roles r ON u.id_rol = r.id
   ORDER BY u.id DESC"
);
$usuarios = $users_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de usuarios - Olympus Bar</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="admin-page">

<div class="admin-wrapper">
    <header class="admin-header">
        <h1>Gestión de usuarios</h1>
        <a href="dashboard.php">← Volver al dashboard</a>
    </header>

    <?php if ($mensaje): ?>
        <div class="alert success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section class="admin-section">
        <h2>Crear nuevo usuario</h2>
        <form method="post" class="admin-form">
            <input type="hidden" name="accion" value="crear">

            <label>Nombre completo</label>
            <input type="text" name="nombre" required>

            <label>Correo electrónico</label>
            <input type="email" name="email" required>

            <label>Contraseña</label>
            <input type="password" name="password" required>

            <label>Rol</label>
            <select name="id_rol" required>
                <option value="">Selecciona un rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id'] ?>">
                        <?= htmlspecialchars($rol['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Crear usuario</button>
        </form>
    </section>

    <section class="admin-section">
        <h2>Lista de usuarios</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['rol']) ?></td>
                    <td><?= $u['creado_en'] ?></td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <a href="usuarios.php?eliminar=<?= $u['id'] ?>"
                               onclick="return confirm('¿Eliminar este usuario?');">
                               Eliminar
                            </a>
                        <?php else: ?>
                            (tú)
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>
