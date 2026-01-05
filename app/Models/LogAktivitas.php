<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'tanggal',
        'hari',
        'waktu',
        'timestamp_ms',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}