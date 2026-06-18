<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../config/database.php";
require "../app/controllers/TaskController.php";

$tasks = TaskController::index($pdo);

$search = $_GET["search"] ?? "";
$status = $_GET["status"] ?? "";

?>

<?php require "../includes/header.php"; ?>
<?php require "../includes/sidebar.php"; ?>

<div class="content">

<div class="container py-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h1 class="fw-bold">Tasks</h1>

<a href="create.php" class="btn btn-primary">
+ Neue Task
</a>

</div>

<form method="GET" class="row mb-4">

<div class="col-md-4">

<div class="input-group shadow-sm">

<span class="input-group-text">🔍</span>

<input
type="text"
name="search"
class="form-control"
placeholder="Task nach Titel suchen..."
value="<?= $search ?>">

</div>

</div>

<div class="col-md-3">

<select name="status" class="form-select">

<option value="">Alle Status</option>

<option value="todo">Todo</option>
<option value="progress">In Progress</option>
<option value="done">Done</option>

</select>

</div>

<div class="col-md-2">

<button type="submit" class="btn btn-primary w-100">
🔍 Suche starten
</button>

</div>

</form>

<!-- TASK LIST -->
<?php foreach ($tasks as $task): ?>

<div class="card shadow-sm border-0 mb-3">

<div class="card-body">

<h3 class="fw-bold">
<?= $task["title"] ?>
</h3>

<p class="text-muted">
<?= $task["description"] ?>
</p>

<p>

<strong>Projekt:</strong>
<?= $task["project_title"] ?? "" ?>

<br>

<strong>Zugewiesen:</strong>
<?= $task["assigned_user"] ?? "Nicht zugewiesen" ?>

<br>

<strong>Priorität:</strong>
<?= ucfirst($task["priority"] ?? "low") ?>

<br>

<strong>Deadline:</strong>
<?= $task["deadline"] ?? "-" ?>

</p>

<!-- AJAX STATUS -->
<select
class="form-select w-25 mb-3 task-status"
data-id="<?= $task["id"] ?>">

<option value="todo" <?= $task["status"]=="todo"?"selected":"" ?>>Todo</option>
<option value="progress" <?= $task["status"]=="progress"?"selected":"" ?>>In Progress</option>
<option value="done" <?= $task["status"]=="done"?"selected":"" ?>>Done</option>

</select>

<a href="edit.php?id=<?= $task["id"] ?>" class="btn btn-warning btn-sm">
Edit
</a>

<a href="delete.php?id=<?= $task["id"] ?>" class="btn btn-danger btn-sm">
Delete
</a>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<script>
document.querySelectorAll(".task-status").forEach(select => {

select.addEventListener("change", function () {

let taskId = this.dataset.id;
let status = this.value;

fetch("update_status.php", {
method: "POST",
headers: {
"Content-Type": "application/x-www-form-urlencoded"
},
body: "task_id=" + taskId + "&status=" + status
});

});

});
</script>

<?php require "../includes/footer.php"; ?>