<?php

namespace App\Http\Controllers;

use App\Models\LaporanPerpanjangan;
use App\Models\LaporanRealisasi;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanPerpanjanganController extends Controller
{
    /**
     * Memastikan akses pengguna terautentikasi.
     */
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    /**
     * Validasi logika bisnis: Tanggal perpanjangan tidak boleh mendahului tanggal jatuh tempo awal.
     */
    private function validateTanggalPerpanjangan(int $realisasiId, string $tanggalPerpanjangan): ?string
    {
        $realisasi = LaporanRealisasi::find($realisasiId);

        if (!$realisasi) return 'Data realisasi tidak ditemukan.';

        if (empty($realisasi->tanggal_jatuh_tempo)) {
            return 'Realisasi yang dipilih tidak memiliki tanggal jatuh tempo.';
        }

        $jatuhTempo     = Carbon::parse($realisasi->tanggal_jatuh_tempo)->startOfDay();
        $tglPerpanjangan = Carbon::parse($tanggalPerpanjangan)->startOfDay();

        // Pengecekan apakah perpanjangan valid dilakukan setelah jatuh tempo
        if ($tglPerpanjangan->lt($jatuhTempo)) {
            return 'Tanggal perpanjangan tidak boleh sebelum tanggal jatuh tempo realisasi ('
                . $jatuhTempo->format('d/m/Y') . ').';
        }

        return null;
    }

    public function index()
    {
        return redirect()->route('data-laporan.index');
    }

    /**
     * Menampilkan form input perpanjangan.
     */
    public function create()
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $daftarRealisasi = LaporanRealisasi::orderBy('tanggal_realisasi', 'desc')->get();
        $tarifUjrah       = (float) Pengaturan::getValue('tarif_ujrah', 16000);

        return view('laporan_perpanjangan.create', compact('daftarRealisasi', 'tarifUjrah'));
    }

    /**
     * Menyimpan data perpanjangan dan menghitung biaya sewa tambahan.
     */
    public function store(Request $request)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $validated = $request->validate([
            'laporan_realisasi_id'     => 'required|exists:laporan_realisasi,id',
            'nama_debitur'             => 'required|string|max:255',
            'berat_ref'                => 'required|numeric|min:0',
            'tanggal_perpanjangan'     => 'required|date',
            'jumlah_bulan'             => 'required|integer|min:1',
            'tanggal_jatuh_tempo_baru' => 'required|date|after:tanggal_perpanjangan',
            'biaya_sewa_tambahan'      => 'required|numeric|min:0',
        ]);

        // Validasi logika bisnis tanggal
        $errorTanggal = $this->validateTanggalPerpanjangan(
            (int) $validated['laporan_realisasi_id'],
            $validated['tanggal_perpanjangan']
        );

        if ($errorTanggal) {
            return back()->withInput()->withErrors(['tanggal_perpanjangan' => $errorTanggal]);
        }

        // Mengambil nomor akad dari data realisasi referensi
        $realisasiRef = LaporanRealisasi::find($validated['laporan_realisasi_id']);
        if ($realisasiRef) {
            $validated['no_akad'] = $realisasiRef->no_akad;
            $validated['no_loan'] = $realisasiRef->no_loan;
        }

        // Perhitungan otomatis biaya sewa tambahan
        $tarifUjrah = (float) Pengaturan::getValue('tarif_ujrah', 16000);
        $validated['biaya_sewa_tambahan'] = $validated['berat_ref'] * $tarifUjrah * $validated['jumlah_bulan'];

        LaporanPerpanjangan::create($validated);

        return redirect()
            ->route('data-laporan.index')
            ->with('success', 'Data perpanjangan berhasil disimpan! No. Akad: ' . $validated['no_akad']);
    }

    // Metode edit, update, dan destroy (operasi CRUD standar)
    public function edit($id)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $perpanjangan = LaporanPerpanjangan::with('laporanRealisasi')->findOrFail($id);
        $tarifUjrah   = (float) Pengaturan::getValue('tarif_ujrah', 16000);

        return view('laporan_perpanjangan.edit', compact('perpanjangan', 'tarifUjrah'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $perpanjangan = LaporanPerpanjangan::findOrFail($id);

        $validated = $request->validate([
            'nama_debitur'             => 'required|string|max:255',
            'tanggal_perpanjangan'     => 'required|date',
            'jumlah_bulan'             => 'required|integer|min:1',
            'tanggal_jatuh_tempo_baru' => 'required|date',
            'biaya_sewa_tambahan'      => 'required|numeric|min:0',
        ]);

        $tarifUjrah = (float) Pengaturan::getValue('tarif_ujrah', 16000);
        $validated['biaya_sewa_tambahan'] = $perpanjangan->berat_ref * $tarifUjrah * $validated['jumlah_bulan'];

        $perpanjangan->update($validated);

        return redirect()
            ->route('data-laporan.index')
            ->with('success', 'Data perpanjangan ' . $perpanjangan->no_akad . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $perpanjangan = LaporanPerpanjangan::findOrFail($id);
        $noAkad       = $perpanjangan->no_akad;
        $perpanjangan->delete();

        return redirect()
            ->route('data-laporan.index')
            ->with('success', 'Perpanjangan ' . $noAkad . ' berhasil dihapus.');
    }
}