<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$sql = "
SELECT tasks.*, projects.title AS project_title
FROM tasks
JOIN projects ON tasks.project_id = projects.id
ORDER BY tasks.created_at DESC
";

$stmt = $pdo->query($sql);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Tasks</h2>

<a href="create.php">+ Neue Task</a>

<br><br>

<?php foreach ($tasks as $task): ?>

<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">

    <h3><?= $task["title"] ?></h3>

    <p><?= $task["description"] ?></p>

    <strong>Projekt:</strong>
    <?= $task["project_title"] ?>

    <br><br>

    <strong>Status:</strong>
    <?= $task["status"] ?>

</div>

<?php endforeach; ?>