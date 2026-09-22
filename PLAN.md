# Rencana Upgrade Aplikasi ZYA CBT

Dokumen ini menjadi acuan persiapan upgrade. Setiap tahap harus diuji di branch `develop` sebelum digabungkan ke `main`.

## Prioritas 0 — Keamanan dan Stabilitas

- [x] Pindahkan `encryption_key`, kredensial database, dan konfigurasi sensitif Docker ke environment variable melalui `.env`.
- [x] Ganti password database default pada deployment production dan dokumentasikan konfigurasi production.
- [x] Aktifkan CSRF protection, tambahkan token pada template, form POST biasa, dan AJAX, lalu verifikasi request tanpa token menghasilkan `403`.
- [x] Lindungi folder `uploads` dan `public/uploads` agar file PHP tidak dapat dieksekusi.
- [x] Tambahkan `Upload_service` untuk validasi MIME, ukuran, ekstensi, nama/path, penghapusan file, dan ekstraksi ZIP tanpa Zip Slip.
- [x] Pastikan error detail hanya tampil di environment development melalui `CI_ENV=production` pada compose production.

## Prioritas 1 — Versi dan Dependency

- [x] Buat satu sumber versi pada file `VERSION` dengan format `MAJOR.MINOR.PATCH`.
- [x] Gunakan sumber versi tersebut untuk footer dan Docker tag; GitHub Release production dipicu tag `vMAJOR.MINOR.PATCH` yang harus sama dengan `VERSION`.
- [x] Audit dasar patch kompatibilitas CodeIgniter 3 terhadap PHP 8.4 melalui regression check dan lint seluruh PHP.
- [x] Uji upgrade MariaDB 10.4 ke MariaDB 10.11 menggunakan dump dan direktori sementara di `/tmp`; `./database` tidak disentuh.
- [ ] Periksa dependency Composer dan hilangkan library deprecated secara bertahap.

## Prioritas 2 — Testing dan CI

- [ ] Tambahkan PHPUnit untuk authentication, authorization, token ujian, submit jawaban, dan import/export; saat ini tersedia security check mandiri tanpa dependency baru.
- [x] Jalankan `php -l` untuk file PHP pada setiap pull request melalui workflow validation.
- [x] Tambahkan integration smoke test HTTP dengan MariaDB sementara melalui `scripts/smoke-docker.sh` dan workflow CI.
- [x] Tambahkan regression test login/logout peserta terhadap schema `cbt_user`.
- [x] Pisahkan workflow validation, build Docker, dan release.
- [x] Tambahkan smoke test HTTP untuk endpoint utama dan verifikasi permission file manager pada environment Docker.

## Prioritas 3 — Struktur Aplikasi

- [x] Buat service terpusat untuk upload dan operasi file; service import/export memakai validasi yang sama.
- [ ] Kurangi logic bisnis dari controller besar dan pindahkan ke module yang dapat diuji.
- [ ] Standarkan response error dan validasi input lintas controller.
- [x] Dokumentasikan alur domain: peserta, tes, token, soal, jawaban, dan hasil.

## Prioritas 4 — Docker dan Operasional

- [x] Buat konfigurasi image production immutable tanpa bind mount seluruh source code; `APP_IMAGE` wajib memakai tag spesifik.
- [x] Pertahankan volume khusus untuk database dan upload pada compose production.
- [x] Tambahkan health check aplikasi selain health check database.
- [x] Sediakan command backup dan restore database yang terdokumentasi.
- [x] Tambahkan `log_message` untuk login berhasil/gagal, upload, import, dan penyimpanan jawaban.

## Kriteria Selesai

- Semua perubahan diuji di branch `develop`.
- Regression check PHP 8.4, security check upload, lint, smoke test Docker, dan uji upgrade database lulus.
- Backup tersedia sebelum upgrade database produksi dan rollback image/compose terdokumentasi.

## Status Finalisasi

Baseline keamanan Docker, versioning, CI validation, backup/restore, upload hardening, CSRF, smoke test, logging, dan uji upgrade MariaDB sudah diterapkan serta divalidasi. Item berikut masih tersisa:

- PHPUnit untuk domain utama dan integration test database yang lebih mendalam.
- Refactor controller besar serta standardisasi response error.
- Audit dependency Composer dan penggantian library deprecated.
- Monitoring/alert production berbasis collector eksternal.

Upgrade keamanan dan operasional dianggap siap untuk review. Upgrade produk penuh baru dianggap final setelah item tersisa selesai, backup/restore teruji di production-like environment, dan rollback ke versi sebelumnya didokumentasikan.
