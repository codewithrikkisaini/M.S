<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_document_id')->constrained('hotel_documents')->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50)->index();
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30)->nullable();
            $table->text('comment')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['hotel_document_id', 'created_at']);
            $table->index(['hotel_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_audit_logs');
    }
};
