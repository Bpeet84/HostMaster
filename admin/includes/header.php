<?php
require_once __DIR__ . '/init.php';

// Lekérjük az összes felhasználót
$users = get_all_users();
?>

<header>
    <h1>HostMaster Admin</h1>
    <div>
        <?php if (isset($_SESSION['original_admin_id'])): ?>
            <a href="switch_back.php" class="admin-return-btn">Vissza az admin felületre</a>
        <?php endif; ?>
        <div class="search-container">
            <input type="text" id="user-search" placeholder="Felhasználó keresése...">
            <select id="user-select">
                <option value="">Válassz felhasználót</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                <?php endforeach; ?>
            </select>
            <button id="switch-user-btn">Átlépés felhasználói fiókba</button>
        </div>
        <form method="post" action="logout.php" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <button class="logout-btn" type="submit">Kijelentkezés</button>
        </form>
        <button class="menu-btn">&#9776;</button>
    </div>
</header>
<nav class="sidebar">
    <ul>
        <li><a href="/index.php">Kezdőlap</a></li>
        <li><a href="/modules/profile/index.php">Felhasználók Kezelése</a></li>
        <!-- További admin menüpontok itt -->
    </ul>
</nav>
<script src="assets/js/admin_scripts.js"></script>
