<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

$stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="fw-bold">
                Projekte
            </h1>

            <a href="create.php" class="btn btn-primary">
                + Neues Projekt
            </a>

        </div>

        <?php foreach ($projects as $project): ?>

            <div class="card shadow-sm border-0 mb-3">

                <div class="card-body">

                    <h3 class="fw-bold">
                        <?= $project["title"] ?>
                    </h3>

                    <p class="text-muted">
                        <?= $project["description"] ?>
                    </p>

                    <a
                        href="edit.php?id=<?= $project["id"] ?>"
                        class="btn btn-warning btn-sm">

                        Edit
                    </a>
                    <as
                    href="members.php?id=<?= $project["id"] ?>"
                    class="btn btn-info btn-sm">

                    Members

                    </a>
                    <a
                        href="delete.php?id=<?= $project["id"] ?>"
                        class="btn btn-danger btn-sm">

                        Delete
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<?php require "../includes/footer.php"; ?>