<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel pivot untuk relasi many-to-many antara Notifikasi dan User
// (satu notifikasi bisa diterima banyak user, satu user bisa terima banyak notifikasi)

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_user', function (Blueprint $table) {
            $table->foreignId('notifikasi_id')->constrained('notifikasi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['notifikasi_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_user');
    }
};