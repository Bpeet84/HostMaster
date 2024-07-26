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
$form_data = [
    'username' => '',
    'email' => '',
    'password' => '',
    'default_domain' => '',
    'bandwidth_limit' => 100,
    'disk_space_limit' => 100,
    'inode_limit' => 100,
    'domains_limit' => 100,
    'subdomains_limit' => 100,
    'domain_pointers_limit' => 100,
    'email_accounts_limit' => 100,
    'email_forwarders_limit' => 100,
    'autoresponders_limit' => 100,
    'mysql_databases_limit' => 100,
    'ftp_accounts_limit' => 100,
];

// Generálunk egy form tokent
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        $error = 'Érvénytelen form token. Kérjük, próbálja újra.';
    } else {
        unset($_SESSION['form_token']); // Token felhasználva, eltávolítjuk
        $form_data['username'] = sanitize_input($_POST['username'] ?? '');
        $form_data['email'] = sanitize_input($_POST['email'] ?? '');
        $form_data['password'] = sanitize_input($_POST['password'] ?? '');
        $form_data['default_domain'] = sanitize_input($_POST['default_domain'] ?? '');
        $form_data['bandwidth_limit'] = intval($_POST['bandwidth_limit'] ?? 100);
        $form_data['disk_space_limit'] = intval($_POST['disk_space_limit'] ?? 100);
        $form_data['inode_limit'] = intval($_POST['inode_limit'] ?? 100);
        $form_data['domains_limit'] = intval($_POST['domains_limit'] ?? 100);
        $form_data['subdomains_limit'] = intval($_POST['subdomains_limit'] ?? 100);
        $form_data['domain_pointers_limit'] = intval($_POST['domain_pointers_limit'] ?? 100);
        $form_data['email_accounts_limit'] = intval($_POST['email_accounts_limit'] ?? 100);
        $form_data['email_forwarders_limit'] = intval($_POST['email_forwarders_limit'] ?? 100);
        $form_data['autoresponders_limit'] = intval($_POST['autoresponders_limit'] ?? 100);
        $form_data['mysql_databases_limit'] = intval($_POST['mysql_databases_limit'] ?? 100);
        $form_data['ftp_accounts_limit'] = intval($_POST['ftp_accounts_limit'] ?? 100);

        // CSRF token ellenőrzése
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error = 'Érvénytelen CSRF token. Kérjük, próbálja újra.';
        } else {
            // Szerver oldali validáció
            if (empty($form_data['username']) || empty($form_data['email']) || empty($form_data['password'])) {
                $error = 'Minden kötelező mezőt ki kell tölteni.';
            } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Érvénytelen email cím.';
            } elseif (strlen($form_data['password']) < 8) {
                $error = 'A jelszónak legalább 8 karakter hosszúnak kell lennie.';
            } else {
                $pdo = get_db_connection();
                $transactionStarted = false;

                try {
                    // Ellenőrizzük, hogy a felhasználónév vagy email már létezik-e
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
                    $stmt->execute([$form_data['username'], $form_data['email']]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception('A felhasználónév vagy email cím már foglalt.');
                    }

                    // Shell script meghívása sudo-val
                    $command = escapeshellcmd("sudo /var/www/HostMaster/admin/assets/scripts/add_user.sh " . $form_data['username'] . " " . $form_data['password'] . " " . $form_data['default_domain']);
                    $output = shell_exec($command . ' 2>&1');  // Redirect stderr to stdout

                    // Naplózzuk a script kimenetét
                    file_put_contents('/var/www/HostMaster/logs/add_user_php.log', "Script output: $output\n", FILE_APPEND);

                    if (strpos($output, 'successfully') !== false) {
                        $pdo->beginTransaction();
                        $transactionStarted = true;

                        // Felhasználó hozzáadása az adatbázishoz
                        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, default_domain, bandwidth_limit, disk_space_limit, inode_limit, domains_limit, subdomains_limit, domain_pointers_limit, email_accounts_limit, email_forwarders_limit, autoresponders_limit, mysql_databases_limit, ftp_accounts_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                        $stmt->execute([
                            $form_data['username'],
                            $form_data['email'],
                            hash_password($form_data['password']),
                            $form_data['default_domain'],
                            $form_data['bandwidth_limit'],
                            $form_data['disk_space_limit'],
                            $form_data['inode_limit'],
                            $form_data['domains_limit'],
                            $form_data['subdomains_limit'],
                            $form_data['domain_pointers_limit'],
                            $form_data['email_accounts_limit'],
                            $form_data['email_forwarders_limit'],
                            $form_data['autoresponders_limit'],
                            $form_data['mysql_databases_limit'],
                            $form_data['ftp_accounts_limit']
                        ]);

                        $pdo->commit();
                        $transactionStarted = false;

                        $success = "A felhasználó sikeresen létrehozva: " . $form_data['username'] . ", domain: " . $form_data['default_domain'];
                        // Form adatok alaphelyzetbe állítása
                        $form_data = [
                            'username' => '',
                            'email' => '',
                            'password' => '',
                            'default_domain' => '',
                            'bandwidth_limit' => 100,
                            'disk_space_limit' => 100,
                            'inode_limit' => 100,
                            'domains_limit' => 100,
                            'subdomains_limit' => 100,
                            'domain_pointers_limit' => 100,
                            'email_accounts_limit' => 100,
                            'email_forwarders_limit' => 100,
                            'autoresponders_limit' => 100,
                            'mysql_databases_limit' => 100,
                            'ftp_accounts_limit' => 100,
                        ];
                    } else {
                        // Adjuk vissza a naplófájl tartalmát hiba esetén
                        $log_contents = file_get_contents('/var/www/HostMaster/logs/add_user.log');
                        throw new Exception("Hiba történt a felhasználó létrehozása során: $output\nNaplófájl tartalma:\n$log_contents");
                    }
                } catch (Exception $e) {
                    if ($transactionStarted) {
                        $pdo->rollBack();
                    }
                    $error = 'Hiba történt: ' . $e->getMessage();
                    // Naplózzuk a hibát
                    file_put_contents('/var/www/HostMaster/logs/add_user_php.log', "Hiba történt: " . $e->getMessage() . "\n", FILE_APPEND);
                }
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
            <div class="error-message"><?php echo nl2br(htmlspecialchars($error)); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form id="addUserForm" method="post" action="add_user.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($_SESSION['form_token']); ?>">
            <div class="form-row">
                <div class="form-column">
                    <div class="form-group">
                        <label for="username">Felhasználónév:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
                        <span class="validation-icon" id="username-validation"></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                        <span class="validation-icon" id="email-validation"></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Jelszó:</label>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($form_data['password']); ?>" required>
                        <span class="validation-icon" id="password-validation"></span>
                    </div>
                    <div class="form-group">
                        <label for="default_domain">Alapértelmezett Domain:</label>
                        <input type="text" id="default_domain" name="default_domain" value="<?php echo htmlspecialchars($form_data['default_domain']); ?>" required>
                        <span class="validation-icon" id="domain-validation"></span>
                    </div>
                </div>
                <div class="form-column">
                    <div class="form-group">
                        <label for="bandwidth_limit">Sávszélesség Limit (GB):</label>
                        <input type="number" id="bandwidth_limit" name="bandwidth_limit" value="<?php echo htmlspecialchars($form_data['bandwidth_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="disk_space_limit">Lemezterület Limit (MB):</label>
                        <input type="number" id="disk_space_limit" name="disk_space_limit" value="<?php echo htmlspecialchars($form_data['disk_space_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="inode_limit">INode Limit:</label>
                        <input type="number" id="inode_limit" name="inode_limit" value="<?php echo htmlspecialchars($form_data['inode_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="domains_limit">Domainek Limit:</label>
                        <input type="number" id="domains_limit" name="domains_limit" value="<?php echo htmlspecialchars($form_data['domains_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="subdomains_limit">Aldomainek Limit:</label>
                        <input type="number" id="subdomains_limit" name="subdomains_limit" value="<?php echo htmlspecialchars($form_data['subdomains_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="domain_pointers_limit">Domain Pointerek Limit:</label>
                        <input type="number" id="domain_pointers_limit" name="domain_pointers_limit" value="<?php echo htmlspecialchars($form_data['domain_pointers_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email_accounts_limit">Email Fiókok Limit:</label>
                        <input type="number" id="email_accounts_limit" name="email_accounts_limit" value="<?php echo htmlspecialchars($form_data['email_accounts_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email_forwarders_limit">Email Továbbítók Limit:</label>
                        <input type="number" id="email_forwarders_limit" name="email_forwarders_limit" value="<?php echo htmlspecialchars($form_data['email_forwarders_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="autoresponders_limit">Automatikus Válaszolók Limit:</label>
                        <input type="number" id="autoresponders_limit" name="autoresponders_limit" value="<?php echo htmlspecialchars($form_data['autoresponders_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mysql_databases_limit">MySQL Adatbázisok Limit:</label>
                        <input type="number" id="mysql_databases_limit" name="mysql_databases_limit" value="<?php echo htmlspecialchars($form_data['mysql_databases_limit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ftp_accounts_limit">FTP Fiókok Limit:</label>
                        <input type="number" id="ftp_accounts_limit" name="ftp_accounts_limit" value="<?php echo htmlspecialchars($form_data['ftp_accounts_limit']); ?>" required>
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
