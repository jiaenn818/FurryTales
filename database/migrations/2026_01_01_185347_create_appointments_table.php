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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('AppointmentID');
            $table->string('CustomerID');
            $table->foreign('CustomerID')->references('customerID')->on('customers')->onDelete('cascade');
            $table->string('PetID');
            $table->foreign('PetID')->references('PetID')->on('pets')->onDelete('cascade');
            
            $table->dateTime('AppointmentDateTime');
            $table->enum('Method', ['In-Person', 'Video Call']);
            $table->string('CustomerName');
            $table->string('CustomerPhone', 20);
            $table->enum('Status', ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'])->default('Upcoming');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
