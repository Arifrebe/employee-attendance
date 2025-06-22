<?php
include "database.php";
$stmt = $database->prepare("SELECT COUNT(*) FROM temporary_rfid");
$stmt->execute();
$stmt->bind_result($rfid_count);
$stmt->fetch();
$stmt->close();

if ($rfid_count == 0) {
    echo "<script>alert('Tidak ada RFID yang didaftarkan sementara. Silakan scan terlebih dahulu.'); window.location.href = './card-attendance';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = $_POST['name'];
        $position = $_POST['position'];
        $salary = $_POST['salary'];
        $start_work = $_POST['start_work'];
        $rfid = $_POST['rfid'];

        // Validasi gaji sebagai angka positif
        if (!is_numeric($salary) || $salary <= 0) {
            throw new Exception("Gaji harus berupa angka positif.");
        }

        // Query untuk memasukkan data karyawan
        $query = "INSERT INTO employees (name, position, salary, start_work, rfid) VALUES (?, ?, ?, ?, ?)";
        $stmt = $database->prepare($query);
        $stmt->bind_param('sssss', $name, $position, $salary, $start_work, $rfid);

        if ($stmt->execute()) {
            // Hapus data RFID sementara setelah memasukkan data karyawan
            $stmt = $database->prepare("DELETE FROM temporary_rfid");
            $stmt->execute();

            // Redirect ke halaman utama
            header("Location: ./");
            exit();
        } else {
            throw new Exception('Insert query failed.');
        }
    } catch (\Throwable $th) {
        echo "Terjadi kesalahan: " . $th->getMessage();
    }
}

$stmt = $database->prepare("SELECT rfid FROM temporary_rfid LIMIT 1");
$stmt->execute();
$stmt->bind_result($rfid_record);
$stmt->fetch();
$stmt->close();

if (empty($rfid_record)) {
    $rfid_record = "Silahkan scan terlebih dahulu!";
}

include "layout/topper.php"; 
?>
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title">Tambah Karyawan</h5>
        </div>
    </div>
    <form method="post">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-3 mb-3">
                        <label for="rfid" class="form-label">RFID Token</label>
                        <input type="text" class="form-control" id="rfid" name="rfid" value="<?= htmlspecialchars($rfid_record) ?>" placeholder="Masukkan RFID karyawan" required readonly>
                    </div>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama karyawan" required>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="position" class="form-label">Posisi</label>
                    <input type="text" class="form-control" id="position" name="position" placeholder="Masukkan posisi karyawan" required>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="salary" class="form-label">Gaji</label>
                    <input type="number" class="form-control" id="salary" name="salary" placeholder="Masukkan gaji karyawan" required>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="start_work" class="form-label">Kerja Dimulai</label>
                    <input type="date" class="form-control" id="start_work" name="start_work" required>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
    </form>
</div>

<?php include "layout/bottom.php"; ?>
