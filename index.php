<?php include "header.php"; ?>

<h1>Dashboard</h1>

<div id="data-container">
    Chargement des données…
</div>

<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script>
function refreshData() {
    fetch("dashboard_data.php")
        .then(r => r.text())
        .then(html => {
            document.getElementById("data-container").innerHTML = html;
        });
}
refreshData();
setInterval(refreshData, 5000);
</script>

<?php include "footer.php"; ?>
