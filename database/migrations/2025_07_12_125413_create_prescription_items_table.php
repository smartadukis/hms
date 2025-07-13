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
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->foreignId('medication_id')->constrained()->onDelete('cascade');

            $table->integer('dosage_quantity'); // e.g. 1 or 2
            $table->string('dosage_unit'); // e.g. tablet, ml, etc
            $table->string('frequency'); // e.g. once daily, twice daily
            $table->string('duration'); // e.g. 7 days
            $table->text('instructions')->nullable(); // Take after food, etc.

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
