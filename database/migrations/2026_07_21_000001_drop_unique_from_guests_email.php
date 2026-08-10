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
            DB::statement('DROP INDEX IF EXISTS guests_email_unique');
            DB::statement('DROP INDEX IF EXISTS guests_guest_id_unique');
        } catch (\Throwable $e) {}

        try {
            Schema::table('guests', function (Blueprint $table) {
                $table->dropUnique('guests_email_unique');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('guests', function (Blueprint $table) {
                $table->dropUnique('guests_guest_id_unique');
            });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
    }
};
