<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->decimal('daily_rate', 10, 2)->default(0.00)->after('name');
            $table->decimal('weekly_rate', 10, 2)->default(0.00)->after('daily_rate');
            $table->decimal('monthly_rate', 10, 2)->default(0.00)->after('weekly_rate');
            $table->decimal('tax_percentage', 5, 2)->default(15.00)->after('monthly_rate');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('tax_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['daily_rate', 'weekly_rate', 'monthly_rate', 'tax_percentage', 'status']);
        });
    }
};
