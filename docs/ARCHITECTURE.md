# Ringkasan Arsitektur ZYA CBT

ZYA CBT adalah aplikasi monolitik CodeIgniter 3 yang berjalan di Apache/PHP dan memakai MariaDB/MySQL. `index.php` menjadi front controller; routing, session, database, dan template dimuat melalui `application/config` serta `application/config/autoload.php`.

## Alur Domain

1. Operator mengelola modul, topik, soal, jawaban, peserta, token, dan konfigurasi melalui controller `application/controllers/manager`.
2. Peserta login melalui `Welcome`, masuk ke dashboard tes, lalu `Tes_kerjakan` memuat soal dan menyimpan jawaban ke tabel percobaan tes.
3. Token menghubungkan peserta dengan tes; `cbt_tes_user` dan `cbt_tes_soal` menyimpan status, waktu, jawaban, dan nilai.
4. Laporan membaca hasil percobaan untuk rekap, detail hasil, dan analisis butir soal.

## Batas Penyimpanan

- Database menyimpan data domain dan session.
- `uploads/` menyimpan media soal dan hasil import yang diproses internal.
- `public/` menyimpan aset statis dan `public/uploads/` untuk kompatibilitas instalasi lama.
- `Upload_service` membatasi path file ke root upload, memvalidasi isi file, dan mencegah traversal.

Perubahan baru sebaiknya ditempatkan pada service/library kecil sebelum menambah logic ke controller besar.
