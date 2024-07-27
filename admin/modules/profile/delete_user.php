<?php
require_once '../../includes/init.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if (isset($_POST['id'])) {
    $user_id = intval($_POST['id']);
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$user_id]);

    header('Location: index.php');
    exit();
} elseif (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        die('Felhasználó nem található.');
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználó Törlése</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>
    <?php include '../../includes/header.php'; // Közös fejléc include-olása ?>
    <?php include '../../includes/sidebar.php'; // Közös sidebar include-olása ?>
    <div class="container">
        <h1>Felhasználó Törlése</h1>
        <p>Biztosan törölni szeretnéd a következő felhasználót: <strong><?php echo htmlspecialchars($user['username']); ?></strong>?</p>
        <form method="post" action="delete_user.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <button type="submit">Igen, törlés</button>
            <a href="index.php">Mégsem</a>
        </form>
    </div>
    <?php include '../../includes/footer.php'; // Közös lábléc include-olása ?>
</body>
</html>
