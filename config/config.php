<?php
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
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}
?>
