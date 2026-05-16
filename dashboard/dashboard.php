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

        <div class="mb-5">

            <h1 class="fw-bold">
                Willkommen <?= $_SESSION["username"] ?> 👋
            </h1>

            <p class="text-muted">
                Smart Task Manager Dashboard
            </p>

        </div>


        <!-- Statistik-Karten -->

        <div class="row g-4 mb-5">

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2><?= $totalProjects ?></h2>

                        <p class="text-muted mb-0">
                            Projekte
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2><?= $totalTasks ?></h2>

                        <p class="text-muted mb-0">
                            Tasks
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2><?= $openTasks ?></h2>

                        <p class="text-muted mb-0">
                            Offene Tasks
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <h2><?= $doneTasks ?></h2>

                        <p class="text-muted mb-0">
                            Erledigte Tasks
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- Schnellaktionen -->

        <div class="row mb-5">

            <div class="col">

                <a href="../projects/create.php"
                   class="btn btn-primary">

                    + Projekt erstellen
                </a>

                <a href="../tasks/create.php"
                   class="btn btn-success">

                    + Task erstellen
                </a>

            </div>

        </div>


        <!-- Letzte Projekte -->

        <?php
        $stmt = $pdo->query("
        SELECT *
        FROM projects
        ORDER BY created_at DESC
        LIMIT 5
        ");

        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body">

                <h4 class="mb-3">
                    Letzte Projekte
                </h4>

                <ul class="list-group">

                    <?php foreach($projects as $project): ?>

                    <li class="list-group-item">

                        <?= $project["title"] ?>

                    </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        </div>


        <!-- Letzte Tasks -->

        <?php
        $stmt = $pdo->query("
        SELECT *
        FROM tasks
        ORDER BY created_at DESC
        LIMIT 5
        ");

        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <h4 class="mb-3">
                    Letzte Tasks
                </h4>

                <ul class="list-group">

                    <?php foreach($tasks as $task): ?>

                    <li class="list-group-item d-flex justify-content-between">

                        <?= $task["title"] ?>

                        <span class="badge bg-primary">

                            <?= $task["status"] ?>

                        </span>

                    </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        </div>

    </div>

</div>
<?php require "../includes/footer.php"; ?>