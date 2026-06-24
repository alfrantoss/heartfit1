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
        Schema::table('package_types', function (Blueprint $table) {
            $table->boolean('is_personal')->default(false)->after('packageType')
                  ->comment('Jika true, order dengan tipe ini masuk ke dashboard Ahli Gizi');
        });
    }

    public function down(): void
    {
        Schema::table('package_types', function (Blueprint $table) {
            $table->dropColumn('is_personal');
        });
    }
};
