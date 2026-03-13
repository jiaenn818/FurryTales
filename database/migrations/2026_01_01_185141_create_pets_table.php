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
        Schema::create('pets', function (Blueprint $table) {
            $table->string('PetID')->primary();
            $table->string('PetName');
            $table->enum('Type', ['Cat', 'Dog']);
            $table->string('Breed')->nullable();
            $table->integer('Age');
            $table->enum('Gender', ['Male', 'Female']);
            $table->string('Color')->nullable();
            $table->enum('Size', ['Small', 'Medium', 'Large']);
            $table->decimal('Price', 10, 2);
            $table->enum('HealthStatus', ['Excellent', 'Good', 'Fair']);
            $table->enum('VaccinationStatus', ['Up-to-date', 'Due Soon', 'Overdue', 'Not-vaccinated', 'Partial']);
            $table->string('Photo1')->nullable();
            $table->string('Photo2')->nullable();
            $table->string('Photo3')->nullable();
            $table->longText('image_features')->nullable(); // JSON validation handled by logic
            $table->string('OutletID')->nullable();
            $table->foreign('OutletID')->references('OutletID')->on('outlets')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
