<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->text('special_notes')->nullable();
            $table->enum('status', ['Confirmed', 'Checked-In', 'Checked-Out', 'Cancelled'])->default('Confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
