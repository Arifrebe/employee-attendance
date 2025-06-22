<?php $current_page = basename($_SERVER['REQUEST_URI']);?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kehadiran pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Datatable css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-primary py-3" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Kehadiran pegawai</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'employe-attendances') ? 'active' : ''; ?>" href="/employe-attendances/">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'attendances') ? 'active' : ''; ?>" href="attendances">Absensi pegawai</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'add-employee') ? 'active' : ''; ?>" href="add-employee">Tambah pegawai</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'card-attendance') ? 'active' : ''; ?>" href="card-attendance">Absensi kartu</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- /Navbar -->
    <div class="container-fluid my-3">
        <?php
            if (isset($_SESSION['pesan']) && is_array($_SESSION['pesan'])) {
                foreach ($_SESSION['pesan'] as $pesan) {
                    echo "<div class='alert alert-" . htmlspecialchars($pesan['status']) . " alert-dismissible fade show' role='alert' id='flashMessage'>";
                    echo htmlspecialchars($pesan['pesan']);
                    echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
                    echo "</div>";
                }
                unset($_SESSION['pesan']); // Hapus setelah ditampilkan
            }            
        ?>