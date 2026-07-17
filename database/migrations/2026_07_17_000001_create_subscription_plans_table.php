<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('billing_cycle')->default('monthly'); // trial, monthly, yearly, lifetime
            $table->integer('trial_days')->default(0);
            $table->integer('max_rooms')->nullable(); // null means unlimited
            $table->integer('max_users')->nullable(); // null means unlimited
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
