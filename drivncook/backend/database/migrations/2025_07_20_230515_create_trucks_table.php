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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchisee_id')->constrained()->onDelete('cascade');
            $table->string('plate_number')->unique();
            $table->string('model');
            $table->string('current_location')->nullable();
            $table->enum('status', ['en_service', 'en_panne', 'entretien'])->default('en_service');
            $table->date('last_service_date')->nullable();
            $table->date('next_service_due')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
