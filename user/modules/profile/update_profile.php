<?php
// modules/profile/update_profile.php

require_once __DIR__ . '/../../includes/init.php';

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$pdo = get_db_connection();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // CSRF token ellenőrzése
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Érvénytelen CSRF token.';
    } else {
        // Felhasználói adatok frissítése
        try {
            if (!empty($password)) {
                $hashed_password = hash_password($password);
                $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?');
                $stmt->execute([$username, $email, $hashed_password, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
                $stmt->execute([$username, $email, $_SESSION['user_id']]);
            }
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            $error = 'Hiba történt a profil frissítése során: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Frissítése - HostMaster</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="container">
        <div class="profile-container">
            <h1>Profil Frissítése</h1>
            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form action="update_profile.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                <div>
                    <label for="username">Felhasználónév:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div>
                    <label for="password">Új jelszó:</label>
                    <input type="password" id="password" name="password">
                </div>
                <div>
                    <input type="submit" value="Profil frissítése">
                </div>
            </form>
        </div>
    </div>
    <?php include '../../includes/sidebar.php'; ?>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
