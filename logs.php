<?php include "header.php"; ?>

<h1>Logs</h1>

<form method="GET">
    <input type="text" name="username" placeholder="Filtrer par username">
    <input type="text" name="search" placeholder="Recherche globale">
    <button type="submit">Filtrer</button>
</form>

<div id="data-container">
    Chargement…
</div>

<script>
function refreshLogs() {
    const params = new URLSearchParams(window.location.search);
    fetch("dashboard_data.php?" + params.toString())
        .then(r => r.text())
        .then(html => {
            document.getElementById("data-container").innerHTML = html;
        });
}
refreshLogs();
setInterval(refreshLogs, 5000);
</script>

<?php include "footer.php"; ?>
