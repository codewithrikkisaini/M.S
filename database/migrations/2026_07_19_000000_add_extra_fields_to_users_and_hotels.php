<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('email');
            $table->string('phone')->nullable()->after('username');
            $table->string('employee_id')->nullable()->after('phone');
            $table->string('profile_photo_path')->nullable()->after('employee_id');
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'employee_id', 'profile_photo_path']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['code']);
        });
    }
};
