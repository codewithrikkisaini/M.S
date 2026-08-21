<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('guest_blacklists', 'case_number')) {
            Schema::table('guest_blacklists', function (Blueprint $table) {
                $table->string('case_number')->nullable()->after('hotel_id');
            });
        }

        if (!Schema::hasColumn('guest_blacklists', 'release_reason')) {
            Schema::table('guest_blacklists', function (Blueprint $table) {
                $table->text('release_reason')->nullable()->after('reason');
            });
        }

        if (!Schema::hasColumn('guest_blacklists', 'release_notes')) {
            Schema::table('guest_blacklists', function (Blueprint $table) {
                $table->text('release_notes')->nullable()->after('release_reason');
            });
        }

        if (!Schema::hasColumn('guest_blacklists', 'released_by')) {
            Schema::table('guest_blacklists', function (Blueprint $table) {
                $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete()->after('blacklisted_by');
            });
        }

        if (!Schema::hasColumn('guest_blacklists', 'released_at')) {
            Schema::table('guest_blacklists', function (Blueprint $table) {
                $table->timestamp('released_at')->nullable()->after('released_by');
            });
        }

        // Migrate existing 'removed' status to 'released' if columns exist
        if (Schema::hasColumn('guest_blacklists', 'removed_at') && Schema::hasColumn('guest_blacklists', 'released_at')) {
            DB::table('guest_blacklists')
                ->where('status', 'removed')
                ->update([
                    'status' => 'released',
                    'released_at' => DB::raw('removed_at'),
                    'released_by' => DB::raw('removed_by'),
                ]);
        }

        // Generate case numbers for records that don't have them
        if (Schema::hasColumn('guest_blacklists', 'case_number')) {
            $records = DB::table('guest_blacklists')
                ->whereNull('case_number')
                ->orWhere('case_number', '')
                ->orderBy('id')
                ->get();
                
            foreach ($records as $record) {
                $year = !empty($record->created_at) ? date('Y', strtotime($record->created_at)) : date('Y');
                $caseNumber = 'BL-' . $year . '-' . str_pad($record->id, 6, '0', STR_PAD_LEFT);
                DB::table('guest_blacklists')->where('id', $record->id)->update(['case_number' => $caseNumber]);
            }
        }

        // Add unique constraint if not already present
        try {
            $indexes = Schema::getIndexes('guest_blacklists');
            $hasUnique = collect($indexes)->contains(fn($idx) => in_array('case_number', $idx['columns'] ?? []) && ($idx['unique'] ?? false));
            if (!$hasUnique) {
                Schema::table('guest_blacklists', function (Blueprint $table) {
                    $table->unique('case_number');
                });
            }
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        Schema::table('guest_blacklists', function (Blueprint $table) {
            try {
                $table->dropUnique(['case_number']);
            } catch (\Throwable $e) {}
            
            $cols = array_filter(['case_number', 'release_reason', 'release_notes', 'released_by', 'released_at'], function($c) {
                return Schema::hasColumn('guest_blacklists', $c);
            });
            
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
