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
        Schema::create('agcodes', function (Blueprint $table) {
            $table->id(); // Primary key 'ID'
            $table->timestamp('created_at')->useCurrent(); // Default current timestamp for 'created_at'
            $table->timestamp('updated_at')->useCurrent()->nullable()->useCurrentOnUpdate(); // Default current timestamp for 'updated_at'
            $table->unsignedBigInteger('agcodepackage_id'); // Foreign key 'agCodePackage_id'
            $table->string('code'); // 'code' field
            $table->string('article'); // 'article' field
            $table->string('status'); // 'status' field
            $table->string('pin'); // 'pin' field

            // You can define foreign key relationships here, if applicable
            // $table->foreign('agcodepackage_id')->references('id')->on('code_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agcodes');
    }
};
