<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'bed_type')) {
                $table->string('bed_type')->nullable()->after('floor');
            }
            if (!Schema::hasColumn('rooms', 'room_option')) {
                $table->string('room_option')->nullable()->after('bed_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'bed_type')) {
                $table->dropColumn('bed_type');
            }
            if (Schema::hasColumn('rooms', 'room_option')) {
                $table->dropColumn('room_option');
            }
        });
    }
};
