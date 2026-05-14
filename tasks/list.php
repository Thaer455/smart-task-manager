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

<form action="update_status.php" method="POST">

    <input type="hidden" name="task_id"
           value="<?= $task["id"] ?>">

    <select name="status" onchange="this.form.submit()">

        <option value="todo"
            <?= $task["status"] == "todo" ? "selected" : "" ?>>
            Todo
        </option>

        <option value="progress"
            <?= $task["status"] == "progress" ? "selected" : "" ?>>
            In Progress
        </option>

        <option value="done"
            <?= $task["status"] == "done" ? "selected" : "" ?>>
            Done
        </option>

    </select>

</form>

</div>

<?php endforeach; ?>