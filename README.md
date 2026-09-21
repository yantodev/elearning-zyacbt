<div align="center">

# ZYA CBT

__Aplikasi Ujian Online ZYA CBT.__

<p>
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/CodeIgniter-3-EF4223?logo=codeigniter&logoColor=white" alt="CodeIgniter 3">
  <img src="https://img.shields.io/badge/MariaDB-10.4-003545?logo=mariadb&logoColor=white" alt="MariaDB 10.4">
  <img src="https://img.shields.io/badge/Docker-Containerized-2496ED?logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/Apache-2.4-D22128?logo=apache&logoColor=white" alt="Apache 2.4">
  <img src="https://img.shields.io/badge/Composer-2-885630?logo=composer&logoColor=white" alt="Composer 2">
  <img src="https://img.shields.io/badge/PHPWord-1.4.0-4F81BD" alt="PHPWord 1.4.0">
  <img src="https://img.shields.io/badge/PhpSpreadsheet-1.30.7-217346" alt="PhpSpreadsheet 1.30.7">
</p>

</div>

### Menjalankan dengan Docker

Pastikan Docker Engine dan Docker Compose v2 sudah terpasang, lalu jalankan dari root repository:

```bash
mkdir -p database
docker compose up --build -d
```

Aplikasi dapat diakses melalui [http://localhost:8080](http://localhost:8080). Service `db` menggunakan MariaDB 10.4 dan menyimpan data secara persistent di folder `./database`. Pada inisialisasi pertama, dump `zyacbt-public-2024-05-05-tanpa-database.sql` diimpor otomatis.

Entrypoint Docker otomatis menyiapkan permission untuk folder `uploads`, `public/uploads`, cache, dan log setiap container dimulai. Pengaturan ini hanya berlaku di Docker dan tidak mengubah cara aplikasi dijalankan menggunakan XAMPP.

Perintah operasional yang umum digunakan:

```bash
docker compose logs -f app  # melihat log aplikasi
docker compose ps           # melihat status service
docker compose down         # menghentikan service tanpa menghapus database
```

![](https://achmadlutfi.files.wordpress.com/2017/10/halaman-login-zya-cbt.png?w=620&h=508)

Alhamdulillah, setelah berkutat dengan kode kode yang suka mem php dalam waktu yang cukup lama. Akhirnya aplikasi ujian online atau biasa kita kenal dengan nama ujian berbasis komputer telah diluncurkan. Aplikasi ini diberi nama ZYA CBT.

![](https://achmadlutfi.files.wordpress.com/2017/10/halaman-mengerjakan-tes-zya-cbt.png?w=620&h=508)

Aplikasi Ujian Online ZYA CBT dikembangkan dengan framework CodeIgniter yang sudah terkenal tangguh dikalangan programmer web dan didukung oleh database MySQL. ZYA CBT saat ini mendukung tiga tipe pertanyaan, yaitu Soal pilihan ganda, Soal jawaban essay (esai), dan Soal Jawaban Singkat.

![](https://achmadlutfi.files.wordpress.com/2018/08/soal-jawaban-singkat.png?w=434&h=276)

Saat ini Aplikasi Ujian Online ZYA CBT sudah mendukung soal listening (audio) dengan baik. File Audio yang didukung Aplikasi Ujian Online ZYA CBT hanya file dengan format MP3.

![](https://achmadlutfi.files.wordpress.com/2018/08/soal-audio.png?w=470&h=344)

### Fasilitas
Fasilitas Export / Import Data Soal pada ZYA CBT digunakan untuk mendistribusikan Data Soal dengan mudah ke server lain yang menggunakan sesama ZYA CBT dan menggunakan soal yang sama. Seperti pelaksanaan Ujian yang dilakukan oleh Sub Rayon Sekolah dengan anggotanya atau lainnya.

![](https://achmadlutfi.files.wordpress.com/2019/02/aplikasiujianonline-exportimportsoal.png?w=620&h=295)

### Panduan
Untuk memudahkan dalam mengoperasikan Aplikasi Ujian Online ZYA CBT, silahkan download Manual nya dibawah ini.
- [Panduan Aplikasi](https://www.slideshare.net/achmadlutfi1987/manual-aplikasi-ujian-online-zya-cbt-administrator-rev20200919)
- [Panduan Peserta](https://www.slideshare.net/achmadlutfi1987/manual-aplikasi-ujian-online-zya-cbt-untuk-peserta-tes)

### Persetujuan Penggunaan Aplikasi
Dengan anda menggunakan Aplikasi Ujian Online ZYA CBT, maka anda telah setuju untuk :

- Tidak mengubah Nama Aplikasi Ujian Online ZYA CBT menjadi nama aplikasi lain.
- Tidak mengubah footer yang menunjukkan alamat website Aplikasi Ujian Online ZYA CBT
- Tidak diperkanankan menjual Aplikasi Ujian Online ZYA CBT. Tetapi anda diperbolehkan untuk mengambil keuntungan dari jasa proses instalasi, konsultasi dan lain sebagainya yang berkaitan dengan Aplikasi Ujian Online ZYA CBT.

### Donasi
Dari email yang masuk, ada beberapa dari agan-agan yang menanyakan tentang donasi untuk aplikasi ujian online ZYACBT. Berikut rekening yang bisa digunakan :

    Rekening BRI : 613001002590535 a/n MUHAMMAD LUTFIAL HAKIM

### Download
- Distribusi dalam bentuk Source Code dapat anda install manual ke server Linux ataupun Windows.
Silahkan download Source Code versi terbaru Aplikasi Ujian Online ZYA CBT [**di sini**](https://drive.google.com/file/d/1PODUyodz-WFRMPWbaX0ToLVvA4uytoTm/view?usp=sharing)

- XAMPP Portable dengan Aplikasi Ujian Online ZYA CBT
Distribusi dalam XAMPP Portable yang sudah terinstall ZYA CBT hanya dapat anda install pada Sistem Operasi Windows. Distribusi ini untuk memudahkan teman-teman yang belum familiar dengan Apache dan MySQL. Petunjuk penggunaan XAMPP Portable ada di dalam folder xampp setelah file di extract.
Silahkan download XAMPP Portable dengan ZYA CBT [**di sini**](https://drive.google.com/file/d/1XLQ-kpKWOYkajqlVDbRDPGCVubzHvgEh/view?usp=sharing)

### Sumber
Seluruh materi dalam repositori ini berasal dari [achmadlutfi.wordpress.com](https://achmadlutfi.wordpress.com/zya-cbt-aplikasi-ujian-online/)
