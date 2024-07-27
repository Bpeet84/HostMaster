<?php
// init.php - Inicializáló fájl

// Strict típusos mód bekapcsolása
declare(strict_types=1);

// Hibakezelés beállítása (produkciós környezetben módosítandó)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Munkamenet beállítások
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_secure', '1');
session_start();

// Adatbázis kapcsolat
require_once __DIR__ . '/../../config/config.php';

// Közös függvények betöltése
require_once __DIR__ . '/functions.php';

// Időzóna beállítása
date_default_timezone_set('Europe/Budapest');

// Alapvető biztonsági fejlécek beállítása
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
?>