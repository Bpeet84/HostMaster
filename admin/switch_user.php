<?php
// Admin felhasználói váltás - HostMaster

require_once 'includes/init.php';

header('Content-Type: application/json');

function send_error($message, $status_code = 400) {
    http_response_code($status_code);
    echo json_encode(['error' => $message]);
    exit();
}

// Ellenőrizzük, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    send_error("Nincs jogosultsága ehhez a művelethez.", 403);
}

$user_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF ellenőrzés POST kérésnél
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        send_error("Érvénytelen CSRF token.", 403);
    }
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
} else {
    send_error("Nem támogatott HTTP metódus.", 405);
}

if ($user_id === null) {
    send_error("Hiányzó felhasználói azonosító.");
}

try {
    // Ellenőrizzük, hogy a kiválasztott felhasználó létezik-e és felhasználói szerepkörrel rendelkezik-e
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE id = ? AND role = "user"');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['original_admin_id'] = $_SESSION['user_id'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        echo json_encode(['success' => true, 'redirect' => 'http://' . $_SERVER['SERVER_NAME'] . ':8085/index.php']);
    } else {
        send_error("Érvénytelen felhasználó.");
    }
} catch (PDOException $e) {
    send_error("Adatbázis hiba: " . $e->getMessage(), 500);
}
?>