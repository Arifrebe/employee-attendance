# Employee Attendance System

## Table of Contents

- [About](#about)
- [Getting Started](#getting_started)
- [Usage](#usage)
- [Contributing](CONTRIBUTING.md)

## About <a name = "about"></a>

Employee Attendance System adalah aplikasi web sederhana berbasis PHP yang digunakan untuk mencatat kehadiran karyawan menggunakan sistem kartu RFID. Sistem ini cocok diterapkan di perkantoran, laboratorium, sekolah, atau ruang kerja yang memerlukan pencatatan absensi otomatis dan efisien.

Fitur utamanya meliputi pemindaian kartu RFID, manajemen data karyawan, riwayat kehadiran, serta halaman login dan antarmuka admin sederhana.

## Getting Started <a name = "getting_started"></a>

Instruksi berikut akan membantu kamu menjalankan proyek ini secara lokal di komputer menggunakan XAMPP atau web server lainnya. Lihat bagian [deployment](#deployment) untuk catatan mengenai cara menjalankan di server langsung (live).


### Prerequisites

Hal-hal yang perlu kamu siapkan sebelum menjalankan sistem:

- XAMPP / WAMP (untuk menjalankan Apache & MySQL)
- Web browser (Chrome, Firefox, dll.)
- Editor teks (VS Code direkomendasikan)
- RFID Reader (jika digunakan secara fisik)

### Installing

Langkah demi langkah untuk menjalankan sistem secara lokal:

1. Clone atau download repository ini:

```
git clone https://github.com/Arifrebe/employee-attendance.git
```

2. Pindahkan folder ke direktori `htdocs` di XAMPP:

```
C:/xampp/htdocs/employee-attendance
```

3. Jalankan XAMPP, aktifkan Apache & MySQL

4. Buka `phpMyAdmin` dan buat database dengan nama `attendance`

5. Import file SQL dari folder assets

6. Akses aplikasi di browser:

```
http://localhost/employee-attendance
```

## Usage <a name = "usage"></a>

Apa saja yang bisa dilakukan:

- Tambah, edit, dan hapus data karyawan (`add-employee.php`, `edit-employee.php`, `delete-employee.php`)
- Pemindaian kartu RFID via `card-attendance.php` (dapat terhubung dengan `esp_code.ino`)
- Data kehadiran tersimpan otomatis ke database
- Riwayat kehadiran dapat dilihat di `attendances.php`
- Tampilan dikustomisasi dengan video/gambar (folder `assets/`)

Contoh penggunaan:
1. Tambah karyawan baru
3. Gunakan kartu RFID untuk melakukan absensi
4. Cek data kehadiran di halaman `attendances.php`

