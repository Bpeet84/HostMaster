<?php
// add_user.php - Felhasználó hozzáadása - HostMaster

require_once '../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = sanitize_input($_POST['password'] ?? '');
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
    
    $password_hashed = hash_password($password);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, default_domain, bandwidth_limit, disk_space_limit, inode_limit, domains_limit, subdomains_limit, domain_pointers_limit, email_accounts_limit, email_forwarders_limit, autoresponders_limit, mysql_databases_limit, ftp_accounts_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$username, $email, $password_hashed, $default_domain, $bandwidth_limit, $disk_space_limit, $inode_limit, $domains_limit, $subdomains_limit, $domain_pointers_limit, $email_accounts_limit, $email_forwarders_limit, $autoresponders_limit, $mysql_databases_limit, $ftp_accounts_limit]);

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új Felhasználó Hozzáadása</title>
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
        <h1>Új Felhasználó Hozzáadása</h1>
        <form id="addUserForm" method="post" action="add_user.php">
            <div class="form-column">
                <div class="form-group">
                    <label for="username">Felhasználónév:</label>
                    <input type="text" id="username" name="username" required>
                    <span class="validation-icon" id="username-validation"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <span class="validation-icon" id="email-validation"></span>
                </div>
                <div class="form-group">
                    <label for="password">Jelszó:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="validation-icon" id="password-validation"></span>
                </div>
                <div class="form-group">
                    <label for="default_domain">Alapértelmezett Domain:</label>
                    <input type="text" id="default_domain" name="default_domain" required>
                    <span class="validation-icon" id="default_domain-validation"></span>
                </div>
                <div class="form-group">
                    <label for="bandwidth_limit">Sávszélesség (GB):</label>
                    <input type="number" id="bandwidth_limit" name="bandwidth_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="disk_space_limit">Lemez Terület (MB):</label>
                    <input type="number" id="disk_space_limit" name="disk_space_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="inode_limit">Inode Limit:</label>
                    <input type="number" id="inode_limit" name="inode_limit" value="0" required>
                </div>
            </div>
            <div class="form-column">
                <div class="form-group">
                    <label for="domains_limit">Domain Nevek:</label>
                    <input type="number" id="domains_limit" name="domains_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="subdomains_limit">Aldomainek:</label>
                    <input type="number" id="subdomains_limit" name="subdomains_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="domain_pointers_limit">Domain Név Mutatók:</label>
                    <input type="number" id="domain_pointers_limit" name="domain_pointers_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="email_accounts_limit">Email Fiókok:</label>
                    <input type="number" id="email_accounts_limit" name="email_accounts_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="email_forwarders_limit">Email Átirányítások:</label>
                    <input type="number" id="email_forwarders_limit" name="email_forwarders_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="autoresponders_limit">Automatikus Válaszolók:</label>
                    <input type="number" id="autoresponders_limit" name="autoresponders_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="mysql_databases_limit">MySQL Adatbázisok:</label>
                    <input type="number" id="mysql_databases_limit" name="mysql_databases_limit" value="0" required>
                </div>
                <div class="form-group">
                    <label for="ftp_accounts_limit">FTP Fiókok:</label>
                    <input type="number" id="ftp_accounts_limit" name="ftp_accounts_limit" value="0" required>
                </div>
                <div class="form-group">
                    <button type="submit">Mentés</button>
                </div>
            </div>
        </form>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
