<?php
// functions.php - Profilmodul-specifikus függvények

function get_form_data(): array {
    return [
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
}

function validate_form_data(array $form_data): string {
    // Ellenőrizzük, hogy minden szükséges adat megvan-e
    $required_fields = ['username', 'email', 'password', 'default_domain'];
    foreach ($required_fields as $field) {
        if (!isset($form_data[$field]) || empty($form_data[$field])) {
            return "Hiba: A(z) $field mező hiányzik vagy üres.";
        }
    }

    // Felhasználónév validáció
    if (!preg_match('/^[a-z0-9_][a-z0-9_-]{0,31}$/', $form_data['username'])) {
        return "Hiba: A felhasználónév csak kisbetűket, számokat, alulvonást és kötőjelet tartalmazhat, és 1-32 karakter hosszú lehet.";
    }

    // E-mail validáció
    if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        return "Hiba: Érvénytelen e-mail cím.";
    }

    // Jelszó validáció
    if (strlen($form_data['password']) < 8) {
        return "Hiba: A jelszónak legalább 8 karakter hosszúnak kell lennie.";
    }

    // Domain validáció
    if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}$/', $form_data['default_domain'])) {
        return "Hiba: Érvénytelen domain név.";
    }

    // Limit validációk
    $limit_fields = [
        'bandwidth_limit', 'disk_space_limit', 'inode_limit', 'domains_limit',
        'subdomains_limit', 'domain_pointers_limit', 'email_accounts_limit',
        'email_forwarders_limit', 'autoresponders_limit', 'mysql_databases_limit',
        'ftp_accounts_limit'
    ];
    foreach ($limit_fields as $field) {
        if (!is_numeric($form_data[$field]) || $form_data[$field] < 0) {
            return "Hiba: A(z) $field értékének pozitív számnak kell lennie.";
        }
    }

    return '';
}

function add_user(array $form_data): string {
    $pdo = get_db_connection();
    try {
        $pdo->beginTransaction();

        // Ellenőrizzük, hogy a felhasználónév vagy email már létezik-e
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$form_data['username'], $form_data['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('A felhasználónév vagy email cím már foglalt.');
        }

        // Felhasználó hozzáadása az adatbázishoz
        $hashed_password = password_hash($form_data['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, default_domain, bandwidth_limit, disk_space_limit, inode_limit, domains_limit, subdomains_limit, domain_pointers_limit, email_accounts_limit, email_forwarders_limit, autoresponders_limit, mysql_databases_limit, ftp_accounts_limit, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "user")');
        $stmt->execute([
            $form_data['username'],
            $form_data['email'],
            $hashed_password,
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

        // Rendszerfelhasználó létrehozása
        $script_path = '/var/www/HostMaster/admin/assets/scripts/add_user.sh';
        $command = sprintf('sudo %s %s %s %s 2>&1',
            escapeshellarg($script_path),
            escapeshellarg($form_data['username']),
            escapeshellarg($form_data['password']),
            escapeshellarg($form_data['default_domain'])
        );

        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            throw new Exception('Hiba történt a rendszerfelhasználó létrehozása során: ' . implode("\n", $output));
        }

        $pdo->commit();
        return 'Felhasználó sikeresen hozzáadva.';
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Hiba a felhasználó hozzáadása során: ' . $e->getMessage());
        return 'Hiba történt a felhasználó hozzáadása során: ' . $e->getMessage();
    }
}

// További profilmodul-specifikus függvények...
?>