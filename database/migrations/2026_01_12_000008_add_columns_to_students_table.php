<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'nama')) {
                $table->string('nama')->nullable();
            }
            if (!Schema::hasColumn('students', 'ttl')) {
                $table->string('ttl')->nullable();
            }
            if (!Schema::hasColumn('students', 'no_hp_siswa')) {
                $table->string('no_hp_siswa')->nullable();
            }
            if (!Schema::hasColumn('students', 'asal_sekolah')) {
                $table->string('asal_sekolah')->nullable();
            }
            if (!Schema::hasColumn('students', 'kelas')) {
                $table->string('kelas')->nullable();
            }
            if (!Schema::hasColumn('students', 'asal_kota')) {
                $table->string('asal_kota')->nullable();
            }
            if (!Schema::hasColumn('students', 'program')) {
                $table->string('program')->nullable();
            }
            if (!Schema::hasColumn('students', 'nama_medsos')) {
                $table->string('nama_medsos')->nullable();
            }
            if (!Schema::hasColumn('students', 'platform_medsos')) {
                $table->json('platform_medsos')->nullable();
            }
            if (!Schema::hasColumn('students', 'info_villa_merah_dari')) {
                $table->json('info_villa_merah_dari')->nullable();
            }
            if (!Schema::hasColumn('students', 'alamat_siswa')) {
                $table->text('alamat_siswa')->nullable();
            }
            if (!Schema::hasColumn('students', 'nama_ortu')) {
                $table->string('nama_ortu')->nullable();
            }
            if (!Schema::hasColumn('students', 'no_tlp_ortu')) {
                $table->string('no_tlp_ortu')->nullable();
            }
            if (!Schema::hasColumn('students', 'biaya_pendidikan')) {
                $table->bigInteger('biaya_pendidikan')->nullable();
            }
            if (!Schema::hasColumn('students', 'sisa_angsuran')) {
                $table->bigInteger('sisa_angsuran')->nullable();
            }
            if (!Schema::hasColumn('students', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('students', 'from_leads_id')) {
                $table->string('from_leads_id')->nullable();
            }
            if (!Schema::hasColumn('students', 'tanggal_daftar')) {
                $table->date('tanggal_daftar')->nullable();
            }
        });

        if (Schema::hasColumn('students', 'data')) {
            DB::statement("
                UPDATE students
                SET
                    nama = COALESCE(nama, JSON_UNQUOTE(JSON_EXTRACT(data, '$.nama'))),
                    ttl = COALESCE(ttl, JSON_UNQUOTE(JSON_EXTRACT(data, '$.ttl'))),
                    no_hp_siswa = COALESCE(no_hp_siswa, JSON_UNQUOTE(JSON_EXTRACT(data, '$.no_hp_siswa'))),
                    asal_sekolah = COALESCE(asal_sekolah, JSON_UNQUOTE(JSON_EXTRACT(data, '$.asal_sekolah'))),
                    kelas = COALESCE(kelas, JSON_UNQUOTE(JSON_EXTRACT(data, '$.kelas'))),
                    asal_kota = COALESCE(asal_kota, JSON_UNQUOTE(JSON_EXTRACT(data, '$.asal_kota'))),
                    program = COALESCE(program, JSON_UNQUOTE(JSON_EXTRACT(data, '$.program'))),
                    nama_medsos = COALESCE(nama_medsos, JSON_UNQUOTE(JSON_EXTRACT(data, '$.nama_medsos'))),
                    platform_medsos = COALESCE(platform_medsos, JSON_EXTRACT(data, '$.platform_medsos')),
                    info_villa_merah_dari = COALESCE(info_villa_merah_dari, JSON_EXTRACT(data, '$.info_villa_merah_dari')),
                    alamat_siswa = COALESCE(alamat_siswa, JSON_UNQUOTE(JSON_EXTRACT(data, '$.alamat_siswa'))),
                    nama_ortu = COALESCE(nama_ortu, JSON_UNQUOTE(JSON_EXTRACT(data, '$.nama_ortu'))),
                    no_tlp_ortu = COALESCE(no_tlp_ortu, JSON_UNQUOTE(JSON_EXTRACT(data, '$.no_tlp_ortu'))),
                    biaya_pendidikan = COALESCE(
                        biaya_pendidikan,
                        CASE
                            WHEN JSON_UNQUOTE(JSON_EXTRACT(data, '$.biaya_pendidikan')) REGEXP '^-?[0-9]+$'
                                THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.biaya_pendidikan')) AS SIGNED)
                            ELSE NULL
                        END
                    ),
                    sisa_angsuran = COALESCE(
                        sisa_angsuran,
                        CASE
                            WHEN JSON_UNQUOTE(JSON_EXTRACT(data, '$.sisa_angsuran')) REGEXP '^-?[0-9]+$'
                                THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.sisa_angsuran')) AS SIGNED)
                            ELSE NULL
                        END
                    ),
                    location = COALESCE(location, JSON_UNQUOTE(JSON_EXTRACT(data, '$.location'))),
                    from_leads_id = COALESCE(from_leads_id, JSON_UNQUOTE(JSON_EXTRACT(data, '$.fromLeadsId'))),
                    tanggal_daftar = COALESCE(
                        tanggal_daftar,
                        STR_TO_DATE(
                            NULLIF(LEFT(JSON_UNQUOTE(JSON_EXTRACT(data, '$.createdAt')), 10), ''),
                            '%Y-%m-%d'
                        )
                    )
                WHERE data IS NOT NULL
            ");

            DB::statement("ALTER TABLE students DROP COLUMN data");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('students')) {
            return;
        }

        if (!Schema::hasColumn('students', 'data')) {
            Schema::table('students', function (Blueprint $table) {
                $table->json('data')->nullable();
            });
        }
    }
};
