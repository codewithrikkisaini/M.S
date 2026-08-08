<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reservations', 'discount_type')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->enum('discount_type', ['Fixed', 'Percentage'])->default('Fixed')->after('children');
                $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            });
        }

        if (!Schema::hasColumn('checkouts', 'discount')) {
            Schema::table('checkouts', function (Blueprint $table) {
                $table->decimal('discount', 10, 2)->default(0)->after('subtotal');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('reservations', 'discount_type')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn(['discount_type', 'discount_value']);
            });
        }

        if (Schema::hasColumn('checkouts', 'discount')) {
            Schema::table('checkouts', function (Blueprint $table) {
                $table->dropColumn('discount');
            });
        }
    }
};
