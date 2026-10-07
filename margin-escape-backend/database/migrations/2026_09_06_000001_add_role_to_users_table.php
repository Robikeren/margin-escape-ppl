<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Catatan: jalankan migration ini SETELAH migration default 'create_users_table'
// bawaan Laravel. File ini menambahkan kolom 'role' untuk membedakan
// Owner dan Staff (sesuai ERD: tabel users menggunakan single table inheritance).

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pakai string (bukan enum) karena lebih aman & fleksibel di PostgreSQL/Supabase
            $table->string('role')->default('staff')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};