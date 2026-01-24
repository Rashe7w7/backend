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
        // Tabla de relación muchos-a-muchos entre indicadores y categorías
        Schema::create('indicador_categoria_indicador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('categoria_indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['indicador_id', 'categoria_indicador_id']);
        });

        // Modificar tabla indicador
        Schema::table('indicador', function (Blueprint $table) {
            // Agregar el campo tipo
            if (!Schema::hasColumn('indicador', 'tipo')) {
                $table->enum('tipo', ['grupo', 'ano_academico', 'facultad'])->default('grupo')->after('nombre');
            }
            
            // Eliminar la foreign key y la columna fuente si existen
            if (Schema::hasColumn('indicador', 'fuente')) {
                $table->dropColumn('fuente');
            }
            
            if (Schema::hasColumn('indicador', 'categoria_indicador_id')) {
                $table->dropForeign(['categoria_indicador_id']);
                $table->dropColumn('categoria_indicador_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicador_categoria_indicador');
        
        Schema::table('indicador', function (Blueprint $table) {
            $table->dropColumn('tipo');
            $table->enum('fuente', ['baja_estudiante', 'evaluacion_integral'])->after('nombre');
            $table->foreignId('categoria_indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
        });
    }
};
