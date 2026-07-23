<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            if (!Schema::hasColumn('room_types', 'daily_rate')) {
                $table->decimal('daily_rate', 10, 2)->default(59.95)->after('name');
            }
            if (!Schema::hasColumn('room_types', 'weekly_rate')) {
                $table->decimal('weekly_rate', 10, 2)->default(249.90)->after('daily_rate');
            }
            if (!Schema::hasColumn('room_types', 'monthly_rate')) {
                $table->decimal('monthly_rate', 10, 2)->default(990.00)->after('weekly_rate');
            }
            if (!Schema::hasColumn('room_types', 'tax_percent')) {
                $table->decimal('tax_percent', 5, 2)->default(15.00)->after('monthly_rate');
            }
            if (!Schema::hasColumn('room_types', 'status')) {
                $table->string('status')->default('active')->after('tax_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['daily_rate', 'weekly_rate', 'monthly_rate', 'tax_percent', 'status']);
        });
    }
};
