<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('location', 80);
            $table->string('nama', 120);
            $table->string('ttl', 120)->nullable();
            $table->string('no_hp_siswa', 30);
            $table->string('asal_sekolah', 120);
            $table->string('kelas', 50)->nullable();
            $table->string('asal_kota', 120)->nullable();
            $table->string('program', 120)->nullable();
            $table->string('nama_medsos', 120)->nullable();
            $table->json('platform_medsos')->nullable();
            $table->json('info_villa_merah_dari')->nullable();
            $table->string('alamat_siswa', 255)->nullable();
            $table->string('nama_ortu', 120)->nullable();
            $table->string('no_tlp_ortu', 30)->nullable();
            $table->date('tanggal_daftar')->nullable();
            $table->decimal('biaya_pendidikan', 12, 2)->nullable();
            $table->decimal('sisa_angsuran', 12, 2)->nullable();
            $table->timestamps();

            $table->index('location');
            $table->index('program');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_siswa');
    }
};