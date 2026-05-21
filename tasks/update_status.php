<?php

require "../config/database.php";

$task_id=$_POST["task_id"];
$status=$_POST["status"];

$sql="
UPDATE tasks
SET status=?
WHERE id=?
";

$stmt=$pdo->prepare($sql);

$stmt->execute([
$status,
$task_id
]);