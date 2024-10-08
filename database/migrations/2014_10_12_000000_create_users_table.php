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
        Schema::create('agusers', function (Blueprint $table) {
            $table->id();  // Auto-incrementing unique ID for each user
            $table->string('name');  // Full name
            $table->string('username')->unique();  // Username, must be unique
            $table->string('email')->unique();  // Email, must be unique
            $table->string('password');  // Password
            $table->date('dateOfBirth');  // Date of Birth
            $table->tinyInteger('gender');  // Gender: 0 = Women, 1 = Men
            $table->string('phone');  // Phone number
            $table->string('address');
            $table->string('verified_email')->nullable();
            $table->tinyInteger('verified')->default(0);
            $table->string('email_token')->nullable();  // Address
            $table->timestamps();
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
