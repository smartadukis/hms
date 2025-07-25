<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->enum('status', ['pending','partial','dispensed'])
                  ->default('pending')
                  ->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
