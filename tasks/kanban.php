<?php

session_start();

require "../config/database.php";
require "../app/controllers/TaskController.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$tasks = TaskController::index($pdo);

$todoTasks = [];
$progressTasks = [];
$doneTasks = [];

foreach ($tasks as $task) {

    if ($task["status"] === "todo") {
        $todoTasks[] = $task;
    }

    elseif ($task["status"] === "progress") {
        $progressTasks[] = $task;
    }

    elseif ($task["status"] === "done") {
        $doneTasks[] = $task;
    }
}

require "../includes/header.php";
require "../includes/sidebar.php";
?>

<div class="content">

    <div class="container-fluid py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h1 class="fw-bold">
                    Kanban Board
                </h1>

                <p class="text-muted">
                    Aufgaben nach Status verwalten
                </p>

            </div>

            <a
                href="create.php"
                class="btn btn-primary">

                + Neue Task

            </a>

        </div>


        <div class="row g-4">


            <!-- TODO -->

            <div class="col-md-4">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-secondary text-white">

                        <h5 class="mb-0">
                            📝 Todo
                        </h5>

                    </div>

                    <div class="card-body">

                        <?php foreach ($todoTasks as $task): ?>

                            <div class="card mb-3 shadow-sm">

                                <div class="card-body">

                                    <h5 class="fw-bold">
                                        <?= htmlspecialchars($task["title"]) ?>
                                    </h5>

                                    <p class="text-muted mb-2">
                                        <?= htmlspecialchars($task["description"] ?? "") ?>
                                    </p>

                                    <small>
                                        Projekt:
                                        <?= htmlspecialchars($task["project_title"] ?? "") ?>
                                    </small>

                                    <br>

                                    <small>
                                        Priorität:
                                        <?= ucfirst($task["priority"] ?? "low") ?>
                                    </small>

                                    <div class="mt-3">

                                        <a
                                            href="edit.php?id=<?= $task["id"] ?>"
                                            class="btn btn-warning btn-sm">

                                            Edit

                                        </a>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                        <?php if (empty($todoTasks)): ?>

                            <p class="text-muted text-center">
                                Keine Tasks
                            </p>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- IN PROGRESS -->

            <div class="col-md-4">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-warning">

                        <h5 class="mb-0">
                            🔄 In Progress
                        </h5>

                    </div>

                    <div class="card-body">

                        <?php foreach ($progressTasks as $task): ?>

                            <div class="card mb-3 shadow-sm">

                                <div class="card-body">

                                    <h5 class="fw-bold">
                                        <?= htmlspecialchars($task["title"]) ?>
                                    </h5>

                                    <p class="text-muted mb-2">
                                        <?= htmlspecialchars($task["description"] ?? "") ?>
                                    </p>

                                    <small>
                                        Projekt:
                                        <?= htmlspecialchars($task["project_title"] ?? "") ?>
                                    </small>

                                    <br>

                                    <small>
                                        Priorität:
                                        <?= ucfirst($task["priority"] ?? "low") ?>
                                    </small>

                                    <div class="mt-3">

                                        <a
                                            href="edit.php?id=<?= $task["id"] ?>"
                                            class="btn btn-warning btn-sm">

                                            Edit

                                        </a>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                        <?php if (empty($progressTasks)): ?>

                            <p class="text-muted text-center">
                                Keine Tasks
                            </p>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- DONE -->

            <div class="col-md-4">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-success text-white">

                        <h5 class="mb-0">
                            ✅ Done
                        </h5>

                    </div>

                    <div class="card-body">

                        <?php foreach ($doneTasks as $task): ?>

                            <div class="card mb-3 shadow-sm">

                                <div class="card-body">

                                    <h5 class="fw-bold">
                                        <?= htmlspecialchars($task["title"]) ?>
                                    </h5>

                                    <p class="text-muted mb-2">
                                        <?= htmlspecialchars($task["description"] ?? "") ?>
                                    </p>

                                    <small>
                                        Projekt:
                                        <?= htmlspecialchars($task["project_title"] ?? "") ?>
                                    </small>

                                    <br>

                                    <small>
                                        Priorität:
                                        <?= ucfirst($task["priority"] ?? "low") ?>
                                    </small>

                                    <div class="mt-3">

                                        <a
                                            href="edit.php?id=<?= $task["id"] ?>"
                                            class="btn btn-warning btn-sm">

                                            Edit

                                        </a>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                        <?php if (empty($doneTasks)): ?>

                            <p class="text-muted text-center">
                                Keine Tasks
                            </p>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>