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
        if (Schema::hasTable('rooms') && !Schema::hasColumn('rooms', 'capacity')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->integer('capacity')->default(2)->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rooms') && Schema::hasColumn('rooms', 'capacity')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('capacity');
            });
        }
    }
};

