<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla de categorías (tipo de indicador: Numérica, Cualitativa, etc.)
        if (!Schema::hasTable('categoria_indicador')) {
            Schema::create('categoria_indicador', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique(); // Ej: Numérica, Cualitativa
                $table->timestamps();
            });
        }

        // 2. Agregar FK a categoría en indicador
        Schema::table('indicador', function (Blueprint $table) {
            // Eliminar columnas antiguas si existen
            if (Schema::hasColumn('indicador', 'tipo')) {
                $table->dropColumn('tipo');
            }
            if (Schema::hasColumn('indicador', 'fuente')) {
                $table->dropColumn('fuente');
            }
            
            // Agregar FK a categoría
            if (!Schema::hasColumn('indicador', 'categoria_indicador_id')) {
                $table->foreignId('categoria_indicador_id')
                    ->nullable()
                    ->constrained('categoria_indicador')
                    ->cascadeOnDelete();
            }
        });

        // 3. Eliminar tabla antigua de relación si existe
        if (Schema::hasTable('indicador_categoria_indicador')) {
            Schema::dropIfExists('indicador_categoria_indicador');
        }
    }

    public function down(): void
    {
        Schema::table('indicador', function (Blueprint $table) {
            if (Schema::hasColumn('indicador', 'categoria_indicador_id')) {
                $table->dropColumn('categoria_indicador_id');
            }
        });

        Schema::dropIfExists('categoria_indicador');
    }
};
