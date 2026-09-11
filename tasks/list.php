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

// Search & Filter
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Tasks laden mit Filter
$sql = "SELECT t.*, p.title as project_title, u.username as assigned_user 
        FROM tasks t 
        LEFT JOIN projects p ON t.project_id = p.id 
        LEFT JOIN users u ON t.assigned_to = u.id 
        WHERE 1=1";

$params = [];

if (!empty($search)) {
    $sql .= " AND (t.title LIKE ? OR t.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($statusFilter)) {
    $sql .= " AND t.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Priority Badge Funktion
function priorityBadge($priority) {
    $labels = [
        'high' => ['icon' => '🔴', 'text' => 'Hoch', 'class' => 'high'],
        'medium' => ['icon' => '🟡', 'text' => 'Mittel', 'class' => 'medium'],
        'low' => ['icon' => '🟢', 'text' => 'Niedrig', 'class' => 'low']
    ];
    $p = $labels[$priority] ?? $labels['low'];
    return '<span class="priority-badge ' . $p['class'] . '">' . $p['icon'] . ' ' . $p['text'] . '</span>';
}

// Status Badge Funktion
function statusBadge($status) {
    $labels = [
        'todo' => ['icon' => '📋', 'text' => 'Todo', 'class' => 'todo'],
        'progress' => ['icon' => '⏳', 'text' => 'In Arbeit', 'class' => 'progress'],
        'done' => ['icon' => '✅', 'text' => 'Erledigt', 'class' => 'done']
    ];
    $s = $labels[$status] ?? $labels['todo'];
    return '<span class="status-badge ' . $s['class'] . '">' . $s['icon'] . ' ' . $s['text'] . '</span>';
}

// Deadline Badge Funktion
function deadlineBadge($deadline, $status) {
    if (empty($deadline)) {
        return '<span class="deadline-badge normal"><i class="bi bi-calendar"></i> Keine Deadline</span>';
    }
    
    $today = new DateTime();
    $deadlineDate = new DateTime($deadline);
    
    if ($status !== "done" && $deadlineDate < $today) {
        return '<span class="deadline-badge overdue"><i class="bi bi-exclamation-triangle"></i> Überfällig: ' . date("d.m.Y", strtotime($deadline)) . '</span>';
    }
    
    if ($status === "done") {
        return '<span class="deadline-badge done"><i class="bi bi-check-circle"></i> ' . date("d.m.Y", strtotime($deadline)) . '</span>';
    }
    
    return '<span class="deadline-badge normal"><i class="bi bi-calendar"></i> ' . date("d.m.Y", strtotime($deadline)) . '</span>';
}
?>

<div class="content">
    
    <!-- Header -->
    <div class="task-list-header">
        <h1><i class="bi bi-list-check"></i> Tasks</h1>
        <p>Verwalte alle deine Aufgaben an einem Ort</p>
    </div>

    <!-- Action Button -->
    <div style="margin-bottom: 24px;">
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Neue Task
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="search-filter-section">
        <form method="GET" action="">
            <div class="search-row">
                <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" placeholder="Task nach Titel suchen..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="status" class="filter-select">
                    <option value="">Alle Status</option>
                    <option value="todo" <?= $statusFilter === 'todo' ? 'selected' : '' ?>>📋 Todo</option>
                    <option value="progress" <?= $statusFilter === 'progress' ? 'selected' : '' ?>>⏳ In Arbeit</option>
                    <option value="done" <?= $statusFilter === 'done' ? 'selected' : '' ?>>✅ Erledigt</option>
                </select>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i> Suche starten
                </button>
            </div>
        </form>
    </div>

    <!-- Task List -->
    <div class="task-list">
        
        <?php if (empty($tasks)): ?>
            <div class="task-list-empty">
                <i class="bi bi-inbox"></i>
                <h3>Keine Tasks gefunden</h3>
                <p>Erstelle deinen ersten Task oder ändere die Suchkriterien.</p>
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Neue Task erstellen
                </a>
            </div>
        <?php else: ?>
            
            <?php foreach ($tasks as $task): ?>
                <div class="task-list-card">
                    
                    <!-- Header mit Titel und Badges -->
                    <div class="task-list-card-header">
                        <h2 class="task-list-card-title"><?= htmlspecialchars($task["title"]) ?></h2>
                        <div class="task-list-card-badges">
                            <?= statusBadge($task["status"]) ?>
                            <?= priorityBadge($task["priority"] ?? "low") ?>
                        </div>
                    </div>

                    <!-- Beschreibung -->
                    <?php if (!empty($task["description"])): ?>
                        <p class="task-list-card-description">
                            <?= htmlspecialchars($task["description"]) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Meta Informationen -->
                    <div class="task-list-card-meta">
                        <div class="meta-item">
                            <i class="bi bi-folder"></i>
                            <div>
                                <strong>Projekt:</strong><br>
                                <span><?= htmlspecialchars($task["project_title"] ?? "Kein Projekt") ?></span>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-person"></i>
                            <div>
                                <strong>Zugewiesen:</strong><br>
                                <span><?= htmlspecialchars($task["assigned_user"] ?? "Nicht zugewiesen") ?></span>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-calendar"></i>
                            <div>
                                <strong>Deadline:</strong><br>
                                <span><?= $task["deadline"] ? date("d.m.Y", strtotime($task["deadline"])) : "Keine Deadline" ?></span>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-clock"></i>
                            <div>
                                <strong>Erstellt:</strong><br>
                                <span><?= date("d.m.Y", strtotime($task["created_at"])) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Change & Actions -->
                    <div class="task-list-card-actions">
                        <form method="POST" action="update_status.php" style="flex-grow: 1;">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <select name="status" class="status-select-card" onchange="this.form.submit()">
                                <option value="todo" <?= $task["status"] === "todo" ? "selected" : "" ?>>📋 Todo</option>
                                <option value="progress" <?= $task["status"] === "progress" ? "selected" : "" ?>>⏳ In Arbeit</option>
                                <option value="done" <?= $task["status"] === "done" ? "selected" : "" ?>>✅ Erledigt</option>
                            </select>
                        </form>
                        
                        <a href="edit.php?id=<?= $task['id'] ?>" class="btn-edit-task">
                            <i class="bi bi-pencil"></i> Bearbeiten
                        </a>
                        
                        <a href="delete.php?id=<?= $task['id'] ?>" class="btn-delete-task" onclick="return confirm('Möchtest du diesen Task wirklich löschen?')">
                            <i class="bi bi-trash"></i> Löschen
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
            
        <?php endif; ?>
        
    </div>
</div>

<?php require "../includes/footer.php"; ?>