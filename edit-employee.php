<?php
include "database.php";

$id = $_GET['id'];

$stmt = $database->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$employesQuery = $stmt->get_result();

if ($employesQuery) {
    $employes = $employesQuery->fetch_object();
} else {
    echo "Error fetching employee: " . $database->error;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = $_POST['name'];
        $position = $_POST['position'];
        $salary = $_POST['salary'];
        $start_work = $_POST['start_work'];

        $query = "UPDATE employees SET name= ?, position= ?, salary= ?, start_work= ? WHERE id= ? ";
        $stmt = $database->prepare($query);
        
        $stmt->bind_param('ssssi', $name, $position, $salary, $start_work, $id);

        if ($stmt->execute()) {
            header("Location: ../");
            exit();
        } else {
            echo "Error inserting employee: " . $stmt->error;
        }
    } catch (\Throwable $th) {
        echo "An error occurred: " . $th->getMessage();
    }
}

$stmt->close();

include "layout/topper.php"; 
?>
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title">Ubah data karyawan</h5>
        </div>
    </div>
    <form method="post">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-6 mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama karyawan" value="<?= $employes->name; ?>" required>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="position" class="form-label">Posisi</label>
                    <input type="text" class="form-control" id="position" name="position" placeholder="Masukkan posisi karyawan" value="<?= $employes->position; ?>" required>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="salary" class="form-label">Gaji</label>
                    <input type="number" class="form-control" id="salary" name="salary" placeholder="Masukkan gaji karyawan" value="<?= $employes->salary; ?>" required>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="start_work" class="form-label">Kerja dimulai</label>
                    <input type="date" class="form-control" id="start_work" name="start_work" placeholder="Masukkan posisi karyawan" value="<?= $employes->start_work; ?>" required>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
    </form>
</div>

<?php include "layout/bottom.php";
?>
