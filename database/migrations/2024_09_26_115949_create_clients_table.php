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
        Schema::create('agclients', function (Blueprint $table) {
            $table->id(); // Primary key 'ID'
            $table->timestamp('created_at')->useCurrent(); // Default current timestamp for 'created_at'
            $table->timestamp('updated_at')->useCurrent()->nullable()->useCurrentOnUpdate(); // Default current timestamp for 'updated_at'
            $table->string('name'); // 'name' field
            $table->string('address'); // 'address' field
            $table->string('phone'); // 'phone' field
            $table->string('email'); // 'email' field
            $table->string('web')->nullable(); // 'web' field, can be nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agclients');
    }
};
