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
        Schema::create('agcheckinglog', function (Blueprint $table) {
            $table->id(); // Auto-incrementing unsigned primary key 'ID'
            $table->timestamp('created_at')->useCurrent(); // Default current timestamp for 'created_at'
            $table->timestamp('updated_at')->useCurrent()->nullable()->useCurrentOnUpdate(); // Default current timestamp for 'updated_at'

            // Unsigned big integers for the foreign keys and related fields
            $table->unsignedBigInteger('agproductclient_id'); // 'AgproductclientId'
            $table->unsignedBigInteger('agcodes_id'); // 'AgcodesId'
            $table->unsignedBigInteger('aguser_id'); // 'AguserId'

            $table->string('appid'); // 'appid' field
            $table->string('geoloclangitude'); // 'Geoloclangitude' field
            $table->string('geoloclongitude'); // 'Geoloclongitude' field

            // Optional foreign key constraints (if needed)
            // $table->foreign('agproductclient_id')->references('id')->on('product_clients')->onDelete('cascade');
            // $table->foreign('agcodes_id')->references('id')->on('codes')->onDelete('cascade');
            // $table->foreign('aguser_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agcheckinglog');
    }
};
