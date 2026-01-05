<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataLead extends Model
{
    protected $table = 'data_leads';

    protected $fillable = [
        'wilayah',
        'nama_siswa',
        'ttl',
        'asal_sekolah',
        'kelas',
        'no_hp',
        'nama_medsos',
        'alamat',
        'asal_kota',
        'program',
        'nama_ortu',
        'no_tlp_ortu',
        'tanggal_input',
        'info_villa_merah_dari',
        'platform_medsos',
        'status_crm',
        'tgl_followup',
        'tanggal_closing',
        'catatan_crm',
        'program_pendidikan',
        'is_closing',
    ];

    protected $casts = [
        'info_villa_merah_dari' => 'array',
        'platform_medsos' => 'array',
        'tanggal_input' => 'date',
        'tgl_followup' => 'date',
        'tanggal_closing' => 'date',
        'is_closing' => 'boolean',
    ];
}