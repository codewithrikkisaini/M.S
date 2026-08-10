<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            // Business Info (Step 1)
            $table->string('business_name')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('company_reg_number')->nullable();
            $table->string('business_license_number')->nullable();

            // Contact Info (Step 2)
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();

            // Location (Step 3)
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('currency')->default('USD');

            // Hotel Info (Step 4)
            $table->integer('rooms_count')->nullable();
            $table->string('category')->nullable();
            $table->string('property_type')->nullable();
            $table->string('current_pms')->nullable();
            $table->string('current_channel_manager')->nullable();
            $table->string('current_website')->nullable();
        });

        // Modify status column enum in systems that support it, or handle it fallback
        try {
            Schema::table('hotels', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        } catch (\Exception $e) {
            // SQLite or certain drivers may ignore/fail on change(), which is fine
        }
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'business_name', 'owner_name', 'tax_id', 'company_reg_number', 'business_license_number',
                'whatsapp', 'website',
                'country', 'state', 'city', 'postal_code', 'timezone', 'currency',
                'rooms_count', 'category', 'property_type', 'current_pms', 'current_channel_manager', 'current_website'
            ]);
        });
    }
};
