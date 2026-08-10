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
        if (!Schema::hasColumn('reservations', 'pnr')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('pnr', 10)->nullable()->after('hotel_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('reservations', 'pnr')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('pnr');
            });
        }
    }
};
