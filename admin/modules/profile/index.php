<?php
// modules/profile/index.php - Felhasználók kezelése oldal

// Maximális hibajelentés bekapcsolása
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Időzóna beállítása a pontos időbélyegekhez
date_default_timezone_set('Europe/Budapest');

require_once '../../includes/init.php';

debug_log("Script started");
debug_log("Init file loaded");

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    debug_log("User not logged in or not admin. Redirecting to login page.");
    header('Location: ../../login.php');
    exit();
}

debug_log("Admin authentication passed");

try {
    $pdo = get_db_connection();
    debug_log("Database connection established");

    // Először ellenőrizzük, hogy milyen mezők vannak a users táblában
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $table_structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    debug_log("Table structure: " . json_encode($table_structure));

    $query = 'SELECT id, username, email, default_domain, bandwidth_limit, disk_space_limit FROM users WHERE role = "user"';
    debug_log("Query: " . $query);

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    debug_log("Query executed and data fetched");
    debug_log("Fetched data before header include: " . json_encode($users));

} catch (PDOException $e) {
    debug_log("Database error: " . $e->getMessage());
    die("Adatbázis hiba történt. Kérjük, próbálja újra később.");
}

include '../../includes/header.php';
debug_log("Fetched data after header include: " . json_encode($users));

include '../../includes/sidebar.php';
debug_log("Fetched data after sidebar include: " . json_encode($users));
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
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <h1>Felhasználók Kezelése</h1>
            <div class="action-bar">
                <a href="add_user.php" class="btn btn-primary">
                    <i data-feather="user-plus"></i> Új Felhasználó
                </a>
            </div>
            <div class="table-responsive">
                <?php debug_log("Before table generation, users: " . json_encode($users)); ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Felhasználónév</th>
                            <th>Email</th>
                            <th>Alapértelmezett Domain</th>
                            <th>Sávszélesség Limit (GB)</th>
                            <th>Lemezterület Limit (MB)</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php debug_log("Processing user in table: " . json_encode($user)); ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['default_domain'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['bandwidth_limit'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['disk_space_limit'] ?? 'N/A'); ?></td>
                                <td class="actions">
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-edit" title="Szerkesztés">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-delete" title="Törlés" onclick="return confirm('Biztosan törölni szeretné ezt a felhasználót?');">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php debug_log("After table generation, users: " . json_encode($users)); ?>
            </div>
        </div>
    </div>
    <?php include '../../includes/footer.php'; ?>
    <script>
      feather.replace();
    </script>
</body>
</html>
<?php
debug_log("Script ended");
?>