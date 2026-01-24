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
        // Indicador por Grupo
        Schema::create('indicador_grupo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupo')->cascadeOnDelete();
            $table->foreignId('curso_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['indicador_id', 'grupo_id', 'curso_academico_id']);
        });

        // Indicador por Año Académico
        Schema::create('indicador_anno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('anio_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            $table->foreignId('curso_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['indicador_id', 'anio_academico_id', 'curso_academico_id']);
        });

        // Indicador por Carrera
        Schema::create('indicador_carrera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('carrera_id')->constrained('carrera')->cascadeOnDelete();
            $table->foreignId('curso_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['indicador_id', 'carrera_id', 'curso_academico_id']);
        });

        // Indicador por Departamento
        Schema::create('indicador_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamento')->cascadeOnDelete();
            $table->foreignId('curso_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['indicador_id', 'departamento_id', 'curso_academico_id']);
        });

        // Indicador por Facultad (sin curso académico)
        Schema::create('indicador_facultad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('facultad_id')->constrained('facultad')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['indicador_id', 'facultad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicador_facultad');
        Schema::dropIfExists('indicador_departamento');
        Schema::dropIfExists('indicador_carrera');
        Schema::dropIfExists('indicador_anno');
        Schema::dropIfExists('indicador_grupo');
    }
};
