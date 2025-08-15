<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Colonnes d'activation (ajout simple)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_activated')) {
                $table->boolean('is_activated')->default(false);
            }
            if (!Schema::hasColumn('users', 'activation_token')) {
                $table->string('activation_token')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'activation_token_expires_at')) {
                $table->timestamp('activation_token_expires_at')->nullable();
            }
        });

        // 2) Normaliser la colonne role pour Postgres : VARCHAR + CHECK
        //    - On drop toute contrainte CHECK existante si elle existe
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

        // S'il n'y a pas de colonne 'role', on la crée; sinon on la convertit proprement
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('client');
            });
            DB::statement("ALTER TABLE users ALTER COLUMN role SET NOT NULL");
        } else {
            // Forcer le type texte (varchar), les valeurs NULL → 'client', puis NOT NULL + DEFAULT
            DB::table('users')->whereNull('role')->update(['role' => 'client']);
            // Convertit le type en VARCHAR(20) proprement (USING role::text couvre le cas enum)
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(20) USING role::text");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'client'");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET NOT NULL");
        }

        // Ajouter la contrainte CHECK séparément (la seule manière correcte en Postgres)
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_role_check
            CHECK (role IN ('admin', 'client', 'franchise'))
        ");
    }

    public function down(): void
    {
        // Supprime la contrainte CHECK d'abord
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

        // Rendre role à nouveau nullable et sans défaut (état neutre, portable)
        DB::statement("ALTER TABLE users ALTER COLUMN role DROP NOT NULL");
        DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");

        // Supprimer les colonnes d'activation si elles existent
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'activation_token')) {
                $table->dropUnique(['activation_token']);
            }
            if (Schema::hasColumn('users', 'is_activated')) {
                $table->dropColumn('is_activated');
            }
            if (Schema::hasColumn('users', 'activation_token')) {
                $table->dropColumn('activation_token');
            }
            if (Schema::hasColumn('users', 'activation_token_expires_at')) {
                $table->dropColumn('activation_token_expires_at');
            }
        });

    }
};
