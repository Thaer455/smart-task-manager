<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../config/database.php";
require "../includes/header.php";
require "../includes/sidebar.php";

// Anzahl Projekte
$stmt = $pdo->query("SELECT COUNT(*) FROM projects");
$totalProjects = $stmt->fetchColumn();

// Anzahl Tasks
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks");
$totalTasks = $stmt->fetchColumn();

// Offene Tasks
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status != 'done'");
$openTasks = $stmt->fetchColumn();

// Fertige Tasks
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'done'");
$doneTasks = $stmt->fetchColumn();
?>

<div class="content">

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-5">

            <div>

                <h1 class="fw-bold">
                    Willkommen <?= $_SESSION["username"] ?> 👋
                </h1>

                <p class="text-muted">
                    Smart Task Manager Dashboard
                </p>

            </div>

        </div>

        <div class="row g-4">

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2 class="fw-bold">
                            <?= $totalProjects ?>
                        </h2>

                        <p class="text-muted mb-0">
                            Projekte
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2 class="fw-bold">
                            <?= $totalTasks ?>
                        </h2>

                        <p class="text-muted mb-0">
                            Tasks
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2 class="fw-bold">
                            <?= $openTasks ?>
                        </h2>

                        <p class="text-muted mb-0">
                            Offene Tasks
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2 class="fw-bold">
                            <?= $doneTasks ?>
                        </h2>

                        <p class="text-muted mb-0">
                            Erledigte Tasks
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>