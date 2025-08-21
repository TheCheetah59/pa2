<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Renames sûrs côté Postgres (pas besoin de doctrine/dbal)
        if (Schema::hasColumn('supplies', 'item')) {
            DB::statement('ALTER TABLE supplies RENAME COLUMN item TO product_name');
        }
        if (Schema::hasColumn('supplies', 'qty')) {
            DB::statement('ALTER TABLE supplies RENAME COLUMN qty TO quantity');
        }
        if (Schema::hasColumn('supplies', 'source')) {
            DB::statement('ALTER TABLE supplies RENAME COLUMN source TO source_type');
        }

        // Colonnes utiles si absentes
        Schema::table('supplies', function (Blueprint $table) {
            if (!Schema::hasColumn('supplies', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('supplies', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        // Contrainte CHECK 80/20 sur source_type
        DB::statement("DO $$
        BEGIN
            IF NOT EXISTS (
                SELECT 1 FROM pg_constraint WHERE conname = 'chk_supplies_source_type'
            ) THEN
                EXECUTE 'ALTER TABLE supplies
                         ADD CONSTRAINT chk_supplies_source_type
                         CHECK (source_type IN (''obligatoire'',''libre''))';
            END IF;
        END$$;");

        // Index pratiques
        Schema::table('supplies', function (Blueprint $table) {
            $table->index('franchisee_id');
            $table->index('warehouse_id');
            $table->index('source_type');
        });
    }

    public function down(): void
    {
        // Drop contrainte + index
        DB::statement("ALTER TABLE supplies DROP CONSTRAINT IF EXISTS chk_supplies_source_type");
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropIndex(['franchisee_id']);
            $table->dropIndex(['warehouse_id']);
            $table->dropIndex(['source_type']);
            $table->dropColumn(['unit_price','notes']);
        });

        // Renames inverses (si besoin)
        if (Schema::hasColumn('supplies', 'product_name')) {
            DB::statement('ALTER TABLE supplies RENAME COLUMN product_name TO item');
        }
        if (Schema::hasColumn('supplies', 'quantity')) {
            DB::statement('ALTER TABLE supplies RENAME COLUMN quantity TO qty');
        }
        if (Schema::hasColumn('supplies', 'source_type')) {
            DB::statement('ALTER TABLE supplies RENAME COLUMN source_type TO source');
        }
    }
};
