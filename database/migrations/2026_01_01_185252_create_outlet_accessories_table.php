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
        Schema::create('outlet_accessories', function (Blueprint $table) {
            $table->id('OutletAccessoryID');
            $table->string('OutletID');
            $table->foreign('OutletID')->references('OutletID')->on('outlets')->onDelete('cascade');
            $table->string('AccessoryID');
            $table->foreign('AccessoryID')->references('AccessoryID')->on('accessories')->onDelete('cascade');
            $table->unsignedBigInteger('VariantID')->nullable();
            
            // Composite FK logic for VariantID+AccessoryID integrity if enforced by DB, 
            // but standard FK here:
            $table->foreign(['VariantID', 'AccessoryID'])->references(['VariantID', 'AccessoryID'])->on('accessory_variants')->onDelete('cascade');

            $table->integer('StockQty');
            $table->timestamps();

            $table->unique(['OutletID', 'AccessoryID', 'VariantID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_accessories');
    }
};
