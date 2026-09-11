<?php
session_start();
require "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require "includes/header.php";
require "includes/sidebar.php";

// Benutzerdaten laden
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    
    // Profil aktualisieren
    if ($action === "update_profile") {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        
        if (empty($username) || empty($email)) {
            $error = "Bitte fülle alle Felder aus.";
        } else {
            // Prüfen ob E-Mail schon von anderem Benutzer verwendet wird
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->execute([$email, $_SESSION["user_id"]]);
            
            if ($checkStmt->fetch()) {
                $error = "Diese E-Mail wird bereits von einem anderen Benutzer verwendet.";
            } else {
                try {
                    $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $updateStmt->execute([$username, $email, $_SESSION["user_id"]]);
                    
                    // Session aktualisieren
                    $_SESSION["username"] = $username;
                    $success = "Profil erfolgreich aktualisiert!";
                    
                    // Benutzerdaten neu laden
                    $stmt->execute([$_SESSION["user_id"]]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $error = "Fehler beim Aktualisieren: " . $e->getMessage();
                }
            }
        }
    }
    
    // Passwort aktualisieren
    if ($action === "update_password") {
        $current_password = $_POST["current_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "Bitte fülle alle Passwort-Felder aus.";
        } elseif (!password_verify($current_password, $user["password"])) {
            $error = "Aktuelles Passwort ist falsch.";
        } elseif (strlen($new_password) < 6) {
            $error = "Das neue Passwort muss mindestens 6 Zeichen lang sein.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Die neuen Passwörter stimmen nicht überein.";
        } else {
            try {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$hashedPassword, $_SESSION["user_id"]]);
                
                $success = "Passwort erfolgreich geändert!";
            } catch (PDOException $e) {
                $error = "Fehler beim Ändern des Passworts: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="content">
    <div class="container py-5">
        
        <div class="profile-container">
            
            <!-- Header -->
            <div class="profile-header">
                <h1><i class="bi bi-person-circle"></i> Mein Profil</h1>
                <p>Verwalte deine persönlichen Einstellungen</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert-success">
                    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="profile-grid">
                
                <!-- Profilbild/Avatar -->
                <div class="profile-avatar-section">
                    <div class="profile-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h3><?= htmlspecialchars($user["username"]) ?></h3>
                    <p class="text-muted"><?= htmlspecialchars($user["email"]) ?></p>
                    <div class="member-since">
                        <i class="bi bi-calendar"></i>
                        Mitglied seit <?= date("d.m.Y", strtotime($user["created_at"])) ?>
                    </div>
                </div>

                <!-- Profil bearbeiten -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="bi bi-pencil"></i> Profil bearbeiten</h2>
                    </div>
                    
                    <form method="POST" class="profile-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label class="form-label" for="username">
                                <i class="bi bi-person"></i> Benutzername
                            </label>
                            <input type="text" id="username" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($user["username"]) ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">
                                <i class="bi bi-envelope"></i> E-Mail Adresse
                            </label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($user["email"]) ?>" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <i class="bi bi-check-lg"></i> Änderungen speichern
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Passwort ändern -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2><i class="bi bi-lock"></i> Passwort ändern</h2>
                    </div>
                    
                    <form method="POST" class="profile-form">
                        <input type="hidden" name="action" value="update_password">
                        
                        <div class="form-group">
                            <label class="form-label" for="current_password">
                                <i class="bi bi-key"></i> Aktuelles Passwort
                            </label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="new_password">
                                <i class="bi bi-key-fill"></i> Neues Passwort
                            </label>
                            <input type="password" id="new_password" name="new_password" 
                                   class="form-control" minlength="6" required>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i> Mindestens 6 Zeichen lang
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">
                                <i class="bi bi-lock-fill"></i> Passwort bestätigen
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="form-control" minlength="6" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <i class="bi bi-check-lg"></i> Passwort ändern
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require "includes/footer.php"; ?>