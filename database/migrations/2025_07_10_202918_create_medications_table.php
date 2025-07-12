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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('generic_name')->nullable();

            $table->float('strength', 10, 4);
            $table->enum('unit_of_strength', ['mg', 'g', 'mcg', 'IU', 'ml', 'unit', '%']);
            $table->enum('category', ['Tablet', 'Capsule', 'Syrup', 'Injection', 'Cream', 'Drops', 'Patch', 'Spray', 'Suppository', 'Inhaler', 'Others']);

            $table->enum('dispensing_unit', ['Tablet', 'Capsule', 'ml', 'sachet', 'vial', 'puff', 'drop', 'unit']);
            $table->integer('pack_size');

            $table->string('manufacturer')->nullable();
            $table->string('barcode_or_ndc')->unique()->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_controlled')->default(false);
            $table->boolean('requires_refrigeration')->default(false);
            $table->string('storage_conditions')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
