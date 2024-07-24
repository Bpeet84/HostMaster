<?php
require_once '../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = hash_password(sanitize_input($_POST['password']));
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

    $pdo = get_db_connection();
    $stmt = $pdo->prepare('INSERT INTO users (username, password, email, role, default_domain, bandwidth_limit, disk_space_limit, inode_limit, domains_limit, subdomains_limit, domain_pointers_limit, email_accounts_limit, email_forwarders_limit, autoresponders_limit, mysql_databases_limit, ftp_accounts_limit) VALUES (?, ?, ?, "user", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$username, $password, $email, $default_domain, $bandwidth_limit, $disk_space_limit, $inode_limit, $domains_limit, $subdomains_limit, $domain_pointers_limit, $email_accounts_limit, $email_forwarders_limit, $autoresponders_limit, $mysql_databases_limit, $ftp_accounts_limit]);

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Felhasználó Hozzáadása</title>
    <link rel="stylesheet" href="../../assets/css/admin_styles.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="container">
        <h1>Új Felhasználó Hozzáadása</h1>
        <form method="post" action="add_user.php">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="default_domain">Alapértelmezett Domain:</label>
            <input type="text" id="default_domain" name="default_domain" required>
            
            <label for="bandwidth_limit">Sávszélesség (GB):</label>
            <input type="number" id="bandwidth_limit" name="bandwidth_limit" required>
            
            <label for="disk_space_limit">Lemez Terület (MB):</label>
            <input type="number" id="disk_space_limit" name="disk_space_limit" required>
            
            <label for="inode_limit">Inode Limit:</label>
            <input type="number" id="inode_limit" name="inode_limit" required>
            
            <label for="domains_limit">Domain Nevek:</label>
            <input type="number" id="domains_limit" name="domains_limit" required>
            
            <label for="subdomains_limit">Aldomainek:</label>
            <input type="number" id="subdomains_limit" name="subdomains_limit" required>
            
            <label for="domain_pointers_limit">Domain Név Mutatók:</label>
            <input type="number" id="domain_pointers_limit" name="domain_pointers_limit" required>
            
            <label for="email_accounts_limit">Email Fiókok:</label>
            <input type="number" id="email_accounts_limit" name="email_accounts_limit" required>
            
            <label for="email_forwarders_limit">Email Átirányítások:</label>
            <input type="number" id="email_forwarders_limit" name="email_forwarders_limit" required>
            
            <label for="autoresponders_limit">Automatikus Válaszolók:</label>
            <input type="number" id="autoresponders_limit" name="autoresponders_limit" required>
            
            <label for="mysql_databases_limit">MySQL Adatbázisok:</label>
            <input type="number" id="mysql_databases_limit" name="mysql_databases_limit" required>
            
            <label for="ftp_accounts_limit">FTP Fiókok:</label>
            <input type="number" id="ftp_accounts_limit" name="ftp_accounts_limit" required>
            
            <button type="submit">Hozzáadás</button>
        </form>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>
