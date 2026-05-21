<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     * Initial state: Pengguna belum login.
     * Final state: Menampilkan view login atau redirect ke home jika sudah login.
     */
    public function showLogin()
    {
        if (session('login')) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Memproses data login dari form.
     * Initial state: Input username dan password dari request.
     * Final state: Session login aktif dan redirect ke home, atau kembali ke login jika gagal.
     */
    public function login(Request $request)
    {
        // Validasi input form
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Mencari pengguna berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Pengecekan kredensial (username valid & password benar)
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput(['username' => $request->username])
                ->withErrors(['Username atau password salah.']);
        }

        // Menyimpan data sesi jika login berhasil
        session([
            'login'      => true,
            'user_id'    => $user->id,
            'admin_name' => $user->name,
            'admin_role' => $user->role,
        ]);

        return redirect()->route('home');
    }

    /**
     * Menghapus sesi login (Logout).
     * Initial state: Sesi login masih aktif.
     * Final state: Seluruh sesi dihapus dan redirect ke halaman login.
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}