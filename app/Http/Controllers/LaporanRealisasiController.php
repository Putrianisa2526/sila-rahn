<?php

namespace App\Http\Controllers;

use App\Models\LaporanRealisasi;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanRealisasiController extends Controller
{
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    private function generateNoAkad(): string
    {
        $now    = now();
        $urutan = LaporanRealisasi::whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)->count() + 1;

        return str_pad($urutan, 4, '0', STR_PAD_LEFT)
            . '.3.22.'
            . $now->year . '.'
            . str_pad($now->month, 3, '0', STR_PAD_LEFT)
            . ' RAHN';
    }

    private function generateNoLoan(): string
    {
        $now    = now();
        $urutan = LaporanRealisasi::whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)->count() + 1;

        return '108-85-' . str_pad($urutan, 5, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        return redirect()->route('home');
    }

    public function create()
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $previewNoAkad    = $this->generateNoAkad();
        $previewNoLoan    = $this->generateNoLoan();
        $laporanRealisasi = null;
        $tarifUjrah       = (float) Pengaturan::getValue('tarif_ujrah', 16000);

        return view('laporan_realisasi.create', compact(
            'previewNoAkad', 'previewNoLoan', 'laporanRealisasi', 'tarifUjrah'
        ));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $validated = $request->validate([
            'nama_debitur'       => 'required|string|max:255',
            'berat'              => 'required|numeric|min:0',
            'kadar'              => 'required|numeric|min:0',
            'taksiran'           => 'required|numeric|min:0',
            'pembiayaan'         => 'required|numeric|min:0',
            'tanggal_realisasi'  => 'required|date',
            'tanggal_jatuh_tempo'=> 'required|date|after:tanggal_realisasi',
        ]);

        $validated['no_akad'] = $this->generateNoAkad();
        $validated['no_loan'] = $this->generateNoLoan();

        /* Hitung pendapatan sewa otomatis */
        $tarifUjrah  = (float) Pengaturan::getValue('tarif_ujrah', 16000);
        $tglRealisasi = Carbon::parse($validated['tanggal_realisasi']);
        $tglJT        = Carbon::parse($validated['tanggal_jatuh_tempo']);
        $bulan        = max(1, (int) $tglRealisasi->diffInMonths($tglJT));

        $validated['pendapatan_sewa'] = $validated['berat'] * $tarifUjrah * $bulan;

        LaporanRealisasi::create($validated);

        return back()->with('success', 'Laporan realisasi berhasil disimpan! No. Akad: ' . $validated['no_akad']);
    }

    public function edit($id)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $laporanRealisasi = LaporanRealisasi::findOrFail($id);
        $previewNoAkad    = $laporanRealisasi->no_akad;
        $previewNoLoan    = $laporanRealisasi->no_loan;
        $tarifUjrah       = (float) Pengaturan::getValue('tarif_ujrah', 16000);

        return view('laporan_realisasi.create', compact(
            'laporanRealisasi', 'previewNoAkad', 'previewNoLoan', 'tarifUjrah'
        ));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $realisasi = LaporanRealisasi::findOrFail($id);

        $validated = $request->validate([
            'no_akad'            => 'required|string|max:255',
            'no_loan'            => 'nullable|string|max:255',
            'nama_debitur'       => 'required|string|max:255',
            'berat'              => 'nullable|numeric|min:0',
            'kadar'              => 'nullable|numeric|min:0',
            'taksiran'           => 'nullable|numeric|min:0',
            'pembiayaan'         => 'nullable|numeric|min:0',
            'pendapatan_sewa'    => 'nullable|numeric|min:0',
            'tanggal_realisasi'  => 'required|date',
            'tanggal_jatuh_tempo'=> 'nullable|date',
        ]);

        /* Hitung ulang pendapatan sewa jika berat/tanggal berubah */
        if (!empty($validated['berat']) && !empty($validated['tanggal_jatuh_tempo'])) {
            $tarifUjrah  = (float) Pengaturan::getValue('tarif_ujrah', 16000);
            $tglRealisasi = Carbon::parse($validated['tanggal_realisasi']);
            $tglJT        = Carbon::parse($validated['tanggal_jatuh_tempo']);
            $bulan        = max(1, (int) $tglRealisasi->diffInMonths($tglJT));
            $validated['pendapatan_sewa'] = $validated['berat'] * $tarifUjrah * $bulan;
        }

        $realisasi->update($validated);

        return redirect()
            ->route('data-laporan.index')
            ->with('success', 'Laporan realisasi ' . $realisasi->no_akad . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $realisasi = LaporanRealisasi::findOrFail($id);
        $noAkad    = $realisasi->no_akad;
        $realisasi->delete();

        return redirect()
            ->route('data-laporan.index')
            ->with('success', 'Laporan realisasi ' . $noAkad . ' berhasil dihapus.');
    }
}