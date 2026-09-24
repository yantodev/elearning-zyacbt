# Rencana Upgrade Aplikasi ZYA CBT — Final

Dokumen ini mencatat hasil upgrade yang sudah diterapkan pada branch `develop`.

## Keamanan dan Stabilitas

- [x] Konfigurasi sensitif memakai environment variable.
- [x] CSRF aktif pada form dan AJAX; request tanpa token ditolak `403`.
- [x] Folder upload dilindungi dari eksekusi script dan memakai `Upload_service` untuk MIME, ukuran, ekstensi, path traversal, penghapusan file, serta import ZIP aman.
- [x] Error detail hanya aktif pada environment development.
- [x] Login, upload, import, dan penyimpanan jawaban tercatat melalui `log_message` serta event monitoring.

## Versi, Dependency, dan Compatibility

- [x] `VERSION` menjadi sumber versi footer, Docker image, dan release tag.
- [x] PHP 8.4 dan MariaDB 10.11 diuji melalui regression check dan script upgrade terisolasi.
- [x] Dependency Composer dikunci; PHPWord 1.4.0 dan PhpSpreadsheet 1.30.7 dipertahankan karena masih dipakai fitur import/export.
- [x] PHPUnit 9.6.37 ditambahkan sebagai dependency development dan `composer audit --locked` dijalankan di CI.

## Testing dan CI

- [x] PHPUnit mencakup autentikasi, authorization, token ujian, submit jawaban, penilaian, upload, serta kontrak import/export.
- [x] Lint seluruh PHP, regression PHP 8.4, security check upload, smoke test HTTP, logout schema, dan smoke test Docker berjalan pada GitHub Actions.
- [x] Workflow validation, build image, dan release dipisahkan.

## Struktur Aplikasi

- [x] Aturan token, akses, submit, dan penilaian dipindahkan ke `Exam_policy` serta `Exam_answer_service` agar dapat diuji tanpa controller besar.
- [x] Response JSON AJAX utama menggunakan `Api_response` dengan format status dan pesan yang konsisten.
- [x] Service upload dipakai bersama oleh file manager dan import/export.
- [x] Alur peserta, tes, token, soal, jawaban, dan hasil didokumentasikan.

## Docker dan Operasional

- [x] Compose development memakai bind mount; production memakai image immutable dan volume upload/database.
- [x] Health check aplikasi memeriksa endpoint `/health` dan koneksi database.
- [x] Backup, restore, upgrade schema, rollback image, dan prosedur validasi tersedia di `scripts/` serta README.
- [x] Collector eksternal dapat menerima event melalui `MONITORING_WEBHOOK_URL` dengan timeout pendek dan token opsional.

## Kriteria Final

Semua item upgrade dalam dokumen ini selesai dan telah disiapkan untuk divalidasi pada branch `develop`. Sebelum deployment production, jalankan backup database, `composer audit --locked`, smoke test Docker, lalu gunakan image bertag spesifik agar rollback dapat dilakukan ke versi sebelumnya.
