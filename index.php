<?php
// --- CONFIG ---
$host = "localhost";
$user = "root";
$pass = "motdepasse";
$db   = "logs_db";

// --- CONNEXION MYSQL ---
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erreur MySQL : " . $conn->connect_error);
}

// --- FILTRES ---
$username = isset($_GET['username']) ? $_GET['username'] : "";
$search   = isset($_GET['search']) ? $_GET['search'] : "";

// --- REQUÊTE SQL DYNAMIQUE ---
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

// --- STATUT ONLINE/OFFLINE ---
$status = "OFFLINE";
if (file_exists("heartbeat.txt")) {
    $last = trim(file_get_contents("heartbeat.txt"));
    $last_dt = strtotime($last);
    if (time() - $last_dt < 10) {
        $status = "ONLINE";
    }
}

// --- PRÉPARATION DES DONNÉES POUR PLOTLY ---
$counts = [];
if ($result && $result->num_rows > 0) {
    foreach ($result as $row) {
        $u = $row["username"];
        if (!isset($counts[$u])) $counts[$u] = 0;
        $counts[$u]++;
    }
}
?>
<!DOCTYPE html>
<html>
<head>

    <script>
function refreshData() {
    fetch("dashboard_data.php")
        .then(response => response.text())
        .then(html => {
            document.getElementById("data-container").innerHTML = html;
        });
}

setInterval(refreshData, 5000);
</script>
    
    <title>Dashboard Logs</title>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #333; color: white; }
        tr:nth-child(even) { background: #eee; }
        input { padding: 8px; margin-right: 10px; }
        button { padding: 8px 15px; }
        .status-online { color: green; font-weight: bold; }
        .status-offline { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h1>Dashboard des Logs</h1>

<p>
    Statut du script :
    <?php if ($status === "ONLINE"): ?>
        <span class="status-online">ONLINE</span>
    <?php else: ?>
        <span class="status-offline">OFFLINE</span>
    <?php endif; ?>
</p>

<form method="GET">
    <input type="text" name="username" placeholder="Filtrer par username" value="<?= htmlspecialchars($username) ?>">
    <input type="text" name="search" placeholder="Recherche globale" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Filtrer</button>
</form>

<div id="chart" style="width:100%; height:400px; margin-top:40px;"></div>

<script>
// --- DONNÉES POUR LE GRAPHIQUE ---
var usernames = <?= json_encode(array_keys($counts)) ?>;
var values    = <?= json_encode(array_values($counts)) ?>;

var data = [{
    x: usernames,
    y: values,
    type: 'bar'
}];

var layout = {
    title: 'Nombre de fichiers traités par utilisateur'
};

Plotly.newPlot('chart', data, layout);
</script>

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

    <?php if ($result && $result->num_rows > 0): ?>
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
    <?php endif; ?>
</table>

</body>
</html>
