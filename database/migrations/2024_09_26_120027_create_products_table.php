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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Equivalent to the primary_key in the Go struct for 'ID'
            $table->timestamp('created_at')->useCurrent(); // Default to current timestamp, same as sql:"DEFAULT:CURRENT_TIMESTAMP"
            $table->timestamp('updated_at')->useCurrent()->nullable()->useCurrentOnUpdate(); // Updates automatically
            $table->string('image')->nullable(); // Image field, nullable by default
            $table->string('nama'); // Equivalent to 'Nama' field, string type
            $table->string('material'); // Material field
            $table->string('color'); // Color field
            $table->string('price'); // Price field, assuming it's stored as a string
            $table->string('size'); // Size field
            $table->date('expiredate')->nullable(); // Expiredate as a date type, nullable
            $table->string('distributedon')->nullable(); // DistributedOn field, assuming it's a string, nullable
            $table->unsignedBigInteger('agclientbrand_id'); // Foreign key, unsigned big integer for 'agClientBrand_id'

            // Setting up the foreign key relationship
            $table->foreign('agclientbrand_id')->references('id')->on('agclientbrand')->onDelete('cascade'); // Assuming the related table is 'brands'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
