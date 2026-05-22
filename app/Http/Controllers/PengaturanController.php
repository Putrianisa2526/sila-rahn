<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    /**
     * Fungsi pengecekan status login pengguna.
     * Initial state: Akses ke route pengaturan.
     * Final state: Mengembalikan redirect ke halaman login jika sesi tidak aktif.
     */
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    /**
     * Memperbarui tarif ujrah ke dalam database.
     * Initial state: Request data tarif baru dari input pengguna.
     * Final state: Nilai di database terupdate dan sistem kembali ke halaman sebelumnya.
     */
    public function update(Request $request)
    {
        // Pastikan pengguna sudah terautentikasi
        if ($redirect = $this->checkLogin()) return $redirect;

        // Validasi bahwa tarif ujrah harus berupa angka dan minimal 0
        $request->validate([
            'tarif_ujrah' => 'required|numeric|min:0',
        ]);

        // Menyimpan nilai tarif baru menggunakan model Pengaturan
        Pengaturan::setValue('tarif_ujrah', $request->tarif_ujrah);

        // Memberikan notifikasi sukses kepada pengguna
        return back()->with('success_tarif', 'Tarif ujrah berhasil diperbarui menjadi Rp '
            . number_format($request->tarif_ujrah, 0, ',', '.') . ' / gram / bulan.');
    }
}