<?php

namespace App\Http\Controllers;

use App\Models\LaporanPerpanjangan;
use App\Models\LaporanRealisasi;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $bulanIni = now()->month;
        $tahunIni = now()->year;

        /* Statistik bulan ini */
        $totalRealisasi    = LaporanRealisasi::whereMonth('tanggal_realisasi', $bulanIni)
                                ->whereYear('tanggal_realisasi', $tahunIni)->count();
        $totalPerpanjangan = LaporanPerpanjangan::whereMonth('tanggal_perpanjangan', $bulanIni)
                                ->whereYear('tanggal_perpanjangan', $tahunIni)->count();

        $labelBulan = Carbon::createFromDate($tahunIni, $bulanIni, 1)
                        ->locale('id')->isoFormat('MMMM YYYY');

        /* Aktivitas terakhir (10 data) */
        $realisasiList    = LaporanRealisasi::latest()->take(10)->get()
            ->map(fn($r) => array_merge($r->toArray(), ['type' => 'realisasi', 'tanggal' => $r->created_at]));
        $perpanjanganList = LaporanPerpanjangan::latest()->take(10)->get()
            ->map(fn($p) => array_merge($p->toArray(), ['type' => 'perpanjangan', 'tanggal' => $p->created_at]));

        $activities = $realisasiList->merge($perpanjanganList)
            ->sortByDesc('tanggal')->take(10)->values();

        /* Chart — 6 bulan terakhir */
        $chartLabels      = [];
        $chartRealisasi   = [];
        $chartPerpanjangan= [];
        $chartPendapatan  = [];

        $namaBulanId = ['', 'Januari','Februari','Maret','April','Mei','Juni',
                        'Juli','Agustus','September','Oktober','November','Desember'];

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