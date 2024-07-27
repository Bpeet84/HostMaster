<?php
// edit_user.php - Felhasználó szerkesztése - HostMaster

require_once '../../includes/init.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die('Felhasználó nem található.');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = intval($_POST['id']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $default_domain = sanitize_input($_POST['default_domain']);
    $bandwidth_limit = intval($_POST['bandwidth_limit']);
    $disk_space_limit = intval($_POST['disk_space_limit']);
    $inode_limit = intval($_POST['inode_limit']);
    $domains_limit = intval($_POST['domains_limit']);
    $subdomains_limit = intval($_POST['subdomains_limit']);
    $domain_pointers_limit = intval($_POST['domain_pointers_limit']);
    $email_accounts_limit = intval($_POST['email_accounts_limit']);
    $email_forwarders_limit = intval($_POST['email_forwarders_limit']);
    $autoresponders_limit = intval($_POST['autoresponders_limit']);
    $mysql_databases_limit = intval($_POST['mysql_databases_limit']);
    $ftp_accounts_limit = intval($_POST['ftp_accounts_limit']);

    // Szerver oldali validáció
    if (empty($username) || empty($email)) {
        $error = 'Minden kötelező mezőt ki kell tölteni.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Érvénytelen email cím.';
    } else {
        $pdo = get_db_connection();
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, default_domain = ?, bandwidth_limit = ?, disk_space_limit = ?, inode_limit = ?, domains_limit = ?, subdomains_limit = ?, domain_pointers_limit = ?, email_accounts_limit = ?, email_forwarders_limit = ?, autoresponders_limit = ?, mysql_databases_limit = ?, ftp_accounts_limit = ? WHERE id = ?');
            $stmt->execute([
                $username, $email, $default_domain, $bandwidth_limit, $disk_space_limit, $inode_limit, $domains_limit, $subdomains_limit, $domain_pointers_limit, $email_accounts_limit, $email_forwarders_limit, $autoresponders_limit, $mysql_databases_limit, $ftp_accounts_limit, $user_id
            ]);
            $pdo->commit();
            $success = 'Felhasználó sikeresen frissítve.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Hiba történt a felhasználó frissítése során: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználó Szerkesztése</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include '../../includes/header.php'; // Közös fejléc include-olása ?>
    <?php include '../../includes/sidebar.php'; // Közös sidebar include-olása ?>
    <div class="container">
        <h1>Felhasználó Szerkesztése</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="post" action="edit_user.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="default_domain">Alapértelmezett Domain:</label>
                <input type="text" id="default_domain" name="default_domain" value="<?php echo htmlspecialchars($user['default_domain']); ?>">
            </div>
            <div class="form-group">
                <label for="bandwidth_limit">Sávszélesség Limit (GB):</label>
                <input type="number" id="bandwidth_limit" name="bandwidth_limit" value="<?php echo htmlspecialchars($user['bandwidth_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="disk_space_limit">Lemezterület Limit (MB):</label>
                <input type="number" id="disk_space_limit" name="disk_space_limit" value="<?php echo htmlspecialchars($user['disk_space_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="inode_limit">Inode Limit:</label>
                <input type="number" id="inode_limit" name="inode_limit" value="<?php echo htmlspecialchars($user['inode_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="domains_limit">Domainek Limit:</label>
                <input type="number" id="domains_limit" name="domains_limit" value="<?php echo htmlspecialchars($user['domains_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="subdomains_limit">Aldomainek Limit:</label>
                <input type="number" id="subdomains_limit" name="subdomains_limit" value="<?php echo htmlspecialchars($user['subdomains_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="domain_pointers_limit">Domain Pointerek Limit:</label>
                <input type="number" id="domain_pointers_limit" name="domain_pointers_limit" value="<?php echo htmlspecialchars($user['domain_pointers_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email_accounts_limit">Email Fiókok Limit:</label>
                <input type="number" id="email_accounts_limit" name="email_accounts_limit" value="<?php echo htmlspecialchars($user['email_accounts_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email_forwarders_limit">Email Továbbítók Limit:</label>
                <input type="number" id="email_forwarders_limit" name="email_forwarders_limit" value="<?php echo htmlspecialchars($user['email_forwarders_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="autoresponders_limit">Automatikus Válaszolók Limit:</label>
                <input type="number" id="autoresponders_limit" name="autoresponders_limit" value="<?php echo htmlspecialchars($user['autoresponders_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="mysql_databases_limit">MySQL Adatbázisok Limit:</label>
                <input type="number" id="mysql_databases_limit" name="mysql_databases_limit" value="<?php echo htmlspecialchars($user['mysql_databases_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ftp_accounts_limit">FTP Fiókok Limit:</label>
                <input type="number" id="ftp_accounts_limit" name="ftp_accounts_limit" value="<?php echo htmlspecialchars($user['ftp_accounts_limit']); ?>" required>
            </div>
            <div class="form-group submit-group">
                <button type="submit">Felhasználó Frissítése</button>
            </div>
        </form>
    </div>
    <?php include '../../includes/footer.php'; // Közös lábléc include-olása ?>
</body>
</html>
