<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    private function checkLogin()
    {
        if (!session('login')) {
            return redirect()->route('login')->withErrors(['Silakan login terlebih dahulu']);
        }
        return null;
    }

    public function update(Request $request)
    {
        if ($redirect = $this->checkLogin()) return $redirect;

        $request->validate([
            'tarif_ujrah' => 'required|numeric|min:0',
        ]);

        Pengaturan::setValue('tarif_ujrah', $request->tarif_ujrah);

        return back()->with('success_tarif', 'Tarif ujrah berhasil diperbarui menjadi Rp '
            . number_format($request->tarif_ujrah, 0, ',', '.') . ' / gram / bulan.');
    }
}