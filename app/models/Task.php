<?php

require_once __DIR__ . "/../../config/database.php";

class Task {

    public static function all($pdo) {

        $stmt = $pdo->query("
            SELECT * FROM tasks
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateStatus($pdo, $id, $status) {

        $stmt = $pdo->prepare("
            UPDATE tasks
            SET status=?
            WHERE id=?
        ");

        return $stmt->execute([$status, $id]);
    }
}