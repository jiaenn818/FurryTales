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
        Schema::create('accessories', function (Blueprint $table) {
            $table->string('AccessoryID')->primary();
            $table->string('SupplierID');
            $table->foreign('SupplierID')->references('SupplierID')->on('suppliers')->onDelete('cascade');
            $table->string('AccessoryName');
            $table->enum('Category', ['Feeding', 'Grooming & Hygiene', 'Health & Safety', 'Travel & Outdoor', 'Fun & Comfort']);
            $table->text('Description');
            $table->decimal('Price', 10, 2);
            $table->string('Brand');
            $table->string('Image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
