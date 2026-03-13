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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('CartItemID');
            $table->foreignId('CartID')->constrained('carts', 'CartID')->onDelete('cascade');
            
            $table->string('PetID')->nullable();
            $table->foreign('PetID')->references('PetID')->on('pets')->onDelete('cascade');
            
            $table->string('AccessoryID')->nullable();
            $table->foreign('AccessoryID')->references('AccessoryID')->on('accessories')->onDelete('cascade');
            
            $table->string('OutletID')->nullable();
            $table->foreign('OutletID')->references('OutletID')->on('outlets')->onDelete('set null');
            
            $table->unsignedBigInteger('VariantID')->nullable();
            $table->foreign('VariantID')->references('VariantID')->on('accessory_variants')->onDelete('set null');
            
            $table->integer('Quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
