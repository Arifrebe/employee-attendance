<?php
include "database.php";

$employesQuery = mysqli_query($database,"SELECT * FROM employees");
if ($employesQuery) {
    $employes = mysqli_fetch_all($employesQuery,MYSQLI_ASSOC);
}

$no = 1;

include "layout/topper.php"; 
?>

<h3 class="mb-3">Selamat datang, Admin.</h3>
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title">Data pegawai</h5>
            <a href="./add-employee" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i></a>
        </div>
    </div>
    <div class="card-body">
        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Posisi</th>
                    <th>Gaji</th>
                    <th>Mulai bekerja</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employes as $data) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $data['name']; ?></td>
                        <td><?= $data['position']; ?></td>
                        <td>Rp.<?= $data['salary']; ?></td>
                        <td><?= $data['start_work']; ?></td>
                        <td>
                            <a href="edit-employee/<?= $data['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-user-pen"></i></a>
                            <a href="delete-employee/<?= $data['id'] ?>" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>  
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Posisi</th>
                    <th>Gaji</th>
                    <th>Mulai bekerja</th>
                    <th>Opsi</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php include "layout/bottom.php"; ?>