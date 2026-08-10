<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (Schema::hasColumn('rooms', 'image_path')) {
                    $table->text('image_path')->nullable()->change();
                } else {
                    $table->text('image_path')->nullable()->after('room_number');
                }

                if (!Schema::hasColumn('rooms', 'description')) {
                    $table->text('description')->nullable()->after('floor');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rooms') && Schema::hasColumn('rooms', 'image_path')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('image_path', 255)->nullable()->change();
            });
        }
    }
};
