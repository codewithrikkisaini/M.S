<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $defaults = [
            ['key' => 'hotel_name',      'value' => 'Merahkie PMS Lite'],
            ['key' => 'hotel_address',   'value' => ''],
            ['key' => 'hotel_phone',     'value' => ''],
            ['key' => 'hotel_email',     'value' => ''],
            ['key' => 'hotel_website',   'value' => ''],
            ['key' => 'hotel_timezone',  'value' => 'UTC'],
            ['key' => 'currency',        'value' => 'USD'],
            ['key' => 'date_format',     'value' => 'd M Y'],
            ['key' => 'checkin_time',    'value' => '14:00'],
            ['key' => 'checkout_time',   'value' => '12:00'],
            ['key' => 'email_notifications', 'value' => '1'],
            ['key' => 'sms_notifications',   'value' => '0'],
            ['key' => 'invoice_prefix',  'value' => 'INV-'],
            ['key' => 'invoice_footer',  'value' => 'Thank you for staying with us!'],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
