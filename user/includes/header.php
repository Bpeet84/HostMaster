<?php
require_once 'init.php';

// Alap URL meghatározása
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

// A kijelentkezési URL meghatározása
$logout_url = $base_url . '/logout.php';
?>

<header>
    <h1>HostMaster</h1>
    <div>
        <form method="post" action="<?php echo htmlspecialchars($logout_url); ?>" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <button class="logout-btn" type="submit">Kijelentkezés</button>
        </form>
        <button class="menu-btn">&#9776;</button>
    </div>
</header>
