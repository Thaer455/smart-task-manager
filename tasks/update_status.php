<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $task_id = $_POST["task_id"];
    $status = $_POST["status"];

    $sql = "UPDATE tasks SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $task_id]);

    header("Location: list.php");
    exit();
}