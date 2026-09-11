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
    } elseif ($task["status"] === "progress") {
        $progressTasks[] = $task;
    } elseif ($task["status"] === "done") {
        $doneTasks[] = $task;
    }
}

require "../includes/header.php";
require "../includes/sidebar.php";


// ==========================================
// PRIORITÄT DARSTELLEN
// ==========================================

function priorityBadge($priority)
{
    switch ($priority) {

        case "high":
            return '<span class="badge bg-danger">🔴 Hoch</span>';

        case "medium":
            return '<span class="badge bg-warning text-dark"> Mittel</span>';

        case "low":
        default:
            return '<span class="badge bg-success">🟢 Niedrig</span>';
    }
}


// ==========================================
// DEADLINE DARSTELLEN
// ==========================================

function deadlineBadge($deadline, $status)
{
    if (empty($deadline)) {
        return '<span class="text-muted">Keine Deadline</span>';
    }

    $today = new DateTime();
    $deadlineDate = new DateTime($deadline);

    // Nur offene Tasks können überfällig sein
    if ($status !== "done" && $deadlineDate < $today) {

        return '
            <span class="badge bg-danger">
                ⚠️ Überfällig: ' .
                htmlspecialchars(date("d.m.Y", strtotime($deadline))) .
            '</span>
        ';
    }

    // Erledigte Tasks
    if ($status === "done") {

        return '
            <span class="badge bg-success">
                ✅ ' .
                htmlspecialchars(date("d.m.Y", strtotime($deadline))) .
            '</span>
        ';
    }

    // Normale Deadline
    return '
        <span class="badge bg-secondary">
            📅 ' .
            htmlspecialchars(date("d.m.Y", strtotime($deadline))) .
        '</span>
    ';
}

?>

<div class="content">

    <div class="container-fluid py-5">

        <!-- ==========================================
             HEADER
        =========================================== -->

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
                class="btn btn-primary"
            >
                + Neue Task
            </a>

        </div>


        <!-- ==========================================
             KANBAN BOARD
        =========================================== -->

        <div class="row g-4">


            <!-- ======================================
                 TODO
            ======================================= -->

            <div class="col-md-4">

                <div class="card shadow-sm border-0 h-100">

                    <div class="card-header bg-secondary text-white">

                        <h5 class="mb-0">

                            📝 Todo

                            <span class="badge bg-light text-dark ms-2">
                                <?= count($todoTasks) ?>
                            </span>

                        </h5>

                    </div>


                    <div
                        class="card-body kanban-column"
                        data-status="todo"
                    >

                        <?php foreach ($todoTasks as $task): ?>

                            <div
                                class="card mb-3 shadow-sm kanban-task"
                                draggable="true"
                                data-id="<?= htmlspecialchars($task["id"]) ?>"
                            >

                                <div class="card-body">

                                    <!-- Titel -->

                                    <h5 class="fw-bold mb-2">

                                        <?= htmlspecialchars($task["title"]) ?>

                                    </h5>


                                    <!-- Beschreibung -->

                                    <?php if (!empty($task["description"])): ?>

                                        <p class="text-muted small mb-3">

                                            <?= htmlspecialchars($task["description"]) ?>

                                        </p>

                                    <?php endif; ?>


                                    <!-- Projekt -->

                                    <div class="small mb-2">

                                        

                                        <strong>Projekt:</strong>

                                        <?= htmlspecialchars(
                                            $task["project_title"] ?? "Unbekannt"
                                        ) ?>

                                    </div>


                                    <!-- Benutzer -->

                                    <div class="small mb-2">

                                        👤

                                        <strong>Zugewiesen:</strong>

                                        <?= htmlspecialchars(
                                            $task["assigned_user"] ?? "Nicht zugewiesen"
                                        ) ?>

                                    </div>


                                    <!-- Priorität -->

                                    <div class="mb-2">

                                        <?= priorityBadge(
                                            $task["priority"] ?? "low"
                                        ) ?>

                                    </div>


                                    <!-- Deadline -->

                                    <div class="mb-3">

                                        <?= deadlineBadge(
                                            $task["deadline"] ?? null,
                                            $task["status"]
                                        ) ?>

                                    </div>


                                    <!-- Aktionen -->

                                    <a
                                        href="edit.php?id=<?= htmlspecialchars($task["id"]) ?>"
                                        class="btn btn-warning btn-sm"
                                    >
                                        ✏️ Edit
                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>


                        <?php if (empty($todoTasks)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-circle" style="font-size: 2.5rem; color: var(--text-muted); opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                                <p class="text-muted mb-3">Keine offenen Tasks</p>
                                <a href="create.php" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-lg"></i> Ersten Task erstellen
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- ======================================
                 IN PROGRESS
            ======================================= -->

            <div class="col-md-4">

                <div class="card shadow-sm border-0 h-100">

                    <div class="card-header bg-warning">

                        <h5 class="mb-0">

                            🔄 In Progress

                            <span class="badge bg-light text-dark ms-2">
                                <?= count($progressTasks) ?>
                            </span>

                        </h5>

                    </div>


                    <div
                        class="card-body kanban-column"
                        data-status="progress"
                    >

                        <?php foreach ($progressTasks as $task): ?>

                            <div
                                class="card mb-3 shadow-sm kanban-task"
                                draggable="true"
                                data-id="<?= htmlspecialchars($task["id"]) ?>"
                            >

                                <div class="card-body">

                                    <!-- Titel -->

                                    <h5 class="fw-bold mb-2">

                                        <?= htmlspecialchars($task["title"]) ?>

                                    </h5>


                                    <!-- Beschreibung -->

                                    <?php if (!empty($task["description"])): ?>

                                        <p class="text-muted small mb-3">

                                            <?= htmlspecialchars($task["description"]) ?>

                                        </p>

                                    <?php endif; ?>


                                    <!-- Projekt -->

                                    <div class="small mb-2">

                                        📁

                                        <strong>Projekt:</strong>

                                        <?= htmlspecialchars(
                                            $task["project_title"] ?? "Unbekannt"
                                        ) ?>

                                    </div>


                                    <!-- Benutzer -->

                                    <div class="small mb-2">

                                        

                                        <strong>Zugewiesen:</strong>

                                        <?= htmlspecialchars(
                                            $task["assigned_user"] ?? "Nicht zugewiesen"
                                        ) ?>

                                    </div>


                                    <!-- Priorität -->

                                    <div class="mb-2">

                                        <?= priorityBadge(
                                            $task["priority"] ?? "low"
                                        ) ?>

                                    </div>


                                    <!-- Deadline -->

                                    <div class="mb-3">

                                        <?= deadlineBadge(
                                            $task["deadline"] ?? null,
                                            $task["status"]
                                        ) ?>

                                    </div>


                                    <!-- Aktionen -->

                                    <a
                                        href="edit.php?id=<?= htmlspecialchars($task["id"]) ?>"
                                        class="btn btn-warning btn-sm"
                                    >
                                        ✏️ Edit
                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>


                        <?php if (empty($progressTasks)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-hourglass-split" style="font-size: 2.5rem; color: var(--text-muted); opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                                <p class="text-muted mb-0">Keine Tasks in Bearbeitung</p>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- ======================================
                 DONE
            ======================================= -->

            <div class="col-md-4">

                <div class="card shadow-sm border-0 h-100">

                    <div class="card-header bg-success text-white">

                        <h5 class="mb-0">

                            ✅ Done

                            <span class="badge bg-light text-dark ms-2">
                                <?= count($doneTasks) ?>
                            </span>

                        </h5>

                    </div>


                    <div
                        class="card-body kanban-column"
                        data-status="done"
                    >

                        <?php foreach ($doneTasks as $task): ?>

                            <div
                                class="card mb-3 shadow-sm kanban-task"
                                draggable="true"
                                data-id="<?= htmlspecialchars($task["id"]) ?>"
                            >

                                <div class="card-body">

                                    <!-- Titel -->

                                    <h5 class="fw-bold mb-2">

                                        <?= htmlspecialchars($task["title"]) ?>

                                    </h5>


                                    <!-- Beschreibung -->

                                    <?php if (!empty($task["description"])): ?>

                                        <p class="text-muted small mb-3">

                                            <?= htmlspecialchars($task["description"]) ?>

                                        </p>

                                    <?php endif; ?>


                                    <!-- Projekt -->

                                    <div class="small mb-2">

                                        📁

                                        <strong>Projekt:</strong>

                                        <?= htmlspecialchars(
                                            $task["project_title"] ?? "Unbekannt"
                                        ) ?>

                                    </div>


                                    <!-- Benutzer -->

                                    <div class="small mb-2">

                                        👤

                                        <strong>Zugewiesen:</strong>

                                        <?= htmlspecialchars(
                                            $task["assigned_user"] ?? "Nicht zugewiesen"
                                        ) ?>

                                    </div>


                                    <!-- Priorität -->

                                    <div class="mb-2">

                                        <?= priorityBadge(
                                            $task["priority"] ?? "low"
                                        ) ?>

                                    </div>


                                    <!-- Deadline -->

                                    <div class="mb-3">

                                        <?= deadlineBadge(
                                            $task["deadline"] ?? null,
                                            $task["status"]
                                        ) ?>

                                    </div>


                                    <!-- Aktionen -->

                                    <a
                                        href="edit.php?id=<?= htmlspecialchars($task["id"]) ?>"
                                        class="btn btn-warning btn-sm"
                                    >
                                        ✏️ Edit
                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>


                        <?php if (empty($doneTasks)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle" style="font-size: 2.5rem; color: var(--text-muted); opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                                <p class="text-muted mb-2">Noch nichts erledigt</p>
                                <small class="text-muted">Du schaffst das! 💪</small>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ==========================================
     KANBAN JAVASCRIPT
=========================================== -->

<script>

let draggedTask = null;


// ==========================================
// TASK ZIEHEN
// ==========================================

document
    .querySelectorAll(".kanban-task")
    .forEach(task => {

        task.addEventListener("dragstart", function () {

            draggedTask = this;

            this.classList.add("opacity-50");

        });


        task.addEventListener("dragend", function () {

            this.classList.remove("opacity-50");

        });

    });


// ==========================================
// KANBAN-SPALTEN
// ==========================================

document
    .querySelectorAll(".kanban-column")
    .forEach(column => {


        // Drag über Spalte

        column.addEventListener("dragover", function (event) {

            event.preventDefault();

            this.classList.add("bg-light");

        });


        // Drag verlässt Spalte

        column.addEventListener("dragleave", function () {

            this.classList.remove("bg-light");

        });


        // Task wird abgelegt

        column.addEventListener("drop", function (event) {

            event.preventDefault();

            this.classList.remove("bg-light");


            if (!draggedTask) {
                return;
            }


            const taskId =
                draggedTask.dataset.id;

            const newStatus =
                this.dataset.status;


            fetch("update_status.php", {

                method: "POST",

                headers: {

                    "Content-Type":
                        "application/x-www-form-urlencoded"

                },

                body:
                    "task_id=" +
                    encodeURIComponent(taskId) +
                    "&status=" +
                    encodeURIComponent(newStatus)

            })

            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        "Status konnte nicht geändert werden."
                    );

                }

                return response.text();

            })

            .then(() => {

                this.appendChild(draggedTask);

                console.log(
                    "Task " +
                    taskId +
                    " wurde auf " +
                    newStatus +
                    " gesetzt."
                );

            })

            .catch(error => {

                console.error(
                    "Fehler beim Statuswechsel:",
                    error
                );

                alert(
                    "Der Status konnte nicht geändert werden."
                );

            });


            draggedTask = null;

        });

    });

</script>


<?php require "../includes/footer.php"; ?>