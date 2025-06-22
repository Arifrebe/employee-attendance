<?php

include "database.php";

$id = $_GET['id'];

$stmt = $database->prepare("DELETE FROM employees WHERE id = ?");
$stmt->bind_param("i", $id); 
$stmt->execute();
$stmt->close();

header('Location: ../');
die();