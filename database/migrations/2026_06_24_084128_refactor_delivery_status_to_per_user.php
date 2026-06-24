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
        // Truncate first to avoid constraint violations during refactor
        \Illuminate\Support\Facades\DB::table('order_delivery_statuses')->truncate();

        Schema::table('order_delivery_statuses', function (Blueprint $table) {
            // Drop foreign key first because it relies on the unique index
            $table->dropForeign(['menu_makanan_id']);
            $table->dropUnique('ods_unique_pkg_menu_date');
            
            // Re-add the foreign key
            $table->foreign('menu_makanan_id')->references('id')->on('menu_makanans')->cascadeOnDelete();

            if (Schema::hasColumn('order_delivery_statuses', 'meal_package_id')) {
                $table->dropConstrainedForeignId('meal_package_id');
            }
            
            $table->foreignId('order_id')->after('id')
                  ->constrained('orders')->cascadeOnDelete();
                  
            $table->unique(['order_id', 'menu_makanan_id', 'delivery_date'], 'ods_unique_order_menu_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('order_delivery_statuses')->truncate();

        Schema::table('order_delivery_statuses', function (Blueprint $table) {
            $table->dropForeign(['menu_makanan_id']);
            $table->dropUnique('ods_unique_order_menu_date');
            
            $table->foreign('menu_makanan_id')->references('id')->on('menu_makanans')->cascadeOnDelete();
            $table->dropConstrainedForeignId('order_id');
            
            // We don't re-add meal_package_id because it was already missing before
            // but we re-add the old unique constraint
            $table->unique(['menu_makanan_id', 'delivery_date'], 'ods_unique_pkg_menu_date');
        });
    }
};
