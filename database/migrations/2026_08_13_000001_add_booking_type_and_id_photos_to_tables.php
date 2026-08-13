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
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'booking_type')) {
                $table->string('booking_type')->default('Walk in')->after('status');
            }
        });

        Schema::table('guests', function (Blueprint $table) {
            if (!Schema::hasColumn('guests', 'id_card_front')) {
                $table->string('id_card_front')->nullable()->after('passport_number');
            }
            if (!Schema::hasColumn('guests', 'id_card_back')) {
                $table->string('id_card_back')->nullable()->after('id_card_front');
            }
            if (!Schema::hasColumn('guests', 'guest_photo')) {
                $table->string('guest_photo')->nullable()->after('id_card_back');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'booking_type')) {
                $table->dropColumn('booking_type');
            }
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['id_card_front', 'id_card_back', 'guest_photo']);
        });
    }
};
