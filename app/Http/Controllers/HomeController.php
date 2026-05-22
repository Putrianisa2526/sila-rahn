<?php

/**
 * File: HomeController.php
 * Deskripsi: Controller untuk mengelola halaman Dashboard (Home).
 * Berfungsi menampilkan statistik, aktivitas terkini, dan data grafik untuk 6 bulan terakhir.
 * Author: Putri Anisa
 */

namespace App\Http\Controllers;

use App\Models\LaporanPerpanjangan;
use App\Models\LaporanRealisasi;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Memastikan user terautentikasi sebelum masuk dashboard.
     */
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    /**
     * Menampilkan data Dashboard.
     * Mengelola statistik real-time, log aktivitas, dan data untuk visualisasi grafik.
     */
    public function index()
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $bulanIni = now()->month;
        $tahunIni = now()->year;

        /* 1. Statistik Bulan Ini (Data agregat) */
        $totalRealisasi    = LaporanRealisasi::whereMonth('tanggal_realisasi', $bulanIni)
                                        ->whereYear('tanggal_realisasi', $tahunIni)->count();
        $totalPerpanjangan = LaporanPerpanjangan::whereMonth('tanggal_perpanjangan', $bulanIni)
                                        ->whereYear('tanggal_perpanjangan', $tahunIni)->count();

        $labelBulan = Carbon::createFromDate($tahunIni, $bulanIni, 1)
                        ->locale('id')->isoFormat('MMMM YYYY');

        /* 2. Aktivitas Terakhir (Menggunakan teknik penggabungan koleksi) */
        $realisasiList    = LaporanRealisasi::latest()->take(10)->get()
            ->map(fn($r) => array_merge($r->toArray(), ['type' => 'realisasi', 'tanggal' => $r->created_at]));
        $perpanjanganList = LaporanPerpanjangan::latest()->take(10)->get()
            ->map(fn($p) => array_merge($p->toArray(), ['type' => 'perpanjangan', 'tanggal' => $p->created_at]));

        // Menggabungkan dua koleksi array dan mengurutkannya berdasarkan tanggal (Descending)
        $activities = $realisasiList->merge($perpanjanganList)
            ->sortByDesc('tanggal')->take(10)->values();

        /* 3. Pengolahan Data untuk Chart (6 Bulan Terakhir) */
        $chartLabels      = [];
        $chartRealisasi   = [];
        $chartPerpanjangan= [];
        $chartPendapatan  = [];

        $namaBulanId = ['', 'Januari','Februari','Maret','April','Mei','Juni',
                        'Juli','Agustus','September','Oktober','November','Desember'];

        // Perulangan untuk mengambil data historis per bulan
        for ($i = 5; $i >= 0; $i--) {
            $dt  = now()->subMonths($i);
            $bln = (int) $dt->format('m');
            $thn = (int) $dt->format('Y');

            $chartLabels[]       = $namaBulanId[$bln] . ' ' . $thn;
            $chartRealisasi[]    = LaporanRealisasi::whereMonth('tanggal_realisasi', $bln)
                                            ->whereYear('tanggal_realisasi', $thn)->count();
            $chartPerpanjangan[] = LaporanPerpanjangan::whereMonth('tanggal_perpanjangan', $bln)
                                            ->whereYear('tanggal_perpanjangan', $thn)->count();
            $chartPendapatan[]   = (float) LaporanRealisasi::whereMonth('tanggal_realisasi', $bln)
                                            ->whereYear('tanggal_realisasi', $thn)->sum('pendapatan_sewa');
        }

        $tarifUjrah = (float) Pengaturan::getValue('tarif_ujrah', 16000);

        return view('home.index', compact(
            'totalRealisasi', 'totalPerpanjangan', 'labelBulan',
            'activities', 'bulanIni', 'tahunIni',
            'chartLabels', 'chartRealisasi', 'chartPerpanjangan', 'chartPendapatan',
            'tarifUjrah'
        ));
    }
}