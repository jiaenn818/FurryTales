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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->string('PurchaseID');
            $table->foreign('PurchaseID')->references('PurchaseID')->on('purchases')->onDelete('cascade');
            
            $table->string('PaymentMethod');
            $table->decimal('Amount', 10, 2);
            $table->string('TransactionID')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
