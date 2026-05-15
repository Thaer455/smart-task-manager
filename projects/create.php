<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];
    $user_id = $_SESSION["user_id"];

    $sql = "INSERT INTO projects (title, description, created_by)
            VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $title,
        $description,
        $user_id
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
                    Neues Projekt
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

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Projekt erstellen
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php require "../includes/footer.php"; ?>