<?php
require_once '../../includes/init.php';

$pdo = get_db_connection();
$stmt = $pdo->prepare('SELECT * FROM users');
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználók Kezelése</title>
    <link rel="stylesheet" href="../../assets/css/admin_styles.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="main-container">
        <div class="content">
            <h1>Felhasználók Kezelése</h1>
            <a href="add_user.php" class="btn">Új Felhasználó</a>
            <table>
                <thead>
                    <tr>
                        <th>Felhasználónév</th>
                        <th>Email</th>
                        <th>Alapértelmezett Domain</th>
                        <th>Műveletek</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['default_domain']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Szerkesztés</a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>">Törlés</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php include '../../includes/sidebar.php'; ?>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
