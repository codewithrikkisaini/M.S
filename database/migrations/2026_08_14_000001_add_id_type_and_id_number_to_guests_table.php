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
        Schema::table('guests', function (Blueprint $table) {
            if (!Schema::hasColumn('guests', 'id_type')) {
                $table->string('id_type')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('guests', 'id_number')) {
                $table->string('id_number')->nullable()->after('id_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (Schema::hasColumn('guests', 'id_type')) {
                $table->dropColumn('id_type');
            }
            if (Schema::hasColumn('guests', 'id_number')) {
                $table->dropColumn('id_number');
            }
        });
    }
};
