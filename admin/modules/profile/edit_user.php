<?php
// edit_user.php - Felhasználó szerkesztése - HostMaster
require_once '../../includes/init.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        die('Felhasználó nem található.');
    }
    // Alapértelmezett értékek beállítása, ha hiányoznak
    $user['email'] = $user['email'] ?? '';
    $user['default_domain'] = $user['default_domain'] ?? '';
    $user['bandwidth_limit'] = $user['bandwidth_limit'] ?? 0;
    $user['disk_space_limit'] = $user['disk_space_limit'] ?? 0;
    $user['inode_limit'] = $user['inode_limit'] ?? 0;
    $user['domains_limit'] = $user['domains_limit'] ?? 0;
    $user['subdomains_limit'] = $user['subdomains_limit'] ?? 0;
    $user['domain_pointers_limit'] = $user['domain_pointers_limit'] ?? 0;
    $user['email_accounts_limit'] = $user['email_accounts_limit'] ?? 0;
    $user['email_forwarders_limit'] = $user['email_forwarders_limit'] ?? 0;
    $user['autoresponders_limit'] = $user['autoresponders_limit'] ?? 0;
    $user['mysql_databases_limit'] = $user['mysql_databases_limit'] ?? 0;
    $user['ftp_accounts_limit'] = $user['ftp_accounts_limit'] ?? 0;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = intval($_POST['id']);
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $default_domain = sanitize_input($_POST['default_domain'] ?? '');
    $bandwidth_limit = intval($_POST['bandwidth_limit'] ?? 0);
    $disk_space_limit = intval($_POST['disk_space_limit'] ?? 0);
    $inode_limit = intval($_POST['inode_limit'] ?? 0);
    $domains_limit = intval($_POST['domains_limit'] ?? 0);
    $subdomains_limit = intval($_POST['subdomains_limit'] ?? 0);
    $domain_pointers_limit = intval($_POST['domain_pointers_limit'] ?? 0);
    $email_accounts_limit = intval($_POST['email_accounts_limit'] ?? 0);
    $email_forwarders_limit = intval($_POST['email_forwarders_limit'] ?? 0);
    $autoresponders_limit = intval($_POST['autoresponders_limit'] ?? 0);
    $mysql_databases_limit = intval($_POST['mysql_databases_limit'] ?? 0);
    $ftp_accounts_limit = intval($_POST['ftp_accounts_limit'] ?? 0);

    $pdo = get_db_connection();

    if (!empty($_POST['password'])) {
        $password = hash_password(sanitize_input($_POST['password']));
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, password = ?, default_domain = ?, bandwidth_limit = ?, disk_space_limit = ?, inode_limit = ?, domains_limit = ?, subdomains_limit = ?, domain_pointers_limit = ?, email_accounts_limit = ?, email_forwarders_limit = ?, autoresponders_limit = ?, mysql_databases_limit = ?, ftp_accounts_limit = ? WHERE id = ?');
        $stmt->execute([$username, $email, $password, $default_domain, $bandwidth_limit, $disk_space_limit, $inode_limit, $domains_limit, $subdomains_limit, $domain_pointers_limit, $email_accounts_limit, $email_forwarders_limit, $autoresponders_limit, $mysql_databases_limit, $ftp_accounts_limit, $user_id]);
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, default_domain = ?, bandwidth_limit = ?, disk_space_limit = ?, inode_limit = ?, domains_limit = ?, subdomains_limit = ?, domain_pointers_limit = ?, email_accounts_limit = ?, email_forwarders_limit = ?, autoresponders_limit = ?, mysql_databases_limit = ?, ftp_accounts_limit = ? WHERE id = ?');
        $stmt->execute([$username, $email, $default_domain, $bandwidth_limit, $disk_space_limit, $inode_limit, $domains_limit, $subdomains_limit, $domain_pointers_limit, $email_accounts_limit, $email_forwarders_limit, $autoresponders_limit, $mysql_databases_limit, $ftp_accounts_limit, $user_id]);
    }
    header('Location: index.php');
    exit();
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
    <script src="../../assets/js/validation.js" defer></script>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="container">
        <h1>Felhasználó Szerkesztése</h1>
        <form id="editUserForm" method="post" action="edit_user.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <span class="validation-icon" id="username-validation"></span>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <span class="validation-icon" id="email-validation"></span>
            <label for="password">Új Jelszó (ha változtatni szeretnél):</label>
            <input type="password" id="password" name="password">
            <span class="validation-icon" id="password-validation"></span>
            <label for="default_domain">Alapértelmezett Domain:</label>
            <input type="text" id="default_domain" name="default_domain" value="<?php echo htmlspecialchars($user['default_domain']); ?>" required>
            <span class="validation-icon" id="default_domain-validation"></span>
            <label for="bandwidth_limit">Sávszélesség (GB):</label>
            <input type="number" id="bandwidth_limit" name="bandwidth_limit" value="<?php echo htmlspecialchars($user['bandwidth_limit']); ?>" required>
            <label for="disk_space_limit">Lemez Terület (MB):</label>
            <input type="number" id="disk_space_limit" name="disk_space_limit" value="<?php echo htmlspecialchars($user['disk_space_limit']); ?>" required>
            <label for="inode_limit">Inode Limit:</label>
            <input type="number" id="inode_limit" name="inode_limit" value="<?php echo htmlspecialchars($user['inode_limit']); ?>" required>
            <label for="domains_limit">Domain Nevek:</label>
            <input type="number" id="domains_limit" name="domains_limit" value="<?php echo htmlspecialchars($user['domains_limit']); ?>" required>
            <label for="subdomains_limit">Aldomainek:</label>
            <input type="number" id="subdomains_limit" name="subdomains_limit" value="<?php echo htmlspecialchars($user['subdomains_limit']); ?>" required>
            <label for="domain_pointers_limit">Domain Pointerek:</label>
            <input type="number" id="domain_pointers_limit" name="domain_pointers_limit" value="<?php echo htmlspecialchars($user['domain_pointers_limit']); ?>" required>
            <label for="email_accounts_limit">Email Fiókok:</label>
            <input type="number" id="email_accounts_limit" name="email_accounts_limit" value="<?php echo htmlspecialchars($user['email_accounts_limit']); ?>" required>
            <label for="email_forwarders_limit">Email Továbbítók:</label>
            <input type="number" id="email_forwarders_limit" name="email_forwarders_limit" value="<?php echo htmlspecialchars($user['email_forwarders_limit']); ?>" required>
            <label for="autoresponders_limit">Automatikus Válaszadók:</label>
            <input type="number" id="autoresponders_limit" name="autoresponders_limit" value="<?php echo htmlspecialchars($user['autoresponders_limit']); ?>" required>
            <label for="mysql_databases_limit">MySQL Adatbázisok:</label>
            <input type="number" id="mysql_databases_limit" name="mysql_databases_limit" value="<?php echo htmlspecialchars($user['mysql_databases_limit']); ?>" required>
            <label for="ftp_accounts_limit">FTP Fiókok:</label>
            <input type="number" id="ftp_accounts_limit" name="ftp_accounts_limit" value="<?php echo htmlspecialchars($user['ftp_accounts_limit']); ?>" required>
            <button type="submit">Mentés</button>
        </form>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>