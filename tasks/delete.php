<?php
session_start();

require "../config/database.php";
require "../app/controllers/TaskController.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET["id"];

TaskController::delete($pdo, $id);

header("Location: list.php");
exit();