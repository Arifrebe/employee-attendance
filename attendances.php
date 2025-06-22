<?php
require_once "database.php";

$attendanceQuery = mysqli_query($database,"
    SELECT 
        employee_attendance.id AS attendance_id,
        employee_attendance.date AS attendance_date,
        TIME(employee_attendance.check_in_time) AS check_in_time,
        TIME(employee_attendance.break_start_time) AS break_start_time,
        TIME(employee_attendance.break_end_time) AS break_end_time,
        TIME(employee_attendance.check_out_time) AS check_out_time,
        employee_attendance.total_hours,
        employee_attendance.status,
        employees.name AS employee_name,
        employees.rfid AS employee_uid
    FROM employee_attendance
    INNER JOIN employees ON employee_attendance.employee_id = employees.id
    ORDER BY employee_attendance.date ASC
");

if ($attendanceQuery) {
    $attendance = mysqli_fetch_all($attendanceQuery,MYSQLI_ASSOC);
}

$stmt = $database->prepare('SELECT * FROM employees');
$stmt->execute();
$result = $stmt->get_result();
$employees = $result->fetch_all(MYSQLI_ASSOC);

function checkIn($id, $database){
    $date = date('Y-m-d');
    $checkIn = date('Y-m-d H:i:s');
    $status = 'Present';

    $stmt = $database->prepare('SELECT * FROM employee_attendance WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('is', $id, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();
    
    if ($result->num_rows > 0) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda telah absen hari ini!',
            'status' => 'warning',
        ];

        header('Location: attendances');
        exit;
    }

    $stmt = $database->prepare('INSERT INTO employee_attendance(employee_id, date, check_in_time, status) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $id, $date, $checkIn, $status);

    if (!$stmt->execute()) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Terjadi kesalahan saat absen!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }
    
    $_SESSION['pesan'][] = [
        'pesan' => 'Absensi berhasil, selamat bekerja!',
        'status' => 'success',
    ];

    $stmt->close();
    header('Location: attendances');
    exit;
}   

function startBreak($id, $database) {
    $date = date('Y-m-d');
    $startBreak = date('Y-m-d H:i:s');
    $status = 'break';

    $stmt = $database->prepare('SELECT * FROM employee_attendance WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('is', $id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $row = $result->fetch_assoc();
    
    if ($result->num_rows == 0) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda belum melakukan check-in hari ini!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    if (!empty($row['break_start_time'])) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda telah absensi istirahat hari ini!',
            'status' => 'warning',
        ];

        header('Location: attendances');
        exit;
    }

    $stmt = $database->prepare('UPDATE employee_attendance SET break_start_time = ? WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('sis', $startBreak, $id, $date);
    
    if(!$stmt->execute()){
        $_SESSION['pesan'][] = [
            'pesan' => 'Terjadi kesalahan saat absen!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    $_SESSION['pesan'][] = [
        'pesan' => 'Absensi berhasil, selamat beristirahat!',
        'status' => 'success',
    ];

    $stmt->close();
    header('Location: attendances');
    exit;
}

function endBreak($id, $database) {
    $date = date('Y-m-d');
    $endBreak = date('Y-m-d H:i:s');

    $stmt = $database->prepare('SELECT * FROM employee_attendance WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('is', $id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $row = $result->fetch_assoc();
    
    if ($result->num_rows == 0) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda belum melakukan check-in hari ini!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    if (empty($row['break_start_time'])) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda belum check-in istirahat hari ini!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    if (!empty($row['break_end_time'])) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda telah absensi selesai istirahat hari ini!',
            'status' => 'warning',
        ];

        header('Location: attendances');
        exit;
    }

    $stmt = $database->prepare('UPDATE employee_attendance SET break_end_time = ? WHERE employee_id = ? AND date = ?');
    $stmt->bind_param('sis', $endBreak, $id, $date);
    
    if(!$stmt->execute()){
        $_SESSION['pesan'][] = [
            'pesan' => 'Terjadi kesalahan saat absen!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    $_SESSION['pesan'][] = [
        'pesan' => 'Absensi berhasil, selamat kembali bekerja!',
        'status' => 'success',
    ];

    $stmt->close();
    header('Location: attendances');
    exit;
}

function checkOut($id, $database) {
    $date = date('Y-m-d');
    $checkOut = date('Y-m-d H:i:s');
    $status = 'Leave';

    $stmt = $database->prepare('SELECT * FROM employee_attendance WHERE employee_id = ? AND date = ?');
    if (!$stmt) {
        die("Query error: " . $database->error);
    }

    $stmt->bind_param('is', $id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 0) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda belum melakukan check-in hari ini!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    $row = $result->fetch_assoc();

    if (!$row || empty($row['check_in_time'])) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Data absensi tidak ditemukan atau invalid!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    $checkIn = $row['check_in_time'];
    $startBreak = $row['break_start_time'] ?? null;
    $endBreak = $row['break_end_time'] ?? null;

    if (!empty($row['check_out_time'])) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Anda telah absensi pulang hari ini!',
            'status' => 'warning',
        ];
        header('Location: attendances');
        exit;
    }

    $checkInTimestamp = strtotime($checkIn);
    $checkOutTimestamp = strtotime($checkOut);
    $breakStartTimestamp = $startBreak ? strtotime($startBreak) : 0;
    $breakEndTimestamp = $endBreak ? strtotime($endBreak) : 0;

    $breakDuration = ($breakEndTimestamp > $breakStartTimestamp) ? ($breakEndTimestamp - $breakStartTimestamp) : 0;
    $totalHours = $checkOutTimestamp - $checkInTimestamp - $breakDuration;
    $formattedTotalHours = gmdate("H:i:s", max(0, $totalHours));

    $stmt = $database->prepare('UPDATE employee_attendance SET check_out_time = ?, total_hours = ?, status= ? WHERE employee_id = ? AND date = ?');
    if (!$stmt) {
        die("Query error: " . $database->error);
    }

    $stmt->bind_param('sssis', $checkOut, $formattedTotalHours,  $status, $id, $date,);
    
    if (!$stmt->execute()) {
        $_SESSION['pesan'][] = [
            'pesan' => 'Terjadi kesalahan saat absen!',
            'status' => 'danger',
        ];
        header('Location: attendances');
        exit;
    }

    $_SESSION['pesan'][] = [
        'pesan' => 'Absensi berhasil, terima kasih telah bekerja!',
        'status' => 'success',
    ];

    $stmt->close();
    header('Location: attendances');
    exit;    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $employee_id = $_POST['employee_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'check_in':
            checkIn($employee_id, $database);
            break;
        case 'start_break':
            startBreak($employee_id, $database);
            break;
        case 'end_break':
            endBreak($employee_id, $database);
            break;
        case 'check_out':
            checkOut($employee_id, $database);
            break;
        default:
            break;
    }
}
$no = 1;

include "layout/topper.php"; 
?>

<h3 class="mb-4">Kehadiran pegawai</h3>
<div class="card mb-4" style="width:27rem">
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <select name="employee_id" id="" class="form-select mb-2">
                    <?php foreach ($employees as $data) { ?>
                        <option value="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                    <?php } ?>
                </select>
                <button type="submit" name="action" class="btn btn-primary m-1" value="check_in">Check-in</button>
                <button type="submit" name="action" class="btn btn-primary m-1" value="start_break">Mulai Istirahat</button>
                <button type="submit" name="action" class="btn btn-primary m-1" value="end_break">Selesai Istirahat</button>
                <button type="submit" name="action" class="btn btn-primary m-1" value="check_out">Check-out</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Data pegawai</h5>
    </div>
    <div class="card-body">
        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Mulai istirahat</th>
                    <th>Selesai istirahat</th>
                    <th>Pulang</th>
                    <th>Total jam kerja</th>
                    <th>Status</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance as $data) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $data['employee_name']; ?></td>
                        <td><?= $data['attendance_date']; ?></td>
                        <td><?= $data['check_in_time'] ?? '-'; ?></td>
                        <td><?= $data['break_start_time'] ?? '-'; ?></td>
                        <td><?= $data['break_end_time'] ?? '-'; ?></td>
                        <td><?= $data['check_out_time'] ?? '-'; ?></td>
                        <td><?= $data['total_hours'] ?? '-'; ?></td>
                        <td><?= $data['status']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>  
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Masuk</th>
                    <th>Mulai istirahat</th>
                    <th>Selesai istirahat</th>
                    <th>Pulang</th>
                    <th>Total jam kerja</th>
                    <th>Status</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php include "layout/bottom.php"; ?>