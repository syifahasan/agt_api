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
        Schema::create('zappid', function (Blueprint $table) {
            $table->id()->nullable(); // Auto-incrementing unsigned primary key 'ID'
            $table->string('appid'); // 'appid' field
            $table->unsignedBigInteger('clientid'); // 'clientid' field as an unsigned big integer
            $table->string('name'); // 'name' field
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zappid');
    }
};
