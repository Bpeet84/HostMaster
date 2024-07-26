<?php
// admin/includes/init.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/admin_functions.php';

// CSRF token generálása
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ellenőrizzük, hogy nincs-e itt valami, ami felülírhatja a $users változót
// Ha van ilyen kód, kommenteljük ki vagy töröljük

// Debug információ hozzáadása
if (!function_exists('debug_log')) {
    function debug_log($message) {
        error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, __DIR__ . '/../debug.log');
    }
}

debug_log("Init.php loaded");
?>