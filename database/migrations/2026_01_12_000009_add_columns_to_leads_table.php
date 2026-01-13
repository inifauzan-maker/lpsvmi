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
        if (!Schema::hasTable('leads')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'nama_siswa')) {
                $table->string('nama_siswa')->nullable();
            }
            if (!Schema::hasColumn('leads', 'ttl')) {
                $table->string('ttl')->nullable();
            }
            if (!Schema::hasColumn('leads', 'program')) {
                $table->string('program')->nullable();
            }
            if (!Schema::hasColumn('leads', 'asal_sekolah')) {
                $table->string('asal_sekolah')->nullable();
            }
            if (!Schema::hasColumn('leads', 'kelas')) {
                $table->string('kelas')->nullable();
            }
            if (!Schema::hasColumn('leads', 'asal_kota')) {
                $table->string('asal_kota')->nullable();
            }
            if (!Schema::hasColumn('leads', 'no_hp')) {
                $table->string('no_hp')->nullable();
            }
            if (!Schema::hasColumn('leads', 'nama_medsos')) {
                $table->string('nama_medsos')->nullable();
            }
            if (!Schema::hasColumn('leads', 'nama_ortu')) {
                $table->string('nama_ortu')->nullable();
            }
            if (!Schema::hasColumn('leads', 'no_tlp_ortu')) {
                $table->string('no_tlp_ortu')->nullable();
            }
            if (!Schema::hasColumn('leads', 'tanggal_input')) {
                $table->date('tanggal_input')->nullable();
            }
            if (!Schema::hasColumn('leads', 'alamat')) {
                $table->text('alamat')->nullable();
            }
            if (!Schema::hasColumn('leads', 'wilayah')) {
                $table->string('wilayah')->nullable();
            }
            if (!Schema::hasColumn('leads', 'platform_medsos')) {
                $table->json('platform_medsos')->nullable();
            }
            if (!Schema::hasColumn('leads', 'info_villa_merah_dari')) {
                $table->json('info_villa_merah_dari')->nullable();
            }
            if (!Schema::hasColumn('leads', 'tgl_followup')) {
                $table->date('tgl_followup')->nullable();
            }
            if (!Schema::hasColumn('leads', 'status_crm')) {
                $table->string('status_crm')->nullable();
            }
            if (!Schema::hasColumn('leads', 'tanggal_closing')) {
                $table->date('tanggal_closing')->nullable();
            }
            if (!Schema::hasColumn('leads', 'catatan_crm')) {
                $table->text('catatan_crm')->nullable();
            }
            if (!Schema::hasColumn('leads', 'program_pendidikan')) {
                $table->string('program_pendidikan')->nullable();
            }
            if (!Schema::hasColumn('leads', 'is_closing')) {
                $table->boolean('is_closing')->default(false);
            }
        });

        if (Schema::hasColumn('leads', 'data')) {
            DB::statement("
                UPDATE leads
                SET
                    nama_siswa = COALESCE(nama_siswa, JSON_UNQUOTE(JSON_EXTRACT(data, '$.nama_siswa'))),
                    ttl = COALESCE(ttl, JSON_UNQUOTE(JSON_EXTRACT(data, '$.ttl'))),
                    program = COALESCE(program, JSON_UNQUOTE(JSON_EXTRACT(data, '$.program'))),
                    asal_sekolah = COALESCE(asal_sekolah, JSON_UNQUOTE(JSON_EXTRACT(data, '$.asal_sekolah'))),
                    kelas = COALESCE(kelas, JSON_UNQUOTE(JSON_EXTRACT(data, '$.kelas'))),
                    asal_kota = COALESCE(asal_kota, JSON_UNQUOTE(JSON_EXTRACT(data, '$.asal_kota'))),
                    no_hp = COALESCE(no_hp, JSON_UNQUOTE(JSON_EXTRACT(data, '$.no_hp'))),
                    nama_medsos = COALESCE(nama_medsos, JSON_UNQUOTE(JSON_EXTRACT(data, '$.nama_medsos'))),
                    nama_ortu = COALESCE(nama_ortu, JSON_UNQUOTE(JSON_EXTRACT(data, '$.nama_ortu'))),
                    no_tlp_ortu = COALESCE(no_tlp_ortu, JSON_UNQUOTE(JSON_EXTRACT(data, '$.no_tlp_ortu'))),
                    tanggal_input = COALESCE(
                        tanggal_input,
                        STR_TO_DATE(
                            NULLIF(
                                NULLIF(LEFT(
                                    COALESCE(
                                        JSON_UNQUOTE(JSON_EXTRACT(data, '$.tanggal_input')),
                                        JSON_UNQUOTE(JSON_EXTRACT(data, '$.createdAt'))
                                    ),
                                    10
                                ), 'null'),
                                ''
                            ),
                            '%Y-%m-%d'
                        )
                    ),
                    alamat = COALESCE(alamat, JSON_UNQUOTE(JSON_EXTRACT(data, '$.alamat'))),
                    wilayah = COALESCE(wilayah, JSON_UNQUOTE(JSON_EXTRACT(data, '$.wilayah'))),
                    platform_medsos = COALESCE(platform_medsos, JSON_EXTRACT(data, '$.platform_medsos')),
                    info_villa_merah_dari = COALESCE(info_villa_merah_dari, JSON_EXTRACT(data, '$.info_villa_merah_dari')),
                    tgl_followup = COALESCE(
                        tgl_followup,
                        STR_TO_DATE(
                            NULLIF(
                                NULLIF(LEFT(JSON_UNQUOTE(JSON_EXTRACT(data, '$.tgl_followup')), 10), 'null'),
                                ''
                            ),
                            '%Y-%m-%d'
                        )
                    ),
                    status_crm = COALESCE(status_crm, JSON_UNQUOTE(JSON_EXTRACT(data, '$.status_crm'))),
                    tanggal_closing = COALESCE(
                        tanggal_closing,
                        STR_TO_DATE(
                            NULLIF(
                                NULLIF(LEFT(JSON_UNQUOTE(JSON_EXTRACT(data, '$.tanggal_closing')), 10), 'null'),
                                ''
                            ),
                            '%Y-%m-%d'
                        )
                    ),
                    catatan_crm = COALESCE(catatan_crm, JSON_UNQUOTE(JSON_EXTRACT(data, '$.catatan_crm'))),
                    program_pendidikan = COALESCE(program_pendidikan, JSON_UNQUOTE(JSON_EXTRACT(data, '$.program_pendidikan'))),
                    is_closing = COALESCE(is_closing, JSON_EXTRACT(data, '$.is_closing'))
                WHERE data IS NOT NULL
            ");

            DB::statement("ALTER TABLE leads DROP COLUMN data");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }

        if (!Schema::hasColumn('leads', 'data')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->json('data')->nullable();
            });
        }
    }
};
