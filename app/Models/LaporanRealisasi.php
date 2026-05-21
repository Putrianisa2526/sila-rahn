<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanRealisasi extends Model
{
    protected $table = 'laporan_realisasi';

    protected $fillable = [
        'no_akad',
        'nama_debitur',
        'no_loan',
        'berat',
        'kadar',
        'taksiran',
        'pembiayaan',
        'pendapatan_sewa',
        'tanggal_realisasi',
        'tanggal_jatuh_tempo',
    ];

    protected $casts = [
        'berat'              => 'decimal:2',
        'taksiran'           => 'decimal:2',
        'pembiayaan'         => 'decimal:2',
        'pendapatan_sewa'    => 'decimal:2',
        'tanggal_realisasi'  => 'date',
        'tanggal_jatuh_tempo'=> 'date',
    ];

    /* Relasi ke perpanjangan */
    public function perpanjangan()
    {
        return $this->hasMany(LaporanPerpanjangan::class, 'laporan_realisasi_id');
    }
}