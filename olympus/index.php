<?php
session_start();
require_once __DIR__ . '/inc/functions.php';

$page = isset($_GET['page']) ? strtolower($_GET['page']) : 'home';

// solo de momento tenemos "home"
$allowed_pages = ['home'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

include __DIR__ . '/inc/header.php';
include __DIR__ . "/pages/{$page}.php";
include __DIR__ . '/inc/footer.php';
