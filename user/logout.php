<?php
// logout.php

require_once __DIR__ . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    
    if (verify_csrf_token($csrf_token)) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        die('Érvénytelen CSRF token.');
    }
} else {
    header("Location: login.php");
    exit();
}
?>
