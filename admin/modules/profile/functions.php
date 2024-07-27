<?php
// functions.php - Modul funkciók - HostMaster

require_once '../../includes/init.php';

function get_form_data() {
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

function validate_form_data($form_data) {
    if (empty($form_data['username']) || empty($form_data['email']) || empty($form_data['password'])) {
        return 'Minden kötelező mezőt ki kell tölteni.';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        return 'Érvénytelen email cím.';
    } elseif (strlen($form_data['password']) < 8) {
        return 'A jelszónak legalább 8 karakter hosszúnak kell lennie.';
    }
    return '';
}

function add_user($form_data) {
    $pdo = get_db_connection();
    try {
        $pdo->beginTransaction();

        // Ellenőrizzük, hogy a felhasználónév vagy email már létezik-e
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$form_data['username'], $form_data['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('A felhasználónév vagy email cím már foglalt.');
        }

        // Felhasználó hozzáadása
        $hashed_password = password_hash($form_data['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, default_domain, bandwidth_limit, disk_space_limit, inode_limit, domains_limit, subdomains_limit, domain_pointers_limit, email_accounts_limit, email_forwarders_limit, autoresponders_limit, mysql_databases_limit, ftp_accounts_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $form_data['username'], $form_data['email'], $hashed_password, $form_data['default_domain'], $form_data['bandwidth_limit'], $form_data['disk_space_limit'], $form_data['inode_limit'], $form_data['domains_limit'], $form_data['subdomains_limit'], $form_data['domain_pointers_limit'], $form_data['email_accounts_limit'], $form_data['email_forwarders_limit'], $form_data['autoresponders_limit'], $form_data['mysql_databases_limit'], $form_data['ftp_accounts_limit']
        ]);

        $pdo->commit();
        return 'Felhasználó sikeresen hozzáadva.';
    } catch (Exception $e) {
        $pdo->rollBack();
        return 'Hiba történt a felhasználó hozzáadása során: ' . $e->getMessage();
    }
}
?>
