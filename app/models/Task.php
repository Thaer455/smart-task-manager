<?php

require_once __DIR__ . "/../../config/database.php";

class Task
{
    public static function getAll($pdo, $search = "", $status = "")
    {
        $sql = "
        SELECT
            tasks.*,
            projects.title AS project_title,
            users.username AS assigned_user
        FROM tasks
        JOIN projects
            ON tasks.project_id = projects.id
        LEFT JOIN users
            ON tasks.assigned_to = users.id
        WHERE 1=1
        ";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND tasks.title LIKE ?";
            $params[] = "%{$search}%";
        }

        if (!empty($status)) {
            $sql .= " AND tasks.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY tasks.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateStatus($pdo, $id, $status)
    {
        $stmt = $pdo->prepare("
            UPDATE tasks
            SET status = ?
            WHERE id = ?
        ");

        return $stmt->execute([$status, $id]);
    }
    public static function create($pdo, $data)
{
    $stmt = $pdo->prepare("
        INSERT INTO tasks
        (
            project_id,
            assigned_to,
            title,
            description,
            status,
            priority,
            deadline
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $data["project_id"],
        $data["assigned_to"],
        $data["title"],
        $data["description"],
        $data["status"],
        $data["priority"],
        $data["deadline"]
    ]);
}
public static function find($pdo, $id)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM tasks
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public static function update($pdo, $id, $data)
{
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET
            project_id = ?,
            assigned_to = ?,
            title = ?,
            description = ?,
            status = ?,
            priority = ?,
            deadline = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $data["project_id"],
        $data["assigned_to"],
        $data["title"],
        $data["description"],
        $data["status"],
        $data["priority"],
        $data["deadline"],
        $id
    ]);
}
}