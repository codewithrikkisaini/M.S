<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'users',
        'rooms',
        'room_types',
        'guests',
        'reservations',
        'checkins',
        'checkouts',
        'invoices',
        'housekeeping',
        'maintenance_tickets',
        'settings',
        'payments'
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('hotel_id')->nullable()->after('id')->constrained('hotels')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign([$tableName . '_hotel_id_foreign']);
                    $table->dropColumn('hotel_id');
                });
            }
        }
    }
};
