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
        if (Schema::hasTable('settings')) {
            if (!Schema::hasColumn('settings', 'hotel_id')) {
                Schema::table('settings', function (Blueprint $table) {
                    $table->foreignId('hotel_id')->nullable()->after('id')->constrained('hotels')->nullOnDelete();
                });
            }

            try {
                $indexes = collect(Schema::getIndexes('settings'));
                if ($indexes->contains('name', 'settings_key_unique')) {
                    Schema::table('settings', function (Blueprint $table) {
                        $table->dropUnique('settings_key_unique');
                    });
                }
            } catch (\Throwable $e) {
                // Ignore if dropping index fails
            }

            try {
                $indexes = collect(Schema::getIndexes('settings'));
                if (!$indexes->contains('name', 'settings_hotel_id_key_unique')) {
                    Schema::table('settings', function (Blueprint $table) {
                        $table->unique(['hotel_id', 'key'], 'settings_hotel_id_key_unique');
                    });
                }
            } catch (\Throwable $e) {
                // Ignore if index creation fails
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'hotel_id')) {
            try {
                Schema::table('settings', function (Blueprint $table) {
                    $table->dropUnique('settings_hotel_id_key_unique');
                });
            } catch (\Throwable $e) {
                // Ignore
            }

            try {
                Schema::table('settings', function (Blueprint $table) {
                    $table->dropForeign(['hotel_id']);
                    $table->dropColumn('hotel_id');
                });
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
};
