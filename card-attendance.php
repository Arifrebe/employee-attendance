<?php
require_once "database.php";

$webMassage = "Silahkan tempel kartu RFID anda";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $rfid = $data['rfid'] ?? null;

    if (!$rfid) {
        sendResponse([
            'status' => 'error',
            'message' => 'RFID tidak ada.'
        ]);
    }

    $stmt = $database->prepare('SELECT id FROM employees WHERE rfid = ?');
    $stmt->bind_param('s', $rfid);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    if (!$employee) {
        $stmt = $database->prepare("SELECT COUNT(*) FROM temporary_rfid");
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            sendResponse([
                'status' => 'Berhasil',
                'message' => 'ID sementara sudah ada.'
            ]);
        }

        $stmt = $database->prepare("INSERT INTO temporary_rfid (rfid) VALUES (?)");
        $stmt->bind_param('s', $rfid);
        $stmt->execute();

        sendResponse([
            'status' => 'Berhasil',
            'message' => "RFID $rfid disimpan."
        ]);
    }

    $employee_id = $employee['id'];
    $today = date('Y-m-d');

    $stmt = $database->prepare('
        SELECT * FROM employee_attendance 
        WHERE employee_id = ? 
        AND DATE(date) = ?
    ');

    $stmt->bind_param('is', $employee_id, $today);
    $stmt->execute();
    $attendance_result = $stmt->get_result();
    $attendance = $attendance_result->fetch_assoc();

    include "RFIDFunction.php";

    if (!empty($attendance['break_start_time']) && !empty($attendance['break_end_time']) && !empty($attendance['check_in_time']) && !empty($attendance['check_out_time'])) {
        sendResponse([
            'status' => 'Gagal',
            'message' => 'Absensi sudah selesai.'
        ]);
    }

    if (!$attendance) {
        checkIn($employee_id, $database);
    } else if (empty($attendance['break_start_time'])) {
        startBreak($employee_id, $database);
    } else if (!empty($attendance['break_start_time']) && empty($attendance['break_end_time'])) {
        endBreak($employee_id, $database);
    } else if (!empty($attendance['break_start_time']) && !empty($attendance['break_end_time']) && !empty($attendance['check_in_time'])) {
        checkOut($employee_id, $database);
    }
}

include "layout/topper.php"; 
?>

<div style="display: flex; justify-content: center; align-items: center; height: 80vh; text-align: center;">
    <div>
        <h3 class="mb-5" id="status"><?= $webMassage ?></h3>
        <img src="./assets/radio-wave.png" alt="Radio Wave" style="width:50%; margin-bottom:40px;">
        <div class="d-flex justify-content-center">
            <div class="progress" style="width: 50%; height: 20px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
            </div>
        </div>

        <!-- Input RFID -->
        <input type="text" id="rfid" autofocus style="opacity: 0; position: absolute;">

        <!-- Menampilkan hasil respons -->
        <p id="response" style="margin-top: 30px;"></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rfidInput = document.getElementById('rfid');
    const statusEl = document.getElementById('status');
    const responseEl = document.getElementById('response');
    let lastMessage = '';

    setInterval(() => {
        const timestamp = new Date().getTime();
        
        fetch('last_response.json?' + timestamp)
            .then(res => res.json())
            .then(data => {
                // Cek jika ada pesan baru yang belum ditampilkan
                if (data && data.message && data.message !== lastMessage) {
                    // Tampilkan pesan dan status baru
                    responseEl.innerText = data.message;
                    statusEl.innerText = data.status;

                    // Simpan pesan terakhir
                    lastMessage = data.message;

                    // Hapus respons setelah 4 detik dan pastikan tidak terbaca lagi
                    setTimeout(() => {
                        responseEl.innerText = '';
                        statusEl.innerText = 'Silahkan tempel kartu RFID anda.';

                        // Panggil file untuk menghapus isi file JSON
                        fetch('clear_response.php');  // Clear response di file JSON

                    }, 4000);  // Respons dihapus setelah 4 detik
                }
            })
            .catch(err => {
                console.error("Gagal ambil response terakhir:", err);
            });
    }, 3000);  // Polling setiap 3 detik

    if (rfidInput) {
        rfidInput.addEventListener('input', function () {
            const rfid = this.value.trim();

            if (rfid.length >= 10) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ rfid: rfid })
                })
                .then(response => response.json())
                .then(data => {
                    responseEl.innerText = data.message;
                    statusEl.innerText = data.status;
                    this.value = '';
                    this.focus();
                })
                .catch(error => {
                    console.error('Gagal:', error);
                    responseEl.innerText = "❌ Terjadi kesalahan.";
                    statusEl.innerText = "❌ Gagal membaca kartu.";
                });
            }
        });
    }
});
</script>


<?php include "layout/bottom.php"; ?>
