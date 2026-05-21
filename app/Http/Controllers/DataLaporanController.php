<?php

namespace App\Http\Controllers;

use App\Models\LaporanPerpanjangan;
use App\Models\LaporanRealisasi;
use Illuminate\Http\Request;

class DataLaporanController extends Controller
{
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->year);
        $jenis = $request->input('jenis', 'semua');

        $namaBulan = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        $daftarTahun = range(now()->year, 2024);

        /* Query realisasi */
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
                    '_sort'              => $r->tanggal_realisasi,
                ]))->toArray();
        }

        /* Query perpanjangan */
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
                    '_sort'             => $p->tanggal_perpanjangan,
                ]))->toArray();
        }

        /* Gabung & urutkan */
        $laporanGabungan = collect(array_merge($realisasiData, $perpanjanganData))
            ->sortBy('_sort')
            ->values();

        return view('data_laporan.index', compact(
            'laporanGabungan', 'bulan', 'tahun', 'jenis', 'namaBulan', 'daftarTahun'
        ));
    }
}