<?php
// user/includes/init.php - Inicializáló fájl a felhasználói oldalakhoz

// Munkamenet indítása, ha még nem aktív
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/functions.php';

// CSRF token generálása, ha még nincs
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Felhasználói hitelesítés ellenőrzése
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'logout.php', 'register.php']; // Nyilvános oldalak listája

if (!in_array($current_page, $public_pages)) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
        // Ha nem bejelentkezett felhasználó próbál hozzáférni védett oldalhoz
        header('Location: login.php');
        exit();
    }
} else if ($current_page === 'login.php' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'user') {
    // Ha már bejelentkezett felhasználó próbálja elérni a login oldalt
    header('Location: index.php');
    exit();
}

// CSRF token ellenőrzése POST kéréseknél
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die('Érvénytelen CSRF token.');
    }
}

// További inicializálási lépések...
?>