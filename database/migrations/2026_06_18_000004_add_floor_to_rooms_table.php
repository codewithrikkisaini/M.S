<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('rooms', 'floor')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('floor')->nullable()->after('room_type_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('rooms', 'floor')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('floor');
            });
        }
    }
};
