<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_images', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_images', 'title')) {
                $table->string('title')->nullable()->after('image_path');
            }
            if (!Schema::hasColumn('hotel_images', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel_images', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }
};
