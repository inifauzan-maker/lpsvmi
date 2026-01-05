<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetMarket extends Model
{
    protected $table = 'target_market';

    protected $fillable = [
        'wilayah',
        'program',
        'bulan',
        'tahun',
        'target_siswa',
        'target_omset',
    ];

    protected $casts = [
        'target_omset' => 'decimal:2',
    ];
}