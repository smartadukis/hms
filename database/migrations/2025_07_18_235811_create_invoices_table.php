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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->enum('status', ['unpaid', 'paid', 'partial'])->default('unpaid');
            $table->string('payment_method')->nullable(); // e.g., Cash, POS, Transfer
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete(); // Admin/Receptionist/Doctor
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
