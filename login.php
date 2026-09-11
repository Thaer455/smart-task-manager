<?php
session_start();
require __DIR__ . "/config/database.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        header("Location: dashboard/dashboard.php");
        exit();
    } else {
        $error = "E-Mail oder Passwort ist falsch!";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Task Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- KORREKTER PFAD (klein geschrieben!) -->
    <link rel="stylesheet" href="/smart-task-manager/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="auth-logo">🚀</div>
            <h1 class="auth-title">Willkommen zurück</h1>
            <p class="auth-subtitle">Melde dich an, um deine Aufgaben zu verwalten.</p>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="email">E-Mail Adresse</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@beispiel.de" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Passwort</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-login">
                    Anmelden <i class="bi bi-arrow-right"></i>
                </button>
            </form>
            <div class="auth-footer">
                Noch kein Konto? <a href="register.php">Jetzt kostenlos registrieren</a>
            </div>
        </div>
    </div>
</body>
</html>