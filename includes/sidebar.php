<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    
    <!-- Close Button (nur auf Mobile sichtbar) -->
    <button class="sidebar-close" onclick="toggleSidebar()">
        <i class="bi bi-x-lg"></i>
    </button>

    <!-- Logo -->
    <div class="sidebar-brand">
        <i class="bi bi-rocket-takeoff-fill me-2"></i> Smart Task
    </div>

    <!-- Navigation -->
    <nav class="flex-grow-1">
        <a href="/smart-task-manager/dashboard/dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="/smart-task-manager/projects/list.php" class="<?= str_contains($current_page, 'project') ? 'active' : '' ?>">
            <i class="bi bi-folder2-open"></i> Projekte
        </a>

        <a href="/smart-task-manager/tasks/list.php" class="<?= str_contains($current_page, 'list') ? 'active' : '' ?>">
            <i class="bi bi-list-check"></i> Aufgaben (Liste)
        </a>

        <a href="/smart-task-manager/tasks/board.php" class="<?= $current_page === 'board.php' ? 'active' : '' ?>">
            <i class="bi bi-kanban"></i> Kanban Board
        </a>
        <a href="/smart-task-manager/profile.php" class="<?= $current_page === 'profile.php' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i> Mein Profil
        </a>
    </nav>

    <!-- Benutzer-Profil - OPTIMIERT -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Benutzer') ?></div>
                <div class="user-status">
                    <span class="status-dot"></span>
                    Online
                </div>
            </div>
        </div>
        
        <a href="/smart-task-manager/logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Abmelden
        </a>
    </div>

</div>