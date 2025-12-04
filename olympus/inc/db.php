<?php
// Datos de conexión a MySQL (XAMPP)
$DB_HOST = 'localhost';
$DB_NAME = 'olympus_bar';   // el nombre de tu base de datos
$DB_USER = 'root';          // usuario por defecto de XAMPP
$DB_PASS = '';              // contraseña (vacía si no le pusiste una)

// Creamos el objeto PDO
try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // Si falla, detenemos la app y mostramos el error
    die('Error de conexión a la BD: ' . $e->getMessage());
}
