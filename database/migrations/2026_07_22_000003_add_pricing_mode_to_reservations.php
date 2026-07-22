<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reservations', 'pricing_mode')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->enum('pricing_mode', ['auto', 'daily', 'weekly', 'monthly'])->default('auto')->after('misc_charge');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('reservations', 'pricing_mode')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('pricing_mode');
            });
        }
    }
};
