# Sistem Informasi Laporan Realisasi Rahn Berbasis Web

## Deskripsi Proyek
Aplikasi berbasis web yang dikembangkan untuk mendigitalisasi proses pelaporan transaksi pembiayaan Rahn iB. Sistem ini dirancang secara khusus untuk memenuhi kebutuhan operasional pengelolaan data di PT. Bank Riau Kepri Syariah. 

Tujuan utama dari sistem ini adalah untuk meminimalisir kesalahan input data manual (*human error*), mengelola data transaksi historis agar mudah dicari, dan mempercepat proses penyusunan laporan bulanan.

## Fitur Utama
* **Autentikasi Pengguna:** Sistem keamanan login khusus untuk aktor Admin menggunakan *username* dan *password*.
* **Dashboard Interaktif:** Menyajikan ringkasan statistik, grafik perkembangan data bulanan, dan opsi pembaruan tarif ujrah.
* **Input Laporan Realisasi:** Formulir digital untuk melakukan pendataan transaksi akad Rahn baru ke dalam basis data.
* **Input Laporan Perpanjangan:** Formulir untuk memproses perpanjangan masa akad yang merujuk pada data realisasi awal guna mencegah redundansi data.
* **Manajemen Data & Filter:** Fitur pengelolaan data yang memungkinkan Admin untuk melakukan Edit, Hapus, serta memfilter riwayat laporan berdasarkan jenis transaksi maupun rentang periode waktu.
* **Cetak Laporan Otomatis:** Fitur untuk mengekspor dan mencetak rekapitulasi laporan resmi ke dalam dokumen berformat PDF.

## Teknologi yang Digunakan (Tech Stack)
* **Backend:** Framework Laravel (PHP) dengan penerapan pola arsitektur *Model-View-Controller* (MVC).
* **Database:** MySQL.
* **Frontend:** HTML, CSS, JavaScript (untuk manipulasi antarmuka yang responsif dan *real-time*).

## Struktur Basis Data Utama
Sistem ini menggunakan basis data relasional yang saling terintegrasi:
1. **`LaporanRealisasi`**: Menyimpan detail data pinjaman awal nasabah (No Akad, Nama Debitur, Taksiran, Pembiayaan, dll).
2. **`LaporanPerpanjangan`**: Mencatat riwayat transaksi perpanjangan yang terhubung melalui relasi *one-to-many* (`laporan_realisasi_id`) ke tabel Laporan Realisasi.
3. **`Pengaturan`**: Berfungsi sebagai tabel referensi pusat untuk mengelola nominal tarif ujrah yang dapat diperbarui melalui Dashboard.

## Struktur Folder Pemrograman (MVC)
Secara bawaan Laravel, sumber daya pemrograman aplikasi ini diorganisasikan pada direktori utama berikut:
* `app/Http/Controllers/`: Memuat logika kontrol program (Fungsi baca, tulis, update, delete).
* `app/Models/`: Memuat representasi struktur tabel database dan relasi *one-to-many*.
* `resources/views/`: Memuat rancangan antarmuka pengguna (*User Interface*) berbasis HTML/Blade.
* `routes/web.php`: Memuat pengaturan rute URL dari akses komponen pengguna.

## Panduan Instalasi (Development)
Untuk mengeksekusi *source code* ini di lingkungan lokal (*localhost*), pastikan Anda telah menginstal **PHP**, **Composer**, dan **Laragon** (Apache & MySQL).

1. Clone repositori ini ke dalam direktori lokal Anda.
2. Buka terminal/CMD di dalam folder proyek, lalu jalankan perintah instalasi dependensi:
   ```bash
   composer install
3. Salin file konfigurasi .env:
   ```bash
   cp .env.example .env
4. Buka file .env dan sesuaikan konfigurasi database Anda (pastikan MySQL di XAMPP/Laragon sudah menyala):
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=
5. Generate app key Laravel:
   ```bash
   php artisan key:generate
6. Lakukan migrasi database untuk membuat struktur tabel secara otomatis:
   ```bash
   php artisan migrate
7. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
8. Buka browser dan akses aplikasi pada URL: 
   ```bash
   http://localhost:800
