<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSiswa extends Model
{
    protected $table = 'data_siswa';

    protected $fillable = [
        'location',
        'nama',
        'ttl',
        'no_hp_siswa',
        'asal_sekolah',
        'kelas',
        'asal_kota',
        'program',
        'nama_medsos',
        'platform_medsos',
        'info_villa_merah_dari',
        'alamat_siswa',
        'nama_ortu',
        'no_tlp_ortu',
        'tanggal_daftar',
        'biaya_pendidikan',
        'sisa_angsuran',
    ];

    protected $casts = [
        'platform_medsos' => 'array',
        'info_villa_merah_dari' => 'array',
        'tanggal_daftar' => 'date',
        'biaya_pendidikan' => 'decimal:2',
        'sisa_angsuran' => 'decimal:2',
    ];
}