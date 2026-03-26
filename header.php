<!DOCTYPE html>
<html>
<head>
    <title>Console d'administration</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Plotly -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <style>
        body { background: #f8f9fa; }
        .menu a { margin-right: 15px; }
        .card { margin-bottom: 20px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="index.php">Admin Panel</a>
    <div class="navbar-nav">
        <a class="nav-link" href="index.php">🏠 Dashboard</a>
        <a class="nav-link" href="logs.php">📄 Logs</a>
        <a class="nav-link" href="status.php">🟢 Statut</a>
        <a class="nav-link" href="config.php">⚙️ Config</a>
    </div>
</nav>

<div class="container mt-4">
