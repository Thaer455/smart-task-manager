<?php
session_start();
require __DIR__ . "/config/database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $error = "Passwörter stimmen nicht überein!";
    } elseif (strlen($password) < 6) {
        $error = "Passwort muss mindestens 6 Zeichen lang sein!";
    } else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$email]);
        
        if ($checkStmt->fetch()) {
            $error = "Diese E-Mail ist bereits registriert!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$username, $email, $hashedPassword])) {
                $success = "Registrierung erfolgreich! Du kannst dich jetzt anmelden.";
            } else {
                $error = "Ein Fehler ist aufgetreten. Bitte versuche es erneut.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrieren - Smart Task Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- KORREKTER PFAD (klein geschrieben!) -->
    <link rel="stylesheet" href="/smart-task-manager/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="auth-logo">🚀</div>
            <h1 class="auth-title">Konto erstellen</h1>
            <p class="auth-subtitle">Registriere dich kostenlos und starte durch.</p>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert-success">
                    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?> 
                    <a href="login.php" style="font-weight: bold; text-decoration: underline;">Zum Login</a>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="username">Benutzername</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Dein Name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">E-Mail Adresse</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="deine@email.de" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Passwort</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mindestens 6 Zeichen" required>
                    <small class="form-hint"><i class="bi bi-info-circle"></i> Mindestens 6 Zeichen lang</small>
                </div>
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Passwort bestätigen</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Passwort wiederholen" required>
                </div>
                <button type="submit" class="btn btn-primary btn-login">
                    Konto erstellen <i class="bi bi-arrow-right"></i>
                </button>
            </form>
            <div class="auth-footer">
                Bereits registriert? <a href="login.php">Jetzt anmelden</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>