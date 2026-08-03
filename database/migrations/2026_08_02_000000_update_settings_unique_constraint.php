<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        try {
            $indexes = collect(Schema::getIndexes('settings'));
            
            // Drop old single-column unique index if it exists
            if ($indexes->contains('name', 'settings_key_unique')) {
                Schema::table('settings', function (Blueprint $table) {
                    $table->dropUnique('settings_key_unique');
                });
            }
        } catch (\Throwable $e) {
            // Defensive catch
        }

        try {
            $indexes = collect(Schema::getIndexes('settings'));
            
            // Add new composite unique index (hotel_id + key) if it doesn't exist
            if (!$indexes->contains('name', 'settings_hotel_id_key_unique')) {
                Schema::table('settings', function (Blueprint $table) {
                    $table->unique(['hotel_id', 'key'], 'settings_hotel_id_key_unique');
                });
            }
        } catch (\Throwable $e) {
            // Defensive catch
        }
    }

    public function down(): void
    {
        try {
            $indexes = collect(Schema::getIndexes('settings'));
            if ($indexes->contains('name', 'settings_hotel_id_key_unique')) {
                Schema::table('settings', function (Blueprint $table) {
                    $table->dropUnique('settings_hotel_id_key_unique');
                });
            }
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};
