<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

// Projekte laden
$stmt = $pdo->query("SELECT * FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Benutzer laden
$stmt = $pdo->query("SELECT id, username FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $project_id = $_POST["project_id"];
    $assigned_to = $_POST["assigned_to"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];
    $priority = $_POST["priority"];
    $deadline = $_POST["deadline"];

    $sql = "
    INSERT INTO tasks
    (project_id, assigned_to, title, description, status, priority, deadline)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $project_id,
        $assigned_to,
        $title,
        $description,
        $status,
        $priority,
        $deadline
    ]);

    header("Location: list.php");
    exit();
}
?>

<div class="content">

<div class="container py-5">

<div class="card shadow-sm border-0">

<div class="card-body">

<h1 class="fw-bold mb-4">
Neue Task
</h1>

<form method="POST">


<div class="mb-3">

<label class="form-label">
Projekt
</label>

<select
name="project_id"
class="form-select"
required>

<option value="">
Projekt auswählen
</option>

<?php foreach ($projects as $project): ?>

<option value="<?= $project["id"] ?>">

<?= $project["title"] ?>

</option>

<?php endforeach; ?>

</select>

</div>


<div class="mb-3">

<label class="form-label">
Zugewiesen an
</label>

<select
name="assigned_to"
class="form-select">

<?php foreach($users as $user): ?>

<option value="<?= $user["id"] ?>">

<?= $user["username"] ?>

</option>

<?php endforeach; ?>

</select>

</div>


<div class="mb-3">

<label class="form-label">
Titel
</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>


<div class="mb-3">

<label class="form-label">
Beschreibung
</label>

<textarea
name="description"
class="form-control"
rows="5"></textarea>

</div>


<div class="mb-3">

<label class="form-label">
Priorität
</label>

<select
name="priority"
class="form-select">

<option value="low">Low</option>
<option value="medium">Medium</option>
<option value="high">High</option>

</select>

</div>


<div class="mb-3">

<label class="form-label">
Deadline
</label>

<input
type="date"
name="deadline"
class="form-control">

</div>


<div class="mb-3">

<label class="form-label">
Status
</label>

<select
name="status"
class="form-select">

<option value="todo">Todo</option>
<option value="progress">In Progress</option>
<option value="done">Done</option>

</select>

</div>


<button
type="submit"
class="btn btn-primary">

Task erstellen

</button>

</form>

</div>
</div>
</div>
</div>

<?php require "../includes/footer.php"; ?>