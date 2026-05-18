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

$sql="
SELECT users.*, project_members.id AS member_id
FROM project_members
JOIN users
ON project_members.user_id=users.id
WHERE project_members.project_id=?
";

$stmt=$pdo->prepare($sql);

$stmt->execute([$project_id]);

$members=$stmt->fetchAll(PDO::FETCH_ASSOC);

// Mitglied hinzufügen

if($_SERVER["REQUEST_METHOD"]==="POST"){

$user_id=$_POST["user_id"];

$sql="
SELECT *
FROM project_members
WHERE project_id=? AND user_id=?
";

$stmt=$pdo->prepare($sql);

$stmt->execute([
$project_id,
$user_id
]);

$exists=$stmt->fetch();

if(!$exists){

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

header("Location: members.php?id=".$project_id);
exit();

}
?>

<div class="content">

<div class="container py-5">

<h1 class="mb-4">

Mitglieder für:

<?= $project["title"] ?>

</h1>


<div class="card shadow-sm border-0 mb-4">

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


<div class="card shadow-sm border-0">

<div class="card-body">

<h3 class="mb-4">

Projekt-Mitglieder

</h3>

<?php foreach($members as $member): ?>

<div class="d-flex justify-content-between align-items-center border-bottom py-3">

<div>

<?= $member["username"] ?>

</div>

<a
href="remove_member.php?id=<?= $member["member_id"] ?>&project=<?= $project_id ?>"
class="btn btn-outline-danger btn-sm">

Entfernen

</a>

</div>

<?php endforeach; ?>

</div>

</div>

</div>

</div>

<?php require "../includes/footer.php"; ?>