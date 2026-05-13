<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// ID holen
$id = $_GET["id"];

// Projekt löschen
$sql = "DELETE FROM projects WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

// Zurück zur Liste
header("Location: list.php");
exit();