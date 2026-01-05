<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_market', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah', 80);
            $table->string('program', 120);
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('target_siswa');
            $table->decimal('target_omset', 14, 2);
            $table->timestamps();

            $table->unique(['wilayah', 'program', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_market');
    }
};