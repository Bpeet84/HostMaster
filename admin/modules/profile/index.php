<?php
// modules/profile/index.php

require_once '../../includes/init.php';
require_once 'functions.php';
check_auth(); // Ellenőrizzük, hogy az admin be van-e jelentkezve

$users = get_all_users(); // Feltételezzük, hogy van ilyen függvény a functions.php-ban

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználók Kezelése</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    <div class="profile-container">
        <h1>Felhasználók Kezelése</h1>
        <a href="add_user.php" class="btn btn-primary">Új Felhasználó Hozzáadása</a>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Felhasználónév</th>
                    <th>E-mail</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="action-buttons">
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-edit">Szerkesztés</a>
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-delete" onclick="return confirm('Biztosan törölni szeretné ezt a felhasználót?');">Törlés</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>