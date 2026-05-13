<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];
    $user_id = $_SESSION["user_id"];

    $sql = "INSERT INTO projects (title, description, created_by) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $user_id]);

    header("Location: list.php");
    exit();
}
?>

<h2>Projekt erstellen</h2>

<form method="POST">
    <input type="text" name="title" placeholder="Project Title" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <button type="submit">Create</button>
</form>