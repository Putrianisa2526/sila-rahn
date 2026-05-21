# Sistem Informasi Laporan Realisasi Rahn Berbasis Web

## Deskripsi Proyek
[cite_start]Aplikasi berbasis web yang dikembangkan untuk mendigitalisasi proses pelaporan transaksi pembiayaan Rahn iB[cite: 574, 577]. Sistem ini dirancang secara khusus untuk memenuhi kebutuhan operasional pengelolaan data di PT. [cite_start]Bank Riau Kepri Syariah Kantor Cabang Bengkalis[cite: 575, 586]. 

[cite_start]Tujuan utama dari sistem ini adalah untuk meminimalisir kesalahan input data manual (*human error*), mengelola data transaksi historis agar mudah dicari, dan mempercepat proses penyusunan laporan bulanan[cite: 576, 580, 589].

## Fitur Utama
* [cite_start]**Autentikasi Pengguna:** Sistem keamanan login khusus untuk aktor Admin menggunakan *username* dan *password*[cite: 664, 665].
* [cite_start]**Dashboard Interaktif:** Menyajikan ringkasan statistik, grafik perkembangan data bulanan, dan opsi pembaruan tarif ujrah[cite: 667, 668].
* [cite_start]**Input Laporan Realisasi:** Formulir digital untuk melakukan pendataan transaksi akad Rahn baru ke dalam basis data[cite: 669, 670].
* [cite_start]**Input Laporan Perpanjangan:** Formulir untuk memproses perpanjangan masa akad yang merujuk pada data realisasi awal guna mencegah redundansi data[cite: 672, 673].
* [cite_start]**Manajemen Data & Filter:** Fitur pengelolaan data yang memungkinkan Admin untuk melakukan Edit, Hapus, serta memfilter riwayat laporan berdasarkan jenis transaksi maupun rentang periode waktu[cite: 675, 677, 681].
* [cite_start]**Cetak Laporan Otomatis:** Fitur untuk mengekspor dan mencetak rekapitulasi laporan resmi ke dalam dokumen berformat PDF[cite: 580, 678].

## Teknologi yang Digunakan (Tech Stack)
* [cite_start]**Backend:** Framework Laravel (PHP) dengan penerapan pola arsitektur *Model-View-Controller* (MVC)[cite: 578, 639].
* [cite_start]**Database:** MySQL[cite: 578].
* [cite_start]**Frontend:** HTML, CSS, JavaScript (untuk manipulasi antarmuka yang responsif dan *real-time*)[cite: 642].

## Struktur Basis Data Utama
Sistem ini menggunakan basis data relasional yang saling terintegrasi:
1.  [cite_start]**`LaporanRealisasi`**: Menyimpan detail data pinjaman awal nasabah (No Akad, Nama Debitur, Taksiran, Pembiayaan, dll)[cite: 631, 633].
2.  [cite_start]**`LaporanPerpanjangan`**: Mencatat riwayat transaksi perpanjangan yang terhubung melalui relasi *one-to-many* (`laporan_realisasi_id`) ke tabel Laporan Realisasi[cite: 624, 631, 634].
3.  [cite_start]**`Pengaturan`**: Berfungsi sebagai tabel referensi pusat untuk mengelola nominal tarif ujrah yang dapat diperbarui melalui Dashboard[cite: 625, 632].

## Struktur Folder Pemrograman (MVC)
Secara bawaan Laravel, sumber daya pemrograman aplikasi ini diorganisasikan pada direktori utama berikut:
* `app/Http/Controllers/`: Memuat logika kontrol program (Fungsi baca, tulis, update, delete).
* `app/Models/`: Memuat representasi struktur tabel database dan relasi *one-to-many*.
* `resources/views/`: Memuat rancangan antarmuka pengguna (*User Interface*) berbasis HTML/Blade.
* `routes/web.php`: Memuat pengaturan rute URL dari akses komponen pengguna.

## Panduan Instalasi (Development)
Untuk mengeksekusi *source code* ini di lingkungan lokal (*localhost*), pastikan Anda telah menginstal **PHP**, **Composer**, dan **XAMPP** (Apache & MySQL).

1. Clone repositori ini ke dalam direktori lokal Anda.
2. Buka terminal/CMD di dalam folder proyek, lalu jalankan perintah instalasi dependensi:
   ```bash
   composer install