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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchisee_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('delivery_address');
            $table->dateTime('delivery_date');
            $table->enum('status', ['en_attente', 'confirmée', 'livrée', 'annulée'])->default('en_attente');
            $table->enum('payment_status', ['non_payé', 'payé', 'refusé'])->default('non_payé');
            $table->string('payment_method')->nullable();
            $table->decimal('total_amount', 8, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
