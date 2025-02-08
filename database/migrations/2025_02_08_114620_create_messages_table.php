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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade'); // Links to conversation
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Sender reference
            $table->text('content')->nullable(); // Message text content
            $table->string('file')->nullable(); // Optional file attachment
            $table->timestamp('sent_at')->nullable(); // When the message was sent
            $table->timestamps();
            $table->softDeletes(); // Enables soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
