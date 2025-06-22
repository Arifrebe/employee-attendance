<?php
header('Content-Type: application/json');
require_once "database.php";

function checkIn($id, $database) {
    $date = date('Y-m-d');
    $checkIn = date('Y-m-d H:i:s');
    $status = 'Present';

    $stmt = $database->prepare('INSERT INTO employee_attendance(employee_id, `date`, check_in_time, status) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $id, $date, $checkIn, $status);
    if (!$stmt->execute()) {
        sendResponse(['status' => 'Gagal', 'message' => 'Kesalahan saat absen!']);
    }
    sendResponse(['status' => 'Sukses', 'message' => 'Absensi berhasil!']);
}

function startBreak($id, $database) {
    $date = date('Y-m-d');
    $startBreak = date('Y-m-d H:i:s');

    $stmt = $database->prepare('UPDATE employee_attendance SET break_start_time = ? WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('sis', $startBreak, $id, $date);
    if (!$stmt->execute()) {
        sendResponse(['status' => 'Gagal', 'message' => 'Kesalahan saat memulai istirahat!']);
    }
    sendResponse(['status' => 'Sukses', 'message' => 'Mulai istirahat!']);
}

function endBreak($id, $database) {
    $date = date('Y-m-d');
    $endBreak = date('Y-m-d H:i:s');

    $stmt = $database->prepare('UPDATE employee_attendance SET break_end_time = ? WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('sis', $endBreak, $id, $date);
    if (!$stmt->execute()) {
        sendResponse(['status' => 'Gagal', 'message' => 'Kesalahan saat mengakhiri istirahat!']);
    }
    sendResponse(['status' => 'Sukses', 'message' => 'Kembali bekerja!']);
}

function checkOut($id, $database) {
    $date = date('Y-m-d');
    $checkOut = date('Y-m-d H:i:s');
    $status = 'Leave';

    $stmt = $database->prepare('SELECT * FROM employee_attendance WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('is', $id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        sendResponse(['status' => 'Gagal', 'message' => 'Belum check-in!']);
    }

    $row = $result->fetch_assoc();
    $checkIn = $row['check_in_time'];
    $startBreak = $row['break_start_time'] ?? null;
    $endBreak = $row['break_end_time'] ?? null;

    $checkInTimestamp = strtotime($checkIn);
    $checkOutTimestamp = strtotime($checkOut);
    $breakStartTimestamp = $startBreak ? strtotime($startBreak) : 0;
    $breakEndTimestamp = $endBreak ? strtotime($endBreak) : 0;

    $breakDuration = max(0, $breakEndTimestamp - $breakStartTimestamp);
    $totalHours = $checkOutTimestamp - $checkInTimestamp - $breakDuration;
    $formattedTotalHours = gmdate("H:i:s", max(0, $totalHours));

    $stmt = $database->prepare('UPDATE employee_attendance SET check_out_time = ?, total_hours = ?, status = ? WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('sssis', $checkOut, $formattedTotalHours, $status, $id, $date);
    if (!$stmt->execute()) {
        sendResponse(['status' => 'Gagal', 'message' => 'Kesalahan saat absen!']);
    }

    sendResponse(['status' => 'Sukses', 'message' => 'Terima kasih telah bekerja!']);
}
