<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET["id"];

// Task laden
$sql = "SELECT * FROM tasks WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("Task not found");
}

// Projekte laden
$stmt = $pdo->query("SELECT * FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update speichern
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $project_id = $_POST["project_id"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];

    $sql = "
    UPDATE tasks
    SET project_id = ?, title = ?, description = ?, status = ?
    WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $project_id,
        $title,
        $description,
        $status,
        $id
    ]);

    header("Location: list.php");
    exit();
}
?>

<h2>Edit Task</h2>

<form method="POST">

    <select name="project_id">

        <?php foreach ($projects as $project): ?>

            <option value="<?= $project["id"] ?>"
                <?= $task["project_id"] == $project["id"] ? "selected" : "" ?>>

                <?= $project["title"] ?>

            </option>

        <?php endforeach; ?>

    </select>

    <br><br>

    <input type="text"
           name="title"
           value="<?= $task["title"] ?>"
           required>

    <br><br>

    <textarea name="description"><?= $task["description"] ?></textarea>

    <br><br>

    <select name="status">

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

    <br><br>

    <button type="submit">Update Task</button>

</form>