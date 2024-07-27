<?php
// Admin főoldal - HostMaster

require_once 'includes/init.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$pdo = get_db_connection();
$stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

include 'includes/header.php'; // Közös fejléc include-olása
include 'includes/sidebar.php'; // Közös sidebar include-olása
?>

<div class="container">
    <h1>Üdvözöljük az Adminisztrációs Kezelőfelületen, <?php echo htmlspecialchars($admin['username']); ?>!</h1>
    <div class="icon-grid">
        <div class="icon-item">
            <a href="/modules/profile/index.php">
                <img src="assets/images/icon1.png" alt="Modul 1 Ikon">
                <p>Felhasználók Kezelése</p>
            </a>
        </div>
        <!-- További modulok itt -->
    </div>
</div>

<?php include 'includes/footer.php'; // Közös lábléc include-olása ?>
</body>
</html>
