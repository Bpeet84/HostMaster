<?php
// add_user.php - Felhasználó hozzáadása funkció - HostMaster

require_once '../../includes/init.php';
require_once 'functions.php';

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$error = '';
$success = '';
$form_data = get_form_data();

// Generálunk egy form tokent
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        $error = 'Érvénytelen form token. Kérjük, próbálja újra.';
    } else {
        unset($_SESSION['form_token']); // Token felhasználva, eltávolítjuk
        $form_data = array_merge($form_data, array_map('sanitize_input', $_POST));
        $error = validate_form_data($form_data);
        if (empty($error)) {
            $success = add_user($form_data);
            if (strpos($success, 'Hiba') !== false) {
                $error = $success;
                $success = '';
            }
        }
    }
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
</head>
<body>
    <?php include '../../includes/header.php'; // Közös fejléc include-olása ?>
    <?php include '../../includes/sidebar.php'; // Közös sidebar include-olása ?>
    <div class="container">
        <h1>Új Felhasználó Hozzáadása</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="post" action="add_user.php">
            <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($_SESSION['form_token']); ?>">
            <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Jelszó:</label>
                <input type="password" id="password" name="password" value="" required>
            </div>
            <div class="form-group">
                <label for="default_domain">Alapértelmezett Domain:</label>
                <input type="text" id="default_domain" name="default_domain" value="<?php echo htmlspecialchars($form_data['default_domain']); ?>">
            </div>
            <div class="form-group">
                <label for="bandwidth_limit">Sávszélesség Limit (GB):</label>
                <input type="number" id="bandwidth_limit" name="bandwidth_limit" value="<?php echo htmlspecialchars($form_data['bandwidth_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="disk_space_limit">Lemezterület Limit (MB):</label>
                <input type="number" id="disk_space_limit" name="disk_space_limit" value="<?php echo htmlspecialchars($form_data['disk_space_limit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="inode_limit">Inode Limit:</label>
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
            <div class="form-group submit-group">
                <button type="submit">Felhasználó Hozzáadása</button>
            </div>
        </form>
    </div>
    <?php include '../../includes/footer.php'; // Közös lábléc include-olása ?>
</body>
</html>
