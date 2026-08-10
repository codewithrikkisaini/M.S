<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reservations', 'tax_rate')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->decimal('tax_rate', 5, 2)->default(18.00)->after('discount_value');
            });
        }

        if (!Schema::hasColumn('checkouts', 'tax_rate')) {
            Schema::table('checkouts', function (Blueprint $table) {
                $table->decimal('tax_rate', 5, 2)->default(18.00)->after('discount');
            });
        }

        DB::table('reservations')->update(['tax_rate' => 18.00]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('reservations', 'tax_rate')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('tax_rate');
            });
        }

        if (Schema::hasColumn('checkouts', 'tax_rate')) {
            Schema::table('checkouts', function (Blueprint $table) {
                $table->dropColumn('tax_rate');
            });
        }
    }
};
