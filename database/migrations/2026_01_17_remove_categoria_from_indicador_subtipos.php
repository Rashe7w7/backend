<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove categoria_indicador_id from inherited indicator tables.
     * Only the base Indicador table has relationship with CategoriaIndicador
     */
    public function up(): void
    {
        if (Schema::hasTable('indicador_carrera') && Schema::hasColumn('indicador_carrera', 'categoria_indicador_id')) {
            Schema::table('indicador_carrera', function (Blueprint $table) {
                $table->dropColumn('categoria_indicador_id');
            });
        }

        if (Schema::hasTable('indicador_grupo') && Schema::hasColumn('indicador_grupo', 'categoria_indicador_id')) {
            Schema::table('indicador_grupo', function (Blueprint $table) {
                $table->dropColumn('categoria_indicador_id');
            });
        }

        if (Schema::hasTable('indicador_facultad') && Schema::hasColumn('indicador_facultad', 'categoria_indicador_id')) {
            Schema::table('indicador_facultad', function (Blueprint $table) {
                $table->dropColumn('categoria_indicador_id');
            });
        }

        if (Schema::hasTable('indicador_departamento') && Schema::hasColumn('indicador_departamento', 'categoria_indicador_id')) {
            Schema::table('indicador_departamento', function (Blueprint $table) {
                $table->dropColumn('categoria_indicador_id');
            });
        }

        if (Schema::hasTable('indicador_anno') && Schema::hasColumn('indicador_anno', 'categoria_indicador_id')) {
            Schema::table('indicador_anno', function (Blueprint $table) {
                $table->dropColumn('categoria_indicador_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('indicador_carrera')) {
            Schema::table('indicador_carrera', function (Blueprint $table) {
                $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('indicador_grupo')) {
            Schema::table('indicador_grupo', function (Blueprint $table) {
                $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('indicador_facultad')) {
            Schema::table('indicador_facultad', function (Blueprint $table) {
                $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('indicador_departamento')) {
            Schema::table('indicador_departamento', function (Blueprint $table) {
                $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('indicador_anno')) {
            Schema::table('indicador_anno', function (Blueprint $table) {
                $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            });
        }
    }
};
