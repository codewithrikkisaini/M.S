<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('misc_charge', 10, 2)->default(0.00)->after('tax_rate');
        });

        Schema::table('checkouts', function (Blueprint $table) {
            $table->decimal('misc_charge', 10, 2)->default(0.00)->after('tax_rate');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('misc_charge');
        });

        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn('misc_charge');
        });
    }
};
