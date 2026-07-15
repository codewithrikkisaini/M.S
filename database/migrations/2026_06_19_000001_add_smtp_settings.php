<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'smtp_enabled',      'value' => '0'],
            ['key' => 'smtp_host',         'value' => ''],
            ['key' => 'smtp_port',         'value' => '587'],
            ['key' => 'smtp_username',     'value' => ''],
            ['key' => 'smtp_password',     'value' => ''],
            ['key' => 'smtp_encryption',   'value' => 'tls'],
            ['key' => 'smtp_from_address', 'value' => ''],
            ['key' => 'smtp_from_name',    'value' => ''],
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
            'smtp_enabled', 'smtp_host', 'smtp_port', 'smtp_username',
            'smtp_password', 'smtp_encryption', 'smtp_from_address', 'smtp_from_name',
        ])->delete();
    }
};
