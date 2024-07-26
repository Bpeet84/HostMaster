<?php
// add_user.php - Felhasználó hozzáadása funkció - HostMaster

require_once '../../includes/init.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$error = '';
$success = '';

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

    // CSRF token ellenőrzése
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Érvénytelen CSRF token. Kérjük, próbálja újra.';
    } else {
        // Szerver oldali validáció
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'Minden kötelező mezőt ki kell tölteni.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Érvénytelen email cím.';
        } elseif (strlen($password) < 8) {
            $error = 'A jelszónak legalább 8 karakter hosszúnak kell lennie.';
        } else {
            $pdo = get_db_connection();
            
            try {
                $pdo->beginTransaction();

                // Ellenőrizzük, hogy a felhasználónév vagy email már létezik-e
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
                $stmt->execute([$username, $email]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('A felhasználónév vagy email cím már foglalt.');
                }

                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password, default_domain, bandwidth_limit, disk_space_limit, inode_limit, domains_limit, subdomains_limit, domain_pointers_limit, email_accounts_limit, email_forwarders_limit, autoresponders_limit, mysql_databases_limit, ftp_accounts_limit, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "user")');
                $stmt->execute([$username, $email, $password_hashed, $default_domain, $bandwidth_limit, $disk_space_limit, $inode_limit, $domains_limit, $subdomains_limit, $domain_pointers_limit, $email_accounts_limit, $email_forwarders_limit, $autoresponders_limit, $mysql_databases_limit, $ftp_accounts_limit]);

                $pdo->commit();
                $success = 'Felhasználó sikeresen hozzáadva!';
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Hiba történt: ' . $e->getMessage();
            }
        }
    }
}

$csrf_token = get_csrf_token();

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új Felhasználó Hozzáadása - HostMaster Admin</title>
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
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form id="addUserForm" method="post" action="add_user.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="form-row">
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
                        <label for="bandwidth_limit">Sávszélesség Limit (GB):</label>
                        <input type="number" id="bandwidth_limit" name="bandwidth_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="disk_space_limit">Lemezterület Limit (MB):</label>
                        <input type="number" id="disk_space_limit" name="disk_space_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="inode_limit">Inode Limit:</label>
                        <input type="number" id="inode_limit" name="inode_limit" value="0" required>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="domains_limit">Domain Nevek Limit:</label>
                        <input type="number" id="domains_limit" name="domains_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="subdomains_limit">Aldomainek Limit:</label>
                        <input type="number" id="subdomains_limit" name="subdomains_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="domain_pointers_limit">Domain Pointerek Limit:</label>
                        <input type="number" id="domain_pointers_limit" name="domain_pointers_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="email_accounts_limit">Email Fiókok Limit:</label>
                        <input type="number" id="email_accounts_limit" name="email_accounts_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="email_forwarders_limit">Email Továbbítók Limit:</label>
                        <input type="number" id="email_forwarders_limit" name="email_forwarders_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="autoresponders_limit">Automatikus Válaszolók Limit:</label>
                        <input type="number" id="autoresponders_limit" name="autoresponders_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="mysql_databases_limit">MySQL Adatbázisok Limit:</label>
                        <input type="number" id="mysql_databases_limit" name="mysql_databases_limit" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="ftp_accounts_limit">FTP Fiókok Limit:</label>
                        <input type="number" id="ftp_accounts_limit" name="ftp_accounts_limit" value="0" required>
                    </div>
                </div>
            </div>
            <div class="form-group submit-group">
                <button type="submit">Felhasználó Hozzáadása</button>
            </div>
        </form>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>