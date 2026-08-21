<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair databases where the original upgrade migration was recorded before
     * its professional blacklist columns were added.
     */
    public function up(): void
    {
        Schema::table('guest_blacklists', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_blacklists', 'case_number')) {
                $table->string('case_number')->nullable()->after('hotel_id');
            }

            if (!Schema::hasColumn('guest_blacklists', 'release_reason')) {
                $table->text('release_reason')->nullable()->after('reason');
            }

            if (!Schema::hasColumn('guest_blacklists', 'release_notes')) {
                $table->text('release_notes')->nullable()->after('release_reason');
            }

            if (!Schema::hasColumn('guest_blacklists', 'released_by')) {
                $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete()->after('blacklisted_by');
            }

            if (!Schema::hasColumn('guest_blacklists', 'released_at')) {
                $table->timestamp('released_at')->nullable()->after('released_by');
            }
        });

        DB::table('guest_blacklists')
            ->where('status', 'removed')
            ->update([
                'status' => 'released',
                'released_at' => DB::raw('removed_at'),
                'released_by' => DB::raw('removed_by'),
            ]);

        DB::table('guest_blacklists')
            ->whereNull('case_number')
            ->orWhere('case_number', '')
            ->orderBy('id')
            ->each(function (object $record): void {
                $year = $record->created_at ? date('Y', strtotime($record->created_at)) : date('Y');
                $caseNumber = 'BL-' . $year . '-' . str_pad((string) $record->id, 6, '0', STR_PAD_LEFT);

                DB::table('guest_blacklists')
                    ->where('id', $record->id)
                    ->update(['case_number' => $caseNumber]);
            });

        $hasUniqueCaseNumber = collect(Schema::getIndexes('guest_blacklists'))
            ->contains(fn (array $index): bool => $index['unique'] && in_array('case_number', $index['columns'], true));

        if (!$hasUniqueCaseNumber) {
            Schema::table('guest_blacklists', function (Blueprint $table) {
                $table->unique('case_number');
            });
        }
    }

    public function down(): void
    {
        // This migration repairs a schema inconsistency and is intentionally irreversible.
    }
};
