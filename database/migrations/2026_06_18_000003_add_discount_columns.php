<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('discount_type', ['Fixed', 'Percentage'])->default('Fixed')->after('children');
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
        });

        Schema::table('checkouts', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });

        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
