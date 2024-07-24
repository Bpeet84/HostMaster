<?php
require_once 'includes/init.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminisztrációs Kezelőfelület</title>
    <link rel="stylesheet" href="assets/css/admin_styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="content">
            <h1>Üdvözöljük az Adminisztrációs Kezelőfelületen</h1>
            <nav>
                <ul>
                    <li><a href="modules/profile/index.php">Felhasználók Kezelése</a></li>
                    <!-- További admin menüpontok itt -->
                </ul>
            </nav>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
