<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontaks', function (Blueprint $table) {
            $table->id(); // Primary key auto increment
            $table->string('nama'); // VARCHAR(255)
            $table->text('alamat'); // TEXT
            $table->string('no_hp', 15); // Disimpan sebagai teks agar angka 0 di depan tidak hilang
            $table->string('foto')->nullable(); // Path foto, boleh kosong
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontaks');
    }
};