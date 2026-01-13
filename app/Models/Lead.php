<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_siswa',
        'ttl',
        'program',
        'asal_sekolah',
        'kelas',
        'asal_kota',
        'no_hp',
        'nama_medsos',
        'nama_ortu',
        'no_tlp_ortu',
        'tanggal_input',
        'alamat',
        'wilayah',
        'platform_medsos',
        'info_villa_merah_dari',
        'tgl_followup',
        'status_crm',
        'tanggal_closing',
        'catatan_crm',
        'program_pendidikan',
        'is_closing',
    ];

    protected $casts = [
        'platform_medsos' => 'array',
        'info_villa_merah_dari' => 'array',
        'tanggal_input' => 'date',
        'tgl_followup' => 'date',
        'tanggal_closing' => 'date',
        'is_closing' => 'boolean',
    ];
}
