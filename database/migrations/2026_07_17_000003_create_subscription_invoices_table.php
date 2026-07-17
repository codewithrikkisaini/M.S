<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 8, 2);
            $table->string('status')->default('unpaid'); // paid, unpaid, pending, refunded
            $table->date('billing_date');
            $table->date('due_date');
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_method')->default('Manual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
