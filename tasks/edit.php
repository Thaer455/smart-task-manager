<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

$id = $_GET["id"];

// Task laden
$sql = "SELECT * FROM tasks WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("Task not found");
}

// Projekte laden
$stmt = $pdo->query("SELECT * FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $project_id = $_POST["project_id"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];

    $sql = "
    UPDATE tasks
    SET project_id = ?, title = ?, description = ?, status = ?
    WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $project_id,
        $title,
        $description,
        $status,
        $id
    ]);

    header("Location: list.php");
    exit();
}
?>

<div class="content">

    <div class="container py-5">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <h1 class="fw-bold mb-4">
                    Task bearbeiten
                </h1>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            Projekt
                        </label>

                        <select
                            name="project_id"
                            class="form-select">

                            <?php foreach ($projects as $project): ?>

                                <option
                                    value="<?= $project["id"] ?>"
                                    <?= $task["project_id"] == $project["id"] ? "selected" : "" ?>>

                                    <?= $project["title"] ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Titel
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?= $task["title"] ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Beschreibung
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="5"><?= $task["description"] ?></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

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

                    </div>

                    <button
                        type="submit"
                        class="btn btn-success">

                        Task aktualisieren
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>