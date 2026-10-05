<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dokumen');
            $table->date('tanggal_dokumen');
            $table->string('jenis_dokumen');
            $table->text('perihal');
            $table->string('asal_tujuan');
            $table->string('kategori_asal_tujuan');
            $table->string('sifat_surat')->default('Biasa');
            $table->string('bidang')->nullable();
            $table->unsignedInteger('jumlah_lampiran')->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedTinyInteger('cluster')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};