<?php
// Admin főoldal - HostMaster

require_once 'includes/init.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$pdo = get_db_connection();
$stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminisztrációs Kezelőfelület</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <script src="assets/js/scripts.js" defer></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="container">
        <h1>Üdvözöljük az Adminisztrációs Kezelőfelületen, <?php echo htmlspecialchars($admin['username']); ?>!</h1>
        <div class="icon-grid">
            <div class="icon-item">
                <a href="/modules/profile/index.php">
                    <img src="assets/images/icon1.png" alt="Modul 1 Ikon">
                    <p>Felhasználók Kezelése</p>
                </a>
            </div>
            <!-- További modulok itt -->
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
