<?php

namespace App\Http\Controllers;

use App\Models\LaporanPerpanjangan;
use App\Models\LaporanRealisasi;
use Illuminate\Http\Request;

class DataLaporanController extends Controller
{
    /**
     * Fungsi pengecekan autentikasi pengguna.
     * Initial state: Akses ke route tertentu.
     * Final state: Mengembalikan redirect ke login jika sesi tidak aktif.
     */
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    /**
     * Menampilkan daftar laporan berdasarkan filter bulan, tahun, dan jenis.
     * Logika utama: Mengambil data dari dua model berbeda, menggabungkannya
     * menjadi satu Array (koleksi), lalu melakukan pengurutan (*sorting*).
     */
    public function index(Request $request)
    {
        // Pengecekan autentikasi
        if ($redirect = $this->checkLogin()) return $redirect;

        // Mengambil input filter dari request
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->year);
        $jenis = $request->input('jenis', 'semua');

        // Mapping nama bulan untuk keperluan tampilan
        $namaBulan = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',      '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',  '09' => 'September',
            '10' => 'Oktober',  '11' => 'November', '12' => 'Desember',
        ];

        // Daftar tahun untuk opsi filter (Array sederhana)
        $daftarTahun = range(now()->year, 2024);

        /* Query Data Realisasi */
        $realisasiData = [];
        if (in_array($jenis, ['semua', 'realisasi'])) {
            $realisasiData = LaporanRealisasi::whereMonth('tanggal_realisasi', (int) $bulan)
                ->whereYear('tanggal_realisasi', $tahun)
                ->orderBy('tanggal_realisasi')
                ->get()
                ->map(fn($r) => array_merge($r->toArray(), [
                    'jenis'              => 'realisasi',
                    'tanggal_realisasi'  => $r->tanggal_realisasi,
                    'jatuh_tempo'        => $r->tanggal_jatuh_tempo,
                    '_sort'              => $r->tanggal_realisasi, // Penanda untuk sorting
                ]))->toArray();
        }

        /* Query Data Perpanjangan */
        $perpanjanganData = [];
        if (in_array($jenis, ['semua', 'perpanjangan'])) {
            $perpanjanganData = LaporanPerpanjangan::whereMonth('tanggal_perpanjangan', (int) $bulan)
                ->whereYear('tanggal_perpanjangan', $tahun)
                ->orderBy('tanggal_perpanjangan')
                ->get()
                ->map(fn($p) => array_merge($p->toArray(), [
                    'jenis'             => 'perpanjangan',
                    'tanggal_realisasi' => $p->tanggal_perpanjangan,
                    'jatuh_tempo'       => $p->tanggal_jatuh_tempo_baru,
                    '_sort'             => $p->tanggal_perpanjangan, // Penanda untuk sorting
                ]))->toArray();
        }

        /* * Logika Penggabungan & Pengurutan Array:
         * Menggabungkan dua dataset dari tabel berbeda ke dalam satu koleksi (Array),
         * lalu diurutkan berdasarkan tanggal menggunakan method sortBy.
         */
        $laporanGabungan = collect(array_merge($realisasiData, $perpanjanganData))
            ->sortBy('_sort')
            ->values();

        // Mengirim data ke view
        return view('data_laporan.index', compact(
            'laporanGabungan', 'bulan', 'tahun', 'jenis', 'namaBulan', 'daftarTahun'
        ));
    }
}