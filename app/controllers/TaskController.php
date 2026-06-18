<?php

require_once __DIR__ . "/../models/Task.php";

class TaskController {

    public static function index($pdo) {

        return Task::all($pdo);
    }

    public static function updateStatus($pdo, $id, $status) {

        return Task::updateStatus($pdo, $id, $status);
    }
}