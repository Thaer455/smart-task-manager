<?php

require "../config/database.php";

$id=$_GET["id"];
$project=$_GET["project"];

$sql="
DELETE FROM project_members
WHERE id=?
";

$stmt=$pdo->prepare($sql);

$stmt->execute([$id]);

header("Location: members.php?id=".$project);
exit();