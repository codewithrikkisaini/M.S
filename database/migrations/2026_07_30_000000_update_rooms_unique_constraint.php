<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Drop old single-column unique index if it exists
        try {
            $indexes = collect(DB::select("SHOW INDEX FROM rooms WHERE Key_name = 'rooms_room_number_unique'"));
            if ($indexes->isNotEmpty()) {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->dropUnique('rooms_room_number_unique');
                });
            }
        } catch (\Throwable $e) {
            // Ignore if index does not exist
        }

        // Add new composite unique index if it doesn't exist
        try {
            $indexes = collect(DB::select("SHOW INDEX FROM rooms WHERE Key_name = 'rooms_hotel_id_room_number_unique'"));
            if ($indexes->isEmpty()) {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->unique(['hotel_id', 'room_number'], 'rooms_hotel_id_room_number_unique');
                });
            }
        } catch (\Throwable $e) {
            // Ignore if index already exists
        }
    }

    public function down(): void
    {
        try {
            $indexes = collect(DB::select("SHOW INDEX FROM rooms WHERE Key_name = 'rooms_hotel_id_room_number_unique'"));
            if ($indexes->isNotEmpty()) {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->dropUnique('rooms_hotel_id_room_number_unique');
                });
            }
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};
