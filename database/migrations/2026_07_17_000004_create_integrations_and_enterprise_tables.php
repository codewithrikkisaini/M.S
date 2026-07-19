<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Activity Logs table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // 2. API Keys table
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('status')->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        // 3. Email & WhatsApp Templates table
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->string('type'); // booking_confirmation, check_in, invoice_receipt, whatsapp_welcome
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('variables')->nullable(); // JSON list of available variables
            $table->timestamps();
        });

        // 4. Channel Connections table
        Schema::create('channel_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->string('channel_name'); // Booking.com, Expedia, Airbnb
            $table->string('status')->default('disconnected'); // connected, disconnected
            $table->string('sync_status')->default('pending'); // synced, failed, pending
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('channel_connections');
    }
};
