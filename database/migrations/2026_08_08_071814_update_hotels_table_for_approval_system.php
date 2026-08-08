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
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'hotel_code')) {
                $table->string('hotel_code')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('hotels', 'account_status')) {
                $table->string('account_status')->default('pending_approval')->index()->after('status');
            }
            if (!Schema::hasColumn('hotels', 'registration_notes')) {
                $table->text('registration_notes')->nullable()->after('account_status');
            }
            if (!Schema::hasColumn('hotels', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('registration_notes');
            }
            if (!Schema::hasColumn('hotels', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['hotel_code', 'account_status', 'registration_notes', 'approved_at', 'approved_by']);
        });
    }
};
