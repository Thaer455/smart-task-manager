<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

$stmt = $pdo->query("
SELECT *
FROM tasks
ORDER BY created_at DESC
");

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$todo=[];
$progress=[];
$done=[];

foreach($tasks as $task){

    if($task["status"]=="todo"){
        $todo[]=$task;
    }

    elseif($task["status"]=="progress"){
        $progress[]=$task;
    }

    else{
        $done[]=$task;
    }

}
?>

<div class="content">

<div class="container-fluid py-4">

<h1 class="fw-bold mb-4">

📋 Kanban Board

</h1>

<div class="row">

<!-- Todo -->

<div class="col-md-4">

<div class="card shadow-sm">

<div class="card-header">

Todo

</div>

<div class="card-body">

<?php foreach($todo as $task): ?>

<div class="card mb-3">

<div class="card-body">

<h6>

<?= $task["title"] ?>

</h6>

<small class="text-muted">

<?= $task["deadline"] ?>

</small>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</div>


<!-- Progress -->

<div class="col-md-4">

<div class="card shadow-sm">

<div class="card-header">

Progress

</div>

<div class="card-body">

<?php foreach($progress as $task): ?>

<div class="card mb-3">

<div class="card-body">

<h6>

<?= $task["title"] ?>

</h6>

<small class="text-muted">

<?= $task["deadline"] ?>

</small>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</div>


<!-- Done -->

<div class="col-md-4">

<div class="card shadow-sm">

<div class="card-header">

Done

</div>

<div class="card-body">

<?php foreach($done as $task): ?>

<div class="card mb-3">

<div class="card-body">

<h6>

<?= $task["title"] ?>

</h6>

<small class="text-muted">

<?= $task["deadline"] ?>

</small>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</div>

</div>

</div>

</div>

<?php require "../includes/footer.php"; ?>