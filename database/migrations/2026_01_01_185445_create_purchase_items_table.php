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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id('PurchaseItemID');
            $table->string('PurchaseID');
            $table->foreign('PurchaseID')->references('PurchaseID')->on('purchases')->onDelete('cascade');
            
            $table->string('ItemID')->nullable(); // Reference to PetID
            $table->foreign('ItemID')->references('PetID')->on('pets')->onDelete('cascade');
            
            $table->string('AccessoryID')->nullable();
            $table->foreign('AccessoryID')->references('AccessoryID')->on('accessories')->onDelete('cascade');
            
            $table->string('OutletID')->nullable();
            $table->foreign('OutletID')->references('OutletID')->on('outlets')->onDelete('set null');
            
            $table->unsignedBigInteger('VariantID')->nullable();
            $table->foreign('VariantID')->references('VariantID')->on('accessory_variants')->onDelete('set null');
            
            $table->longText('SelectedDetails')->nullable(); // JSON validation handled by logic
            $table->integer('Quantity');
            $table->decimal('Price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
