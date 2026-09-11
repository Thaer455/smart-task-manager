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

// Priority Badge Funktion
function priorityBadge($priority) {
    $labels = [
        'high' => ['icon' => '', 'text' => 'Hoch', 'class' => 'high'],
        'medium' => ['icon' => '🟡', 'text' => 'Mittel', 'class' => 'medium'],
        'low' => ['icon' => '🟢', 'text' => 'Niedrig', 'class' => 'low']
    ];
    $p = $labels[$priority] ?? $labels['low'];
    return '<span class="priority-badge ' . $p['class'] . '">' . $p['icon'] . ' ' . $p['text'] . '</span>';
}

// Deadline Badge Funktion
function deadlineBadge($deadline, $status) {
    if (empty($deadline)) {
        return '<span class="deadline-badge normal"><i class="bi bi-calendar"></i> Keine Deadline</span>';
    }
    
    $today = new DateTime();
    $deadlineDate = new DateTime($deadline);
    
    if ($status !== "done" && $deadlineDate < $today) {
        return '<span class="deadline-badge overdue"><i class="bi bi-exclamation-triangle"></i> Überfällig: ' . date("d.m.Y", strtotime($deadline)) . '</span>';
    }
    
    if ($status === "done") {
        return '<span class="deadline-badge done"><i class="bi bi-check-circle"></i> ' . date("d.m.Y", strtotime($deadline)) . '</span>';
    }
    
    return '<span class="deadline-badge normal"><i class="bi bi-calendar"></i> ' . date("d.m.Y", strtotime($deadline)) . '</span>';
}
?>

<div class="content">
    
    <!-- Header -->
    <div class="kanban-header-section">
        <div>
            <h1><i class="bi bi-kanban"></i> Kanban Board</h1>
            <p>Aufgaben nach Status verwalten</p>
        </div>
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Neuer Task
        </a>
    </div>

    <!-- Kanban Board Grid -->
    <div class="kanban-board">

        <!-- TODO SPALTE -->
        <div class="kanban-column">
            <div class="kanban-column-header">
                <div class="kanban-column-title todo">
                    <i class="bi bi-circle"></i> TODO
                </div>
                <span class="kanban-count"><?= count($todoTasks) ?></span>
            </div>
            <div class="drop-zone" data-status="todo">
                <?php if (empty($todoTasks)): ?>
                    <div class="empty-state">
                        <i class="bi bi-circle"></i>
                        <p>Keine Tasks</p>
                        <a href="create.php" class="btn">
                            <i class="bi bi-plus"></i> Task erstellen
                        </a>
                    </div>
                <?php endif; ?>

                <?php foreach ($todoTasks as $task): ?>
                    <div class="task-card" draggable="true" data-id="<?= htmlspecialchars($task['id']) ?>">
                        <h6><?= htmlspecialchars($task["title"]) ?></h6>
                        
                        <?php if (!empty($task["description"])): ?>
                            <p class="text-muted small" style="margin: 0; font-size: 0.85rem;">
                                <?= htmlspecialchars(substr($task["description"], 0, 80)) ?><?= strlen($task["description"]) > 80 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>

                        <div class="task-meta">
                            <?php if (!empty($task["project_title"])): ?>
                                <span><i class="bi bi-folder"></i> <?= htmlspecialchars($task["project_title"]) ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($task["assigned_user"])): ?>
                                <span><i class="bi bi-person"></i> <?= htmlspecialchars($task["assigned_user"]) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="task-meta">
                            <?= priorityBadge($task["priority"] ?? "low") ?>
                            <?= deadlineBadge($task["deadline"] ?? null, $task["status"]) ?>
                        </div>

                        <div class="task-actions">
                            <a href="edit.php?id=<?= htmlspecialchars($task['id']) ?>" class="btn-edit">
                                <i class="bi bi-pencil"></i> Bearbeiten
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- IN PROGRESS SPALTE -->
        <div class="kanban-column">
            <div class="kanban-column-header">
                <div class="kanban-column-title progress">
                    <i class="bi bi-hourglass-split"></i> IN ARBEIT
                </div>
                <span class="kanban-count"><?= count($progressTasks) ?></span>
            </div>
            <div class="drop-zone" data-status="progress">
                <?php if (empty($progressTasks)): ?>
                    <div class="empty-state">
                        <i class="bi bi-hourglass-split"></i>
                        <p>Keine Tasks in Bearbeitung</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($progressTasks as $task): ?>
                    <div class="task-card" draggable="true" data-id="<?= htmlspecialchars($task['id']) ?>">
                        <h6><?= htmlspecialchars($task["title"]) ?></h6>
                        
                        <?php if (!empty($task["description"])): ?>
                            <p class="text-muted small" style="margin: 0; font-size: 0.85rem;">
                                <?= htmlspecialchars(substr($task["description"], 0, 80)) ?><?= strlen($task["description"]) > 80 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>

                        <div class="task-meta">
                            <?php if (!empty($task["project_title"])): ?>
                                <span><i class="bi bi-folder"></i> <?= htmlspecialchars($task["project_title"]) ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($task["assigned_user"])): ?>
                                <span><i class="bi bi-person"></i> <?= htmlspecialchars($task["assigned_user"]) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="task-meta">
                            <?= priorityBadge($task["priority"] ?? "low") ?>
                            <?= deadlineBadge($task["deadline"] ?? null, $task["status"]) ?>
                        </div>

                        <div class="task-actions">
                            <a href="edit.php?id=<?= htmlspecialchars($task['id']) ?>" class="btn-edit">
                                <i class="bi bi-pencil"></i> Bearbeiten
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- DONE SPALTE -->
        <div class="kanban-column">
            <div class="kanban-column-header">
                <div class="kanban-column-title done">
                    <i class="bi bi-check-circle-fill"></i> ERLEDIGT
                </div>
                <span class="kanban-count"><?= count($doneTasks) ?></span>
            </div>
            <div class="drop-zone" data-status="done">
                <?php if (empty($doneTasks)): ?>
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Noch nichts erledigt</p>
                        <small style="font-style: italic;">Du schaffst das! 💪</small>
                    </div>
                <?php endif; ?>

                <?php foreach ($doneTasks as $task): ?>
                    <div class="task-card is-done" draggable="true" data-id="<?= htmlspecialchars($task['id']) ?>">
                        <h6><?= htmlspecialchars($task["title"]) ?></h6>
                        
                        <?php if (!empty($task["description"])): ?>
                            <p class="text-muted small" style="margin: 0; font-size: 0.85rem;">
                                <?= htmlspecialchars(substr($task["description"], 0, 80)) ?><?= strlen($task["description"]) > 80 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>

                        <div class="task-meta">
                            <?php if (!empty($task["project_title"])): ?>
                                <span><i class="bi bi-folder"></i> <?= htmlspecialchars($task["project_title"]) ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($task["assigned_user"])): ?>
                                <span><i class="bi bi-person"></i> <?= htmlspecialchars($task["assigned_user"]) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="task-meta">
                            <?= priorityBadge($task["priority"] ?? "low") ?>
                            <?= deadlineBadge($task["deadline"] ?? null, $task["status"]) ?>
                        </div>

                        <div class="task-actions">
                            <a href="edit.php?id=<?= htmlspecialchars($task['id']) ?>" class="btn-edit">
                                <i class="bi bi-pencil"></i> Bearbeiten
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<!-- Drag & Drop JavaScript -->
<script>
    let draggedTask = null;

    document.querySelectorAll(".task-card").forEach(task => {
        task.addEventListener("dragstart", function() {
            draggedTask = this;
            setTimeout(() => this.classList.add("dragging"), 0);
        });

        task.addEventListener("dragend", function() {
            this.classList.remove("dragging");
            draggedTask = null;
            document.querySelectorAll(".drop-zone").forEach(zone => {
                zone.classList.remove("drag-over");
            });
        });
    });

    document.querySelectorAll(".drop-zone").forEach(zone => {
        zone.addEventListener("dragover", function(e) {
            e.preventDefault();
            this.classList.add("drag-over");
        });

        zone.addEventListener("dragleave", function(e) {
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove("drag-over");
            }
        });

        zone.addEventListener("drop", function(e) {
            e.preventDefault();
            this.classList.remove("drag-over");

            if (draggedTask) {
                const emptyState = this.querySelector(".empty-state");
                if (emptyState) emptyState.remove();

                this.appendChild(draggedTask);

                const taskId = draggedTask.dataset.id;
                const newStatus = this.dataset.status;

                fetch("update_status.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "task_id=" + encodeURIComponent(taskId) + "&status=" + encodeURIComponent(newStatus)
                })
                .then(response => {
                    if (!response.ok) throw new Error("Status konnte nicht geändert werden.");
                    return response.text();
                })
                .then(() => {
                    console.log("Task " + taskId + " wurde auf " + newStatus + " gesetzt.");
                })
                .catch(error => {
                    console.error("Fehler beim Statuswechsel:", error);
                    alert("Der Status konnte nicht geändert werden.");
                });

                draggedTask = null;
            }
        });
    });
</script>

<?php require "../includes/footer.php"; ?>