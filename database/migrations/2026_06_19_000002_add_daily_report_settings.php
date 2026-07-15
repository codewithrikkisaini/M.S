<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'daily_report_auto_send',     'value' => '0'],
            ['key' => 'daily_report_email',         'value' => ''],
            ['key' => 'daily_report_time',          'value' => '23:30'],
            ['key' => 'daily_report_last_sent_date', 'value' => ''],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->insertOrIgnore(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'daily_report_auto_send', 'daily_report_email', 'daily_report_time', 'daily_report_last_sent_date',
        ])->delete();
    }
};
