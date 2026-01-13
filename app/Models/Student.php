<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'biaya_pendidikan',
        'sisa_angsuran',
        'location',
        'from_leads_id',
        'tanggal_daftar',
    ];

    protected $casts = [
        'platform_medsos' => 'array',
        'info_villa_merah_dari' => 'array',
        'biaya_pendidikan' => 'integer',
        'sisa_angsuran' => 'integer',
        'tanggal_daftar' => 'date',
    ];
}
