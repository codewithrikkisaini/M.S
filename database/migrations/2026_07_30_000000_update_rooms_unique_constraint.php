<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        try {
            $indexes = collect(Schema::getIndexes('rooms'));
            
            // Drop old single-column unique index if it exists
            if ($indexes->contains('name', 'rooms_room_number_unique')) {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->dropUnique('rooms_room_number_unique');
                });
            }
        } catch (\Throwable $e) {
            // Defensive catch
        }

        try {
            $indexes = collect(Schema::getIndexes('rooms'));
            
            // Add new composite unique index (hotel_id + room_number) if it doesn't exist
            if (!$indexes->contains('name', 'rooms_hotel_id_room_number_unique')) {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->unique(['hotel_id', 'room_number'], 'rooms_hotel_id_room_number_unique');
                });
            }
        } catch (\Throwable $e) {
            // Defensive catch
        }
    }

    public function down(): void
    {
        try {
            $indexes = collect(Schema::getIndexes('rooms'));
            if ($indexes->contains('name', 'rooms_hotel_id_room_number_unique')) {
                Schema::table('rooms', function (Blueprint $table) {
                    $table->dropUnique('rooms_hotel_id_room_number_unique');
                });
            }
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};

