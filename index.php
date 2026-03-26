<?php include "header.php"; ?>

<div class="container mt-4">
    <h1 class="mb-4">Dashboard</h1>

    <div id="data-container" class="mt-3">
        Chargement des données…
    </div>
</div>

<script>
refreshData();
setInterval(refreshData, 5000);
</script>

<?php include "footer.php"; ?>
