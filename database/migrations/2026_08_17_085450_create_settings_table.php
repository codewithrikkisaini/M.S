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
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
                $table->string('key');
                $table->text('value')->nullable();
                $table->string('type')->default('string'); // string, boolean, json
                $table->text('description')->nullable();
                $table->timestamps();
                $table->unique(['hotel_id', 'key'], 'settings_hotel_id_key_unique');
            });
        } else {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'hotel_id')) {
                    $table->foreignId('hotel_id')->nullable()->after('id')->constrained('hotels')->nullOnDelete();
                }
                if (!Schema::hasColumn('settings', 'type')) {
                    $table->string('type')->default('string')->after('value');
                }
                if (!Schema::hasColumn('settings', 'description')) {
                    $table->text('description')->nullable()->after('type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep settings table intact on rollback
    }
};
