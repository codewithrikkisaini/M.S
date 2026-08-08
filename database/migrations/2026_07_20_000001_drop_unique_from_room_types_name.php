<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('DROP INDEX IF EXISTS room_types_name_unique');
        } catch (\Throwable $e) {}

        try {
            Schema::table('room_types', function (Blueprint $table) {
                $table->dropUnique('room_types_name_unique');
            });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
    }
};
