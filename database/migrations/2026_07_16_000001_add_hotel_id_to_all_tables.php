<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'users',
        'room_types',
        'rooms',
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
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'hotel_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('hotel_id')->nullable()->after('id')->constrained('hotels')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'hotel_id')) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropForeign(['hotel_id']);
                    });
                } catch (\Throwable $e) {
                    // Ignore if foreign key constraint does not exist
                }

                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('hotel_id');
                    });
                } catch (\Throwable $e) {
                    // Ignore if column cannot be dropped
                }
            }
        }
    }
};
