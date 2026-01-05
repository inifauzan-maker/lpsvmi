<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_leads', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah', 80);
            $table->string('nama_siswa', 120);
            $table->string('ttl', 120)->nullable();
            $table->string('asal_sekolah', 120);
            $table->string('kelas', 50)->nullable();
            $table->string('no_hp', 30);
            $table->string('nama_medsos', 120)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('asal_kota', 120)->nullable();
            $table->string('program', 120)->nullable();
            $table->string('nama_ortu', 120)->nullable();
            $table->string('no_tlp_ortu', 30)->nullable();
            $table->date('tanggal_input')->nullable();
            $table->json('info_villa_merah_dari')->nullable();
            $table->json('platform_medsos')->nullable();
            $table->string('status_crm', 20)->nullable();
            $table->date('tgl_followup')->nullable();
            $table->date('tanggal_closing')->nullable();
            $table->text('catatan_crm')->nullable();
            $table->string('program_pendidikan', 120)->nullable();
            $table->boolean('is_closing')->default(false);
            $table->timestamps();

            $table->index('wilayah');
            $table->index('program');
            $table->index('status_crm');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_leads');
    }
};