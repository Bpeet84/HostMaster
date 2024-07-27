<?php
// test_create_user.php

// Biztonsági ellenőrzés: csak parancssori futtatás engedélyezett
if (php_sapi_name() !== 'cli') {
    die("Ez a script csak parancssorból futtatható.");
}

function create_system_user($username, $password, $domain) {
    echo "Felhasználó létrehozásának kezdete...\n";

    // A bash script teljes elérési útja
    $script_path = '/var/www/HostMaster/admin/assets/scripts/add_user.sh';

    // Ellenőrizzük, hogy a script létezik-e
    if (!file_exists($script_path)) {
        return ['success' => false, 'message' => 'A add_user.sh script nem található.'];
    }

    echo "add_user.sh script megtalálva.\n";

    // Bash script futtatása
    $command = sprintf('sudo bash -x %s %s %s %s 2>&1', 
        escapeshellarg($script_path),
        escapeshellarg($username),
        escapeshellarg($password),
        escapeshellarg($domain)
    );

    echo "Parancs végrehajtása: $command\n";

    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);

    echo "Parancs végrehajtva. Kilépési kód: $return_var\n";

    if ($return_var !== 0) {
        return [
            'success' => false, 
            'message' => "Hiba történt a felhasználó létrehozása során. Kilépési kód: $return_var\nKimenet:\n" . implode("\n", $output)
        ];
    }

    return [
        'success' => true, 
        'message' => "Felhasználó sikeresen létrehozva.\nKimenet:\n" . implode("\n", $output)
    ];
}

// Tesztelési rész
if ($argc < 4) {
    echo "Használat: php test_create_user.php <felhasználónév> <jelszó> <domain>\n";
    exit(1);
}

$username = $argv[1];
$password = $argv[2];
$domain = $argv[3];

echo "Paraméterek:\n";
echo "Felhasználónév: $username\n";
echo "Jelszó: " . str_repeat('*', strlen($password)) . "\n";
echo "Domain: $domain\n\n";

$result = create_system_user($username, $password, $domain);

if ($result['success']) {
    echo "Siker: " . $result['message'] . "\n";
} else {
    echo "Hiba: " . $result['message'] . "\n";
}
?>