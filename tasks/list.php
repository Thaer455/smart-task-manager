<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

$search = $_GET["search"] ?? "";
$status = $_GET["status"] ?? "";

$sql = "
SELECT tasks.*, projects.title AS project_title
FROM tasks
JOIN projects ON tasks.project_id = projects.id
WHERE 1=1
";

$params = [];

if (!empty($search)) {

    $sql .= " AND tasks.title LIKE ?";
    $params[] = "%$search%";
}

if (!empty($status)) {

    $sql .= " AND tasks.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY tasks.created_at DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <form method="GET" class="row mb-4">
            <div class="col-md-4">

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Task suchen..."
                    value="<?= $search ?>">

            </div>

            <div class="col-md-3">

                <select
                    name="status"
                    class="form-select">

                    <option value="">
                        Alle Status
                    </option>

                    <option value="todo"
                        <?= $status=="todo" ? "selected" : "" ?>>

                        Todo

                    </option>

                    <option value="progress"
                        <?= $status=="progress" ? "selected" : "" ?>>

                        In Progress

                    </option>

                    <option value="done"
                        <?= $status=="done" ? "selected" : "" ?>>

                        Done

                    </option>

                </select>

            </div>

            <div class="col-md-2">

                <button
                    type="submit"
                    class="btn btn-primary">

                    Suchen

                </button>

            </div>

        </form>

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