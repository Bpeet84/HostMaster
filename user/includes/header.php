<?php
require_once 'init.php';

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]:8085";

// A kijelentkezési URL meghatározása
$logout_url = $base_url . '/logout.php';

// Admin visszalépés URL meghatározása
$admin_return_url = (isset($_SESSION['original_admin_id'])) ? "http://$_SERVER[SERVER_NAME]:8086/switch_back.php" : null;
?>

<header>
    <h1>HostMaster</h1>
    <div>
        <?php if ($admin_return_url): ?>
            <a href="<?php echo htmlspecialchars($admin_return_url); ?>" class="admin-return-btn">Vissza az admin felületre</a>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($logout_url); ?>" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <button class="logout-btn" type="submit">Kijelentkezés</button>
        </form>
        <button class="menu-btn">&#9776;</button>
    </div>
</header>
