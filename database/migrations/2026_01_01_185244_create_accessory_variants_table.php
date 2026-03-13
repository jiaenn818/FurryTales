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
        Schema::create('accessory_variants', function (Blueprint $table) {
            $table->id('VariantID');
            $table->string('AccessoryID');
            $table->foreign('AccessoryID')->references('AccessoryID')->on('accessories')->onDelete('cascade');
            $table->string('VariantKey');
            $table->decimal('PriceModifier', 10, 2)->default(0.00);
            $table->timestamps();
            
            $table->unique(['AccessoryID', 'VariantKey']);
            $table->index(['VariantID', 'AccessoryID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessory_variants');
    }
};
