<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Meine Projekte</h2>

<a href="create.php">+ Neues Projekt</a>

<br><br>

<?php foreach ($projects as $project): ?>
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <h3><?= $project["title"] ?></h3>
        <p><?= $project["description"] ?></p>

        <a href="edit.php?id=<?= $project["id"] ?>">Edit</a>

        <a href="delete.php?id=<?= $project["id"] ?>">
            Delete
        </a>
    </div>
<?php endforeach; ?>