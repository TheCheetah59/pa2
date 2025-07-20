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
    Schema::create('franchisees', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->string('address')->nullable();
        $table->string('city');
        $table->string('postal_code');
        $table->string('country')->default('France');
        $table->string('siret_number');
        $table->string('franchise_code')->unique();
        $table->boolean('entry_fee_paid')->default(false);
        $table->decimal('sales_percentage', 5, 2)->default(4.00);
        #$table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete(); // Table a créer
        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchisees');
    }
};
