<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../config/database.php";

// Anzahl User-Projekte
$stmt = $pdo->query("SELECT COUNT(*) FROM projects");
$totalProjects = $stmt->fetchColumn();

// Anzahl Tasks
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks");
$totalTasks = $stmt->fetchColumn();

// Offene Tasks
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status != 'done'");
$openTasks = $stmt->fetchColumn();

// Fertige Tasks
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'done'");
$doneTasks = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .container { padding: 20px; }
        .card {
            display: inline-block;
            background: white;
            padding: 20px;
            margin: 10px;
            width: 200px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 { margin: 0; }
    </style>
</head>
<body>

<div class="container">

    <h1>Willkommen <?= $_SESSION["username"] ?> 🚀</h1>

    <div class="card">
        <h2><?= $totalProjects ?></h2>
        <p>Projekte</p>
    </div>

    <div class="card">
        <h2><?= $totalTasks ?></h2>
        <p>Tasks</p>
    </div>

    <div class="card">
        <h2><?= $openTasks ?></h2>
        <p>Offene Tasks</p>
    </div>

    <div class="card">
        <h2><?= $doneTasks ?></h2>
        <p>Erledigte Tasks</p>
    </div>

</div>

</body>
</html>