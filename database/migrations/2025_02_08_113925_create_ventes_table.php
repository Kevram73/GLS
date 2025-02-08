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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->decimal('montant', 10, 2);
            $table->foreignId('point_of_sale_id')->constrained('point_of_sales')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('journal_id')->nullable()->constrained('journals')->onDelete('set null');
            $table->integer('nbre')->default(1);
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes(); // Enables soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
