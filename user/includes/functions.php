<?php
// functions.php

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}
?>
