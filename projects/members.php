<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

$project_id = $_GET["id"];

// Projekt laden
$stmt = $pdo->prepare(
"SELECT * FROM projects WHERE id=?"
);

$stmt->execute([$project_id]);

$project = $stmt->fetch(PDO::FETCH_ASSOC);


// Alle User laden

$stmt = $pdo->query(
"SELECT id, username FROM users"
);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Mitglied hinzufügen

if($_SERVER["REQUEST_METHOD"]==="POST"){

$user_id=$_POST["user_id"];

$sql="
INSERT INTO project_members
(project_id,user_id)
VALUES (?,?)
";

$stmt=$pdo->prepare($sql);

$stmt->execute([
$project_id,
$user_id
]);

}
?>


<div class="content">

<div class="container py-5">

<h1 class="mb-4">

Mitglieder für:

<?= $project["title"] ?>

</h1>


<div class="card shadow-sm border-0">

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">

Benutzer auswählen

</label>

<select
name="user_id"
class="form-select">

<?php foreach($users as $user): ?>

<option value="<?= $user["id"] ?>">

<?= $user["username"] ?>

</option>

<?php endforeach; ?>

</select>

</div>


<button
class="btn btn-primary">

Mitglied hinzufügen

</button>

</form>

</div>

</div>

</div>

</div>

<?php require "../includes/footer.php"; ?>