<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediksi_restock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_kopi_id')->constrained('jenis_kopi')->cascadeOnDelete();
            $table->date('tanggal_prediksi');
            $table->integer('jumlah_prediksi');
            $table->integer('rekomendasi_restock');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediksi_restock');
    }
};