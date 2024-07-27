<?php
// add_user.php - Felhasználó hozzáadása funkció

require_once '../../includes/init.php';
require_once 'functions.php';
check_auth(); // Ellenőrizzük, hogy az admin be van-e jelentkezve

$error = '';
$success = '';
$form_data = get_form_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Érvénytelen űrlap token. Kérjük, próbálja újra.';
    } else {
        $form_data = array_map('trim', $_POST);
        $error = validate_form_data($form_data);
        if (empty($error)) {
            $success = add_user($form_data);
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    <div class="profile-container">
        <h1>Új Felhasználó Hozzáadása</h1>
        <?php if ($error): ?>
            <p class="error-message"><?= xss_clean($error) ?></p>
        <?php elseif ($success): ?>
            <p class="success-message"><?= xss_clean($success) ?></p>
        <?php endif; ?>
        <form method="post" action="add_user.php">
            <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
            <div class="form-group">
                <input type="text" id="username" name="username" value="<?= xss_clean($form_data['username']) ?>" placeholder=" " required>
                <label for="username">Felhasználónév</label>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" value="<?= xss_clean($form_data['email']) ?>" placeholder=" " required>
                <label for="email">E-mail</label>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password">Jelszó</label>
            </div>
            <div class="form-group">
                <input type="text" id="default_domain" name="default_domain" value="<?= xss_clean($form_data['default_domain']) ?>" placeholder=" ">
                <label for="default_domain">Alapértelmezett Domain</label>
            </div>
            <div class="form-group">
                <input type="number" id="bandwidth_limit" name="bandwidth_limit" value="<?= xss_clean($form_data['bandwidth_limit']) ?>" placeholder=" " required>
                <label for="bandwidth_limit">Sávszélesség Limit (GB)</label>
            </div>
            <div class="form-group">
                <input type="number" id="disk_space_limit" name="disk_space_limit" value="<?= xss_clean($form_data['disk_space_limit']) ?>" placeholder=" " required>
                <label for="disk_space_limit">Lemezterület Limit (MB)</label>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Felhasználó Hozzáadása</button>
            </div>
        </form>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>