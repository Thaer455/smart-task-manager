<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// Projekte laden
$stmt = $pdo->query("SELECT * FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $project_id = $_POST["project_id"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];

    $sql = "INSERT INTO tasks (project_id, title, description, status)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$project_id, $title, $description, $status]);

    header("Location: list.php");
    exit();
}
?>

<h2>Create Task</h2>

<form method="POST">

    <select name="project_id" required>
        <option value="">Select Project</option>

        <?php foreach ($projects as $project): ?>
            <option value="<?= $project["id"] ?>">
                <?= $project["title"] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <input type="text" name="title" placeholder="Task Title" required>

    <br><br>

    <textarea name="description" placeholder="Description"></textarea>

    <br><br>

    <select name="status">
        <option value="todo">Todo</option>
        <option value="progress">In Progress</option>
        <option value="done">Done</option>
    </select>

    <br><br>

    <button type="submit">Create Task</button>
</form>