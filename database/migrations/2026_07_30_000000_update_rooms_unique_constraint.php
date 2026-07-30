<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("DROP INDEX IF EXISTS rooms_room_number_unique;");
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS rooms_hotel_id_room_number_unique ON rooms (hotel_id, room_number);");
        } else {
            Schema::table('rooms', function (Blueprint $table) {
                try {
                    $table->dropUnique('rooms_room_number_unique');
                } catch (\Throwable $e) {
                    // Ignore if index does not exist
                }
                try {
                    $table->unique(['hotel_id', 'room_number'], 'rooms_hotel_id_room_number_unique');
                } catch (\Throwable $e) {
                    // Ignore if index already exists
                }
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("DROP INDEX IF EXISTS rooms_hotel_id_room_number_unique;");
        } else {
            Schema::table('rooms', function (Blueprint $table) {
                try {
                    $table->dropUnique('rooms_hotel_id_room_number_unique');
                } catch (\Throwable $e) {
                    // Ignore
                }
            });
        }
    }
};
