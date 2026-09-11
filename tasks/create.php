<?php
session_start();
require "../config/database.php";
require "../app/controllers/TaskController.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

// Projekte laden
$stmt = $pdo->query("SELECT * FROM projects ORDER BY title ASC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Benutzer laden
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY username ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $project_id = $_POST["project_id"] ?? null;
    $assigned_to = $_POST["assigned_to"] ?? null;
    $title = trim($_POST["title"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $status = $_POST["status"] ?? 'todo';
    $priority = $_POST["priority"] ?? 'medium';
    $deadline = !empty($_POST["deadline"]) ? $_POST["deadline"] : null;

    if (empty($title)) {
        $error = "Der Titel darf nicht leer sein.";
    } elseif (empty($project_id)) {
        $error = "Bitte wähle ein Projekt aus.";
    } else {
        try {
            TaskController::create($pdo, [
                "project_id" => $project_id,
                "assigned_to" => $assigned_to,
                "title" => $title,
                "description" => $description,
                "status" => $status,
                "priority" => $priority,
                "deadline" => $deadline
            ]);

            header("Location: list.php?success=1");
            exit();
        } catch (PDOException $e) {
            $error = "Fehler beim Erstellen: " . $e->getMessage();
        }
    }
}
?>

<div class="content">
    <div class="container py-5">
        
        <div class="form-container">
            
            <!-- Header -->
            <div class="form-header">
                <h1><i class="bi bi-plus-circle"></i> Neue Aufgabe erstellen</h1>
                <p>Fülle die Details aus, um einen neuen Task anzulegen</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="taskForm">
                
                <!-- Basis-Informationen -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="bi bi-info-circle"></i>
                        <span>Basis-Informationen</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="title">
                            <i class="bi bi-card-heading"></i>
                            Titel <span class="required">*</span>
                        </label>
                        <input type="text" id="title" name="title" class="form-control" 
                               placeholder="z.B. Homepage redesign fertigstellen" 
                               required maxlength="200"
                               oninput="updatePreview()">
                        <div class="form-hint">
                            <i class="bi bi-info-circle"></i> Maximal 200 Zeichen
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">
                            <i class="bi bi-text-paragraph"></i> Beschreibung
                        </label>
                        <textarea id="description" name="description" class="form-control" 
                                  placeholder="Beschreibe die Aufgabe im Detail..."
                                  oninput="updatePreview()"></textarea>
                    </div>
                </div>

                <!-- Projekt & Team -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="bi bi-people"></i>
                        <span>Projekt & Team</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="project_id">
                                    <i class="bi bi-folder2-open"></i>
                                    Projekt <span class="required">*</span>
                                </label>
                                <select id="project_id" name="project_id" class="form-select" required onchange="updatePreview()">
                                    <option value="">-- Projekt auswählen --</option>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?= $project['id'] ?>">
                                            <?= htmlspecialchars($project['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="assigned_to">
                                    <i class="bi bi-person-check"></i>
                                    Zugewiesen an
                                </label>
                                <select id="assigned_to" name="assigned_to" class="form-select" onchange="updatePreview()">
                                    <option value="">-- Nicht zugewiesen --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zeitplan -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="bi bi-calendar3"></i>
                        <span>Zeitplan</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="deadline">
                            <i class="bi bi-calendar-event"></i>
                            Deadline
                        </label>
                        <input type="date" id="deadline" name="deadline" class="form-control"
                               min="<?= date('Y-m-d') ?>" onchange="updatePreview()">
                        <div class="form-hint">
                            <i class="bi bi-info-circle"></i> Optional - Leer lassen, wenn keine Deadline
                        </div>
                    </div>
                </div>

                <!-- Status & Priorität -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="bi bi-sliders"></i>
                        <span>Status & Priorität</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="status">
                                    <i class="bi bi-flag"></i>
                                    Status
                                </label>
                                <select id="status" name="status" class="form-select" onchange="updatePreview()">
                                    <option value="todo">📋 Todo</option>
                                    <option value="progress">⏳ In Arbeit</option>
                                    <option value="done">✅ Erledigt</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-exclamation-diamond"></i>
                                    Priorität
                                </label>
                                <div class="priority-options">
                                    <div class="priority-option">
                                        <input type="radio" name="priority" value="low" id="priority-low" onchange="updatePreview()">
                                        <label for="priority-low" class="priority-label">
                                            <span style="color: #48bb78;">●</span> Niedrig
                                        </label>
                                    </div>
                                    <div class="priority-option">
                                        <input type="radio" name="priority" value="medium" id="priority-medium" checked onchange="updatePreview()">
                                        <label for="priority-medium" class="priority-label">
                                            <span style="color: #f6ad55;">●</span> Mittel
                                        </label>
                                    </div>
                                    <div class="priority-option">
                                        <input type="radio" name="priority" value="high" id="priority-high" onchange="updatePreview()">
                                        <label for="priority-high" class="priority-label">
                                            <span style="color: #f56565;">●</span> Hoch
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live-Vorschau -->
                <div class="form-section preview-card">
                    <div class="section-title">
                        <i class="bi bi-eye"></i>
                        <span>Live-Vorschau</span>
                    </div>
                    <div class="preview-title" id="previewTitle">Titel erscheint hier...</div>
                    <div class="preview-meta">
                        <span><i class="bi bi-folder"></i> <span id="previewProject">Kein Projekt</span></span>
                        <span><i class="bi bi-calendar"></i> <span id="previewDeadline">Keine Deadline</span></span>
                        <span><i class="bi bi-person"></i> <span id="previewAssigned">Nicht zugewiesen</span></span>
                    </div>
                </div>

                <!-- Aktionen -->
                <div class="form-actions">
                    <a href="list.php" class="btn-cancel">
                        <i class="bi bi-x-lg"></i> Abbrechen
                    </a>
                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Task erstellen
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- JavaScript für Live-Vorschau -->
<script>
    function updatePreview() {
        const title = document.querySelector('[name="title"]').value;
        const projectSelect = document.querySelector('[name="project_id"]');
        const userSelect = document.querySelector('[name="assigned_to"]');
        const deadline = document.querySelector('[name="deadline"]').value;
        
        document.getElementById('previewTitle').textContent = title || 'Titel erscheint hier...';
        
        const projectText = projectSelect.options[projectSelect.selectedIndex]?.text || 'Kein Projekt';
        document.getElementById('previewProject').textContent = 
            projectSelect.value ? projectText : 'Kein Projekt';
        
        const userText = userSelect.options[userSelect.selectedIndex]?.text || 'Nicht zugewiesen';
        document.getElementById('previewAssigned').textContent = 
            userSelect.value ? userText : 'Nicht zugewiesen';
        
        if (deadline) {
            const date = new Date(deadline);
            document.getElementById('previewDeadline').textContent = 
                date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
        } else {
            document.getElementById('previewDeadline').textContent = 'Keine Deadline';
        }
    }
</script>

<?php require "../includes/footer.php"; ?>