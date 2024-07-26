<?php
// Admin fejléc - HostMaster

require_once 'init.php';

// Ellenőrizzük, hogy a $users változó létezik-e, és ha nem, inicializáljuk üres tömbként
if (!isset($users)) {
    $users = [];
}

// Lekérjük az összes felhasználót, ha a változó üres
if (empty($users)) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, username FROM users WHERE role = "user"');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]:8086";

// A kijelentkezési URL meghatározása
$logout_url = $base_url . '/logout.php';

// Admin visszalépés URL meghatározása
$admin_return_url = (isset($_SESSION['original_admin_id'])) ? "$base_url/switch_back.php" : null;
?>

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
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                <?php endforeach; ?>
            </select>
            <button id="switch-user-btn" class="btn">Átlépés</button>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($logout_url); ?>" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <button class="btn logout-btn" type="submit">Kijelentkezés</button>
        </form>
        <button class="menu-btn">&#9776;</button>
    </div>
</header>
