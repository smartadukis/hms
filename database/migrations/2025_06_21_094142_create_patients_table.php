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
    Schema::create('patients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->unique();
        $table->enum('gender', ['Male', 'Female']);
        $table->date('dob');
        $table->string('blood_group')->nullable();
        $table->text('address')->nullable();
        $table->unsignedBigInteger('created_by');
        $table->timestamps();

        $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
