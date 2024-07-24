<?php
// sidebar.php

// Hostnév és PHP verzió lekérdezése
$hostname = gethostname();
$php_version = phpversion();
?>

<div class="sidebar active">
    <h2>Szerver Információk</h2>
    <div class="info">Hostnév: <?php echo $hostname; ?></div>
    <div class="info">PHP verzió: <?php echo $php_version; ?></div>
</div>
