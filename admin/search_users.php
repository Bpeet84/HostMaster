<?php
require_once 'includes/init.php';

$query = isset($_GET['query']) ? sanitize_input($_GET['query']) : '';

$pdo = get_db_connection();
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE username LIKE ?");
$stmt->execute(["%$query%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($users);
?>
