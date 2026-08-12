<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('reservations')) {
            DB::statement("ALTER TABLE `reservations` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'Confirmed'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reservations')) {
            DB::statement("ALTER TABLE `reservations` MODIFY `status` ENUM('Confirmed', 'Checked-In', 'Checked-Out', 'Cancelled', 'Pending', 'Rejected') NOT NULL DEFAULT 'Confirmed'");
        }
    }
};
