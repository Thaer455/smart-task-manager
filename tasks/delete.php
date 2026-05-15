<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET["id"];

$sql = "DELETE FROM tasks WHERE id = ?";
$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);

header("Location: list.php");
exit();