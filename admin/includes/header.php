<?php
// header.php - Közös fejléc az admin oldalakhoz

require_once __DIR__ . '/init.php';

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]:8086";

// A kijelentkezési URL meghatározása
$logout_url = $base_url . '/logout.php';

// Admin visszalépés URL meghatározása
$admin_return_url = (isset($_SESSION['original_admin_id'])) ? "$base_url/switch_back.php" : null;
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HostMaster Admin</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/header.css">
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
    <script src="/assets/js/scripts.js" defer></script>
</head>
<body>
<header>
    <div class="logo">
        <span class="host">Host</span><span class="master">Master</span>
    </div>
    <div class="header-actions">
        <?php if ($admin_return_url): ?>
            <a href="<?php echo htmlspecialchars($admin_return_url); ?>" class="btn admin-return-btn">Vissza az admin felületre</a>
        <?php endif; ?>
        <div class="user-switch">
            <select id="user-select" class="user-select">
                <option value="">Válassz felhasználót</option>
                <?php
                $pdo = get_db_connection();
                $stmt = $pdo->query('SELECT id, username FROM users WHERE role = "user"');
                while ($user = $stmt->fetch()) {
                    echo '<option value="' . htmlspecialchars($user['id']) . '">' . htmlspecialchars($user['username']) . '</option>';
                }
                ?>
            </select>
            <button id="switch-user-btn" class="btn" data-csrf-token="<?php echo htmlspecialchars(get_csrf_token()); ?>">Átlépés</button>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($logout_url); ?>" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <button class="btn logout-btn" type="submit">Kijelentkezés</button>
        </form>
        <button class="menu-btn">&#9776;</button>
    </div>
</header>
<!-- A body tag nincs lezárva, mert az a tartalmi rész után fog következni -->