# 💡 Panduan Kontribusi

Halo! Terima kasih sudah tertarik untuk berkontribusi pada proyek **Employee Attendance System**. Kami sangat terbuka untuk saran, perbaikan bug, maupun penambahan fitur baru.

Berikut ini adalah panduan kontribusi agar kolaborasi kita lebih rapi dan efisien.

---

## 📥 1. Cara Clone & Setup Proyek

1. Clone repository ini:

   ```bash
   git clone https://github.com/Arifrebe/employee-attendance.git
   ```

2. Jalankan proyek secara lokal:

- Pindahkan folder ke htdocs XAMPP
- Jalankan XAMPP → Aktifkan Apache & MySQL
- Buat database baru absensi di phpMyAdmin
- Import file attendance.sql

3. Akses via browser:

```
http://localhost/employee-attendance
```

## 🧑‍💻 2. Gaya Penulisan Kode

- Gunakan nama variabel dan fungsi dengan bahasa Inggris jika memungkinkan
- Simpan file PHP di root atau pisahkan dalam folder layout/ dan assets/
- Tambahkan komentar kode jika fungsinya tidak langsung terlihat
- Hindari duplikasi kode (gunakan fungsi reusable di RFIDFunction.php atau database.php)

## 🌿 3. Branching dan Pull Request

- Gunakan branch terpisah untuk setiap fitur atau perbaikan:
    - `feature/fitur-baru`
    - `bugfix/perbaikan-rfid`

- Commit message harus jelas:

    - ✅ Baik: Fix bug tidak simpan data absensi

    - ❌ Hindari: update file

Setelah selesai, buat Pull Request ke branch main disertai deskripsi perubahan

## 📣 4. Lapor Bug atau Minta Fitur
Gunakan tab Issues di GitHub untuk:

- Melaporkan bug/error
- Mengusulkan fitur baru

Sertakan:

- Penjelasan detail
- Langkah mereproduksi (jika bug)
- Screenshot (jika perlu)

Terima kasih telah membantu membuat sistem ini lebih baik 🙏