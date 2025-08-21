<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('truck_maintenances', function (Blueprint $table) {
            // Ajouts requis par le cahier des charges
            $table->string('type')->default('revision')->after('description'); // revision|panne|reparation|autre
            $table->text('notes')->nullable()->after('type');
            $table->decimal('cost', 10, 2)->default(0)->after('notes');

            // Optionnel : index sur type si tu filtres souvent dessus
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('truck_maintenances', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'notes', 'cost']);
        });
    }
};
