<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
    ];

    /**
     * Ambil nilai berdasarkan kunci.
     */
    public static function getValue(string $kunci, mixed $default = null): mixed
    {
        $row = static::where('kunci', $kunci)->first();
        return $row ? $row->nilai : $default;
    }

    /**
     * Set/update nilai berdasarkan kunci (upsert).
     */
    public static function setValue(string $kunci, mixed $nilai): void
    {
        static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai]
        );
    }
}