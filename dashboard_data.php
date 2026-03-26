<?php
$host = "localhost";
$user = "root";
$pass = "motdepasse";
$db   = "logs_db";

// --- STATISTIQUES 7 JOURS ---
$sql7 = "SELECT DATE(date_log) AS d, COUNT(*) AS total
         FROM logs
         WHERE date_log >= NOW() - INTERVAL 7 DAY
         GROUP BY d
         ORDER BY d ASC";
$res7 = $conn->query($sql7);

$jours7 = [];
$totaux7 = [];
foreach ($res7 as $row) {
    $jours7[] = $row["d"];
    $totaux7[] = $row["total"];
}

// --- STATISTIQUES 30 JOURS ---
$sql30 = "SELECT DATE(date_log) AS d, COUNT(*) AS total
          FROM logs
          WHERE date_log >= NOW() - INTERVAL 30 DAY
          GROUP BY d
          ORDER BY d ASC";
$res30 = $conn->query($sql30);

$jours30 = [];
$totaux30 = [];
foreach ($res30 as $row) {
    $jours30[] = $row["d"];
    $totaux30[] = $row["total"];
}


$conn = new mysqli($host, $user, $pass, $db);

$username = isset($_GET['username']) ? $_GET['username'] : "";
$search   = isset($_GET['search']) ? $_GET['search'] : "";

$sql = "SELECT * FROM logs WHERE 1=1";

if ($username !== "") {
    $u = $conn->real_escape_string($username);
    $sql .= " AND username LIKE '%$u%'";
}

if ($search !== "") {
    $s = $conn->real_escape_string($search);
    $sql .= " AND (
        fichier LIKE '%$s%' OR
        action LIKE '%$s%' OR
        chemin_source LIKE '%$s%' OR
        chemin_destination LIKE '%$s%' OR
        erreur LIKE '%$s%'
    )";
}

$sql .= " ORDER BY date_log DESC LIMIT 500";

$result = $conn->query($sql);

// --- STATISTIQUES ---
$par_jour = [];
$par_heure = [];
$par_action = [];

foreach ($result as $row) {
    $date = substr($row["date_log"], 0, 10);
    $heure = substr($row["date_log"], 11, 2);
    $action = $row["action"];

    if (!isset($par_jour[$date])) $par_jour[$date] = 0;
    $par_jour[$date]++;

    if (!isset($par_heure[$heure])) $par_heure[$heure] = 0;
    $par_heure[$heure]++;

    if (!isset($par_action[$action])) $par_action[$action] = 0;
    $par_action[$action]++;
}
?>

<!-- GRAPHIQUES -->
<div id="chart_jour" style="height:300px;"></div>
<div id="chart_heure" style="height:300px;"></div>
<div id="chart_action" style="height:300px;"></div>

<script>
Plotly.newPlot('chart_jour', [{
    x: <?= json_encode(array_keys($par_jour)) ?>,
    y: <?= json_encode(array_values($par_jour)) ?>,
    type: 'scatter'
}], { title: "Fichiers traités par jour" });

Plotly.newPlot('chart_heure', [{
    x: <?= json_encode(array_keys($par_heure)) ?>,
    y: <?= json_encode(array_values($par_heure)) ?>,
    type: 'bar'
}], { title: "Fichiers traités par heure" });

Plotly.newPlot('chart_action', [{
    x: <?= json_encode(array_keys($par_action)) ?>,
    y: <?= json_encode(array_values($par_action)) ?>,
    type: 'pie'
}], { title: "Répartition par action" });
</script>

<!-- TABLEAU -->
<table>
    <tr>
        <th>Date</th>
        <th>Username</th>
        <th>Fichier</th>
        <th>Action</th>
        <th>Taille</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Erreur</th>
    </tr>

    <?php foreach ($result as $row): ?>
        <tr>
            <td><?= $row["date_log"] ?></td>
            <td><?= $row["username"] ?></td>
            <td><?= $row["fichier"] ?></td>
            <td><?= $row["action"] ?></td>
            <td><?= $row["taille_fichier"] ?></td>
            <td><?= $row["chemin_source"] ?></td>
            <td><?= $row["chemin_destination"] ?></td>
            <td><?= $row["erreur"] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
