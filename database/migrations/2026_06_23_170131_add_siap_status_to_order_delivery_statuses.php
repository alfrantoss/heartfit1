<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Tambah status 'siap' (Pesanan Siap Diambil) ke flow pickup:
     * pending → diterima → diproses → siap → diambil
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_siang
            ENUM('pending','diterima','diproses','siap','diambil')
            NOT NULL DEFAULT 'pending'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_malam
            ENUM('pending','diterima','diproses','siap','diambil')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Reset nilai 'siap' ke 'diproses' sebelum revert ENUM
        DB::statement("
            UPDATE order_delivery_statuses
            SET status_siang = 'diproses' WHERE status_siang = 'siap'
        ");
        DB::statement("
            UPDATE order_delivery_statuses
            SET status_malam = 'diproses' WHERE status_malam = 'siap'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_siang
            ENUM('pending','diterima','diproses','diambil')
            NOT NULL DEFAULT 'pending'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_malam
            ENUM('pending','diterima','diproses','diambil')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
