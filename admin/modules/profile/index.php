<?php
// Admin felhasználói profil - HostMaster

require_once '../../includes/init.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$pdo = get_db_connection();
$stmt = $pdo->prepare('SELECT id, username, email, default_domain FROM users WHERE role = "user"');
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználók Kezelése - HostMaster Admin</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="style.css">
    <script src="../../assets/js/scripts.js" defer></script>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="main-container">
        <?php include '../../includes/sidebar.php'; ?>
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
                            <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['default_domain'] ?? 'N/A'); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn">Szerkesztés</a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn">Törlés</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
