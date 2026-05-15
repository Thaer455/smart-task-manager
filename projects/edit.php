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

$sql = "SELECT * FROM projects WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Project not found");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];

    $sql = "UPDATE projects
            SET title = ?, description = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $title,
        $description,
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
                    Projekt bearbeiten
                </h1>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">
                            Titel
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?= $project["title"] ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Beschreibung
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="5"><?= $project["description"] ?></textarea>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-success">

                        Projekt aktualisieren
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>