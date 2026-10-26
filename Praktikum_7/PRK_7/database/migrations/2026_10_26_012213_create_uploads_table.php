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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->String('judul');
            $table->text('keterangan')->nullable();
            $table->string('filename');
            $table->string('filepath');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
