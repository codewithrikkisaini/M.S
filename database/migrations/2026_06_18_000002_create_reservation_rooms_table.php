<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        foreach (DB::table('reservations')->select('id', 'room_id')->get() as $reservation) {
            $price = DB::table('rooms')->where('id', $reservation->room_id)->value('price') ?? 0;

            DB::table('reservation_rooms')->insert([
                'reservation_id' => $reservation->id,
                'room_id'        => $reservation->room_id,
                'price'          => $price,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->after('guest_id')->constrained('rooms')->cascadeOnDelete();
        });

        foreach (DB::table('reservation_rooms')->select('reservation_id', 'room_id')->get() as $row) {
            DB::table('reservations')->where('id', $row->reservation_id)->update(['room_id' => $row->room_id]);
        }

        Schema::dropIfExists('reservation_rooms');
    }
};
