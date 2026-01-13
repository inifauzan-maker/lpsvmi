<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetMarket extends Model
{
    use HasFactory;

    protected $fillable = [
        'wilayah',
        'program',
        'bulan',
        'tahun',
        'target_siswa',
        'target_omset',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'target_siswa' => 'integer',
        'target_omset' => 'integer',
    ];
}
