<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPerpanjangan extends Model
{
    protected $table = 'laporan_perpanjangan';

    protected $fillable = [
        'laporan_realisasi_id',
        'no_akad',
        'no_loan',
        'nama_debitur',
        'berat_ref',
        'tanggal_perpanjangan',
        'jumlah_bulan',
        'tanggal_jatuh_tempo_baru',
        'biaya_sewa_tambahan',
    ];

    protected $casts = [
        'berat_ref'               => 'decimal:2',
        'biaya_sewa_tambahan'     => 'decimal:2',
        'tanggal_perpanjangan'    => 'date',
        'tanggal_jatuh_tempo_baru'=> 'date',
    ];

    /* Relasi ke realisasi asal */
    public function laporanRealisasi()
    {
        return $this->belongsTo(LaporanRealisasi::class, 'laporan_realisasi_id');
    }
}