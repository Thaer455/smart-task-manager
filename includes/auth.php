<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /task-manager/login.php");
    exit();
}