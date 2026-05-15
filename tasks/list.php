<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

$sql = "
SELECT tasks.*, projects.title AS project_title
FROM tasks
JOIN projects ON tasks.project_id = projects.id
ORDER BY tasks.created_at DESC
";

$stmt = $pdo->query($sql);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="fw-bold">
                Tasks
            </h1>

            <a href="create.php" class="btn btn-primary">
                + Neue Task
            </a>

        </div>

        <?php foreach ($tasks as $task): ?>

            <div class="card shadow-sm border-0 mb-3">

                <div class="card-body">

                    <h3 class="fw-bold">
                        <?= $task["title"] ?>
                    </h3>

                    <p class="text-muted">
                        <?= $task["description"] ?>
                    </p>

                    <p>
                        <strong>Projekt:</strong>
                        <?= $task["project_title"] ?>
                    </p>

                    <form action="update_status.php" method="POST">

                        <input
                            type="hidden"
                            name="task_id"
                            value="<?= $task["id"] ?>">

                        <select
                            name="status"
                            class="form-select w-25 mb-3"
                            onchange="this.form.submit()">

                            <option value="todo"
                                <?= $task["status"] == "todo" ? "selected" : "" ?>>
                                Todo
                            </option>

                            <option value="progress"
                                <?= $task["status"] == "progress" ? "selected" : "" ?>>
                                In Progress
                            </option>

                            <option value="done"
                                <?= $task["status"] == "done" ? "selected" : "" ?>>
                                Done
                            </option>

                        </select>

                    </form>

                    <a
                        href="edit.php?id=<?= $task["id"] ?>"
                        class="btn btn-warning btn-sm">

                        Edit
                    </a>

                    <a
                        href="delete.php?id=<?= $task["id"] ?>"
                        class="btn btn-danger btn-sm">

                        Delete
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<?php require "../includes/footer.php"; ?>