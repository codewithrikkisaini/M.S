<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Columns already exist from partial migration. Handle data population and constraints.

        // Migrate existing 'removed' status to 'released'
        DB::table('guest_blacklists')
            ->where('status', 'removed')
            ->update([
                'status' => 'released',
                'released_at' => DB::raw('removed_at'),
                'released_by' => DB::raw('removed_by'),
            ]);

        // Generate case numbers for records that don't have them
        $records = DB::table('guest_blacklists')->whereNull('case_number')->orWhere('case_number', '')->orderBy('id')->get();
        foreach ($records as $record) {
            $caseNumber = 'BL-' . date('Y', strtotime($record->created_at)) . '-' . str_pad($record->id, 6, '0', STR_PAD_LEFT);
            DB::table('guest_blacklists')->where('id', $record->id)->update(['case_number' => $caseNumber]);
        }

        // Add unique constraint if not already present
        $indexes = Schema::getIndexes('guest_blacklists');
        $hasUnique = collect($indexes)->contains(fn($idx) => in_array('case_number', $idx['columns']) && $idx['unique']);
        if (!$hasUnique) {
            Schema::table('guest_blacklists', function (Blueprint $table) {
                $table->unique('case_number');
            });
        }
    }

    public function down(): void
    {
        Schema::table('guest_blacklists', function (Blueprint $table) {
            $table->dropUnique('guest_blacklists_case_number_unique');
            $table->dropColumn(['case_number', 'release_reason', 'release_notes', 'released_by', 'released_at']);
        });
    }
};
