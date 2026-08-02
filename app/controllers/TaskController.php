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
    public static function create($pdo, $data)
    {
        return Task::create($pdo, $data);
    }
    public static function find($pdo, $id)
{
    return Task::find($pdo, $id);
}

    public static function update($pdo, $id, $data)
{
    return Task::update($pdo, $id, $data);
}
}