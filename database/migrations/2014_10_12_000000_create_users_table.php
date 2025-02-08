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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Last name
            $table->string('prenom'); // First name
            $table->string('num_phone')->unique(); // Phone number
            $table->foreignId('type_user_id')->constrained('type_users')->onDelete('cascade'); // User type
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('actif')->default(true); // Active status
            $table->string('password_reset_token')->nullable();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('password_updated_at')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->foreignId('point_of_sale_id')->nullable()->constrained('point_of_sales')->onDelete('set null'); // POS link
            $table->boolean('is_commercial')->default(false);
            $table->integer('stock_journal')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
