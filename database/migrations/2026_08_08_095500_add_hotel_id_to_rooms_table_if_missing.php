<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rooms') && !Schema::hasColumn('rooms', 'hotel_id')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->foreignId('hotel_id')->nullable()->after('id')->constrained('hotels')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rooms') && Schema::hasColumn('rooms', 'hotel_id')) {
            try {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->dropForeign(['hotel_id']);
                });
            } catch (\Throwable $e) {}

            try {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->dropColumn('hotel_id');
                });
            } catch (\Throwable $e) {}
        }
    }
};
