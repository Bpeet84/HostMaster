<?php
// includes/functions.php - Közös függvények

function get_csrf_token(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(string $token): bool {
    return hash_equals($_SESSION['csrf_token'], $token);
}

function check_auth() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: /admin/login.php');
        exit();
    }
}

function xss_clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function safe_redirect($url) {
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = '/' . ltrim($url, '/');
    }
    header("Location: $url");
    exit();
}

// További közös függvények...
?>