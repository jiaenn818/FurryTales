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
        Schema::create('purchases', function (Blueprint $table) {
            $table->string('PurchaseID')->primary();
            $table->string('CustomerID');
            $table->foreign('CustomerID')->references('customerID')->on('customers')->onDelete('cascade');
            
            $table->dateTime('OrderDate');
            $table->enum('Method', ['PickUp', 'Delivery']);
            $table->decimal('TotalAmount', 10, 2);
            $table->text('DeliveryAddress')->nullable();
            $table->string('Postcode')->nullable();
            $table->string('State')->nullable();
            $table->string('Time')->nullable();
            $table->enum('Status', ['Pending', 'Picked Up', 'Out for Delivery', 'Delivered'])->default('Pending');
            $table->timestamp('DeliveredDate')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
