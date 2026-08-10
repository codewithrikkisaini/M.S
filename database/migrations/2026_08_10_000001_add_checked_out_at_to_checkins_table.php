<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('checkins')) {
            Schema::table('checkins', function (Blueprint $table) {
                if (!Schema::hasColumn('checkins', 'checked_out_at')) {
                    $table->timestamp('checked_out_at')->nullable()->after('remarks');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('checkins')) {
            Schema::table('checkins', function (Blueprint $table) {
                if (Schema::hasColumn('checkins', 'checked_out_at')) {
                    $table->dropColumn('checked_out_at');
                }
            });
        }
    }
};
