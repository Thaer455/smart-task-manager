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

<div
class="card-body drop-zone"
data-status="todo">
<?php foreach($todo as $task): ?>

<div
class="card mb-3 task-card"
draggable="true"
data-id="<?= $task["id"] ?>">
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

<div
class="card-body drop-zone"
data-status="progress">
<?php foreach($progress as $task): ?>

<div
class="card mb-3 task-card"
draggable="true"
data-id="<?= $task["id"] ?>">
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

<div
class="card-body drop-zone"
data-status="done">
<?php foreach($done as $task): ?>

<div
class="card mb-3 task-card"
draggable="true"
data-id="<?= $task["id"] ?>">
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
<script>

let draggedTask=null;

document
.querySelectorAll(".task-card")
.forEach(card=>{

card.addEventListener(
"dragstart",
function(){

draggedTask=this;

});

});


document
.querySelectorAll(".drop-zone")
.forEach(zone=>{

zone.addEventListener(
"dragover",
function(e){

e.preventDefault();

});

zone.addEventListener(
"drop",
function(){

this.appendChild(
draggedTask
);

let taskId=
draggedTask.dataset.id;

let status=
this.dataset.status;

fetch(
"update_status.php",
{
method:"POST",

headers:{
"Content-Type":
"application/x-www-form-urlencoded"
},

body:
"task_id="
+taskId+
"&status="
+status

}

)

.then(response=>response.text())

.then(data=>{

console.log(
"Kanban updated"
);

});

});

});

</script>

<?php require "../includes/footer.php"; ?>