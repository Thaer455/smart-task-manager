<?php
session_start();
require "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

require "../includes/header.php";
require "../includes/sidebar.php";

// Projekte laden mit Task-Anzahl
$stmt = $pdo->query("
    SELECT p.*, 
           COUNT(t.id) as task_count,
           (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count
    FROM projects p
    LEFT JOIN tasks t ON p.id = t.project_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    
    <!-- Header -->
    <div class="project-list-header">
        <h1><i class="bi bi-folder2-open"></i> Projekte</h1>
        <p>Verwalte alle deine Projekte an einem Ort</p>
    </div>

    <!-- Action Button -->
    <div style="margin-bottom: 30px;">
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Neues Projekt
        </a>
    </div>

    <!-- Project Grid -->
    <div class="project-grid">
        
        <?php if (empty($projects)): ?>
            <div class="project-list-empty">
                <i class="bi bi-folder-x"></i>
                <h3>Noch keine Projekte vorhanden</h3>
                <p>Erstelle dein erstes Projekt und beginne mit der Organisation deiner Aufgaben.</p>
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Erstes Projekt erstellen
                </a>
            </div>
        <?php else: ?>
            
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    
                    <!-- Icon -->
                    <div class="project-card-header">
                        <div class="project-card-icon">
                            <i class="bi bi-folder2"></i>
                        </div>
                    </div>

                    <!-- Titel & Beschreibung -->
                    <h2 class="project-card-title"><?= htmlspecialchars($project["title"]) ?></h2>
                    
                    <?php if (!empty($project["description"])): ?>
                        <p class="project-card-description">
                            <?= htmlspecialchars($project["description"]) ?>
                        </p>
                    <?php else: ?>
                        <p class="project-card-description" style="font-style: italic; opacity: 0.6;">
                            Keine Beschreibung vorhanden
                        </p>
                    <?php endif; ?>

                    <!-- Meta Informationen -->
                    <div class="project-card-meta">
                        <div class="project-meta-item">
                            <i class="bi bi-list-check"></i>
                            <span><strong><?= $project['task_count'] ?></strong> Tasks</span>
                        </div>
                        
                        <div class="project-meta-item">
                            <i class="bi bi-people"></i>
                            <span><strong><?= $project['member_count'] ?></strong> Mitglieder</span>
                        </div>
                        
                        <div class="project-meta-item">
                            <i class="bi bi-calendar"></i>
                            <span>Erstellt: <strong><?= date("d.m.Y", strtotime($project["created_at"])) ?></strong></span>
                        </div>
                    </div>

                    <!-- Aktionen -->
                    <div class="project-card-actions">
                        <a href="edit.php?id=<?= $project['id'] ?>" class="btn-project-action btn-project-edit">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        
                        <a href="members.php?id=<?= $project['id'] ?>" class="btn-project-action btn-project-members">
                            <i class="bi bi-people"></i> Members
                        </a>
                        
                        <a href="delete.php?id=<?= $project['id'] ?>" class="btn-project-action btn-project-delete" onclick="return confirm('Möchtest du dieses Projekt wirklich löschen? Alle zugehörigen Tasks werden ebenfalls gelöscht.')">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
            
        <?php endif; ?>
        
    </div>
</div>

<?php require "../includes/footer.php"; ?>