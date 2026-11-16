<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\NullableType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tps')->constrained('nama_tps')->cascadeOnDelete();
            $table->string('kecamatan')->constrained('kecamatan')->cascadeOnDelete();
            $table->string('deskripsi');
            $table->integer('kapasitas_kg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TitikSampah');
    }
};

