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
        Schema::create('agclientbrand', function (Blueprint $table) {
            $table->id(); // Auto-incrementing unsigned primary key 'ID'
            $table->timestamp('created_at')->useCurrent(); // Default current timestamp for 'created_at'
            $table->timestamp('updated_at')->useCurrent()->nullable()->useCurrentOnUpdate(); // Default current timestamp for 'updated_at'
            $table->string('name')->nullable(); // 'name' field
            $table->string('addressOfficeOrStore')->nullable(); // 'Addressofficeorstore' field
            $table->string('csPhone')->nullable(); // 'Csphone' field
            $table->string('csEmail')->nullable(); // 'Csemail' field
            $table->string('web')->nullable(); // 'web' field
            $table->string('twitter')->nullable(); // 'twitter' field
            $table->string('facebook')->nullable(); // 'Facebook' field
            $table->string('instagram')->nullable(); // 'instagram' field
            $table->string('image')->nullable(); // 'image' field
            $table->unsignedBigInteger('agClient_id'); // Foreign key 'AgclientId'

            // Foreign key relationship (uncomment if needed)
            // $table->foreign('agClient_id')->references('id')->on('clients')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agclientbrand');
    }
};
