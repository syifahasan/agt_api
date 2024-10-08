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
        Schema::create('agnotificationclient', function (Blueprint $table) {
            $table->id(); // Primary key 'ID'
            $table->timestamp('created_at')->useCurrent(); // Default current timestamp for 'created_at'
            $table->timestamp('updated_at')->useCurrent()->nullable()->useCurrentOnUpdate(); // Default current timestamp for 'updated_at', updates automatically
            $table->unsignedBigInteger('agclient_id'); // Foreign key for 'agclient_id'

            // Use JSON type for storing structured data like 'Data' struct
            $table->json('data'); // 'data' field to store JSON (Code, Product, User)

            $table->tinyInteger('status'); // Tiny integer for 'status'
            $table->string('type'); // 'type' field
            $table->unsignedBigInteger('aguser_id'); // Foreign key for 'aguser_id'

            // Foreign key relationships (if needed)
            // $table->foreign('agclient_id')->references('id')->on('clients')->onDelete('cascade');
            // $table->foreign('aguser_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agnotificationclient');
    }
};
