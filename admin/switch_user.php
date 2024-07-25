<?php
// Admin felhasználói váltás - HostMaster

require_once 'includes/init.php';

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    // Ellenőrizzük, hogy az admin be van-e jelentkezve
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
        $_SESSION['original_admin_id'] = $_SESSION['user_id'];
        $_SESSION['user_id'] = $user_id;
        header('Location: http://' . $_SERVER['SERVER_NAME'] . ':8085/index.php');
        exit();
    }
}
header('Location: index.php');
?>
