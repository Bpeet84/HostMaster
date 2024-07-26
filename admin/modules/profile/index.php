<?php
// modules/profile/index.php - Felhasználók kezelése oldal

// Maximális hibajelentés bekapcsolása
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Időzóna beállítása a pontos időbélyegekhez
date_default_timezone_set('Europe/Budapest');

// Debug log funkció
function debug_log($message) {
    error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, __DIR__ . '/debug.log');
}

debug_log("Script started");

require_once '../../includes/init.php';

debug_log("Init file loaded");

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    debug_log("User not logged in or not admin. Redirecting to login page.");
    header('Location: ../../login.php');
    exit();
}

debug_log("Admin authentication passed");

// PHP verzió és szerver információk kiírása
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Server Protocol: " . $_SERVER['SERVER_PROTOCOL'] . "\n";
echo "</pre>";

$pdo = get_db_connection();
debug_log("Database connection established");

$query = 'SELECT id, username, email, default_domain, bandwidth_limit, disk_space_limit FROM users WHERE role = "user"';
debug_log("Query: " . $query);

$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

debug_log("Query executed and data fetched");

// Adatbázis eredmények kiírása
echo "<pre>";
echo "Database Query Results:\n";
var_dump($users);
echo "</pre>";

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
    <script>
        // JavaScript Debug funkció
        function debugLog(message) {
            console.log("Debug: " + message);
        }
    </script>
</head>
<body>
    <?php 
    debug_log("Starting to include header");
    include '../../includes/header.php';
    debug_log("Header included");
    
    debug_log("Starting to include sidebar");
    include '../../includes/sidebar.php';
    debug_log("Sidebar included");
    ?>
    <div class="main-content">
        <div class="container">
            <h1>Felhasználók Kezelése</h1>
            <div class="action-bar">
                <a href="add_user.php" class="btn btn-primary">
                    <i data-feather="user-plus"></i> Új Felhasználó
                </a>
            </div>
            <div class="table-responsive">
                <!-- Táblázat kezdete -->
                <?php debug_log("Starting table generation"); ?>
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
                            <?php debug_log("Processing user: " . $user['username']); ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['default_domain']); ?></td>
                                <td><?php echo htmlspecialchars($user['bandwidth_limit']); ?></td>
                                <td><?php echo htmlspecialchars($user['disk_space_limit']); ?></td>
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
                <!-- Táblázat vége -->
                <?php debug_log("Table generation completed"); ?>
            </div>
        </div>
    </div>
    <?php 
    debug_log("Starting to include footer");
    include '../../includes/footer.php';
    debug_log("Footer included");
    ?>
    <script>
      feather.replace();
      debugLog("Feather icons replaced");

      // Táblázat tartalmának ellenőrzése
      let table = document.querySelector('.user-table');
      if (table) {
          debugLog("Table found");
          let rows = table.querySelectorAll('tbody tr');
          debugLog("Number of rows: " + rows.length);
          rows.forEach((row, index) => {
              debugLog("Row " + (index + 1) + " content: " + row.textContent);
          });
      } else {
          debugLog("Table not found");
      }
    </script>
</body>
</html>
<?php
debug_log("Script ended");
?>