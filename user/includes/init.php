<?php
// includes/init.php

// Session indítása, ha még nincs elindítva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Konfigurációs fájl betöltése
require_once __DIR__ . '/../../config/config.php';

// Funkciók betöltése
require_once __DIR__ . '/functions.php';
?>
