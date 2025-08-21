<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('truck_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->text('description')->nullable();

            // Champs requis
            $table->string('type')->default('revision');   // revision|panne|reparation|autre
            $table->text('notes')->nullable();
            $table->decimal('cost', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('truck_maintenances');
    }
};
