<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

// Projekte laden
$stmt = $pdo->query("SELECT * FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $project_id = $_POST["project_id"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];

    $sql = "
    INSERT INTO tasks (project_id, title, description, status)
    VALUES (?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $project_id,
        $title,
        $description,
        $status
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
                    Neue Task
                </h1>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            Projekt
                        </label>

                        <select
                            name="project_id"
                            class="form-select"
                            required>

                            <option value="">
                                Projekt auswählen
                            </option>

                            <?php foreach ($projects as $project): ?>

                                <option value="<?= $project["id"] ?>">
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
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Beschreibung
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="5"></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="todo">Todo</option>
                            <option value="progress">In Progress</option>
                            <option value="done">Done</option>

                        </select>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Task erstellen
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>