<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hapus constraint UNIQUE pada kolom 'nik' di tabel user_details.
     * Kolom ini nullable (sejak migrasi 2025_02_08) tapi masih unik —
     * menyebabkan error duplicate key saat registrasi publik (nik = null).
     *
     * Selain itu, update semua meal_packages yang batch-nya null/kosong
     * menjadi 'I' agar muncul di halaman customer (filter ->where('batch','I')).
     */
    public function up(): void
    {
        // ── Fix 1: Drop unique constraint pada nik ──────────────────────
        Schema::table('user_details', function (Blueprint $table) {
            // Drop index unique bernama 'user_details_nik_unique'
            // (nama default Laravel: {table}_{column}_unique)
            $table->dropUnique(['nik']);
        });

        // ── Fix 2: Set batch = 'I' untuk semua meal_packages yang belum punya batch ──
        DB::table('meal_packages')
            ->whereNull('batch')
            ->orWhere('batch', '')
            ->update(['batch' => 'I']);
    }

    public function down(): void
    {
        // Re-add unique ke nik (hati-hati kalau sudah ada data null ganda)
        Schema::table('user_details', function (Blueprint $table) {
            $table->unique('nik');
        });
    }
};
