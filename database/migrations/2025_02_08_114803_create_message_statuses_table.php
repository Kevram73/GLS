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
        Schema::create('message_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade'); // Links to messages
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade'); // Links to the recipient user
            $table->boolean('is_read')->default(false); // Tracks if the message was read
            $table->timestamp('read_at')->nullable(); // Timestamp for when the message was read
            $table->timestamps();
            $table->softDeletes(); // Enables soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_statuses');
    }
};
