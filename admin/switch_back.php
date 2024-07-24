<?php
require_once 'includes/init.php';

if (isset($_SESSION['original_admin_id'])) {
    $_SESSION['user_id'] = $_SESSION['original_admin_id'];
    unset($_SESSION['original_admin_id']);
    header('Location: http://' . $_SERVER['SERVER_NAME'] . ':8086/index.php');
    exit();
}
header('Location: http://' . $_SERVER['SERVER_NAME'] . ':8085/index.php');
?>
