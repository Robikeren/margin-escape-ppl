<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_kopi_id')->constrained('jenis_kopi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // staff yang input
            $table->date('tanggal');
            $table->integer('stok_awal');
            $table->integer('keluar');
            $table->integer('masuk')->default(0);
            $table->integer('stok_akhir'); // dihitung otomatis di sisi aplikasi (Model/Controller)
            $table->timestamps();

            // FR-05: cegah input ganda untuk tanggal + jenis kopi yang sama
            $table->unique(['jenis_kopi_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_opname');
    }
};