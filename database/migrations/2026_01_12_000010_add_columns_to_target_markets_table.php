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
        if (!Schema::hasTable('target_markets')) {
            return;
        }

        Schema::table('target_markets', function (Blueprint $table) {
            if (!Schema::hasColumn('target_markets', 'wilayah')) {
                $table->string('wilayah')->nullable();
            }
            if (!Schema::hasColumn('target_markets', 'program')) {
                $table->string('program')->nullable();
            }
            if (!Schema::hasColumn('target_markets', 'bulan')) {
                $table->unsignedTinyInteger('bulan')->nullable();
            }
            if (!Schema::hasColumn('target_markets', 'tahun')) {
                $table->unsignedSmallInteger('tahun')->nullable();
            }
            if (!Schema::hasColumn('target_markets', 'target_siswa')) {
                $table->integer('target_siswa')->nullable();
            }
            if (!Schema::hasColumn('target_markets', 'target_omset')) {
                $table->bigInteger('target_omset')->nullable();
            }
        });

        if (Schema::hasColumn('target_markets', 'data')) {
            DB::statement("
                UPDATE target_markets
                SET
                    wilayah = COALESCE(wilayah, JSON_UNQUOTE(JSON_EXTRACT(data, '$.wilayah'))),
                    program = COALESCE(program, JSON_UNQUOTE(JSON_EXTRACT(data, '$.program'))),
                    bulan = COALESCE(
                        bulan,
                        CASE
                            WHEN JSON_UNQUOTE(JSON_EXTRACT(data, '$.bulan')) REGEXP '^[0-9]+$'
                                THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.bulan')) AS UNSIGNED)
                            ELSE NULL
                        END
                    ),
                    tahun = COALESCE(
                        tahun,
                        CASE
                            WHEN JSON_UNQUOTE(JSON_EXTRACT(data, '$.tahun')) REGEXP '^[0-9]+$'
                                THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.tahun')) AS UNSIGNED)
                            ELSE NULL
                        END
                    ),
                    target_siswa = COALESCE(
                        target_siswa,
                        CASE
                            WHEN JSON_UNQUOTE(JSON_EXTRACT(data, '$.target_siswa')) REGEXP '^-?[0-9]+$'
                                THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.target_siswa')) AS SIGNED)
                            ELSE NULL
                        END
                    ),
                    target_omset = COALESCE(
                        target_omset,
                        CASE
                            WHEN JSON_UNQUOTE(JSON_EXTRACT(data, '$.target_omset')) REGEXP '^-?[0-9]+$'
                                THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.target_omset')) AS SIGNED)
                            ELSE NULL
                        END
                    )
                WHERE data IS NOT NULL
            ");

            DB::statement("ALTER TABLE target_markets DROP COLUMN data");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('target_markets')) {
            return;
        }

        if (!Schema::hasColumn('target_markets', 'data')) {
            Schema::table('target_markets', function (Blueprint $table) {
                $table->json('data')->nullable();
            });
        }
    }
};
