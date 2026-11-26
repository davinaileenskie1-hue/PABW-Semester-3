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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('penulis', 150);
            $table->string('penerbit', 150);
            $table->year('tahun_terbit');
            $table->unsignedInteger('stok')->default(0);

            // Lokasi dan nama asli sampul buku
            $table->string('cover_path')->nullable();
            $table->string('cover_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
