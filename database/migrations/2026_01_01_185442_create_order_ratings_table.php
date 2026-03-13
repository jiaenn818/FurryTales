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
        Schema::create('order_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('PurchaseID');
            $table->foreign('PurchaseID')->references('PurchaseID')->on('purchases')->onDelete('cascade');
            $table->string('CustomerID');
            $table->foreign('CustomerID')->references('customerID')->on('customers')->onDelete('cascade');
            
            $table->tinyInteger('rating')->unsigned();
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['PurchaseID', 'CustomerID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_ratings');
    }
};
