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
        if (Schema::hasTable('indicador_carrera')) {
            Schema::table('indicador_carrera', function (Blueprint $table) {
                if (!Schema::hasColumn('indicador_carrera', 'categoria_indicador_id')) {
                    $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
                }
            });
        }

        if (Schema::hasTable('indicador_grupo')) {
            Schema::table('indicador_grupo', function (Blueprint $table) {
                if (!Schema::hasColumn('indicador_grupo', 'categoria_indicador_id')) {
                    $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
                }
            });
        }

        if (Schema::hasTable('indicador_facultad')) {
            Schema::table('indicador_facultad', function (Blueprint $table) {
                if (!Schema::hasColumn('indicador_facultad', 'categoria_indicador_id')) {
                    $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
                }
            });
        }

        if (Schema::hasTable('indicador_departamento')) {
            Schema::table('indicador_departamento', function (Blueprint $table) {
                if (!Schema::hasColumn('indicador_departamento', 'categoria_indicador_id')) {
                    $table->foreignId('categoria_indicador_id')->nullable()->after('indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('indicador_carrera') && Schema::hasColumn('indicador_carrera', 'categoria_indicador_id')) {
            Schema::table('indicador_carrera', function (Blueprint $table) {
                $table->dropForeignIdFor('CategoriaIndicador');
                $table->dropColumn('categoria_indicador_id');
            });
        }

        if (Schema::hasTable('indicador_grupo') && Schema::hasColumn('indicador_grupo', 'categoria_indicador_id')) {
            Schema::table('indicador_grupo', function (Blueprint $table) {
                $table->dropForeignIdFor('CategoriaIndicador');
                $table->dropColumn('categoria_indicador_id');
            });
        }

        if (Schema::hasTable('indicador_facultad') && Schema::hasColumn('indicador_facultad', 'categoria_indicador_id')) {
            Schema::table('indicador_facultad', function (Blueprint $table) {
                $table->dropForeignIdFor('CategoriaIndicador');
                $table->dropColumn('categoria_indicador_id');
            });
        }

        if (Schema::hasTable('indicador_departamento') && Schema::hasColumn('indicador_departamento', 'categoria_indicador_id')) {
            Schema::table('indicador_departamento', function (Blueprint $table) {
                $table->dropForeignIdFor('CategoriaIndicador');
                $table->dropColumn('categoria_indicador_id');
            });
        }
    }
};
