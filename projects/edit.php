<?php

session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// ID holen
$id = $_GET["id"];

// Projekt laden
$sql = "SELECT * FROM projects WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Wenn nicht gefunden
if (!$project) {
    die("Project not found");
}

// Update verarbeiten
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];

    $sql = "UPDATE projects SET title = ?, description = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $id]);

    header("Location: list.php");
    exit();
}
?>

<h2>Edit Project</h2>

<form method="POST">
    <input type="text" name="title" value="<?= $project["title"] ?>" required><br><br>

    <textarea name="description"><?= $project["description"] ?></textarea><br><br>

    <button type="submit">Update Project</button>
</form>