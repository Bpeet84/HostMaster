<?php
// index.php

require_once __DIR__ . '/includes/init.php';

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = get_db_connection();
$stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    die('A felhasználói adatok lekérése sikertelen.');
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HostMaster Felhasználói Oldal</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <h1>Üdvözöljük, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <div class="icon-grid">
            <div class="icon-item">
                <img src="assets/images/icon1.png" alt="Modul 1 Ikon">
                <p>Modul 1</p>
            </div>
            <div class="icon-item">
                <img src="assets/images/icon2.png" alt="Modul 2 Ikon">
                <p>Modul 2</p>
            </div>
            <div class="icon-item">
                <img src="assets/images/icon3.png" alt="Modul 3 Ikon">
                <p>Modul 3</p>
            </div>
            <div class="icon-item">
                <img src="assets/images/icon4.png" alt="Modul 4 Ikon">
                <p>Modul 4</p>
            </div>
        </div>
    </div>
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
