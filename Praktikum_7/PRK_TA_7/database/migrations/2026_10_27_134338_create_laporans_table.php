<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');                        // Nama warga pelapor
            $table->string('no_hp');                               // Nomor HP pelapor
            $table->string('lokasi');                              // Lokasi banjir
            $table->string('kecamatan');                           // Kecamatan
            $table->integer('tinggi_air');                         // Tinggi air (cm)
            $table->text('keterangan')->nullable();                // Keterangan (boleh kosong)
            $table->string('foto');                                // Path foto bukti di storage
            $table->string('status')->default('Menunggu');         // Status penanganan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
