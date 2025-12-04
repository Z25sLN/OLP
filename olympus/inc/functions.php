<?php
// Primero conectamos a la BD
require_once __DIR__ . '/db.php';

// Genera rutas correctas a CSS/JS/IMG según la carpeta del proyecto
function asset($path) {
    // dirname('/OLYMPUS/index.php') => '/OLYMPUS'
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base . '/' . ltrim($path, '/');
}

function load_json($file) {
    $path = __DIR__ . "/../data/{$file}.json";
    if (!file_exists($path)) return null;
    $content = file_get_contents($path);
    return json_decode($content, true);
}

function save_json($file, $data) {
    $path = __DIR__ . "/../data/{$file}.json";
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
