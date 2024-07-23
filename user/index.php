<?php
// index.php

include '../config/config.php';
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
        <h1>Üdvözöljük a HostMaster Felhasználói Oldalon</h1>
        <div class="icon-grid">
            <div class="icon-item">
                <img src="assets/images/icon1.png" alt="Ikon 1">
                <p>Modul 1</p>
            </div>
            <div class="icon-item">
                <img src="assets/images/icon2.png" alt="Ikon 2">
                <p>Modul 2</p>
            </div>
            <div class="icon-item">
                <img src="assets/images/icon3.png" alt="Ikon 3">
                <p>Modul 3</p>
            </div>
            <div class="icon-item">
                <img src="assets/images/icon4.png" alt="Ikon 4">
                <p>Modul 4</p>
            </div>
        </div>
    </div>
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
