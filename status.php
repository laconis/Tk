<?php include "header.php"; ?>

<h1>Statut du script</h1>

<?php
$status = "OFFLINE";
if (file_exists("heartbeat.txt")) {
    $last = trim(file_get_contents("heartbeat.txt"));
    $last_dt = strtotime($last);
    if (time() - $last_dt < 10) {
        $status = "ONLINE";
    }
}
?>

<p>
    Statut :
    <?php if ($status === "ONLINE"): ?>
        <span style="color:green;font-weight:bold;">ONLINE</span>
    <?php else: ?>
        <span style="color:red;font-weight:bold;">OFFLINE</span>
    <?php endif; ?>
</p>

<p>Dernière activité : <?= file_exists("heartbeat.txt") ? $last : "Aucune" ?></p>

<?php include "footer.php"; ?>
