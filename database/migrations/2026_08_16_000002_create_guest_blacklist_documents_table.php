<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_blacklist_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_blacklist_id')->constrained('guest_blacklists')->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->string('disk', 30)->default('local');
            $table->string('storage_path', 500);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['guest_blacklist_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_blacklist_documents');
    }
};
