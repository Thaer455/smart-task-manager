<?php
session_start();
require "../config/database.php";
require "../includes/header.php";
require "../includes/sidebar.php";

// --- OPTIMIERTE DATENBANK-ABFRAGEN ---
$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalTasks = $pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();

$todoTasks = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'todo'")->fetchColumn();
$progressTasks = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'progress'")->fetchColumn();
$doneTasks = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'done'")->fetchColumn();

$openTasks = $todoTasks + $progressTasks;

$stmtProjects = $pdo->query("SELECT id, title, created_at FROM projects ORDER BY created_at DESC LIMIT 5");
$projects = $stmtProjects->fetchAll(PDO::FETCH_ASSOC);

$stmtTasks = $pdo->query("SELECT id, title, status, created_at FROM tasks ORDER BY created_at DESC LIMIT 5");
$tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Navbar MIT Hamburger-Menü für Mobile -->
<nav class="top-navbar">
    <div class="d-flex align-items-center gap-2">
        <!-- Dieser Button öffnet die Sidebar auf dem Handy -->
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <span class="fw-bold d-none d-sm-inline">Smart Task Manager 🚀</span>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <button class="btn" onclick="toggleDarkMode()" title="Dark Mode">
            <i class="bi bi-moon-stars-fill"></i>
        </button>
        <a href="/smart-task-manager/logout.php" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i>
            <span class="d-none d-sm-inline">Logout</span>
        </a>
    </div>
</nav>

<!-- Hauptinhalt -->
<div class="content">
    
    <!-- Kopfzeile -->
    <div class="page-header">
        <div>
            <h1>Willkommen zurück, <?= htmlspecialchars($_SESSION["username"]) ?> 👋</h1>
            <p style="color: var(--text-muted); margin: 0;">Hier ist eine Übersicht über deine aktuellen Aufgaben.</p>
        </div>
        <div class="header-actions">
            <a href="/smart-task-manager/projects/create.php" class="btn btn-primary">
                <i class="bi bi-folder-plus"></i> <span class="d-none d-sm-inline">Neues Projekt</span>
                <span class="d-sm-none">Projekt</span> <!-- Kurzer Text für Handy -->
            </a>
            <a href="/smart-task-manager/tasks/create.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Neuer Task</span>
                <span class="d-sm-none">Task</span>
            </a>
        </div>
    </div>

    <!-- Statistik-Karten -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-icon"><i class="bi bi-folder2-open"></i></div>
            <div class="stat-info">
                <h2><?= $totalProjects ?></h2>
                <p>Projekte</p>
            </div>
        </div>

        <div class="card stat-card border-secondary">
            <div class="stat-icon"><i class="bi bi-list-check"></i></div>
            <div class="stat-info">
                <h2><?= $totalTasks ?></h2>
                <p>Gesamt Tasks</p>
            </div>
        </div>

        <div class="card stat-card border-warning">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-info">
                <h2><?= $openTasks ?></h2>
                <p>In Bearbeitung</p>
            </div>
        </div>

        <div class="card stat-card border-success">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <h2><?= $doneTasks ?></h2>
                <p>Erledigt</p>
            </div>
        </div>
    </div>

    <!-- Hauptbereich: Listen & Diagramm -->
    <div class="dashboard-main-grid">
        
        <!-- Linke Spalte: Listen -->
        <div class="dashboard-lists">
            <!-- Letzte Projekte -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-folder" style="color: var(--primary); margin-right: 8px;"></i> Letzte Projekte</h5>
                    <a href="/smart-task-manager/projects/list.php" class="btn-link">Alle ansehen</a>
                </div>
                <ul class="clean-list">
                    <?php if (count($projects) > 0): ?>
                        <?php foreach($projects as $project): ?>
                        <li>
                            <div>
                                <h6><?= htmlspecialchars($project["title"]) ?></h6>
                                <small><i class="bi bi-calendar3"></i> <?= date('d.m.Y', strtotime($project["created_at"])) ?></small>
                            </div>
                            <i class="bi bi-chevron-right" style="color: var(--text-muted);"></i>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="empty-state-item">
                            <div class="text-center py-4">
                                <i class="bi bi-folder-x" style="font-size: 3rem; color: var(--text-muted); opacity: 0.4; display: block; margin-bottom: 10px;"></i>
                                <p class="text-muted mb-2">Noch keine Projekte vorhanden.</p>
                                <a href="/smart-task-manager/projects/create.php" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg"></i> Erstes Projekt erstellen
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Letzte Tasks -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-check2-square" style="color: var(--success); margin-right: 8px;"></i> Letzte Tasks</h5>
                    <a href="/smart-task-manager/tasks/list.php" class="btn-link">Alle ansehen</a>
                </div>
                <ul class="clean-list">
                    <?php if (count($tasks) > 0): ?>
                        <?php foreach($tasks as $task): 
                            $badgeClass = 'status-todo'; $statusText = 'Todo';
                            if ($task['status'] === 'progress') { $badgeClass = 'status-progress'; $statusText = 'In Arbeit'; }
                            if ($task['status'] === 'done') { $badgeClass = 'status-done'; $statusText = 'Erledigt'; }
                        ?>
                        <li>
                            <div>
                                <h6><?= htmlspecialchars($task["title"]) ?></h6>
                                <small><?= date('d.m.Y', strtotime($task["created_at"])) ?></small>
                            </div>
                            <span class="status-badge <?= $badgeClass ?>"><?= $statusText ?></span>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="empty-state-item">
                            <div class="text-center py-4">
                                <i class="bi bi-check2-square" style="font-size: 3rem; color: var(--text-muted); opacity: 0.4; display: block; margin-bottom: 10px;"></i>
                                <p class="text-muted mb-2">Noch keine Tasks vorhanden.</p>
                                <a href="/smart-task-manager/tasks/create.php" class="btn btn-sm btn-success">
                                    <i class="bi bi-plus-lg"></i> Ersten Task erstellen
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Rechte Spalte: Diagramm -->
        <div class="dashboard-chart">
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                <div class="card-header">
                    <h5><i class="bi bi-pie-chart" style="color: var(--primary); margin-right: 8px;"></i> Status Übersicht</h5>
                </div>
                <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                    <div class="chart-container">
                        <canvas id="taskChart"></canvas>
                    </div>
                    <p class="chart-footer">
                        Du hast aktuell <strong><?= $openTasks ?></strong> offene Aufgaben. 
                        <?php if ($openTasks > 0): ?>
                            <br>Halte durch! 💪
                        <?php else: ?>
                            <br>Alles erledigt! Zeit für eine Pause. ☕
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

    </div> <!-- Ende dashboard-main-grid -->
</div> <!-- Ende content -->

<!-- Chart.js Logik -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('taskChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Todo', 'In Arbeit', 'Erledigt'],
            datasets: [{
                data: [<?= (int)$todoTasks ?>, <?= (int)$progressTasks ?>, <?= (int)$doneTasks ?>],
                backgroundColor: ['#e2e8f0', '#f6ad55', '#48bb78'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { family: "'Inter', sans-serif", size: 12 }
                    }
                }
            },
            cutout: '70%'
        }
    });
</script>

<?php require "../includes/footer.php"; ?>