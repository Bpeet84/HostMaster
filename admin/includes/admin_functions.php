<?php
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function get_all_users() {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, username FROM users');
    $stmt->execute();
    return $stmt->fetchAll();
}
?>
