<?php

$serverName = "localhost";
$username = "root";
$password = '';
$dbName = 'attendance';

$database = new mysqli($serverName, $username, $password, $dbName);

if ($database->connect_error) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . $database->connect_error
    ]);
    exit;
}
