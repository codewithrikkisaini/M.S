<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'total')) {
                    $table->decimal('total', 10, 2)->default(0.00)->after('checkout_id');
                }
                if (!Schema::hasColumn('invoices', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->default(0.00)->after('total');
                }
                if (!Schema::hasColumn('invoices', 'status')) {
                    $table->string('status', 50)->default('Paid')->after('total_amount');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (Schema::hasColumn('invoices', 'total')) {
                    $table->dropColumn('total');
                }
                if (Schema::hasColumn('invoices', 'total_amount')) {
                    $table->dropColumn('total_amount');
                }
                if (Schema::hasColumn('invoices', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
