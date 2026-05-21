<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DataLaporanController;
use App\Http\Controllers\LaporanRealisasiController;
use App\Http\Controllers\LaporanPerpanjanganController;
use App\Http\Controllers\PengaturanController;
use Illuminate\Support\Facades\Route;

/* Auth */
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

/* Home */
Route::get('/', [HomeController::class, 'index'])->name('home');

/* Data Laporan */
Route::get('/data-laporan', [DataLaporanController::class, 'index'])->name('data-laporan.index');

/* Laporan Realisasi */
Route::resource('laporan-realisasi', LaporanRealisasiController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

/* Laporan Perpanjangan */
Route::resource('laporan-perpanjangan', LaporanPerpanjanganController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

/* Pengaturan */
Route::post('/pengaturan/update', [PengaturanController::class, 'update'])->name('pengaturan.update');