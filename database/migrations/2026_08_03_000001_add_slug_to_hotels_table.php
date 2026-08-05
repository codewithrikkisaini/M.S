<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'slug')) {
                // Non-unique slug column as uniqueness is maintained by ID at the end of the URL
                $table->string('slug')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
