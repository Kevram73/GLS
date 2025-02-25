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
        Schema::create('conversmsg', function (Blueprint $table) {
            $table->id();
            $table->text('content')->nullable();
            $table->string('file')->nullable();
            $table->date('date_sent')->nullable();
            $table->time('time_sent')->nullable();
            $table->integer('sender_id')->unsigned();
            $table->integer('receiver_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convermsg');
    }
};
