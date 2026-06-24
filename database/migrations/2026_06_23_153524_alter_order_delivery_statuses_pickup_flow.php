<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Ubah flow status dari pengiriman ke pengambilan:
     * pending → diterima → diproses → diambil
     */
    public function up(): void
    {
        // 1) Reset semua nilai lama ke 'pending' terlebih dahulu
        //    sebelum mengubah ENUM agar tidak ada data truncation
        DB::statement("
            UPDATE order_delivery_statuses
            SET status_siang = 'pending'
        ");
        DB::statement("
            UPDATE order_delivery_statuses
            SET status_malam = 'pending'
        ");

        // 2) Update ENUM status_siang ke flow pickup
        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_siang
            ENUM('pending','diterima','diproses','diambil')
            NOT NULL DEFAULT 'pending'
        ");

        // 3) Update ENUM status_malam ke flow pickup
        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_malam
            ENUM('pending','diterima','diproses','diambil')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Kembalikan semua ke pending lalu revert ENUM
        DB::statement("
            UPDATE order_delivery_statuses SET status_siang = 'pending'
        ");
        DB::statement("
            UPDATE order_delivery_statuses SET status_malam = 'pending'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_siang
            ENUM('pending','diproses','sedang dikirim','sampai','gagal dikirim')
            NOT NULL DEFAULT 'pending'
        ");

        DB::statement("
            ALTER TABLE order_delivery_statuses
            MODIFY status_malam
            ENUM('pending','diproses','sedang dikirim','sampai','gagal dikirim')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
