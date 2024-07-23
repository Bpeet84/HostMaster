<?php
// config.php

// Az adatbázis kapcsolat beállításai
define('DB_HOST', 'localhost');
define('DB_USER', 'hostmaster_panel');
define('DB_PASS', 'erős_jelszó');
define('DB_NAME', 'hostmaster');
define('DB_CHARSET', 'utf8mb4');

// Titkos kulcs a jelszavak és egyéb érzékeny adatok titkosításához
define('SECRET_KEY', 'EzEgyNagyonErősTitkosKulcs_2024!');

// Adatbázis kapcsolat létrehozása PDO-val
function get_db_connection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

// Input adatokat tisztító funkció
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Jelszó hashelése
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Jelszó ellenőrzése
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// CSRF token generálása
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token ellenőrzése
function verify_csrf_token($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Példa használat
// session_start();
// $pdo = get_db_connection();
// $hashed_password = hash_password('your_password');

?>
