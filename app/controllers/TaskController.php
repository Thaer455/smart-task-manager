<?php

require_once __DIR__ . "/../models/Task.php";

class TaskController
{
    public static function index($pdo, $search = "", $status = "")
    {
        return Task::getAll($pdo, $search, $status);
    }

    public static function updateStatus($pdo, $id, $status)
    {
        return Task::updateStatus($pdo, $id, $status);
    }
}