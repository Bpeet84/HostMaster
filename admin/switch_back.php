<?php
// Admin visszalépés - HostMaster

require_once 'includes/init.php';

if (isset($_SESSION['original_admin_id'])) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE id = ? AND role = "admin"');
    $stmt->execute([$_SESSION['original_admin_id']]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = $admin['role'];
        unset($_SESSION['original_admin_id']);
        
        // Átirányítás az admin főoldalra
        header('Location: http://' . $_SERVER['SERVER_NAME'] . ':8086/index.php');
        exit();
    } else {
        echo "Hiba történt az admin fiókhoz való visszatérés során.";
        exit();
    }
} else {
    echo "Nincs eredeti admin munkamenet.";
    exit();
}
?>