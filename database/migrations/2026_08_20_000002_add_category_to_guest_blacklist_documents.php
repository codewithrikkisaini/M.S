<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('guest_blacklist_documents', 'category')) {
            Schema::table('guest_blacklist_documents', function (Blueprint $table) {
                $table->string('category', 100)->nullable()->after('original_filename')->comment('Identity Document, Incident Report, Damage Report, Payment Evidence, Management Approval, Release Evidence, Other');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('guest_blacklist_documents', 'category')) {
            Schema::table('guest_blacklist_documents', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
